<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class mitra2 extends Seeder
{
    public function run()
    {
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

        // Mitra 
        $data_mitra1 = [
            [
                'name' => 'Mitra 2',
                'users_id' => '3',
                'alamat' => $alamatRandom
            ]
        ];

        $builder = $db->table('mitras');
        $builder->insertBatch($data_mitra1);

        // kasir mitra 1
        $data_user_mitra1 = [

            // id = 7
            [
                'name' => 'Kasir 2.1',
                'username' => 'kasir21',
                'phone' => '+6281234567282',
                'email' => 'kasir21@gmail.com',
                'password' => password_hash('kasir123', PASSWORD_BCRYPT),
                'role' => 'kasir',
                'mitras_id' => 2,
                'status' => 'aktif',
            ],

            // id = 8
            [
                'name' => 'Kasir 2.2',
                'username' => 'kasir22',
                'phone' => '+6281234516722',
                'email' => 'kasir22@gmail.com',
                'password' => password_hash('kasir123', PASSWORD_BCRYPT),
                'role' => 'kasir',
                'mitras_id' => 2,
                'status' => 'aktif',
            ],

            // id = 9
            [
                'name' => 'Kasir 2.3',
                'username' => 'kasir23',
                'phone' => '+62812344567232',
                'email' => 'kasir23@gmail.com',
                'password' => password_hash('kasir123', PASSWORD_BCRYPT),
                'role' => 'kasir',
                'mitras_id' => 2,
                'status' => 'aktif',
            ],
        ];

        $builder = $db->table('users');
        $builder->insertBatch($data_user_mitra1);

        // Cabang
        $data_cabang_mitra1 = [
            [
                'name' => 'Cabang Mitra 2',
                'mitras_id' => 2,
                'users_id' => '3',
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
            [
                'name' => 'Cabang Mitra 2.1',
                'mitras_id' => 2,
                'users_id' => 7,
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
            [
                'name' => 'Cabang Mitra 2.2',
                'mitras_id' => 2,
                'users_id' => 8,
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
            [
                'name' => 'Cabang Mitra 2.3',
                'mitras_id' => 2,
                'users_id' => 9,
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
        ];

        $builder = $db->table('cabangs');
        $builder->insertBatch($data_cabang_mitra1);

        // Menus
        $data_menus_mitra1 = [
            [
                'name' => 'Roti Bakar Coklat 2',
                'mitras_id' => 2,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Roti Bakar Susu 2',
                'mitras_id' => 2,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Roti Bakar Stroberry 2',
                'mitras_id' => 2,
                'harga_modal' => 7000,
                'harga_jual' => 10000,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Roti Bakar Keju 2',
                'mitras_id' => 2,
                'harga_modal' => 10000,
                'harga_jual' => 15000,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Teh Susu Kocak 2',
                'mitras_id' => 2,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'minuman',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Teh Susu Keju 2',
                'mitras_id' => 2,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'minuman',
                'foto' => null,
                'is_active' => 1,
            ],
        ];

        $builder = $db->table('menus');
        $builder->insertBatch($data_menus_mitra1);
    }
}
