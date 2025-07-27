<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class mitra1 extends Seeder
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
                'name' => 'Mitra 1',
                'users_id' => '2',
                'alamat' => $alamatRandom
            ]
        ];

        $builder = $db->table('mitras');
        $builder->insertBatch($data_mitra1);

        // kasir mitra 1
        $data_user_mitra1 = [

            // id = 4
            [
                'name' => 'Kasir 1.1',
                'username' => 'kasir11',
                'phone' => '+628123456782',
                'email' => 'kasir11@gmail.com',
                'password' => password_hash('kasir123', PASSWORD_BCRYPT),
                'role' => 'kasir',
                'mitras_id' => 1,
                'status' => 'aktif',
            ],

            // id = 5
            [
                'name' => 'Kasir 1.2',
                'username' => 'kasir12',
                'phone' => '+628123456722',
                'email' => 'kasir12@gmail.com',
                'password' => password_hash('kasir123', PASSWORD_BCRYPT),
                'role' => 'kasir',
                'mitras_id' => 1,
                'status' => 'aktif',
            ],

            // id = 6
            [
                'name' => 'Kasir 1.3',
                'username' => 'kasir13',
                'phone' => '+6281234567232',
                'email' => 'kasir13@gmail.com',
                'password' => password_hash('kasir123', PASSWORD_BCRYPT),
                'role' => 'kasir',
                'mitras_id' => 1,
                'status' => 'aktif',
            ],
        ];

        $builder = $db->table('users');
        $builder->insertBatch($data_user_mitra1);

        // Cabang
        $data_cabang_mitra1 = [
            [
                'name' => 'Cabang Mitra 1',
                'mitras_id' => 1,
                'users_id' => '2',
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
            [
                'name' => 'Cabang Mitra 1.1',
                'mitras_id' => 1,
                'users_id' => 4,
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
            [
                'name' => 'Cabang Mitra 1.2',
                'mitras_id' => 1,
                'users_id' => 5,
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
            [
                'name' => 'Cabang Mitra 1.3',
                'mitras_id' => 1,
                'users_id' => 6,
                'alamat' => $alamatRandom,
                'status' => 'tutup',
            ],
        ];

        $builder = $db->table('cabangs');
        $builder->insertBatch($data_cabang_mitra1);

        // Menus
        $data_menus_mitra1 = [
            [
                'name' => 'Roti Bakar Coklat',
                'mitras_id' => 1,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Roti Bakar Susu',
                'mitras_id' => 1,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Roti Bakar Stroberry',
                'mitras_id' => 1,
                'harga_modal' => 7000,
                'harga_jual' => 10000,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Roti Bakar Keju',
                'mitras_id' => 1,
                'harga_modal' => 10000,
                'harga_jual' => 15000,
                'kategori' => 'makanan',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Teh Susu Kocak',
                'mitras_id' => 1,
                'harga_modal' => 5000,
                'harga_jual' => 7500,
                'kategori' => 'minuman',
                'foto' => null,
                'is_active' => 1,
            ],
            [
                'name' => 'Teh Susu Keju',
                'mitras_id' => 1,
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
