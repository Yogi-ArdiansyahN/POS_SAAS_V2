<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsersTable extends Migration
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
            'name' => [
                'type' => 'varchar',
                'constraint' => 150
            ],
            'username' => [
                'type' => 'varchar',
                'constraint' => 150
            ],
            'phone' => [
                'type' => 'varchar',
                'constraint' => 50,
                'unique' => true
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 128,
                'unique' => true
            ],
            'password' => [
                'type' => 'varchar',
                'constraint' => 255
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'mitra', 'kasir']
            ],
            'mitras_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => true
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['aktif', 'tidak_aktif'],
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
