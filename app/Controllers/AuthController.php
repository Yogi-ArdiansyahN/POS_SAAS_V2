<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CookiesModel;
use App\Models\LogActivityModel;
use App\Models\Mitras;
use App\Models\Users;
use App\Models\UsersTokenModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use DateTime;

class AuthController extends BaseController
{

    protected $usersModel, $userstokenModel, $logactivityModel, $cookiesModel, $mitras;

    public function __construct()
    {
        helper('text');
        $this->usersModel = new Users();
        $this->userstokenModel = new UsersTokenModel();
        $this->logactivityModel = new LogActivityModel();
        $this->cookiesModel = new CookiesModel();
        $this->mitras = new Mitras();
    }

    public function index()
    {
        helper('cookie');
        if (isset($_POST['login'])) {
            $input = $this->request->getPost('input', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = $this->request->getPost('password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                if (!$this->validate([
                    'input' => [
                        'rules' => 'required|valid_email',
                        'errors' => [
                            'required' => 'Email tidak boleh kosong'
                        ]
                    ]
                ])) {
                    session()->setFlashdata('failed', 'Gagal Login, silahkan ulangi kembali');
                    $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
                    return redirect()->to('/')->withInput()->with('validation', $validation);
                }
                $users = $this->usersModel->where('email', $input)->first();
            } else {
                $users = $this->usersModel->where('username', $input)->first();
            }

            if (empty($users)) {
                // session()->setFlashdata('errors', 'Invalid email/username or password. Please check your credentials and try again.');
                session()->setFlashdata('errors', 'email/username atau password salah. Silahkan cek kembali');
                return redirect()->to(base_url())->withInput();
            }

            if ($users['password'] != password_verify(strval($password), $users['password'])) {
                // session()->setFlashdata('errors', 'Invalid email/username or password. Please check your credentials and try again.');
                session()->setFlashdata('errors', 'email/username atau password salah. Silahkan cek kembali');
                return redirect()->to(base_url())->withInput();
            }

