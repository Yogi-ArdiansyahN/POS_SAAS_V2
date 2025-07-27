<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\models\Cabangs;
use App\Models\Menus;
use App\models\Mitras;
use App\Models\StokMutasis;
use App\models\Stoks;
use CodeIgniter\HTTP\ResponseInterface;

class StoksController extends BaseController
{

    protected $stoks, $mitras, $cabangs, $menus, $stokmutasis;

    public function __construct()
    {
        $this->stoks = new Stoks();
        $this->mitras = new Mitras();
        $this->cabangs = new Cabangs();
        $this->menus = new Menus();
        $this->stokmutasis = new StokMutasis();
    }

    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
        $cabangs = $this->cabangs->getCabangs($mitras['id'], null, null);

        $stoks = $this->stoks->select('cabangs_id')->where('mitras_id', $mitras['id'])->where('DATE(created_at)', date('Y-m-d'))->groupBy('cabangs_id')->get()->getResultArray();

        //identifikasi cabang yang memiliki stok
        foreach ($cabangs as $index => $cabang) {
            $hasStock = false;

            foreach ($stoks as $stok) {
                if ($stok['cabangs_id'] == $cabang['id']) {
                    $hasStock = true;
                    break; // stok ditemukan, keluar dari loop dalam
                }
            }

            $cabangs[$index]['has_stoks'] = $hasStock;
        }

        $data = [
            'title' => 'Stok',
            'cabangs' => $cabangs,
        ];

