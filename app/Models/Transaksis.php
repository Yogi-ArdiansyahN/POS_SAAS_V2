<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Transaksis extends Model
{
    protected $table            = 'transaksis';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order',
        'cabangs_id',
        'mitras_id',
        'diskons_id',
        'total_setelah_diskon',
        'total',
        'margin'
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

    public function create($data_transaksi, $data_detail_transaksi, $date = null)
    {
        try {
            $this->db->transBegin();

            // insert transaksi
            $transaksi = $this->db->table('transaksis')->insert($data_transaksi);
            if (!$transaksi) {
                throw new Exception('Gagal membuat transaksi. Code [trxf]');
            }

            $order = $data_transaksi['order'];

            // ambil ID insert terakhir
            $transaksi_id = $this->db->insertID();

            // insert detail
            foreach ($data_detail_transaksi as $key => $detail) {
                $data_detail_transaksi[$key]['transaksis_id'] = $transaksi_id;
            }

            $detail_transaksi = $this->db->table('detail_transaksis')->insertBatch($data_detail_transaksi);
            if (!$detail_transaksi) {
                throw new Exception('Gagal tersambung ke server. Code [dtrxf]');
            }

            // update stock menus
            $mitras_id = $data_transaksi['mitras_id'];
            $cabangs_id = $data_transaksi['cabangs_id'];
            $data_stoks_menu = $this->db->table('stoks')->where('mitras_id', $mitras_id)->where('cabangs_id', $cabangs_id)->where('DATE(created_at)', $date)->get()->getResultArray();
            // return $data_stoks_menu;
            foreach ($data_stoks_menu as $data) {
                foreach ($data_detail_transaksi as $datas) {
                    if ($data['menus_id'] == $datas['menus_id']) {
                        $stok = $this->db->table('stoks')
                            ->set(['current_quantity' => $data['current_quantity'] - $datas['quantity']])
                            ->where('mitras_id', $mitras_id)
                            ->where('cabangs_id', $cabangs_id)
                            ->where('menus_id', $datas['menus_id'])
                            ->where('DATE(created_at)', $date)
                            ->update();

                        if (!$stok) {
                            throw new Exception('Gagal tersambung ke server. Code [upstk]');
                        }
                    }
                }
            }

            $this->db->transCommit();

            return [
                'status' => 'success',
                'order' => $order,
                'message' => 'Transaksi Berhasil.'
            ];
        } catch (Exception $e) {
            $this->db->transRollback();

            return [
                'status' => 'errors',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getTransaksis($mitras_id = null, $cabangs_id = null, $order)
    {
        $builder = $this->db->table('transaksis');

        if ($mitras_id) {
            $builder->where('mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $builder->where('cabangs_id', $cabangs_id);
        }

        if ($order) {
            $builder->where('order', $order);
        }

        $builder = $builder->get()->getResultArray();

        return $builder;
    }
}
