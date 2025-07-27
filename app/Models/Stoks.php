<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Stoks extends Model
{
    protected $table            = 'stoks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['mitras_id', 'cabangs_id', 'menus_id', 'quantity', 'current_quantity', 'notes'];

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


    public function create($menus, $mitras_id, $cabang_id)
    {
        // Set timezone ke Indonesia (WIB)
        date_default_timezone_set('Asia/Jakarta');

        $data = [];

        foreach ($menus as $key => $value) {
            if (is_numeric($value) && $value >= 0) {
                $data[] = [
                    'mitras_id' => $mitras_id,
                    'cabangs_id' => $cabang_id,
                    'menus_id' => $key,
                    'quantity' => $value,
                    'current_quantity' => $value,
                    'notes' => 'Stok harian ditambahkan, ' . date('Y-m-d H:i:s'),
                ];
            }
        }

        if (!empty($data)) {
            try {
                $this->db->transBegin();

                // insert data into the stoks table
                if ($this->db->table('stoks')->insertBatch($data)) {
                    // update status cabang 
                    if (!$this->db->table('cabangs')->where('id', $cabang_id)->update(['status' => 'buka'])) {
                        throw new Exception('Gagal tersambung ke server. Error code [upcb]');
                    };
                } else {
                    throw new Exception('Gagal tersambung ke server. Error code [upcb]');
                }

                $result =  [
                    'status' => 'success',
                    'message' => 'Stok berhasil ditambahkan.'
                ];
                $this->db->transCommit();
            } catch (Exception $e) {
                $this->db->transRollback();
                $result =  [
                    'status' => 'errors',
                    'message' => $e->getMessage()
                ];
            }
        }

        return $result;
    }

    public function getStoks($mitras_id = null, $cabangs_id = null, $date = null, $menus_id = null)
    {
        $builder = $this->db->table('stoks')
            ->select('
                stoks.id,
                stoks.quantity,
                stoks.current_quantity,
                stoks.notes,
                menus.name as menu_name,
                menus.id as menus_id,
                cabangs.name as cabang_name,
            ')
            ->join('menus', 'menus.id = stoks.menus_id', 'left')
            ->join('cabangs', 'cabangs.id = stoks.cabangs_id', 'left');

        if ($date) {
            $builder->where('DATE(stoks.created_at)', $date);
        }

        if ($mitras_id) {
            $builder->where('stoks.mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $builder->where('stoks.cabangs_id', $cabangs_id);
        }

        if ($menus_id) {
            $builder->where('stoks.menus_id', $menus_id);
        }

        return $builder->get()->getResultArray();
    }
}