            if ($users != null) {
                // if (isset($_POST['remember'])) {
                //     $token = random_string('alnum', 100);
                //     $expired = date('Y-m-d H:i:s', strtotime("+30 days"));
                //     $cookies_data = [
                //         'users_id' => $users['id'],
                //         'ua' => $this->request->getUserAgent(),
                //         'device' => get_device(),
                //         'token' => $token,
                //         'expired_at' => $expired
                //     ];
                //     if ($this->cookiesModel->save($cookies_data)) {
                //         setcookie("pos_saas", $token, time() + 2592000);
                //     }
                // }

                if ($users['status'] == 'aktif') {
                    $data = [
                        'users' => $users,
                    ];

                    $role = $users['role'];
                    $cookie_key = random_string('alnum', 10);

                    if ($role == 'admin') {
                        $this->logactivityModel->addLog($users['id'], $users['username'] . " melakukan Login");
                        session()->set($data);
                        return redirect()->to(base_url() . 'admin');
                    }

                    if ($role == 'mitra') {
                        $this->logactivityModel->addLog($users['id'], $users['username'] . " melakukan Login");
                        session()->set($data);
                        return redirect()->to(base_url() . 'mitra');
                    }

                    if ($role == 'kasir') {
                        $this->logactivityModel->addLog($users['id'], $users['username'] . " melakukan Login");
                        session()->set($data);
                        return redirect()->to(base_url() . 'kasir/transaksi');
                    }

                    // session()->setFlashdata('errors', 'Invalid email/username or password. Please check your credentials and try again.');
                    session()->setFlashdata('errors', 'email/username atau password salah. Silahkan cek kembali');
                    return redirect()->to(base_url())->withInput();
                } else {
                    session()->setFlashdata('errors', 'Akun anda tidak aktif');
                    return redirect()->back();
                }
            }
        } else {
            return view('auth/login');
        }
    }

    public function daftar()
    {
        if (isset($_POST['daftar'])) {
            if (!$this->validate([
                // 'terms' => [
                //     'rules' => 'required',
                //     'errors' => [
                //         'required' => 'Pastikan Menyetujui Peraturan dan Kebijakan kami',
                //     ]
                // ],
                'name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama Tidak Boleh Kosong',
                    ]
                ],
                'username' => [
                    'rules' => 'required|is_unique[users.username]',
                    'errors' => [
                        'is_unique' => 'username telah terdaftar',
                        'required' => 'Username Tidak Boleh Kosong',
                    ]
                ],
                'phone' => [
                    'rules' => 'required|is_unique[users.phone]|regex_match[/^(?:62)/]',
                    'errors' => [
                        'required' => 'No WA tidak boleh kosong',
                        'is_unique' => 'No WA telah terdaftar',
                        'regex_match' => 'Nomer Handphone Wajib Menggunakan awalan 628XXX'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'required' => 'email tidak boleh kosong',
                        'valid_email' => 'format email tidak sesuai',
                        'is_unique' => 'email telah terdaftar'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[8]',
                    'errors' => [
                        'required' => 'Kata Sandi tidak boleh kosong',
                        'min_length' => 'Kata Sandi minimal 8 karakter',
                    ]
                ],
                'password-confirm' => [
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Kata Sandi tidak boleh kosong',
                        'min_length' => 'Kata Sandi minimal 8 karakter',
                        'matches' => 'Kata Sandi tidak sesuai',
                    ]
                ],
                'name_umkm' => [
                    'rules' => 'required|is_unique[mitras.name]',
                    'errors' => [
                        'required' => 'Nama UMKM tidak boleh kosong',
                        'is_unique' => 'Nama umkm telah terdaftar'
                    ]
                ],
                'alamat_umkm' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'alamat UMKM tidak boleh kosong',
                    ]
                ],

            ])) {
                session()->setFlashdata('failed', 'Gagal Daftar, silahkan ulangi kembali');
                $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
                return redirect()->to('/daftar')->withInput()->with('validation', $validation);
            }

            $nama = $this->request->getPost('name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $username = $this->request->getPost('username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = $this->request->getPost('email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $phone = $this->request->getPost('phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = $this->request->getPost('password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $token = random_string('alnum', 100);
            $expired = date('Y-m-d H:i:s', strtotime("+1 days"));

            $nama_umkm = $this->request->getPost('name_umkm', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $alamat_umkm = $this->request->getPost('alamat_umkm', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data_user = [
                'name' => $nama,
                'username' => $username,
                'phone' => $phone,
                'email' => $email,
                'password' => password_hash(strval($password), PASSWORD_BCRYPT),
                'role' => 'mitra',
                'status' => 'aktif'
            ];

            // $email_data = [
            //     'name' => $nama,
            //     'url_verifikasi' => base_url() . "activate/" . $token,
            //     'title' => 'POS SaaS - Aktivasi Akun'
            // ];

            // $data_mail = [
            //     'to' => $email,
            //     'subject' => 'Verifikasi Akun Estetik !',
            //     'message' => view("email/activation", $email_data)
            // ];

            // if (send_email($data_mail)) {
            //     if ($this->usersModel->insert($data)) {
            //         $this->userstokenModel->create_token($email, $token, $expired);
            //         session()->setFlashdata('success', 'Pendaftaran Berhasil, Silahkan cek email untuk aktivasi akun anda !');
            //         return redirect()->to('/');
            //     } else {
            //         session()->setFlashdata('errors', 'Gagal Terhubung ke server');
            //         return redirect()->to('/register');
            //     }
            // } else {
            //     session()->setFlashdata('errors', 'Terjadi Kesalahan pada server email');
            //     return redirect()->to('/register')->withInput();
            // }

            $create = $this->usersModel->create($data_user, $nama_umkm, $alamat_umkm);

            session()->setFlashdata($create['status'], $create['message']);

            return redirect()->to('/');
        } else {
            $data = [
                'title' => 'Pendaftaran - POS SaaS',
            ];
            return view('auth/register', $data);
        }
    }

    public function forgotpassword()
    {
        $request = $this->request->getPost();

        if ($request) {
            if (!$this->validate([
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email tidak boleh kosong',
                        'regex_match' => 'Tidak Boleh Menggunakan Special Character !'
                    ]
                ],
            ])) {
                session()->setFlashdata('failed', 'Gagal Login, silahkan ulangi kembali');
                $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
                return redirect()->to('/forgotpassword')->withInput()->with('validation', $validation);
            }

            $email = $this->request->getPost('email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $token = random_string('alnum', 100);
            $expired = date('Y-m-d H:i:s', strtotime("+1 days"));

            $user = $this->usersModel->where('email', $email)->get()->getRowArray();

            if ($user) {
                if ($user['status'] == "active") {

                    $email_data = [
                        'name' => $user['name'],
                        'url_verifikasi' => base_url() . "changepassword/" . $token,
                        'title' => 'Project CI 4'
                    ];

                    $data_mail = [
                        'to' => $email,
                        'subject' => 'Change Password!',
                        'message' => view("email/activation", $email_data)
                    ];

                    if (send_email($data_mail)) {
                        if ($this->userstokenModel->create_token($email, $token, $expired)) {
                            session()->setFlashdata('success', 'Silahkan cek email untuk ubah password');
                            return redirect()->to('/');
                        } else {
                            session()->setFlashdata('errors', 'Gagal Terhubung ke server');
                            return redirect()->to('/forgotpassword');
                        }
                    } else {
                        session()->setFlashdata('errors', 'Terjadi Kesalahan pada server email');
                        return redirect()->to('/forgotpassword')->withInput();
                    }
                }
                session()->setFlashdata('errors', 'Email Belum Active, Hubungin CS untuk langkah selanjutnya!');
                return redirect()->to('/forgotpassword')->withInput();
            }

            session()->setFlashdata('errors', 'Email Tidak Terdaftar');
            return redirect()->to('/forgotpassword')->withInput();
        }
        return view('auth/forgotpassword');
    }

    public function changepassword($token)
    {
        $getData = $this->userstokenModel->where('token', $token)->get()->getRowArray();
        $expired_at = $getData['expired_at'];
        $dateNow = date("Y-m-d h:i:s");

        if ($dateNow >= $expired_at) {
            session()->setFlashdata('errors', 'Token anda untuk change password expired. Input email kembali untuk mendapatkan token baru');
            return redirect()->to('/forgotpassword');
        }

        $data = [
            'token' => $token
        ];

        return view('auth/changepassword', $data);
    }

    public function updatepassword()
    {
        $request = $this->request->getPost();
        $token = $this->request->getPost();
        $urlChangePassword = base_url('/changepassword/' . $token['token']);

        if ($request) {
            if (!$this->validate([
                'password' => [
                    'rules' => 'required|min_length[8]',
                    'errors' => [
                        'required' => 'Password Tidak Boleh Kosong',
                        'min_length' => 'Password minimal 8 karakter'
                    ]
                ],
                'confirm-password' => [
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Password Tidak Boleh Kosong',
                        'matches' => 'Password Tidak Sesuai'
                    ]
                ]
            ])) {
                session()->setFlashdata('failed', 'Gagal Login, silahkan ulangi kembali');
                $validation = session()->setFlashdata('errors', \Config\Services::validation()->listErrors());
                return redirect()->to($urlChangePassword)->with('validation', $validation);
            }

            $password = $this->request->getPost('password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $userToken = $this->userstokenModel->where('token', $token['token'])->get()->getRowArray();

            $user_id = $userToken['users_id'];

            $data = [
                'password' => password_hash(strval($password), PASSWORD_BCRYPT),
            ];

            $this->usersModel->update($user_id, $data);
            session()->setFlashdata('success', 'Password berhasil diubah. Silahkan login');
            return redirect()->to('/');
        }
    }


    public function logout()
    {
        session()->destroy();
        // unset($_COOKIE['estetik_cookies']);
        setcookie('pos_saas', '', 1, '/');
        session()->setFlashdata('success', "Berhasil Logout");
        return redirect()->to('/');
    }
}
