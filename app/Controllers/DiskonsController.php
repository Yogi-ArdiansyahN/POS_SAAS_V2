<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Diskons;
use App\Models\Mitras;
use CodeIgniter\HTTP\ResponseInterface;

class DiskonsController extends BaseController
{
    protected $diskons, $mitras;

    public function __construct()
    {
        $this->diskons = new Diskons();
        $this->mitras = new Mitras();
    }

    public function index()
    {
        $users = session()->get('users');
        $mitras = $this->mitras->where('users_id', $users['id'])->first();

        $diskons = $this->diskons->getDiskon($mitras['id'], null);

        $data = [
            'title' => 'Diskon',
            'diskons' => $diskons
        ];
        return view('mitra/diskon/index', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Menu Tidak Boleh Kosong',
                ]
            ],
            'tipe' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tipe Tidak Boleh Kosong',
                ]
            ],
            'nilai_diskon' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Nilai Diskon.',
                    'numeric' => 'Nilai Diskon Harus Angka'
                ]
            ],
            'tanggal_mulai' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal Mulai Tidak Boleh Kosong.',
                ]
            ],
            'tanggal_selesai' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal Selesai Tidak Boleh Kosong.',
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal menambahkan Diskon. Tolong ulangi');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $name = $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tipe = $this->request->getPost('tipe', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $nilai = $this->request->getPost('nilai_diskon', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tanggal_mulai = $this->request->getPost('tanggal_mulai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tanggal_selesai = $this->request->getPost('tanggal_selesai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($tanggal_selesai < $tanggal_mulai) {
            session()->setFlashdata('errors', 'Tanggal selesai harus melebihi tanggan mulai');
            return redirect()->back();
        }

        $users = session()->get('users');
        $mitras = $this->mitras->where('users_id', $users['id'])->first();

        $kode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));

        $data_diskon = [
            'mitras_id' => $mitras['id'],
            'kode' => $kode,
            'name' => $name,
            'value' => $nilai,
            'type' => $tipe,
            'is_active' => 0,
            'start_date' => $tanggal_mulai,
            'end_date' => $tanggal_selesai
        ];

        $type = [
            'nominal',
            'persen'
        ];

        if (!in_array($tipe, $type)) {
            session()->setFlashdata('errors', 'Tolong isi tipe diskon dengan benar.');
            return redirect()->back();
        }

        $create = $this->diskons->create($data_diskon);

        session()->setFlashdata($create['status'], $create['message']);
        return redirect()->back();
    }

    public function edit()
    {
        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $users = session()->get('users');
        $mitras = $this->mitras->where('users_id', $users['id'])->first();

        $diskons = $this->diskons->getDiskon($mitras['id'], $id);

        $data = [
            'diskons' => $diskons['0']
        ];

        return view('mitra/diskon/edit', $data);
    }

    public function update()
    {
        $users = session()->get('users');
        $mitras = $this->mitras->where('users_id', $users['id'])->first();

        $id = $this->request->getPost('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$this->validate([
            'type' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tipe Tidak Boleh Kosong',
                ]
            ],
            'nilai_diskon' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Nilai Diskon.',
                    'numeric' => 'Nilai Diskon Harus Angka'
                ]
            ],
            'tanggal_mulai' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal Mulai Tidak Boleh Kosong.',
                ]
            ],
            'tanggal_selesai' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal Selesai Tidak Boleh Kosong.',
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal mengupdate Diskon. Tolong ulangi');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $tipe = $this->request->getPost('type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $nilai = $this->request->getPost('nilai_diskon', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tanggal_mulai = $this->request->getPost('tanggal_mulai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $tanggal_selesai = $this->request->getPost('tanggal_selesai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = $this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // validasi status
        $statuss = [
            'aktif',
            'tidak_aktif'
        ];
        if (!in_array($status, $statuss)) {
            session()->setFlashdata('errors', 'Tolong isi status dengan benar.');
            return redirect()->back();
        }

        // validasi tanggal
        if ($tanggal_selesai < $tanggal_mulai) {
            session()->setFlashdata('errors', 'Tanggal selesai harus melebihi tanggan mulai');
            return redirect()->back();
        }

        // validasi type 
        $type = [
            'nominal',
            'persen'
        ];
        if (!in_array($tipe, $type)) {
            session()->setFlashdata('errors', 'Tolong isi tipe diskon dengan benar.');
            return redirect()->back();
        }

        $data_diskon = [
            'value' => $nilai,
            'type' => $tipe,
            'is_active' => $status == 'aktif' ? 1 : 0,
            'start_date' => $tanggal_mulai,
            'end_date' => $tanggal_selesai
        ];

        // $update = $this->diskons->set($data_diskon)->where('id', $id)->update();
        $update = $this->diskons->update($id, $data_diskon);

        if (!$update) {
            session()->setFlashdata('errors', 'Gagal terhubung ke server. Code [fud]');
        } else {
            session()->setFlashdata('success', 'Berhasil mengupdate diskon.');
        }

        return redirect()->back();
    }
}
