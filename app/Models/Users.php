<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Users extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['name', 'phone', 'email', 'password', 'role', 'status', 'username', 'balance'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getUsers()
    {
        return $this->db->table('users')
            ->select('*')
            ->get()->getResultArray();
    }

    public function getUserById($id)
    {
        return $this->db->table('users')
            ->select('*')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    public function getKasir($mitras_id = null, $cabangs_id = null, $status = null, $kasir_id = null)
    {
        $builder = $this->db->table('users')
            ->select('
            users.id,
            users.name,
            users.status,
        ')
            ->join('cabangs', 'cabangs.users_id = users.id', 'left')
            ->where('cabangs.users_id IS NULL');

        if ($mitras_id) {
            $builder->where('users.mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $builder->where('cabangs.id', $cabangs_id);
        }

        if ($status) {
            $builder->where('users.status', $status);
        }

        if ($kasir_id) {
            $builder->where('users.id', $kasir_id);
        }

        return $builder->get()->getResultArray();
    }

    public function create($data_user, $nama_umkm, $alamat_umkm)
    {
        try {
            $this->db->transBegin();

            // create user
            if ($this->db->table('users')->insert($data_user)) {
                $user = $this->db->table('users')->where('email', $data_user['email'])->get()->getRowArray();

                $data_umkm = [
                    'users_id' => $user['id'],
                    'name' => $nama_umkm,
                    'alamat' => $alamat_umkm,
                ];

                // create mitra
                if ($this->db->table('mitras')->insert($data_umkm)) {
                    $mitra = $this->db->table('mitras')->where('users_id', $user['id'])->get()->getRowArray();

                    // Update user with mitra id
                    $this->db->table('users')->set(['mitras_id' => $mitra['id']])->where('id', $user['id'])->update();

                    $response = [
                        'status' => "success",
                        'message' => 'Pendaftaran Berhasil, Silahkan login untuk melanjutkan !',
                    ];
                } else {
                    throw new Exception('Gagal Terhubung ke server. Error Code [crtcmpn]');
                }
            } else {
                throw new Exception('Gagal Terhubung ke server. Error Code [crtusr]');
            }

            $this->db->transCommit();
            return $response;
        } catch (\Exception $e) {
            $this->db->transRollback();
            $response = [
                'status' => "errors",
                'message' => 'Gagal Terhubung ke server',
            ];
            return $response;
        }
    }

    public function create_kasir($data, $default_password)
    {
        if (!$this->db->table('users')->insert($data)) {
            $response = [
                'status' => 'errors',
                'message' => 'Gagal menambahkan kasir.'
            ];
            return $response;
        } else {
            $response = [
                'status' => 'success',
                'message' => 'Kasir berhasil ditambahkan. Default password: ' . $default_password . ' (Silakan ganti password setelah login pertama kali)'
            ];
            return $response;
        }
    }

    public function dataTableKasir($start, $length, $search, $mitras_id = null)
    {
        $builder = $this->db->table('users')
            ->select('
                users.id,
                users.name,
                users.email,
                users.phone,
                users.status,
                cabangs.name as cabang_name
            ')
            ->join('cabangs', 'cabangs.users_id = users.id', 'left');

        if ($mitras_id) {
            $builder->where('users.mitras_id', $mitras_id);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('cabang_name', $search)
                ->orLike('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.phone', $search)
                ->orLike('users.status', $search)
                ->groupEnd();
        }

        $builder->limit($length, $start);

        return $builder->get()->getResultArray();
    }

    public function countAllKasir($mitras_id = null, $role = null)
    {
        if ($mitras_id) {
            if ($role) {
                return $this->db->table('users')->where('role', $role)->where('mitras_id', $mitras_id)->countAllResults();
            }
            return $this->db->table('users')->where('mitras_id', $mitras_id)->countAll();
        }

        return $this->countAll();
    }

    public function countFilteredKasir($search, $mitras_id = null)
    {
        $builder = $this->db->table('users')
            ->select('
            users.id,
            users.name,
            users.email,
            users.phone,
            users.status,
            cabangs.name as cabang_name
        ')
            ->join('cabangs', 'cabangs.users_id = users.id', 'left');
        // ->where('users.role', 'cashier');

        if ($mitras_id) {
            $builder->where('users.mitras_id', $mitras_id);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('cabang_name', $search)
                ->orLike('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.phone', $search)
                ->orLike('users.status', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
