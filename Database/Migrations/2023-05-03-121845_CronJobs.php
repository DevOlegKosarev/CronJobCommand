<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CronJobs extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'task_id' => [
                    'type'           => 'INT',
                    'constraint'     => 5,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'preset_id' => [
                    'type'           => 'INT',
                    'constraint'     => 5
                ],
                'task_title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                ],
                'task_vendor' => [
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
                'task_status' => [
                    'type'           => 'ENUM',
                    'constraint'     => ['enabled', 'disabled'],
                    'default'        => 'enabled',
                ]
            ]
        );
        $this->forge->addKey('task_id', true);
        $this->forge->createTable('task');
    }

    public function down()
    {
        $this->forge->dropTable('task');
    }
}
