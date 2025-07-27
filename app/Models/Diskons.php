<?php

namespace App\Models;

use CodeIgniter\Model;

class Diskons extends Model
{
    protected $table            = 'diskons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mitras_id',
        'kode',
        'name',
        'type',
        'value',
        'is_active',
        'start_date',
        'end_date',
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

    public function create($data_diskon)
    {

        // create 
        $create = $this->db->table('diskons')->insert($data_diskon);

        if ($create) {
            $result = [
                'status' => 'success',
                'message' => 'Berhasil membuat diskon.'
            ];
        } else {
            $result = [
                'status' => 'errors',
                'message' => 'Berhasil membuat diskon.'
            ];
        }

        return $result;
    }

    public function getDiskon($mitras_id = null, $id = null)
    {
        $builder = $this->db->table('diskons');

        if ($mitras_id) {
            $builder->where('mitras_id', $mitras_id);
        }

        if ($id) {
            $builder->where('id', $id);
        }

        return $builder->get()->getResultArray();
    }
}
