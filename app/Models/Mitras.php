<?php

namespace App\Models;

use CodeIgniter\Model;

class Mitras extends Model
{
    protected $table            = 'mitras';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'users_id',
        'alamat',
        'logo'
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


    public function getDataTableStokMenuDashboard($start, $length, $search, $mitras_id = null, $cabangs_id = null, $date = null)
    {
        $builder = $this->db->table('stoks')
            ->select('
                menus.name AS menu_name,
                SUM(stoks.current_quantity) AS current_quantity,
                SUM(stoks.quantity) AS quantity')
            ->join('menus', 'menus.id = stoks.menus_id');

        if ($cabangs_id) {
            $builder->where('cabangs_id', $cabangs_id);
        }

        if ($date) {
            $builder->where('DATE(stoks.created_at)', $date);
        }

        if ($mitras_id) {
            $builder->where('stoks.mitras_id', $mitras_id);
        }

        $builder->groupBy('menus.name');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('menus.name', $search)
                ->groupEnd();
        }

        if ($length != 1) {
            $builder->limit($length, $start);
        }

        $menus = $builder->get()->getResultArray();

        return $menus;
    }

    public function countAllgetDataTableStokMenuDashboard($mitras_id = null, $cabangs_id = null, $date = null)
    {
        $builder = $this->db->table('stoks')
            ->select('
                menus.name AS menu_name,
                SUM(stoks.current_quantity) AS current_quantity,
                SUM(stoks.quantity) AS quantity')
            ->join('menus', 'menus.id = stoks.menus_id');

        if ($cabangs_id) {
            $builder->where('cabangs_id', $cabangs_id);
        }

        if ($date) {
            $builder->where('DATE(stoks.created_at)', $date);
        }

        if ($mitras_id) {
            $builder->where('stoks.mitras_id', $mitras_id);
        }

        $builder->groupBy('menus.name');

        $menus = $builder->countAllResults();

        return $menus;
    }

    public function countFilteredgetDataTableStokMenuDashboard($search, $mitras_id = null, $cabangs_id = null, $date = null)
    {
        $builder = $this->db->table('stoks')
            ->select('
                menus.name AS menu_name,
                SUM(stoks.current_quantity) AS current_quantity,
                SUM(stoks.quantity) AS quantity')
            ->join('menus', 'menus.id = stoks.menus_id');

        if ($cabangs_id) {
            $builder->where('cabangs_id', $cabangs_id);
        }

        if ($date) {
            $builder->where('DATE(stoks.created_at)', $date);
        }

        if ($mitras_id) {
            $builder->where('stoks.mitras_id', $mitras_id);
        }

        $builder->groupBy('menus.name');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('menus.name', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function getDataTableTransaksiDashboard($start, $length, $search, $mitras_id = null, $cabangs_id = null, $date = null)
    {
        $transaksis = $this->db->table('transaksis')
            ->select('
                transaksis.order,
                transaksis.total,
                transaksis.created_at,
                transaksis.margin AS margin,
                diskons.name as nama_diskon,
                diskons.kode as kode_diskon,
                transaksis.total_setelah_diskon
            ')
            ->join('diskons', 'diskons.id = transaksis.diskons_id', 'left')
            ->groupBy('transaksis.id');


        if ($mitras_id) {
            $transaksis->where('transaksis.mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $transaksis->where('transaksis.cabangs_id', $cabangs_id);
        }

        if ($date) {
            $transaksis->where('DATE(transaksis.created_at)', $date);
        }

        if (!empty($search)) {
            $transaksis->groupStart()
                ->like('transaksis.order', $search)
                ->orLike('transaksis.total', $search)
                ->orLike('diskons.name', $search)
                ->orLike('diskons.kode', $search)
                ->groupEnd();
        }

        if ($length != 1) {
            $transaksis->limit($length, $start);
        }

        return $transaksis->get()->getResultArray();
    }

    public function countAllgetDataTableTransaksiDashboard($mitras_id = null, $cabangs_id = null, $date = null)
    {
        $transaksis = $this->db->table('transaksis')
            ->select('
                transaksis.order,
                transaksis.total,
                transaksis.created_at,
            ')
            ->where('DATE(transaksis.created_at)', date('Y-m-d'))
            ->where('transaksis.mitras_id', $mitras_id);

        if ($cabangs_id) {
            $transaksis->where('transaksis.cabangs_id', $cabangs_id);
        }

        if ($date) {
            $transaksis->where('DATE(transaksis.created_at)', $date);
        }

        $transaksis = $transaksis->countAllResults();

        return $transaksis;
    }

    public function countFilteredgetDataTableTransaksiDashboard($search, $mitras_id = null, $cabangs_id = null, $date = null)
    {
        $transaksis = $this->db->table('transaksis')
            ->select('
                transaksis.order,
                transaksis.total,
                transaksis.created_at,
                transaksis.margin AS margin,
                diskons.name as nama_diskon,
                diskons.kode as kode_diskon,
                transaksis.total_setelah_diskon
            ')
            ->join('diskons', 'diskons.id = transaksis.diskons_id', 'left')
            ->groupBy('transaksis.id');

        if ($mitras_id) {
            $transaksis->where('transaksis.mitras_id', $mitras_id);
        }

        if ($cabangs_id) {
            $transaksis->where('transaksis.cabangs_id', $cabangs_id);
        }

        if ($date) {
            $transaksis->where('DATE(transaksis.created_at)', $date);
        }

        if (!empty($search)) {
            $transaksis->groupStart()
                ->like('transaksis.order', $search)
                ->orLike('transaksis.total', $search)
                ->orLike('diskons.name', $search)
                ->orLike('diskons.kode', $search)
                ->groupEnd();
        }

        return $transaksis->countAllResults();
    }
}
