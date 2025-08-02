<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class initialSeeder extends Seeder
{
    public function run()
    {
        date_default_timezone_set('Asia/Jakarta');

        $db = \Config\Database::connect();
        $faker = \Faker\Factory::create('id_ID'); // Gunakan lokal Indonesia

        $alamatCianjur = [
            'Jl. Raya Cibeber, Cianjur',
            'Kp. Bojongherang, Cianjur',
            'Jl. HOS Cokroaminoto, Cianjur',
            'Jl. Dr. Muwardi, Cianjur',
            'Kp. Maleber, Cianjur',
        ];

        $alamatRandom = $faker->randomElement($alamatCianjur);

        $data_admin = [
            [
                'name' => 'admin',
                'username' => 'admin',
                'phone' => '+628123456781',
                'email' => 'admin@gmail.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'admin',
                'mitras_id' => null,
                'status' => 'aktif',
            ],
            // id = 2
            [
                'name' => 'Mitra 1',
                'username' => 'mitra1',
                'phone' => '+628123456783',
                'email' => 'mitra1@gmail.com',
                'password' => password_hash('mitra123', PASSWORD_BCRYPT),
                'role' => 'mitra',
                'mitras_id' => 1,
                'status' => 'aktif',
            ],
            // id = 3
            [
                'name' => 'Mitra 2',
                'username' => 'mitra2',
                'phone' => '+628123456183',
                'email' => 'mitra2@gmail.com',
                'password' => password_hash('mitra123', PASSWORD_BCRYPT),
                'role' => 'mitra',
                'mitras_id' => 2,
                'status' => 'aktif',
            ],
        ];

        $builder = $db->table('users');
        $builder->insertBatch($data_admin);

        $data_langganan = [
            [
                'name' => 'Trial',
                'harga' => null,
                'kategori' => 'minggu',
                'durasi' => '3',
                'status' => 1
            ],
            [
                'name' => 'Boss Kecil',
                'harga' => 50000,
                'kategori' => 'bulan',
                'durasi' => '1',
                'status' => 1
            ],
            [
                'name' => 'Boss Besar',
                'harga' => 150000,
                'kategori' => 'bulan',
                'durasi' => '6',
                'status' => 1
            ],
            [
                'name' => 'Jurangan',
                'harga' => null,
                'kategori' => 'tahun',
                'durasi' => '1',
                'status' => 250000
            ],
        ];

        $builder = $db->table('langganans');
        $builder->insertBatch($data_langganan);
    }
}
