<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RiwayatLangganans extends Migration
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
                'unsigned' => true,
            ],
            'langganans_id' => [
                'type' => 'int',
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['lunas', 'belum_lunas', 'expired'],
            ],
            'tanggal_mulai' => [
                'type' => 'date',
            ],
            'tanggal_selesai' => [
                'type' => 'date',
            ],
            'expired_at' => [
                'type' => 'date',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('mitras_id', 'mitras', 'id');
        $this->forge->addForeignKey('langganans_id', 'langganans', 'id');
        $this->forge->createTable('riwayat_langganans', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_langganans');
    }
}
