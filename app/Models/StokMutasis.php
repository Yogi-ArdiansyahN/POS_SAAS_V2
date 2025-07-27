<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class StokMutasis extends Model
{
    protected $table            = 'stokmutasis';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'stoks_id',
        'menus_id',
        'cabangs_id',
        'mitras_id',
        'tipe_mutasi',
        'quantity',
        'current_quantity_sebelum',
        'quantity_sebelum',
        'current_quantity_sesudah',
        'quantity_sesudah',
        'notes'
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

    public function create($data_stoks, $data_stok_mutasi, $data_stok_pindah = null, $cabang_id, $mitras_id, $pindah_cabang_id = null, $date)
    {
        try {
            $this->db->transBegin();

            // update stoks
            foreach ($data_stoks as $data) {
                $stoks = $this->db->table('stoks')
                    ->where('id', $data['id'])
                    ->where('menus_id', $data['menus_id'])
                    ->where('mitras_id', $mitras_id)
                    ->where('DATE(created_at)', $date)
                    ->set([
                        'quantity' => $data['quantity'],
                        'current_quantity' => $data['current_quantity']
                    ])
                    ->update();

                if (!$stoks) {
                    throw new Exception('Gagal koneksi ke server. Error code [upstk]');
                }
            }

            // insert stok mutasi
            $mutasi = $this->db->table('stok_mutasis')
                ->insertBatch($data_stok_mutasi);

            if (!$mutasi) {
                throw new Exception('Gagal koneksi ke server. Error code [instkmts]');
            }

            if ($data_stok_pindah) {
                // dd($data_stok_pindah, $data_stoks);
                foreach ($data_stok_pindah as $key => $data) {
                    $insert_pindah = $this->db->table('stoks')
                        ->where('id', $data['id'])
                        ->where('mitras_id', $mitras_id)
                        ->where('cabangs_id', $pindah_cabang_id[$key])
                        ->where('menus_id', $data['menus_id'])
                        ->where('DATE(created_at)', $date)
                        ->set([
                            'quantity' => $data['quantity'],
                            'current_quantity' => $data['current_quantity']
                        ])
                        ->update();
                    if (!$insert_pindah) {
                        throw new Exception('Gagal koneksi ke server. Error code [upstkpndh]');
                    }
                }
            }

            $result = [
                'status' => 'success',
                'message' => 'Mutasi berhasil dilakukan'
            ];

            $this->db->transCommit();
        } catch (Exception $e) {
            $this->db->transRollback();
            $result = [
                'status' => 'errors',
                'message' => $e->getMessage()
            ];
        }

        return $result;
    }
}
