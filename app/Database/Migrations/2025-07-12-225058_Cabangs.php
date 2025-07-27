<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cabangs extends Migration
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
                'constraint' => 50,
            ],
            'mitras_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'users_id' => [
                'type' => 'int',
                'unsigned' => true,
                'unique' => true,
                'null' => true
            ],
            'alamat' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['buka', 'tutup', 'tidak_aktif'],
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('users_id', 'users', 'id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->createTable('cabangs', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('cabangs');
    }
}
