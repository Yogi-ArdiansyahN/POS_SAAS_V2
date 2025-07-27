<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersTokenModel extends Model
{
    protected $table            = 'users_token';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['id', 'users_id', 'token', 'expired_at', 'created_at', 'updated_at'];

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

    public function create_token($email, $token, $expired)
    {
        $users_id = $this->db->table('users')->where('email', $email)->get()->getRowArray()['id'];
        $data = [
            'users_id' => $users_id,
            'token' => $token,
            'expired_at' => $expired,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->table('users_token')->insert($data);
    }

    public function create_otp($email, $otp)
    {
        $users_id = $this->db->table('users')->where('email', $email)->get()->getRowArray()['id'];
        $data = [
            'users_id' => $users_id,
            'token' => password_hash($otp, PASSWORD_BCRYPT),
            'expired_at' => date('Y-m-d H:i:s', strtotime("+1 days")),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->table('users_token')->insert($data);
    }
}
