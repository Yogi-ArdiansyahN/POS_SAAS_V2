<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transaksis extends Migration
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
            'order' => [
                'type' => 'varchar',
                'constraint' => 25,
                'unique'     => true
            ],
            'mitras_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'cabangs_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'diskons_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => true
            ],
            'total' => [
                'type' => 'int',
            ],
            'total_setelah_diskon' => [
                'type' => 'int',
            ],
            'margin' => [
                'type' => 'int',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->addForeignKey('cabangs_id', 'cabangs', 'id');
        $this->forge->createTable('transaksis', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('transaksis');
    }
}
