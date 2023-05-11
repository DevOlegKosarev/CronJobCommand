<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\CLI\CLI;

/**
 * Class CronJobs
 *
 * This class is a migration that creates a table named 'task' in the database.
 * The table has the following fields:
 * - task_id: an auto-incrementing integer that serves as the primary key
 * - preset_id: an integer that references a preset group of tasks
 * - task_title: a string that describes the task
 * - task_vendor: a string that identifies the vendor of the task
 * - task_command: a string that specifies the command to run the task
 * - task_schedule: a string that defines the schedule of the task
 * - task_last_run: a date and time that records the last time the task was run
 * - task_status: an enum that indicates whether the task is enabled or disabled
 *
 * The class implements the up() and down() methods from the Migration class to create and drop the table respectively.
 * It also checks if the --demo option is passed and runs the TaskSeeder to insert some sample data into the table.
 *
 * @package App\Database\Migrations
 */
class CronJobs extends Migration
{
    /**
     * The up() method that creates the table.
     *
     * @return void
     */
    public function up()
    {
        // Define the fields of the table
        $this->forge->addField(
            [
                'task_id' => [
                    'type'           => 'INT',
                    'constraint'     => 5,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'task_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'task_group' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'task_command' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'task_schedule' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'task_last_run' => [
                    'type'       => 'DATETIME'
                ],
                'task_status' => [
                    'type'           => 'ENUM',
                    'constraint'     => ['enabled', 'disabled'],
                    'default'        => 'enabled',
                ]
            ]
        );
        // Add the primary key
        $this->forge->addKey('task_id', true);
        // Create the table
        $this->forge->createTable('task');

        // Check if the --demo option is passed
        if ($this->cli->getOption('demo')) {
            // Load and run the TaskSeeder to insert sample data
            $seeder = \Config\Database::seeder();
            $seeder->call('TaskSeeder');
        }
    }

    /**
     * The down() method that drops the table.
     *
     * @return void
     */
    public function down()
    {
        // Drop the table
        $this->forge->dropTable('task');
    }
}
