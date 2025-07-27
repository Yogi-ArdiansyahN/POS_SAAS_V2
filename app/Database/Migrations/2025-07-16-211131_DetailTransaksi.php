<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailTransaksi extends Migration
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
            'transaksis_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'menus_id' => [
                'type' => 'int',
                'unsigned' => true,
            ],
            'order' => [
                'type' => 'varchar',
                'constraint' => 25,
            ],
            'harga_modal' => [
                'type' => 'int',
            ],
            'harga_jual' => [
                'type' => 'int',
            ],
            'quantity' => [
                'type' => 'int',
            ],
            'total' => [
                'type' => 'int',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('transaksis_id', 'transaksis', 'id');
        $this->forge->addForeignKey('menus_id', 'menus', 'id');
        $this->forge->createTable('detail_transaksis', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('detail_transaksis');
    }
}
