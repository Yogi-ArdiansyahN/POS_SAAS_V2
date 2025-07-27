<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Diskons extends Migration
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
            'mitras_id' => [
                'type' => 'int',
                'unsigned' => true
            ],
            'kode' => [
                'type' => 'varchar',
                'constraint' => 25,
                'unique' => true
            ],
            'name' => [
                'type' => 'varchar',
                'constraint' => 25
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['nominal', 'persen']
            ],
            'value' => [
                'type' => 'int',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0  // 1 = yes, 0 = no
            ],
            'start_date' => [
                'type' => 'varchar',
                'constraint' => 50,
            ],
            'end_date' => [
                'type' => 'varchar',
                'constraint' => 50,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->createTable('diskons', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('diskons');
    }
}
