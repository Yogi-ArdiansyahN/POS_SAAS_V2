<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cabangs;
use App\Models\Diskons;
use App\Models\Menus;
use App\Models\Mitras;
use App\Models\Transaksis;
use CodeIgniter\HTTP\ResponseInterface;

class TransaksisController extends BaseController
{

    protected $mitras, $menus, $cabangs, $transaksis, $diskons;

    public function __construct()
    {
        $this->mitras = new Mitras();
        $this->menus = new Menus();
        $this->cabangs = new Cabangs();
        $this->transaksis = new Transaksis();
        $this->diskons = new Diskons();
    }

    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('id', session()->get('users')['mitras_id'])->first();
        $cabangs = $this->cabangs->where('mitras_id', $mitras['id'])->first();
        $diskons = $this->diskons->where('mitras_id', $mitras['id'])->where('is_active', 1)->where('end_date >=', date('Y-m-d'))->get()->getResultArray();

        // validasi cabang mitra
        if (empty($cabangs)) {
            session()->setFlashdata('errors', 'Mitra tidak memiliki cabang.');
            return redirect()->to(base_url() . 'mitra');
        }

        $menus_stok = $this->menus->getMenuStoks($mitras['id'], $cabangs['id'], date('Y-m-d'));

        if ($menus_stok == null) {
            session()->setFlashdata('errors', 'Stok pada cabang belum ditambahkan.');
            return redirect()->to(base_url() . 'mitra');
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

        return view('mitra/transaksi/index', $data);
    }

    public function create()
    {
        date_default_timezone_set('Asia/Jakarta');

        $menus = $this->request->getVar('cart', FILTER_SANITIZE_SPECIAL_CHARS);
        $kode_diskon = $this->request->getVar('diskon', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mitras = $this->mitras->where('id', session()->get('users')['mitras_id'])->first();
        $cabangs = $this->cabangs->where('mitras_id', $mitras['id'])->first();
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
        // return json_encode($data_transaksi);

        $create = $this->transaksis->create($data_transaksi, $data_detail_transaksi, date('Y-m-d'));

        return $this->response->setJSON($create);

        // $this
        // $cabangs = $this
    }
}
