<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models;
use Throwable;

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
    protected $description = 'Run cron tasks';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cron:job <task_group>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'task_group' => 'Task Group Name.'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    // Property to store an instance of the Database class
    protected $FoxwayPresetStatesModel;


    // Конструктор класса
    public function __construct()
    {
        $this->FoxwayPresetStatesModel = new Models\FoxwayPresetStatesModel();
    }

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $task_group = array_shift($params);

        if (empty($vendor)) {
            $task_group = CLI::prompt('Group name', null, 'required'); // @codeCoverageIgnore
        }

        try {
            $CronJobModel = new Models\CronJobModel();
            $CronJobModel->where('task_status', 'enabled');
            $CronJobModel->where('task_group', $task_group);
            $tasks = $CronJobModel->findAll();

            if (count($tasks) <= 0) {
                throw new \Exception("Enabled Tasks For Group $task_group not found");
            }
            $tasksShellArray = [];
            foreach ($tasks as $keyTask => $task) {
                $cron_match = $this->cron_match($task->task_schedule, time());
                if ($cron_match == true) {
                    $tasksShellArray[] = $task->task_command;
                }
            }
            if (count($tasksShellArray) > 0) {
                $tasksShellStr = implode(" & ", $tasksShellArray);
                CLI::write("Enabled Tasks For Group $task_group Found And Strat Execute.", "green");
                exec($tasksShellStr);
            } else {
                CLI::write("Enabled Tasks For Group $task_group Found But Not Time To Run", 'green');
            }
        } catch (Throwable $e) {
            CLI::error($e->getMessage(), 'light_gray', 'red');
            CLI::newLine();
        }
    }

    // Функция для сравнения времени крона с текущим временем
    function cron_match($cron_string, $current_time)
    {
        // Разбиваем строку крона на пять частей: минуты, часы, дни месяца, месяцы и дни недели
        $cron_parts = explode(" ", $cron_string);
        if (count($cron_parts) != 5) {
            return false; // Неверный формат строки крона
        }

        // Получаем текущие значения минут, часов, дней месяца, месяцев и дней недели из UNIX timestamp
        list($current_minute, $current_hour, $current_day, $current_month, $current_weekday) = explode(" ", date("i H j n w", $current_time));

        // Проверяем каждую часть строки крона на соответствие текущему времени
        foreach ($cron_parts as $index => $part) {
            // Пропускаем часть, если она равна звездочке (*), что означает любое значение
            if ($part == "*") {
                continue;
            }

            // Разбиваем часть на подчасти по запятой (,), если она содержит список значений
            $subparts = explode(",", $part);

            // Проверяем каждую подчасть на соответствие текущему времени
            $match = false; // Флаг совпадения
            foreach ($subparts as $subpart) {
                // Если подчасть содержит дефис (-), то она задает диапазон значений
                if (strpos($subpart, "-") !== false) {
                    // Разбиваем подчасть на начало и конец диапазона
                    list($start, $end) = explode("-", $subpart);

                    // Определяем текущее значение в зависимости от индекса части строки крона
                    switch ($index) {
                        case 0: // Минуты
                            $current_value = $current_minute;
                            break;
                        case 1: // Часы
                            $current_value = $current_hour;
                            break;
                        case 2: // Дни месяца
                            $current_value = $current_day;
                            break;
                        case 3: // Месяцы
                            $current_value = $current_month;
                            break;
                        case 4: // Дни недели
                            $current_value = $current_weekday;
                            break;
                    }

                    // Проверяем, попадает ли текущее значение в диапазон
                    if ($current_value >= $start && $current_value <= $end) {
                        $match = true; // Совпадение найдено
                        break; // Выходим из цикла по подчастям
                    }
                } else {
                    // Иначе подчасть задает одно значение

                    // Сравниваем подчасть с текущим значением в зависимости от индекса части строки крона
                    switch ($index) {
                        case 0: // Минуты
                            if ($subpart == $current_minute) {
                                $match = true; // Совпадение найдено
                                break; // Выходим из цикла по подчастям
                            }
                            break;
                        case 1: // Часы
                            if ($subpart == $current_hour) {
                                $match = true; // Совпадение найдено
                                break; // Выходим из цикла по подчастям
                            }
                            break;
                        case 2: // Дни месяца
                            if ($subpart == $current_day) {
                                $match = true; // Совпадение найдено
                                break; // Выходим из цикла по подчастям
                            }
                            break;
                        case 3: // Месяцы
                            if ($subpart == $current_month) {
                                $match = true; // Совпадение найдено
                                break; // Выходим из цикла по подчастям
                            }
                            break;
                        case 4: // Дни недели
                            if ($subpart == $current_weekday) {
                                $match = true; // Совпадение найдено
                                break; // Выходим из цикла по подчастям
                            }
                            break;
                    }
                }
            }

            // Если не было найдено ни одного совпадения для данной части строки крона, то возвращаем false
            if (!$match) {
                return false;
            }
        }

        // Если все части строки крона совпали с текущим временем, то возвращаем true
        return true;
    }
}
