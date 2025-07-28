<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cabangs;
use App\Models\DetailTransaksis;
use App\Models\Diskons;
use App\Models\Menus;
use App\Models\Mitras;
use App\Models\RiwayatLangganans;
use App\Models\Transaksis;
use App\Models\Users;
use CodeIgniter\HTTP\ResponseInterface;

class KasirsController extends BaseController
{

    protected $users, $mitras, $menus, $stoks, $cabangs, $transaksis, $detail_transaksis, $diskons, $riwayatLangganans;

    public function __construct()
    {
        $this->users = new Users();
        $this->mitras = new Mitras();
        $this->menus = new Menus();
        $this->cabangs = new Cabangs();
        $this->transaksis = new Transaksis();
        $this->detail_transaksis = new DetailTransaksis();
        $this->diskons = new Diskons();
        $this->riwayatLangganans = new RiwayatLangganans();
    }

    public function index()
    {
        $data = [
            'title' => 'Kasir',
        ];

        return view('mitra/kasir/index', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Kasir Tidak Boleh Kosong',
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email tidak boleh kosong',
                    'valid_email' => 'Format email tidak sesuai',
                    'is_unique' => 'Email telah terdaftar'
                ]
            ],
            'phone' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'No W.A Kasir Tidak Boleh Kosong',
                    'numeric' => 'No W.A harus berupa angka'
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal, silahkan ulangi kembali');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput();
        }

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        // validasi nama kasir mitra
        $users = $this->users->where('name', $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS))->where('mitras_id', $mitras['id'])->first();
        if ($users) {
            session()->setFlashdata('errors', 'Nama Kasir Sudah Tersedia');
            return redirect()->back()->withInput();
        }

        //set password default
        $password = str_replace(' ', '', $mitras['name']) .  rand(2, 100);

        $data = [
            'name' => $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'email' => $this->request->getPost('email', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'username' => $this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'phone' => $this->request->getPost('phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'kasir',
            'mitras_id' => $this->mitras->where('users_id', session()->get('users')['id'])->first()['id'],
        ];
        $create = $this->users->create_kasir($data, $password);
        session()->setFlashdata($create['status'], $create['message']);

        return redirect()->to(base_url() . '/mitra/kasir');
    }

    public function edit()
    {
        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $kasir = $this->users->select('id, name, username, email, phone, status, role')->find($id);

        if (!$kasir) {
            return redirect()->back()->with('error', 'Kasir tidak ditemukan');
        }

        $status = [
            'aktif',
            'tidak_aktif'
        ];

        $data = [
            'title' => 'Edit Kasir',
            'kasir' => $kasir,
            'status' => $status
        ];

        return view('mitra/kasir/edit', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user = $this->users->select('role')->find($id);

        //validasi role user
        if ($user['role'] == 'mitra') {
            session()->setFlashdata('failed', 'Tidak dapat mengubah status kasir mitra');
            return redirect()->back()->withInput();
        }

        $status = $this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $default_status = [
            'aktif',
            'tidak_aktif'
        ];

        if (!in_array($status, $default_status)) {
            session()->setFlashdata('failed', 'Status tidak valid');
            return redirect()->back()->withInput();
        }

        $data = [
            'status' => $status,
        ];

        if ($this->users->update($id, $data)) {
            session()->setFlashdata('success', 'Berhasil memperbarui status kasir');
        } else {
            session()->setFlashdata('failed', 'Gagal memperbarui status kasir, silakan coba lagi');
            return redirect()->back()->withInput();
        }

        return redirect()->to(base_url() . '/mitra/kasir');
    }

    public function getDataTable()
    {
        // $kasirs = $this->users->where('users_id', session()->get('users')['id'])->first();
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $draw = $this->request->getVar('draw', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $start = $this->request->getVar('start', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $length = $this->request->getVar('length', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $search = $this->request->getVar('search', FILTER_SANITIZE_FULL_SPECIAL_CHARS)['value'];

        $data = $this->users->dataTableKasir($start, $length, $search, $mitras['id']);
        $recordsTotal = $this->users->countAllKasir($mitras['id'], 'kasir');
        $recordsFiltered = $this->users->countFilteredKasir($search, $mitras['id']);

        $response = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }

    //index transaksi kasir
    public function transaksi()
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('id', session()->get('users')['mitras_id'])->first();
        $users = session()->get('users');
        $cabangs = $this->cabangs->where('mitras_id', $mitras['id'])->where('users_id', $users['id'])->first();
        $diskons = $this->diskons->where('mitras_id', $mitras['id'])->where('is_active', 1)->where('end_date >=', date('Y-m-d'))->get()->getResultArray();

        // check jika mitra belum berlangganan atau habis masa langganan
        $riwayat_langganan = $this->riwayatLangganans->where('mitras_id', $users['mitras_id'])->where('status', 'lunas')->first();
        if (!$riwayat_langganan) {
            return redirect()->to('/')->with('errors', "Mitra belum berlangganan");
        }

        $menus_stok = $this->menus->getMenuStoks($mitras['id'], $cabangs['id'], date('Y-m-d'));
        if ($menus_stok == null) {
            // session()->destroy();
            // unset($_COOKIE['estetik_cookies']);
            setcookie('pos_saas', '', 1, '/');
            return redirect()->to('/')->with('errors', "Cabang belum buka. Beritahu admin untuk mengisi stok harian");
        }

        $filtered_menus = [];

        foreach ($menus_stok as $data) {
            $filtered_menus[] = [
                'id' => intval($data['id']),
                'name' => $data['name'],
                'price' => intval($data['harga_jual']),
                'category' => $data['kategori'] == 'makanan' ? 'food' : 'drink',
                'image' => $data['foto'] != null ? base_url() . 'uploads/image/' . $data['foto'] :  base_url() . 'uploads/image/default-image-menus.png',
                'current_quantity' => $data['current_quantity']
            ];
        }

        $data = [
            'title' => 'Transaksi',
            'mitra' => $mitras['name'],
            'menus' => json_encode($filtered_menus),
            'cabangs_id' => $cabangs['id'],
            'mitras_id' => $mitras['id'],
            'diskons' => json_encode($diskons)
        ];

        return view('kasir/index', $data);
    }

    // craeat transaksi kasir
    public function create_transaksi()
    {
        date_default_timezone_set('Asia/Jakarta');

        $menus = $this->request->getVar('cart', FILTER_SANITIZE_SPECIAL_CHARS);
        $mitras = $this->mitras->where('id', session()->get('users')['mitras_id'])->first();
        $kode_diskon = $this->request->getVar('diskon', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $users = session()->get('users');
        $cabangs = $this->cabangs->where('mitras_id', $mitras['id'])->where('users_id', $users['id'])->first();

        $menus_stok = $this->menus->getMenuStoks($mitras['id'], $cabangs['id'], date('Y-m-d'));

        $data_diskon = null;
        if (!empty($kode_diskon)) {
            $data_diskon = $this->diskons->where('mitras_id', $mitras['id'])->where('kode', $kode_diskon)->first();
        }

        // order
        $order = 'TRX-' . bin2hex(random_bytes(10));

        // validasi order
        if ($this->transaksis->where('order', $order)->first()) {
        }

        // menu pesanan yang sudah di olah
        // data yang akan di input pada transaksi
        $data_transaksi = [];
        // data yang akan di input pada detail transaksi
        $data_detail_transaksi = [];
        // Inisialisasi total
        $total_transaksi = 0;

        // Data transaksi hanya 1 (global untuk semua menu dalam order)
        $data_transaksi = [
            'order' => $order,
            'cabangs_id' => $cabangs['id'],
            'mitras_id' => $mitras['id'],
            'total' => 0, // akan diisi nanti
            'total_setelah_diskon' => 0, // akan diisi nanti
            'diskons_id' => !empty($data_diskon) ? $data_diskon['id'] : null,
            'margin' => 0 // akan diisi nanti
        ];

        // Isi data detail transaksi
        $data_detail_transaksi = [];
        $margin_transaksi = 0;
        foreach ($menus_stok as $data) {
            foreach ($menus as $datas) {
                if ($data['id'] == $datas['id']) {
                    $subtotal = $data['harga_jual'] * $datas['quantity'];

                    $margin_transaksi += ($data['harga_jual'] - $data['harga_modal']) * $datas['quantity'];
                    $total_transaksi += $subtotal;

                    $data_detail_transaksi[] = [
                        'transaksis_id' => null,
                        'menus_id' => $data['id'],
                        'order' => $order,
                        'harga_modal' => $data['harga_modal'],
                        'harga_jual' => $data['harga_jual'],
                        // 'margin' => $margin,
                        'quantity' => $datas['quantity'],
                        'total' => $subtotal
                    ];
                }
            }
        }

        // Setelah loop selesai, masukkan total yang benar
        $data_transaksi['total'] = $total_transaksi;
        $data_transaksi['margin'] = $margin_transaksi;

        // diskon / pengurangan total transaksi
        $diskon = null;

        // validasi diskon
        if (!empty($data_diskon)) {
            if ($data_diskon['type'] == 'nominal') {
                $diskon = $data_diskon['value'];
            } else {
                $diskon = $data_transaksi['total'] * ($data_diskon['value'] / 100);
            }

            // set total transaksi baru
            $data_transaksi['total_setelah_diskon'] = $total_transaksi - $diskon;
            $data_transaksi['margin'] = $margin_transaksi - $diskon;
        }

        // return json_encode($menus);
        // return json_encode($data_detail_transaksi);

        $create = $this->transaksis->create($data_transaksi, $data_detail_transaksi, date('Y-m-d'));

        return $this->response->setJSON($create);

        // $this
        // $cabangs = $this
    }

    // laporan transaksi kasir
    public function laporan()
    {
        date_default_timezone_set('Asia/Jakarta');

        $users = session()->get('users');
        $mitras = $this->mitras->where('id', $users['mitras_id'])->first();
        $cabang_id = $this->cabangs->where('users_id', $users['id'])->first()['id'];

        if (isset($_POST['filter'])) {
            $tanggal_mulai = $this->request->getPost('tanggal_mulai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $tanggal_selesai = $this->request->getPost('tanggal_selesai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $order = $this->request->getPost('order', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $mitras = $this->mitras->where('id', $users['mitras_id'])->first();

            // tanggal untuk penamaan export
            $tanggals = null;

            $transaksis = $this->transaksis
                ->select('
                    transaksis.order,
                    cabangs.name AS cabang_name,
                    transaksis.total,
                    transaksis.created_at,
                    SUM(transaksis.margin) AS margin,
                    diskons.name as nama_diskon,
                    diskons.kode as kode_diskon,
                    transaksis.total_setelah_diskon
                ')
                ->where('transaksis.mitras_id', $mitras['id'])
                ->where('cabangs.id', $cabang_id)
                ->join('cabangs', 'cabangs.id = transaksis.cabangs_id')
                ->join('diskons', 'diskons.id = transaksis.diskons_id', 'left')
                ->join('detail_transaksis', 'detail_transaksis.transaksis_id = transaksis.id');

            // Filter berdasarkan order
            if (!empty($order)) {
                $transaksis->where('transaksis.order', $order);
            }

            // Filter berdasarkan tanggal mulai & selesai
            if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
                $transaksis->where('DATE(transaksis.created_at) >=', $tanggal_mulai);
                $transaksis->where('DATE(transaksis.created_at) <=', $tanggal_selesai);
                $tanggals = $tanggal_mulai . ' - ' . $tanggal_selesai;
            }

            // Jika hanya tanggal_mulai yang diisi
            if (!empty($tanggal_mulai) && empty($tanggal_selesai)) {
                $transaksis->where('DATE(transaksis.created_at)', $tanggal_mulai);
                $tanggals = $tanggal_mulai;
            }

            // Group by transaksi ID dan urutkan
            $transaksis = $transaksis->groupBy('transaksis.id')
                ->orderBy('transaksis.created_at', 'DESC')
                ->get()
                ->getResultArray();


            // total diskon
            $total_diskon = 0;
            // total transaksi 
            $total_transaksi = count($transaksis);
            // total nominal transaksi
            $total_nominal = 0;
            foreach ($transaksis as $data) {
                $total_nominal += $data['total'];
                $total_diskon += ($data['total_setelah_diskon'] != 0 ? $data['total'] : 0) - $data['total_setelah_diskon'];
            }

            $total_nominal = number_format($total_nominal);

            $tbody = '';
            $no = 1;
            foreach ($transaksis as $data) {
                $tanggal = date('d-m-Y', strtotime($data['created_at']));
                $total = number_format($data['total']);
                $namaDiskon = !empty($data['nama_diskon']) ? $data['nama_diskon'] : '-';
                $kodeDiskon = !empty($data['kode_diskon']) ? ' (' . $data['kode_diskon'] . ')' : '-';
                $total_setelah_diskon = number_format($data['total_setelah_diskon']);
                $nominal_diskon = !empty($data['nama_diskon']) ? 'Rp. ' . number_format($data['total'] - $data['total_setelah_diskon']) : '-';

                $tbody .= "
                    <tr>
                        <td>{$no}</td>
                        <td>{$tanggal}</td>
                        <td>{$data['order']}</td>
                        <td>Rp {$total}</td>
                        <td>
                            {$namaDiskon}{$kodeDiskon} <br> 
                            <sup class='text-danger'>- {$nominal_diskon}</sup>
                            </td>
                        <td>Rp {$total_setelah_diskon}</td>
                        <td>
                            <button class='btn btn-sm btn-info' onclick=\"detail('{$data['order']}')\">Detail</button>
                        </td>
                    </tr>
";
                $no++;
            }


            $main = <<<HTML
                <div class="card-body">
                    <!-- Ringkasan -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Transaksi</small>
                                    <h5 class="fw-bold mb-0"> $total_transaksi </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Pendapatan</small>
                                    <h5 class="fw-bold mb-0">Rp $total_nominal </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Diskon</small>
                                    <h5 class="fw-bold mb-0">Rp $total_diskon </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="datasTable">
                            <thead class="">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Order ID</th>
                                    <th>Total</th>
                                    <th>
                                        Diskon
                                        (kode)
                                    </th>
                                    <th>Total Setelah Diskon</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               $tbody
                            </tbody>
                        </table>
                    </div>
                </div>
            HTML;

            $data = [
                'title' => 'Laporan',
                'mitra' => $mitras['name'],
                'main' => $main,
                'date' => $tanggals,
            ];
        } else {
            $data = [
                'title' => 'Laporan',
                'mitra' => $mitras['name'],
                'main' => null,
                'date' => date('Y-m-d'),
            ];
        }

        return view('kasir/laporan', $data);
    }

    // detail laporan kasir
    public function detail()
    {
        $order = $this->request->getVar('order', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $detail_transaksis = $this->detail_transaksis
            ->select('
                menus.name,
                detail_transaksis.harga_modal,
                detail_transaksis.harga_jual,
                detail_transaksis.quantity,
                detail_transaksis.total,
            ')
            ->where('order', $order)
            ->join('menus', 'menus.id = detail_transaksis.menus_id')
            ->get()->getResultArray();

        $data = [
            'menus' => $detail_transaksis
        ];

        return view('kasir/detail', $data);
    }
}
