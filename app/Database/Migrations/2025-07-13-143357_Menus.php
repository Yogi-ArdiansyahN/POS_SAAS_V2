<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Menus extends Migration
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
            'harga_modal' => [
                'type' => 'int',
            ],
            'harga_jual' => [
                'type' => 'int',
            ],
            'kategori' => [
                'type' => 'enum',
                'constraint' => ['makanan', 'minuman']
            ],
            'foto' => [
                'type' => 'varchar',
                'constraint' => 50,
                'null' => true
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1  // 1 = yes, 0 = no
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->createTable('menus', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}
