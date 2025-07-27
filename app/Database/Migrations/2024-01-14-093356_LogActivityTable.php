<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LogActivityTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'       => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => true
            ],
            'users_id'          => [
                'type'           => 'INT',
                'constraint'     => '11',
            ],
            'ip'          => [
                'type'           => 'VARCHAR',
                'constraint'     => '200',
            ],
            'ua'          => [
                'type'          => 'TEXT',
            ],
            'note'          => [
                'type'           => 'TEXT'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',

        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('log_activity', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('log_activity');
    }
}
