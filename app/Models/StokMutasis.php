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

    public function create($data_stoks, $data_stok_mutasi, $data_stok_pindah = null, $mitras_id, $pindah_cabang_id = null, $date)
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
                        ->where('cabangs_id', $data['cabangs_id'])
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

    public function getDataRiwayatMutasi($start, $length, $search, $mitras_id = null, $cabangs_id = null, $date = null)
    {
        $stok_mutasis = $this->db->table('stok_mutasis')
            ->select('
                stok_mutasis.quantity,
                stok_mutasis.current_quantity_sebelum,
                stok_mutasis.quantity_sebelum,
                stok_mutasis.current_quantity_sesudah,
                stok_mutasis.quantity_sesudah,
                stok_mutasis.notes,
                stok_mutasis.tipe_mutasi,
                menus.name as menu_name,
                cabangs.name as cabang_name,
                stok_mutasis.created_at
            ')
            ->join('menus', 'menus.id = stok_mutasis.menus_id')
            ->join('cabangs', 'cabangs.id = stok_mutasis.cabangs_id');

        if ($mitras_id) {
            $stok_mutasis->where('stok_mutasis.mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $stok_mutasis->where('stok_mutasis.cabangs_id', $cabangs_id);
        }

        if ($date) {
            $stok_mutasis->where('DATE(stok_mutasis.created_at)', $date);
        }

        if (!empty($search)) {
            $stok_mutasis->groupStart()
                ->like('cabangs.name', $search)
                ->orLike('stok_mutasis.tipe_mutasi', $search)
                ->orLike('menus.name', $search)
                ->groupEnd();
        }

        if ($length != 1) {
            $stok_mutasis->limit($length, $start);
        }

        $stok_mutasis = $stok_mutasis->orderBy('stok_mutasis.created_at', 'DESC');

        return $stok_mutasis->get()->getResultArray();
    }

    public function countAllgetDataRiwayatMutasi($mitras_id = null, $cabangs_id = null, $date = null)
    {
        $stok_mutasis = $this->db->table('stok_mutasis')
            ->select('
                stok_mutasis.quantity,
                stok_mutasis.current_quantity_sebelum,
                stok_mutasis.quantity_sebelum,
                stok_mutasis.current_quantity_sesudah,
                stok_mutasis.quantity_sesudah ,
                stok_mutasis.notes,
                stok_mutasis.tipe_mutasi,
                menus.name,
                cabangs.name,
                stok_mutasis.created_at

            ')
            ->join('menus', 'menus.id = stok_mutasis.menus_id')
            ->join('cabangs', 'cabangs.id = stok_mutasis.cabangs_id')
            ->where('stok_mutasis.mitras_id', $mitras_id);

        if ($cabangs_id) {
            $stok_mutasis->where('stok_mutasis.cabangs_id', $cabangs_id);
        }

        if ($date) {
            $stok_mutasis->where('DATE(stok_mutasis.created_at)', $date);
        }

        $stok_mutasis = $stok_mutasis->countAllResults();

        return $stok_mutasis;
    }

    public function countFilteredgetDataRiwayatMutasi($search, $mitras_id = null, $cabangs_id = null, $date = null)
    {
        $stok_mutasis = $this->db->table('stok_mutasis')
            ->select('
                stok_mutasis.quantity,
                stok_mutasis.current_quantity_sebelum,
                stok_mutasis.quantity_sebelum,
                stok_mutasis.current_quantity_sesudah,
                stok_mutasis.quantity_sesudah ,
                stok_mutasis.notes,
                stok_mutasis.tipe_mutasi,
                menus.name,
                cabangs.name,
                stok_mutasis.created_at
            ')
            ->join('menus', 'menus.id = stok_mutasis.menus_id')
            ->join('cabangs', 'cabangs.id = stok_mutasis.cabangs_id');

        if ($mitras_id) {
            $stok_mutasis->where('stok_mutasis.mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $stok_mutasis->where('stok_mutasis.cabangs_id', $cabangs_id);
        }

        if ($date) {
            $stok_mutasis->where('DATE(stok_mutasis.created_at)', $date);
        }

        if (!empty($search)) {
            $stok_mutasis->groupStart()
                ->like('cabangs.name', $search)
                ->orLike('stok_mutasis.tipe_mutasi', $search)
                ->orLike('menus.name', $search)
                ->groupEnd();
        }

        return $stok_mutasis->countAllResults();
    }
}
