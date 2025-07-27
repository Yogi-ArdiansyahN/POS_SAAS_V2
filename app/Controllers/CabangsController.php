<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Mitras;
use App\Models\Cabangs;
use App\Models\Users;
use CodeIgniter\HTTP\ResponseInterface;

class CabangsController extends BaseController
{

    protected $users, $cabangs, $mitras, $kasirs;

    public function __construct()
    {
        $this->users = new Users();
        $this->mitras = new Mitras();
        $this->cabangs = new Cabangs();
        // $this->kasirs = new Kasirs();
    }

    public function index()
    {
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
        $kasirs = $this->users->getKasir($mitras['id'], null, 'aktif', null);
        $cabangs = $this->cabangs->getCabangs($mitras['id'], null, null);

        $status = [
            'tidak_aktif',
            'buka',
            'tutup'
        ];

        $data = [
            'title' => 'Cabang',
            'kasirs' => $kasirs,
            'cabangs' => $cabangs,
            'status' => $status
        ];

        return view('mitra/cabang/index', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Cabang Tidak Boleh Kosong',
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat Cabang Tidak Boleh Kosong',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Cabang Tidak Boleh Kosong',
                ]
            ],
            'cashier' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kasir Tidak Boleh Kosong',
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal menambahkan cabang, silakan periksa kembali inputan Anda.');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput()->with('validation', $validation)->withInput();
        }

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $data_cabang = [
            'name' => $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'alamat' => $this->request->getPost('alamat', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'status' => $this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'mitras_id' => $mitras['id'],
            'users_id' => $this->request->getPost('cashier', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ];

        //create cabang
        $create = $this->cabangs->create($data_cabang);

        session()->setFlashdata($create['status'], $create['message']);

        if ($create['status'] === 'success') {
            return redirect()->to(base_url('mitra/cabang'));
        } else {
            return redirect()->back()->withInput()->with('error', $create['message']);
        }
    }

    public function edit()
    {
        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
        $cabang = $this->cabangs->getCabangs(null, null, $id);
        $kasirs = $this->users->getKasir($mitras['id'], null, 'aktif', null);

        $status = [
            'tidak_aktif',
            'buka',
            'tutup'
        ];

        $data = [
            'data' => $cabang[0],
            'kasirs' => $kasirs,
            'status' => $status,
            'title' => 'Edit Cabang'
        ];

        return view('mitra/cabang/edit', $data);
    }

    public function update()
    {
        if (!$this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Cabang Tidak Boleh Kosong',
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat Cabang Tidak Boleh Kosong',
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Status Cabang Tidak Boleh Kosong',
                ]
            ],
            'cashier' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Kasir Tidak Boleh Kosong',
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal mengupdate cabang, silakan periksa kembali inputan Anda.');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput()->with('validation', $validation)->withInput();
        }

        $id = $this->request->getPost('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data_cabang = [
            'name' => $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'alamat' => $this->request->getPost('alamat', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'status' => $this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'mitras_id' => session()->get('users')['mitras_id'],
            'users_id' => $this->request->getPost('cashier', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ];

        //update cabang
        if ($this->cabangs->set($data_cabang)->where('id', $id)->update()) {
            session()->setFlashdata('success', "Cabang berhasil diupdate.");
        } else {
            session()->setFlashdata('errors', "Gagal mengupdate cabang.");
        }

        return redirect()->to(base_url('mitra/cabang'));
    }
}
