<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsersToken extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'users_id' => [
                'type' => 'int',
                'constraint' => 11
            ],
            'token' => [
                'type' => 'varchar',
                'constraint' => 256,
                'unique' => TRUE
            ],
            'expired_at' => [
                'type' => 'DATETIME'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users_token', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable("users_token");
    }
}
