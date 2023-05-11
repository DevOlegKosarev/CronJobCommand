<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models;
use Throwable;

/**
 * Class CronJobCommand
 *
 * This class is a custom command that runs cron tasks based on the task group name provided as an argument.
 * It extends the BaseCommand class from the CodeIgniter CLI library and implements the run() method.
 * It also uses the FoxwayPresetStatesModel from the App\Models namespace to interact with the database.
 *
 * @package App\Commands
 */
class CronJobCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Cron';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'cron:job';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'A custom command that runs cron tasks based on the task group name argument';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cron:job <task_group> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'task_group' => 'The name of the group of tasks to run. For example, email, backup, etc.'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '-f|--force' => 'This option will force all tasks in the group to run regardless of their schedule or status.',
        '-d|--dry-run|--debug' => 'This option will only display what tasks would be run without actually executing them.',
        '-v|--verbose' => 'This option will display more information about each task that is run, such as its name, command, schedule, status, and last run time.',
    ];


    /**
     * Run cron jobs based on a group name and a schedule.
     *
     * @param array $params An array of parameters, the first element is the group name
     * @return void
     * @throws \Exception If the group name is empty or no enabled tasks are found for the group
     */
    public function run(array $params)
    {
        // Get the group name from the parameters or prompt the user to enter it
        $task_group = array_shift($params);

        if (empty($task_group)) {
            $task_group = CLI::prompt('Group name', null, 'required'); // @codeCoverageIgnore
        }

        try {

            // $tasks = $CronJobModel->where('task_status', 'enabled')->where('task_group', $task_group)->findAll();

            // Parse the command line options
            $options = $_SERVER['argv'];
            // Check if the verbose option is set
            $verbose = in_array('-v', $options) || in_array('--verbose', $options) ? true : false;
            // Check if the dry-run option is set
            $dry_run = in_array('-d', $options) || in_array('--dry-run', $options) || in_array('–-dry-run', $options) ? true : false;
            // Check if the force option is set
            $force = in_array('-f', $options) || in_array('--force', $options) ? true : false;

            // Create an instance of the CronJobModel and query for the enabled tasks for the group
            $CronJobModel = new Models\CronJobModel();

            // If the group name is all, get all tasks from all groups
            if ($task_group == 'all') {
                $tasks = $CronJobModel->findAll();
            } else {
                // Otherwise, get only tasks from the specified group
                $tasks = $CronJobModel->where('task_group', $task_group)->findAll();
            }

            // If no tasks are found, throw an exception
            if (count($tasks) <= 0) {
                throw new \Exception("The group $task_group does not have any enabled tasks. Please check the configuration file or the database for possible errors.");
            }
            // Create an array to store the shell commands for the tasks
            $tasksShellArray = [];
            // Get the current time
            $current_time = time();
            CLI::write("Running task group: $task_group", "yellow");
            CLI::newLine();
            foreach ($tasks as $keyTask => $task) {
                // If not dry-run, update the task last run time
                if (!$dry_run) {
                    $CronJobModel->update($task->task_id, ['task_last_run' => date('Y-m-d H:i:s', $current_time)]);
                }
                // If force, run all tasks regardless of schedule or status
                if ($force) {
                    // Add the task command to the array
                    $tasksShellArray[] = $task->task_command;
                    // If verbose, display more information about the task
                    if ($verbose) {
                        CLI::write("Task name: {$task->task_name}", "yellow");
                        CLI::write("Task command: {$task->task_command}", "yellow");
                        CLI::write("Task schedule: {$task->task_schedule}", "yellow");
                        CLI::write("Task status: {$task->task_status}", "yellow");
                        CLI::write("Task last run: {$task->task_last_run}", "yellow");
                    }
                } else {
                    // Otherwise, check if the task matches the schedule and is enabled
                    $cron_match = $this->cron_match($task->task_schedule, $current_time);
                    if ($cron_match == true && $task->task_status == 'enabled') {
                        // If yes, add the task command to the array
                        $tasksShellArray[] = $task->task_command;
                        // If verbose, display more information about the task
                        if ($verbose) {
                            CLI::write("Task name: {$task->task_name}", "yellow");
                            CLI::write("Task command: {$task->task_command}", "yellow");
                            CLI::write("Task schedule: {$task->task_schedule}", "yellow");
                            CLI::write("Task last run: {$task->task_last_run}", "yellow");
                            CLI::write("Task status: {$task->task_status}", "yellow");
                        }
                    }
                }
                CLI::write("Running task...", "yellow");
                CLI::write("Task completed.");
                CLI::newLine();
            }
            // Get the number of commands in the array
            $tasksShellCount = count($tasksShellArray);
            // If there are any commands in the array, execute them in parallel
            if ($tasksShellCount > 0) {
                $tasksShellStr = implode(" & ", $tasksShellArray);
                CLI::write("Tasks for group $task_group found and started executing.", "green");
                // If not dry-run, execute the commands
                if (!$dry_run) {
                    exec($tasksShellStr);
                    exit();
                }
            } else {
                CLI::write("No tasks for group $task_group found to run", 'green');
            }
        } catch (Throwable $e) {
            // If any error occurs, display it in red color and start a new line
            CLI::error($e->getMessage(), 'light_gray', 'red');
            CLI::newLine();
        }
    }

    /**
     * Compares cron time with current time
     *
     * This function takes a cron string and a current time as arguments and checks if they match.
     * The cron string consists of five parts: minutes, hours, days of month, months and days of week.
     * Each part can have a single value, a list of values separated by commas, or a range of values 
     * separated by hyphens.
     * A star (*) means any value.
     *
     * @param string $cron_string The cron string to compare
     * @param int $current_time The current time as a UNIX timestamp
     * @return bool True if the cron time matches the current time, false otherwise
     */
    function cron_match($cron_string, $current_time)
    {
        // Split the cron string into five parts: minutes, hours, days of month, months and days of week
        $cron_parts = explode(" ", $cron_string);
        if (count($cron_parts) != 5) {
            return false; // Invalid cron string format
        }

        // Get the current values of minutes, hours, days of month, months and days of week from the UNIX timestamp
        list($current_minute, $current_hour, $current_day, $current_month, $current_weekday) = explode(" ", date("i H j n w", $current_time));

        // Check each part of the cron string against the current time
        foreach ($cron_parts as $index => $part) {
            // Skip the part if it is a star (*), which means any value
            if ($part == "*") {
                continue;
            }

            // Split the part into subparts by comma (,) if it contains a list of values
            $subparts = explode(",", $part);

            // Check each subpart against the current time
            $match = false; // Flag for matching
            foreach ($subparts as $subpart) {
                // If the subpart contains a hyphen (-), then it specifies a range of values
                if (strpos($subpart, "-") !== false) {
                    // Split the subpart into start and end of the range
                    list($start, $end) = explode("-", $subpart);

                    // Determine the current value depending on the index of the cron string part
                    switch ($index) {
                        case 0: // Minutes
                            $current_value = $current_minute;
                            break;
                        case 1: // Hours
                            $current_value = $current_hour;
                            break;
                        case 2: // Days of month
                            $current_value = $current_day;
                            break;
                        case 3: // Months
                            $current_value = $current_month;
                            break;
                        case 4: // Days of week
                            $current_value = $current_weekday;
                            break;
                    }

                    // Check if the current value is within the range
                    if ($current_value >= $start && $current_value <= $end) {
                        $match = true; // Match found
                        break; // Exit the loop over subparts
                    }
                } else {
                    // The subpart is a single value, so compare it with the current value directly
                    switch ($index) {
                        case 0: // Minutes
                            if ($subpart == $current_minute) {
                                $match = true; // Match found
                                break; // Exit the loop over subparts
                            }
                            break;
                        case 1: // Hours
                            if ($subpart == $current_hour) {
                                $match = true; // Match found
                                break; // Exit the loop over subparts
                            }
                            break;
                        case 2: // Days of month
                            if ($subpart == $current_day) {
                                $match = true; // Match found
                                break; // Exit the loop over subparts
                            }
                            break;
                        case 3: // Months
                            if ($subpart == $current_month) {
                                $match = true; // Match found
                                break; // Exit the loop over subparts
                            }
                            break;
                        case 4: // Days of week
                            if ($subpart == $current_weekday) {
                                $match = true; // Match found
                                break; // Exit the loop over subparts
                            }
                            break;
                    }
                }
            }

            // If no match was found for this part of the cron string, then return false
            if (!$match) {
                return false;
            }
        }

        // If all parts of the cron string matched with the current time, then return true
        return true;
    }
}
