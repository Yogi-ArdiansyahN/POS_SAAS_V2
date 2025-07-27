<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\UsersTable;
use App\Models\Mitras;
use App\Models\Users;
use App\Models\Cabangs;
use App\Models\Transaksis;

class AdminController extends BaseController
{

    protected $users, $mitras, $cabangs, $transaksis;

    public function __construct()
    {
        $this->users = new Users();
        $this->mitras = new Mitras();
        $this->cabangs = new Cabangs();
        $this->transaksis = new Transaksis();
    }

    public function index(): string
    {
        date_default_timezone_set('Asia/Jakarta');

        $users = $this->users->where('id !=', 1)->get()->getResultArray();
        $mitras = $this->mitras->get()->getResultArray();
        $cabangs = $this->cabangs->get()->getResultArray();
        $transaksis = $this->transaksis->where('DATE(created_at)', date('Y-m-d'))->get()->getResultArray();

        $total_user = count($users);
        $total_mitra = count($mitras);
        $total_cabang = count($cabangs);
        $total_transaksi = count($transaksis);

        $data = [
            'title' => 'Dashboard',
            'total_user' => $total_user,
            'total_mitra' => $total_mitra,
            'total_cabang' => $total_cabang,
            'total_transaksi' => $total_transaksi
        ];

        return view('admin/dashboard', $data);
    }

    public function index_mitra()
    {
        $mitra = $this->users->select('
                users.id,
                users.name,
                users.username,
                mitras.name as mitra,
                COUNT(cabangs.id) as jumlah_cabang,
                users.status
            ')
            ->where('role', 'mitra')
            ->join('cabangs', 'cabangs.mitras_id = users.mitras_id', 'left')
            ->join('mitras', 'mitras.id = users.mitras_id', 'left')
            ->groupBy('users.id, users.name, users.username, mitras.name, users.status')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Mitra',
            'mitras' => json_encode($mitra)
        ];
        return view('admin/mitra/index', $data);
    }

    public function edit_mitra()
    {
        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mitra = $this->users
            ->select('
                id,
                name,
                username,
                status
            ')
            ->where('users.id', $id)
            ->get()
            ->getRowArray();
        $data = [
            'mitra' => $mitra
        ];
        return view('admin/mitra/edit', $data);
    }

    public function update_mitra()
    {
        $id = $this->request->getPost('id', FILTER_SANITIZE_SPECIAL_CHARS);
        $status = $this->request->getPost('status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $statuss = [
            'aktif',
            'tidak_aktif'
        ];

        // validasi 
        if (!in_array($status, $statuss)) {
            session()->setFlashdata('errors', 'Tolong pilih status dengan benar.');
        }

        // update 
        $update = $this->users->set(['status' => $status])->where('id', $id)->update();
        if (!$update) {
            session()->setFlashdata('errors', 'Gagal terhubung ke server.');
        } else {
            session()->setFlashdata('success', 'Berhasil mengubah status user.');
        }

        return redirect()->to(base_url() . 'admin/mitra');
    }
}