        // dd($data);
        return view('mitra/stok/index', $data);
    }

    public function create()
    {
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $cabang_id = $this->request->getVar('cabang_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data_post = $this->request->getPost();

        // set default quantity to 0 if not set
        foreach ($data_post as $key => $value) {
            if (!is_numeric($value) || $value < 0) {
                $data_post[$key] = '0';
            }
        }

        // validasi quantity 0 semua
        $total = 0;
        foreach ($data_post as $key => $value) {
            if (ctype_digit((string)$key)) {
                $total += (int) $value;
            }
        }
        if ($total == 0) {
            return redirect()->back()->withInput()->with('errors', 'Stok tidak boleh kosong.');
        }

        $menu = [];

        // validasi inputan menu
        foreach ($data_post as $key => $value) {
            if (!is_numeric($value) || $value < 0) {
                return redirect()->back()->withInput()->with('error', 'Stok harus berupa angka.');
            }
            if ($key != 'cabang_id') {
                $menu[$key] = $value;
            }
        }

        $create = $this->stoks->create($menu, $mitras['id'], $cabang_id);

        session()->setFlashdata($create['status'], $create['message']);
        return redirect()->to(base_url() . 'mitra/stok');
    }

    public function tambah()
    {
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
        $cabang_id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $cabang = $this->cabangs->where('id', $cabang_id)->first();

        $menus = $this->menus->getMenus($mitras['id'], "1   ");

        $data = [
            'menus' => $menus,
            'cabang_id' => $cabang_id,
            'cabang' => $cabang
        ];

        return view('mitra/stok/tambah', $data);
    }

    public function detail()
    {
        date_default_timezone_set('Asia/Jakarta');

        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $stok = $this->stoks->getStoks($mitras['id'], $id, date('Y-m-d'), null, null);

        if (!$stok) {
            return redirect()->back()->with('error', 'Stok tidak ditemukan.');
        }

        $cabang_name = $stok[0]['cabang_name'] ?? 'Cabang tidak ditemukan';

        $data = [
            'title' => 'Detail Stok',
            'stok' => $stok,
            'cabang_name' => $cabang_name,
        ];

        return view('mitra/stok/detail', $data);
    }

    public function mutasi()
    {
        date_default_timezone_set('Asia/Jakarta');

        if (isset($_POST['mutasi'])) {
            $cabang_id = $this->request->getVar('cabang_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
            $stoks = $this->stoks->getStoks($mitras['id'], $cabang_id, date('Y-m-d'), null, null);

            $menus = $this->request->getPost('menu', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $quantities = $this->request->getPost('quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $mutasi = $this->request->getPost('tipe_mutasi', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // id cabang tujuan perpindahan stok
            $pindah_cabang_id = null;
            // tujuan stok cabang pindah *kekurangan masih harus di optimalisasi perihal perpindahan
            $stok_cabang_pindah = [];
            if (isset($_POST['perpindahan_ke'])) {
                $pindah_cabang_id = $this->request->getPost('perpindahan_ke', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                foreach ($pindah_cabang_id as $data) {
                    $data_stok_cabang_pindah = $this->stoks->getStoks($mitras['id'], $data, date('Y-m-d'), null);
                    // $stok_cabang_pindah[][$data] = $data_stok_cabang_pindah;

                    foreach ($data_stok_cabang_pindah as $datas) {
                        $stok_cabang_pindah[] = [
                            'cabangs_id' => $data,
                            'id' => $datas['id'],
                            'quantity' => $datas['quantity'],
                            'current_quantity' => $datas['current_quantity'],
                            'menu_name' => $datas['menu_name'],
                            'menus_id' => $datas['menus_id'],
                            'cabang_name' => $datas['cabang_name']
                        ];
                    }
                }
            }

            // dd($stok_cabang_pindah);

            //validasi inputan menu, quantities, dan mutasi
            if (empty($menus) || empty($quantities) || empty($mutasi)) {
                session()->setFlashdata('failed', 'Semua inputan harus diisi.');
                return redirect()->back()->withInput();
            }

            // menggabungkan menu, quantities, dan mutasi
            $menus_quantities = [];
            foreach ($menus as $key => $menu) {
                if (isset($quantities[$key]) && is_numeric($quantities[$key]) && $quantities[$key] >= 0) {
                    $menus_quantities[] = [
                        'menus_id' => $menu,
                        'quantity' => (int)$quantities[$key],
                        'mutasi' => $mutasi[$key] ?? null,
                    ];
                }
            }

            // dd($menus_quantities, $stoks, $menus);

            //validasi quantities stok dengan current_quantity
            $stoks_menus_id = array_column($stoks, 'menus_id');
            foreach ($menus_quantities as $key => $data) {
                // if ($stoks[$key]['current_quantity'] < $data['quantity']) {
                if (in_array($data['menus_id'], $stoks_menus_id)) {
                    if ($data['mutasi'] == 'pengurangan') {
                        foreach ($stoks as $stok) {
                            if ($stok['menus_id'] == $data['menus_id']) {
                                if ($stok['current_quantity'] < $data['quantity']) {
                                    session()->setFlashdata('failed', 'Stok tidak mencukupi untuk pengurangan menu');
                                    session()->setFlashdata('errors', 'Stok saat ini: ' . $stoks[$key]['current_quantity'] . ', Jumlah yang akan dikurangi  : ' . $data['quantity']);
                                    return redirect()->back();
                                }
                            }
                        }
                    } elseif ($data['mutasi'] == 'perpindahan') {
                        foreach ($stoks as $stok) {
                            if ($stok['menus_id'] == $data['menus_id']) {
                                if ($stok['current_quantity'] < $data['quantity']) {
                                    session()->setFlashdata('failed', 'Stok tidak mencukupi untuk perpindahan menu');
                                    session()->setFlashdata('errors', 'Stok cabang ' . $stoks[$key]['cabang_name'] . ' saat ini: ' . $stoks[$key]['current_quantity'] . ', Jumlah yang akan dipindahkan  : ' . $data['quantity']);
                                    return redirect()->back();
                                }
                            }
                        }
                    } else {
                        // jika mutasi adalah penambahan, tidak perlu validasi stok
                        continue;
                    }
                }
            }

            // penambahan, pengurangan, dan perpindahan stok mutasi
            // data stoks untuk update pada table stoks
            $data_stoks = [];
            // data stok mutasis data yang akan di input pada table stok mutasi
            $data_stok_mutasis = [];
            // data stok yang akan dipindahkan ke cabang tujuan
            $data_stok_pindah = [];
            foreach ($menus_quantities as $key => $data) {
                if (in_array($data['menus_id'], $stoks_menus_id)) {
                    if ($data['mutasi'] == 'penambahan') {
                        foreach ($stoks as $stok) {
                            if ($stok['menus_id'] == $data['menus_id']) {

                                $data_stoks[] = [
                                    'id' => $stok['id'],
                                    'menus_id' => $stok['menus_id'],
                                    'quantity' =>  intval($stok['quantity']) + intval($data['quantity']),
                                    'current_quantity' => intval($stok['current_quantity']) + intval($data['quantity']),
                                ];

                                $data_stok_mutasis[] = [
                                    'stoks_id' => $stok['id'],
                                    'menus_id' => $data['menus_id'],
                                    'cabangs_id' => $cabang_id,
                                    'mitras_id' => $mitras['id'],
                                    'tipe_mutasi' => 'penambahan',
                                    'quantity' => $data['quantity'],
                                    'current_quantity_sebelum' => intval($stok['current_quantity']),
                                    'quantity_sebelum' => intval($stok['quantity']),
                                    'current_quantity_sesudah' => intval($stok['current_quantity']) + intval($data['quantity']),
                                    'quantity_sesudah' => intval($stok['quantity']) + intval($data['quantity']),
                                    'notes' => 'Penambahan stok ' . $stok['cabang_name'] . ' sebesar ' . $data['quantity'] . ' pada waktu : ' . date('Y-m-d h:i:s')
                                ];
                                break; // keluar loop setelah ketemu

                            }
                        }
                    } elseif ($data['mutasi'] == 'pengurangan') {
                        foreach ($stoks as $stok) {
                            if ($stok['menus_id'] == $data['menus_id']) {
                                $data_stoks[] = [
                                    'id' => $stok['id'],
                                    'menus_id' => $stok['menus_id'],
                                    'quantity' =>  intval($stok['quantity']) - intval($data['quantity']),
                                    'current_quantity' => intval($stok['current_quantity']) - intval($data['quantity']),
                                ];

                                $data_stok_mutasis[] = [
                                    'stoks_id' => $stok['id'],
                                    'menus_id' => $data['menus_id'],
                                    'cabangs_id' => $cabang_id,
                                    'mitras_id' => $mitras['id'],
                                    'tipe_mutasi' => 'penambahan',
                                    'quantity' => $data['quantity'],
                                    'current_quantity_sebelum' => intval($stok['current_quantity']),
                                    'quantity_sebelum' => intval($stok['quantity']),
                                    'current_quantity_sesudah' => intval($stok['current_quantity']) - intval($data['quantity']),
                                    'quantity_sesudah' => intval($stok['quantity']) - intval($data['quantity']),
                                    'notes' => 'Penambahan stok ' . $stok['cabang_name'] . ' sebesar ' . $data['quantity'] . ' pada waktu : ' . date('Y-m-d h:i:s')
                                ];
                                break; // keluar loop setelah ketemu

                            }
                        }
                    } else {
                        foreach ($stoks as $stok) {
                            if ($stok['menus_id'] == $data['menus_id']) {

                                // cabang yang jadi tujuan pindah
                                $data_stoks[] = [
                                    'id' => $stok['id'],
                                    'menus_id' => $stok['menus_id'],
                                    'quantity' =>  intval($stok['quantity']) - intval($data['quantity']),
                                    'current_quantity' => intval($stok['current_quantity']) - intval($data['quantity']),
                                ];

                                $data_stok_mutasis[] = [
                                    'stoks_id' => $stok['id'],
                                    'menus_id' => $data['menus_id'],
                                    'cabangs_id' => $cabang_id,
                                    'mitras_id' => $mitras['id'],
                                    'tipe_mutasi' => 'Perpindahan',
                                    'quantity' => $data['quantity'],
                                    'current_quantity_sebelum' => intval($stok['current_quantity']),
                                    'quantity_sebelum' => intval($stok['quantity']),
                                    'current_quantity_sesudah' => intval($stok['current_quantity']) - intval($data['quantity']),
                                    'quantity_sesudah' => intval($stok['quantity']) - intval($data['quantity']),
                                    'notes' => 'Perpindahan stok ' . $stok['cabang_name'] . ' sebesar ' . $data['quantity'] . '  ke cabang' . ' pada waktu : ' . date('Y-m-d h:i:s')
                                ];
                                break; // keluar loop setelah ketemu

                            }
                        }

                        foreach ($stok_cabang_pindah as $datas) {
                            if ($datas['menus_id'] == $data['menus_id']) {
                                $data_stok_pindah[] = [
                                    'id' => $datas['id'],
                                    'menus_id' => $datas['menus_id'],
                                    'quantity' =>  intval($datas['quantity']) + intval($data['quantity']),
                                    'current_quantity' => intval($datas['current_quantity']) + intval($data['quantity']),
                                ];
                                break; // keluar loop setelah ketemu
                            }
                        }
                    }
                }
            }

            $create_mutasi = $this->stokmutasis->create($data_stoks, $data_stok_mutasis, $data_stok_pindah, $cabang_id, $mitras['id'], $pindah_cabang_id, date('Y-m-d'));

            session()->setFlashdata($create_mutasi['status'], $create_mutasi['message']);
            return redirect()->to(base_url() . 'mitra/stok');
        } else {
            $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
            $cabang = $this->cabangs->where('id', $id)->first()['name'];
            $cabangs = $this->cabangs->getCabangs($mitras['id'], null, null);

            // mendapatkan cabang status != tidak_aktif
            $cabangs = array_filter($cabangs, function ($item) {
                // return $item['status'] !== 'tidak_aktif';
                return $item['status'] !== 'tidak_aktif' && $item['status'] !== 'tutup';
            });

            $cabangs = array_values($cabangs);

            //filter cabang yang bukan $id
            $filtered = [];
            foreach ($cabangs as $data) {
                if ($data['cabang_name'] != $cabang) {
                    $filtered[] = $data;
                }
            }
            $cabangs = $filtered;

            $stoks = $this->stoks->getStoks($mitras['id'], $id, date('Y-m-d'), null, null);

            $total_menu = count($stoks);

            $tipe_mutasi = [
                'penambahan',
                'pengurangan',
                'perpindahan',
            ];

            $data = [
                'title' => 'Mutasi Stok',
                'cabang_name' => $cabang,
                'cabang_id' => $id,
                'stoks' => $stoks,
                'total_menu' => $total_menu,
                'tipe_mutasi' => $tipe_mutasi,
                'cabangs' => $cabangs,
            ];

            return view('mitra/stok/mutasi', $data);
        }
    }
}
