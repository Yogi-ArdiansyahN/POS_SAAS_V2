<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Stoks extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'mitras_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'cabangs_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'menus_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'quantity' => [
                'type' => 'int'
            ],
            'current_quantity' => [
                'type' => 'int'
            ],
            'notes' => [
                'type' => 'varchar',
                'constraint' => 255
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('cabangs_id', 'cabangs', 'id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->createTable('stoks', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('stoks');
    }
}
