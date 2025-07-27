<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Mitras extends Migration
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
                'unique' => true
            ],
            'users_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'alamat' => [
                'type' => 'text',
            ],
            'is_trial' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0  // 1 = yes, 0 = no
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('users_id', 'users', 'id');
        $this->forge->createTable('mitras', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('mitras');
    }
}
