<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Users;
use CodeIgniter\I18n\Time;

class UsersController extends BaseController
{

    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new Users();
    }

    public function KelolaUser()
    {
        $users = $this->usersModel->getUsers();

        $data = [
            'title' => 'Kelola User',
            'users' => $users
        ];
        return view('admin/kelolauser', $data);
    }

    public function getuser()
    {
        $id = $this->request->getPost('id');
        $user = $this->usersModel->getUserById($id);

        $user = json_encode($user);

        return $this->response->setJSON($user);
    }

    public function updateuser()
    {
        $id = $this->request->getPost('id');
        $nama = $this->request->getPost('nama');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');
        $balance = $this->request->getPost('balance');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');

        $data = [
            'name' => $nama,
            'username' => $username,
            'phone' => $phone,
            'email' => $email,
            'role' => $role,
            'balance' => $balance,
            'status' => $status,
            'updated_at' => datenow()
        ];

        $this->usersModel->update($id, $data);

        $response = [
            'status' => 'success',
            'message' => 'Success Update Data User'
        ];

        return $this->response->setJSON($response);
    }

    public function tambahuser()
    {
        $id = $this->request->getPost('id');
        $nama = $this->request->getPost('nama');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');
        $username = $this->request->getPost('username');
        $balance = $this->request->getPost('balance');

        $data = [
            'name' => $nama,
            'phone' => $phone,
            'username' => $username,
            'balance' => $balance,
            'email' => $email,
            'role' => $role,
            'password' => password_hash('12345678', PASSWORD_BCRYPT),
            'status' => $status,
            'updated_at' => datenow(),
            'created_at' => datenow()
        ];

        $this->usersModel->insert($data);

        $response = [
            'status' => 'success',
            'message' => 'Success Create Data User'
        ];

        return $this->response->setJSON($response);
    }
}
