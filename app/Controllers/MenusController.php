<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Menus;
use App\Models\Mitras;
use App\Models\Stoks;
use App\Models\Users;
use CodeIgniter\HTTP\ResponseInterface;

class MenusController extends BaseController
{

    protected $users, $mitras, $menus, $stoks;

    public function __construct()
    {
        $this->users = new Users();
        $this->mitras = new Mitras();
        $this->menus = new Menus();
        $this->stoks = new Stoks();
    }

    public function index()
    {

        $kategori = [
            'makanan',
            'minuman'
        ];

        $data = [
            'title' => 'Menu',
            'kategori' => $kategori
        ];

        return view('mitra/menu/index', $data);
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
            'harga_modal' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Harga Modal Tidak Boleh Kosong',
                    'numeric' => 'Harga Modal Harus Angka'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Harga Jual Tidak Boleh Kosong',
                    'numeric' => 'Harga Jual Harus Angka'
                ]
            ],
        ])) {
            session()->setFlashdata('failed', 'Gagal, silahkan ulangi kembali');
            $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
            return redirect()->back()->withInput();
        }

        $name = $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $harga_modal = $this->request->getPost('harga_modal', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $harga_jual = $this->request->getPost('harga_jual', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $kategori = $this->request->getPost('kategori', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $foto = $this->request->getFile('foto');

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        // validation for harga modal and harga jual
        if ($harga_modal <= 0 || $harga_jual <= 0) {
            session()->setFlashdata('failed', 'Harga Modal dan Harga Jual harus lebih dari 0');
            return redirect()->back()->withInput();
        }
        // validation for harga jual and harga modal
        if ($harga_jual < $harga_modal) {
            session()->setFlashdata('failed', 'Harga Jual tidak boleh kurang dari Harga Modal');
            return redirect()->back()->withInput();
        }

        // validation for name menus
        $validation = $this->validation_name($mitras['id'], $name);

        // activate menu if validation is not active
        if ($validation == 'not_active') {
            $data_update = [
                'harga_modal' => str_replace('.', '', $harga_modal),
                'harga_jual' => str_replace('.', '', $harga_jual),
                'kategori' => $kategori,
                'mitras_id' => $mitras['id'],
                'is_active' => 1
            ];

            if ($this->menus->set($data_update)->where('name', $name)->update()) {

                // insert foto 
                if ($foto && $foto->isFile() && $foto->isValid()) {
                    // Jika validasi berhasil, proses penyimpanan file di sini
                    $newNameFoto = $foto->getRandomName(); // Beri nama file baru secara acak
                    $foto->move(FCPATH . '/uploads/image', $newNameFoto); // Pindahkan file ke folder uploads

                    $data_update = [
                        'foto' => $newNameFoto
                    ];

                    $insert_foto = $this->menus->set($data_update)->where('name', $name)->update();

                    if ($insert_foto) {
                        $response = 'Menu berhasil ditambahkan.';
                    } else {
                        $response = 'Menu berhasil ditambahkan, tetapi gagal menyimpan foto.';
                    }
                } else {
                    $response = 'Menu berhasil ditambahkan';
                }

                session()->setFlashdata('success', $response);
            } else {
                session()->setFlashdata('errors', 'Gagal tersambung ke server');
            }
            return redirect()->to('/mitra/menu');
        }

        if ($validation == true) {
            session()->setFlashdata('failed', 'Nama sudah ada');
            return redirect()->back()->withInput();
        }

        // validation for foto
        if ($foto && $foto->isFile()) {
            if (!$foto->isValid()) {
                $errors = 'File yang diunggah tidak valid';
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }

            // MIME type validation
            $allowedMimeTypes = ['image/jpg', 'image/jpeg', 'image/png'];
            if (!in_array($foto->getMimeType(), $allowedMimeTypes)) {
                $errors = 'Hanya format gambar JPG, JPEG, atau PNG yang diizinkan';
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }

            // Maximum file size validation (e.g. 2 MB)
            if ($foto->getSize() > 2048 * 1024) {
                $errors = 'Ukuran file maksimum yang diizinkan adalah 2 MB';
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }
        }

        // validation for kategori
        if (!in_array($kategori, ['makanan', 'minuman'])) {
            session()->setFlashdata('failed', 'Kategori tidak valid');
            return redirect()->back()->withInput();
        }

        $data = [
            'name' => $name,
            'harga_modal' => str_replace('.', '', $harga_modal),
            'harga_jual' => str_replace('.', '', $harga_jual),
            'kategori' => $kategori,
            'foto' => $foto->getName(),
            'mitras_id' => $mitras['id'],
            'is_active' => 1
        ];

        // insert menu data
        $create = $this->menus->create($data);

        // insert foto 
        if ($create['status'] === 'success' && $foto && $foto->isFile() && $foto->isValid()) {
            // Jika validasi berhasil, proses penyimpanan file di sini
            $newNameFoto = $foto->getRandomName(); // Beri nama file baru secara acak
            $foto->move(FCPATH . '/uploads/image', $newNameFoto); // Pindahkan file ke folder uploads
            $menus_id = $create['menus_id'];

            $data_update = [
                'foto' => $newNameFoto
            ];

            $insert_foto = $this->menus->set($data_update)->where('id', $menus_id)->update();

            if ($insert_foto) {
                $create['message'] = 'Menu berhasil ditambahkan dengan foto.';
            } else {
                $create['status'] = 'errors';
                $create['message'] = 'Menu berhasil ditambahkan, tetapi gagal menyimpan foto.';
            }
        }

        session()->setFlashdata($create['status'], $create['message']);

        return redirect()->to('/mitra/menu');
    }

    public function edit()
    {
        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
        $menu = $this->menus->find($id);

        $kategori = [
            'makanan',
            'minuman'
        ];

        if (!$menu || $menu['mitras_id'] != $mitras['id']) {
            session()->setFlashdata('error', 'Menu tidak ditemukan atau tidak memiliki akses.');
            return redirect()->to('/mitra/menu');
        }

        $data = [
            'menu' => $menu,
            'title' => 'Edit Menu',
            'kategori' => $kategori
        ];

        return view('mitra/menu/edit', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Menu Tidak Boleh Kosong',
                ]
            ],
            'harga_modal' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Harga Modal Tidak Boleh Kosong',
                    'numeric' => 'Harga Modal Harus Angka'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Harga Jual Tidak Boleh Kosong',
                    'numeric' => 'Harga Jual Harus Angka'
                ]
            ],
        ])) {
            return redirect()->back()->withInput();
        }

        $name = $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $harga_modal = $this->request->getPost('harga_modal', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $harga_jual = $this->request->getPost('harga_jual', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $kategori = $this->request->getPost('kategori', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $foto = $this->request->getFile('foto');

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        // validation for harga modal and harga jual
        if ($harga_modal <= 0 || $harga_jual <= 0) {
            session()->setFlashdata('failed', 'Harga Modal dan Harga Jual harus lebih dari 0');
            return redirect()->back()->withInput();
        }
        // validation for harga jual and harga modal
        if ($harga_jual < $harga_modal) {
            session()->setFlashdata('failed', 'Harga Jual tidak boleh kurang dari Harga Modal');
            return redirect()->back()->withInput();
        }

        // validation for name menus
        $validation = $this->validation_name($mitras['id'], $name, $id);

        if ($validation == true) {
            session()->setFlashdata('failed', 'Nama sudah ada');
            return redirect()->back()->withInput();
        }

        // validation for foto
        if ($foto && $foto->isFile()) {
            if (!$foto->isValid()) {
                $errors = 'File yang diunggah tidak valid';
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }

            // MIME type validation
            $allowedMimeTypes = ['image/jpg', 'image/jpeg', 'image/png'];
            if (!in_array($foto->getMimeType(), $allowedMimeTypes)) {
                $errors = 'Hanya format gambar JPG, JPEG, atau PNG yang diizinkan';
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }
            // Maximum file size validation (e.g. 2 MB)
            if ($foto->getSize() > 2048 * 1024) {
                $errors = 'Ukuran file maksimum yang diizinkan adalah 2 MB';
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }
        }
        // validation for kategori
        if (!in_array($kategori, ['makanan', 'minuman'])) {
            session()->setFlashdata('failed', 'Kategori tidak valid');
            return redirect()->back()->withInput();
        }
        $data = [
            'name' => $name,
            'harga_modal' => str_replace('.', '', $harga_modal),
            'harga_jual' => str_replace('.', '', $harga_jual),
            'kategori' => $kategori,
            'mitras_id' => $mitras['id'],
        ];

        // if foto is uploaded
        if ($foto && $foto->isFile() && $foto->isValid()) {
            // Jika validasi berhasil, proses penyimpanan file di sini
            $newNameFoto = $foto->getRandomName(); // Beri nama file baru secara acak
            $foto->move(FCPATH . '/uploads/image', $newNameFoto); // Pindahkan file ke folder uploads
            $data['foto'] = $newNameFoto;
        } else {
            // Jika tidak ada foto yang diunggah, ambil foto lama
            $data['foto'] = $this->request->getPost('old_image', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // update menu data
        $update = $this->menus->update($id, $data);
        if ($update) {
            session()->setFlashdata('success', 'Menu berhasil diupdate.');
        } else {
            session()->setFlashdata('errors', 'Gagal mengupdate menu.');
        }
        return redirect()->to('/mitra/menu');
    }

    public function delete()
    {
        date_default_timezone_set('Asia/Jakarta');

        $id = $this->request->getVar('id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();
        $menu = $this->menus->find($id);

        //validasi menu, apabila ada stok menu tidak bisa dihapus
        if ($this->stoks->where('menus_id', $id)->where('DATE(created_at)', date('Y-m-d'))->first()) {
            return $this->response->setJSON(['status' => 'errors', 'message' => 'Menu tidak bisa dihapus karena masih memiliki stok.']);
        }

        if (!$menu || $menu['mitras_id'] != $mitras['id']) {
            return $this->response->setJSON(['status' => 'errors', 'message' => 'Menu tidak ditemukan atau tidak memiliki akses.']);
        }

        if ($this->menus->set(['is_active' => 0])->where('id', $id)->update()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Menu berhasil dihapus.']);
        } else {
            return $this->response->setJSON(['status' => 'errors', 'message' => 'Gagal menghapus menu.']);
        }
    }

    function validation_name($mitras_id, $name, $id = null)
    {
        $menus = $this->menus->where('mitras_id', $mitras_id)->where('name', $name)->first();

        if ($id == !empty($menus['id'])) {
            return false; // Jika ID sama, tidak perlu validasi
        }

        if ($menus) {
            if ($menus['is_active'] == 0) {
                return 'not_active'; // Jika menu tidak aktif, tidak perlu validasi
            }

            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    public function getDataTable()
    {
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $draw = $this->request->getVar('draw', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $start = $this->request->getVar('start', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $length = $this->request->getVar('length', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $search = $this->request->getVar('search', FILTER_SANITIZE_FULL_SPECIAL_CHARS)['value'];

        $data = $this->menus->dataTableMenu($start, $length, $search, $mitras['id']);
        $recordsTotal = $this->menus->countAllMenu($mitras['id'], 'kasir');
        $recordsFiltered = $this->menus->countFilteredMenu($search, $mitras['id']);

        $response = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }
}
