<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatLangganans extends Model
{
    protected $table            = 'riwayat_langganans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mitras_id',
        'langganans_id',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'expired_at'
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

    public function create($data, $name_langganan)
    {
        $create = $this->db->table('riwayat_langganans')->insert($data);

        if (!$create) {
            $response = [
                'status' => 'errors',
                'message' => 'Gagal Terhubung ke server. Error Kode [fcl]'
            ];
        } else {
            $response = [
                'status' => 'success',
                'message' => 'Berhasil berlangganan'
            ];
        }

        if ($name_langganan == 'Trial') {
            // update mitra 
            $update = $this->db->table('mitras')
                ->set(['is_trial'  => 1])
                ->where('mitras.id', $data['mitras_id'])
                ->update();

            if (!$update) {
                $response = [
                    'status' => 'errors',
                    'message' => 'Gagal Terhubung ke server. Error Kode [fcl]'
                ];
            }
        }

        return $response;
    }
}
