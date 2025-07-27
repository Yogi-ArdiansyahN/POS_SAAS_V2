<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StokMutasis extends Migration
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
            'stoks_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'menus_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'cabangs_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'mitras_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'tipe_mutasi' => [
                'type' => 'enum',
                'constraint' => ['penambahan', 'pengurangan', 'perpindahan', 'stok_harian']
            ],
            'quantity' => [
                'type' => 'int'
            ],
            'current_quantity_sebelum' => [
                'type' => 'int'
            ],
            'quantity_sebelum' => [
                'type' => 'int'
            ],
            'current_quantity_sesudah' => [
                'type' => 'int'
            ],
            'quantity_sesudah' => [
                'type' => 'int'
            ],
            'notes' => [
                'type' => 'text'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('stoks_id', 'stoks', 'id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->addForeignKey('cabangs_id', 'cabangs', 'id');
        $this->forge->addForeignKey('menus_id', 'menus', 'id');
        $this->forge->createTable('stok_mutasis', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('stok_mutasis');
    }
}
