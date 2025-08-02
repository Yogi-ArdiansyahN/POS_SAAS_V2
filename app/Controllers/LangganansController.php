<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Langganans;
use App\Models\Mitras;
use App\Models\RiwayatLangganans;
use CodeIgniter\HTTP\ResponseInterface;

class LangganansController extends BaseController
{

    protected $langganans, $riwayatLangganans, $mitras;

    public function __construct()
    {
        $this->langganans = new Langganans();
        $this->riwayatLangganans = new RiwayatLangganans();
        $this->mitras = new Mitras();
    }

    public function index()
    {
        $langganan = $this->langganans->get()->getResultArray();

        $kategori = [
            'minggu' => 'Mingguan',
            'bulan' => 'Bulanan',
            'tahun' => 'Tahunan'
        ];

        $data = [
            'title' => "Langganan",
            'langganan' => $langganan,
            'kategori' => $kategori
        ];

        return view('admin/langganan/index', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'name' => [
                'rules' => 'required|is_unique[langganans.name]',
                'errors' => [
                    'required' => 'Nama Tidak Boleh Kosong',
                    'is_unique' => 'Nama Telah Terdaftar',
                ]
            ],
            'kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori Tidak Boleh Kosong',
                ]
            ],
            'durasi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Durasi tidak boleh kosong',
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal membuat langganan, silahkan ulangi kembali');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $name = $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $kategori = $this->request->getPost('kategori', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $durasi = $this->request->getPost('durasi', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $harga = $this->request->getPost('harga', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $kategoris = [
            'minggu',
            'bulan',
            'tahun'
        ];

        if (!in_array($kategori, $kategoris)) {
            session()->setFlashdata('errors', 'Kategori tidak valid');
            return redirect()->back();
        }

        $data = [
            'name' => $name,
            'kategori' => $kategori,
            'durasi' => $durasi,
            'harga' => $harga
        ];

        $create = $this->langganans->insert($data);
        if (!$create) {
            session()->setFlashdata('errors', 'Gagal tersambung ke server');
        } else {
            session()->setFlashdata('success', 'Berhasil membuat langganan');
        }

        return redirect()->back();
    }

    public function edit()
    {
        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $langganan = $this->langganans->where('id', $id)->first();
        $data = [
            'title' => "Langganan",
            'langganan' => $langganan
        ];

        return view('admin/langganan/edit', $data);
    }

    public function update()
    {
        if (!$this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Tidak Boleh Kosong',
                ]
            ],
            'kategori' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kategori Tidak Boleh Kosong',
                ]
            ],
            'durasi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Durasi tidak boleh kosong',
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal membuat langganan, silahkan ulangi kembali');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput()->with('validation', $validation);
        }
        $id = $this->request->getPost('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $name = $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $kategori = $this->request->getPost('kategori', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $durasi = $this->request->getPost('durasi', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $harga = $this->request->getPost('harga', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $status = $this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
            'name' => $name,
            'kategori' => $kategori,
            'durasi' => $durasi,
            'harga' => $harga,
            'status' => intval($status)
        ];

        $create = $this->langganans->set($data)->where('id', $id)->update();
        if (!$create) {
            session()->setFlashdata('errors', 'Gagal tersambung ke server');
        } else {
            session()->setFlashdata('success', 'Berhasil membuat langganan');
        }

        return redirect()->back();
    }

    // langganan mitra
    public function index_mitra()
    {
        $langganan = $this->langganans->where('status', 1)->get()->getResultArray();
        $mitras = $this->mitras->where('id', session()->get('users')['mitras_id'])->first();

        $riwayatLangganan = $this->riwayatLangganans->select('
                langganans.name,
                riwayat_langganans.tanggal_mulai,
                riwayat_langganans.tanggal_selesai,
                riwayat_langganans.status,
                langganans.harga,
                riwayat_langganans.created_at,
                riwayat_langganans.expired_at
            ')
            ->where('mitras_id', session()->get('users')['mitras_id'])
            ->join('langganans', 'langganans.id = riwayat_langganans.langganans_id')
            ->orderBy('FIELD(riwayat_langganans.status, "lunas", "belum_lunas")') // lunas duluan
            ->orderBy('riwayat_langganans.created_at', 'DESC') // lalu urutkan berdasarkan waktu
            ->get()
            ->getResultArray();

        $data = [
            'title' => "Langganan",
            'langganan' => $langganan,
            'riwayatLangganan' => json_encode($riwayatLangganan),
            'is_trial' => $mitras['is_trial']
        ];

        return view('mitra/langganan/index', $data);
    }

    // berlangganan
    public function berlangganan()
    {
        date_default_timezone_set('Asia/Jakarta');

        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $berlangganan = $this->langganans->where('id', $id)->where('status', 1)->first();
        $user = session()->get('users');
        $mitras = $this->mitras->where('id', $user['mitras_id'])->first();

        $name_langganan = $berlangganan['name'];

        // ganti kategori indo ke inggris 
        $kategori = null;
        if ($berlangganan) {
            $kategori = ($berlangganan['kategori'] == 'bulan') ? 'month' : (($berlangganan['kategori'] == 'minggu') ? 'week' : 'year');
        }

        // if ada belangganan aktif/lunas
        $lunas = $this->riwayatLangganans->where('status', 'lunas')->where('mitras_id', $mitras['id'])->first();
        if ($lunas) {
            $response = [
                'status' => 'errors',
                'message' => 'Anda masih memiliki langganan aktif hingga ' . $lunas['tanggal_selesai'] . '.'
            ];

            return $this->response->setJSON($response);
        }

        // belangganan hari ini
        $riwayatLangganan = $this->riwayatLangganans->where('mitras_id', $mitras['id'])->where('DATE(created_at)', date('Y-m-d'))->first();
        if ($riwayatLangganan) {
            $response = [
                'status' => 'errors',
                'message' => 'Hari ini ada tagihan langganan yang belum lunas.'
            ];

            return $this->response->setJSON($response);
        }

        if (!$berlangganan) {
            $response = [
                'status' => 'errors',
                'message' => 'Tolong pilih langganan dengan benar.'
            ];
            return $this->response->setJSON($response);
        }

        // data riwayat langganan
        if ($name_langganan == 'Trial') {
            $data = [
                'mitras_id' => $mitras['id'],
                'langganans_id' => $berlangganan['id'],
                'status' => $berlangganan['name'] == 'Trial' ? 'lunas' : 'belum_lunas',
                'tanggal_mulai' => date('Y-m-d'),
                'tanggal_selesai' => date('Y-m-d', strtotime($berlangganan['durasi'] . ' ' . $kategori)),
                'expired_at' => date('Y-m-d'),
            ];
        } else {
            $data = [
                'mitras_id' => $mitras['id'],
                'langganans_id' => $berlangganan['id'],
                'status' => $berlangganan['name'] == 'Trial' ? 'lunas' : 'belum_lunas',
                'expired_at' => date('Y-m-d', strtotime('+1 day'))
            ];
        }

        // create riwayat berlangganan
        $create = $this->riwayatLangganans->create($data, $name_langganan);

        if ($berlangganan['name'] == 'Trial') {
            $response = [
                'status' => 'success_trial',
                'message' => 'Selamat Langganan Trial anda sudah aktif.'
            ];

            return $this->response->setJSON($response);
        }

        return $this->response->setJSON($create);
    }
}
