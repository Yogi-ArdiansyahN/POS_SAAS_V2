<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['GET', 'POST'], '/', 'AuthController::index');
$routes->get('logout', 'AuthController::logout');
$routes->match(['GET', 'POST'], 'forgotpassword', 'AuthController::forgotpassword');
$routes->get('changepassword/(:any)', 'AuthController::changepassword/$1');
$routes->post('updatepassword', 'AuthController::updatepassword');

$routes->group('/', static function ($routes) {
    $routes->match(['GET', 'POST'], 'daftar', 'AuthController::daftar');
    $routes->get('activate/(:any)', 'AuthController::activate/$1');
    $routes->match(['GET', 'POST'], 'forgot_password', 'AuthController::forgot_password');
});

$routes->get('/home/index', 'Home::index');

$routes->group('admin', ['filter' => 'IsLoggedIn'], static function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('kelolauser', 'UsersController::kelolauser');
    $routes->post('getuser', 'UsersController::getuser');
    $routes->post('updateuser', 'UsersController::updateuser');
    $routes->post('tambahuser', 'UsersController::tambahuser');

    $routes->group('langganan', static function ($routes) {
        $routes->get('', 'LangganansController::index');
        $routes->post('create', 'LangganansController::create');
        $routes->get('edit', 'LangganansController::edit');
        $routes->post('update', 'LangganansController::update');
    });

    $routes->group('mitra', static function ($routes) {
        $routes->get('', 'AdminController::index_mitra');
        $routes->post('create', 'AdminController::create_mitra');
        $routes->get('edit', 'AdminController::edit_mitra');
        $routes->post('update', 'AdminController::update_mitra');
    });
});

$routes->group('mitra', ['filter' => 'IsLoggedIn'], static function ($routes) {

    $routes->group('', ['filter' => 'IsLangganan'], static function ($routes) {
        $routes->get('', 'MitrasController::index');

        $routes->group('cabang', static function ($routes) {
            $routes->get('', 'CabangsController::index');
            $routes->post('create', 'CabangsController::create');
            $routes->get('edit', 'CabangsController::edit');
            $routes->post('update', 'CabangsController::update');
            $routes->post('delete', 'CabangsController::delete');
        });

        $routes->group('kasir', static function ($routes) {
            $routes->get('', 'KasirsController::index');
            $routes->post('create', 'KasirsController::create');
            $routes->get('edit', 'KasirsController::edit');
            $routes->post('update', 'KasirsController::update');
            $routes->post('delete', 'KasirsController::delete');
        });

        $routes->group('menu', static function ($routes) {
            $routes->get('', 'MenusController::index');
            $routes->post('create', 'MenusController::create');
            $routes->get('edit', 'MenusController::edit');
            $routes->post('update', 'MenusController::update');
            $routes->get('delete', 'MenusController::delete');
        });

        $routes->group('stok', static function ($routes) {
            $routes->get('', 'StoksController::index');
            $routes->post('create', 'StoksController::create');
            $routes->get('edit', 'StoksController::edit');
            $routes->post('update', 'StoksController::update');
            $routes->post('delete', 'StoksController::delete');

            $routes->get('detail', 'StoksController::detail');
            $routes->match(['GET', 'POST'], 'mutasi', 'StoksController::mutasi');
            $routes->get('tambah', 'StoksController::tambah');

            $routes->get('riwayat_mutasi', 'StoksController::riwayat_mutasi');
        });

        $routes->group('transaksi', static function ($routes) {
            $routes->get('', 'TransaksisController::index');
            $routes->get('create', 'TransaksisController::create');
        });

        $routes->group('laporan', static function ($routes) {
            $routes->get('', 'LaporansController::index');
            $routes->get('detail', 'LaporansController::detail');
            $routes->match(['GET', 'POST'], 'filter', 'LaporansController::filter');
        });

        $routes->group('diskon', static function ($routes) {
            $routes->get('', 'DiskonsController::index');
            $routes->post('create', 'DiskonsController::create');
            $routes->get('edit', 'DiskonsController::edit');
            $routes->post('update', 'DiskonsController::update');
        });
    });

    $routes->group('langganan', static function ($routes) {
        $routes->get('', 'LangganansController::index_mitra');
        $routes->get('berlangganan', 'LangganansController::berlangganan');
    });


    $routes->group('ajax', static function ($routes) {
        $routes->get('getDataTable', 'KasirsController::getDataTable');
        $routes->get('getDataTableMenus', 'MenusController::getDataTable');
        $routes->get('getDataTableLaporans', 'LaporansController::getDataTable');
        $routes->get('getDataTableDiskon', 'DiskonsController::getDataTable');
        $routes->get('getDataTableStokMenuDashboard/(:any)', 'MitrasController::getDataTableStokMenuDashboard/$1');
        $routes->get('getDataTableTransaksiDashboard/(:any)', 'MitrasController::getDataTableTransaksiDashboard/$1');
        $routes->get('getDataRiwayatMutasi/(:any)', 'StoksController::getDataRiwayatMutasi/$1');
    });
});

$routes->group('kasir', ['filter' => 'IsLoggedIn'], static function ($routes) {
    $routes->get('transaksi', 'KasirsController::transaksi');
    $routes->get('create', 'KasirsController::create_transaksi');
    $routes->match(['GET', 'POST'], 'laporan', 'KasirsController::laporan');
    $routes->get('detail', 'KasirsController::detail');
});
