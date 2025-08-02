<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Langganans extends Migration
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
                'constraint' => 25
            ],
            'harga' => [
                'type' => 'int',
            ],
            'kategori' => [
                'type' => 'enum',
                'constraint' => ['minggu', 'bulan', 'tahun'],
            ],
            'durasi' => [
                'type' => 'int',
                'constraint' => 1,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0  // 1 = yes, 0 = no
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('langganans', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('langganans');
    }
}
