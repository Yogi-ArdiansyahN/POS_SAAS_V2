<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Cabangs extends Model
{
    protected $table            = 'cabangs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'mitras_id',
        'users_id',
        'alamat',
        'status'
    ];

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


    public function create($data)
    {

        if ($this->db->table('cabangs')->insert($data)) {
            $response = [
                'status' => 'success',
                'message' => 'Cabang berhasil ditambahkan.'
            ];
        } else {
            $response = [
                'status' => 'errors',
                'message' => 'Gagal menambahkan cabang.'
            ];
        }

        return $response;
    }

    public function getCabangs($mitras_id = null, $status = null, $id = null)
    {
        $builder = $this->db->table('cabangs')
            ->select('
                    cabangs.id as id,
                    cabangs.name as cabang_name,
                    users.name as kasir_name,
                    users.id as kasir_id,
                    cabangs.alamat,
                    cabangs.status
                ');
        $builder->join('users', 'users.id = cabangs.users_id', 'left');

        if ($mitras_id) {
            $builder->where('cabangs.mitras_id', $mitras_id);
        }

        if ($status) {
            $builder->where('cabangs.status', $status);
        }

        if ($id) {
            $builder->where('cabangs.id', $id);
        }

        return $builder->get()->getResultArray();
    }
}
