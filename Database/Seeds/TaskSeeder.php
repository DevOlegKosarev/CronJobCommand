<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Class TaskSeeder
 *
 * This class is a seeder that inserts some sample data into the task table.
 * It uses an array of predefined data for the fields.
 * It also uses the TaskModel from the App\Models namespace to interact with the database.
 *
 * @package App\Database\Seeds
 */
class TaskSeeder extends Seeder
{
    /**
     * The run() method that executes the seeder logic.
     *
     * @return void
     */
    public function run()
    {
        // Load the TaskModel
        $taskModel = new \App\Models\TaskModel();

        // Define the array of predefined data
        $data = [
            [
                'preset_id' => 1,
                'task_title' => 'Send welcome email',
                'task_vendor' => 'Mailchimp',
                'task_command' => 'send_email',
                'task_schedule' => 'daily',
                'task_last_run' => '2023-05-09 10:00:00',
                'task_status' => 'enabled',
            ],
            [
                'preset_id' => 2,
                'task_title' => 'Backup database',
                'task_vendor' => 'AWS',
                'task_command' => 'backup_db',
                'task_schedule' => 'weekly',
                'task_last_run' => '2023-05-08 23:00:00',
                'task_status' => 'enabled',
            ],
            [
                'preset_id' => 3,
                'task_title' => 'Update inventory',
                'task_vendor' => 'Shopify',
                'task_command' => 'update_inventory',
                'task_schedule' => 'monthly',
                'task_last_run' => '2023-05-01 12:00:00',
                'task_status' => 'disabled',
            ],
            [
                'preset_id' => 4,
                'task_title' => 'Generate report',
                'task_vendor' => 'Google Analytics',
                'task_command' => 'generate_report',
                'task_schedule' => 'daily',
                'task_last_run' => null,
                'task_status' => 'enabled',
            ],
            [
                'preset_id' => 5,
                'task_title' => 'Send newsletter',
                'task_vendor' => 'Mailchimp',
                'task_command' => 'send_newsletter',
                'task_schedule' => null,
                'task_last_run' => null,
                'task_status' => null,
            ],
        ];

        // Loop through the array of data
        foreach ($data as $row) {
            // Insert the data into the table
            $taskModel->insert($row);
        }
    }
}
