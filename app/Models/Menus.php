<?php

namespace App\Models;

use CodeIgniter\Model;

class Menus extends Model
{
    protected $table            = 'menus';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'mitras_id', 'harga_modal', 'harga_jual', 'kategori', 'foto', 'is_active'];

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
        if ($this->db->table('menus')->insert($data)) {
            $response = [
                'status' => 'success',
                'message' => 'Menu berhasil ditambahkan.',
                'menus_id' => $this->db->insertID()
            ];
        } else {
            $response = [
                'status' => 'errors',
                'message' => 'Gagal menambahkan menu.'
            ];
        }

        return $response;
    }

    public function getMenus($mitras_id = null, $is_active = null)
    {
        $builder = $this->db->table('menus')
            ->select('id, name, harga_modal, harga_jual, kategori, foto, is_active')
            ->where('is_active', $is_active);

        if ($mitras_id) {
            $builder->where('mitras_id', $mitras_id);
        }

        return $builder->get()->getResultArray();
    }

    public function getMenuStoks($mitras_id = null, $cabangs_id = null, $date = null)
    {
        $builder = $this->db->table('stoks')
            ->select('
                menus.id,
                menus.name,
                menus.harga_modal,
                menus.harga_jual,
                menus.kategori,
                menus.foto,
                stoks.quantity,
                stoks.current_quantity')
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

        $builder->orderBy('stoks.current_quantity', 'DESC');

        $menus = $builder->get()->getResultArray();

        if ($menus != null) {
            $result = $menus;
        } else {
            $result = null;
        }

        return $result;
    }

    public function dataTableMenu($start, $length, $search, $mitras_id = null)
    {
        $builder = $this->db->table('menus')
            ->select('
            id,
            menus.name as name_menus,
            menus.foto,
            menus.harga_modal,
            menus.harga_jual,
            menus.kategori,
            menus.is_active,
        ')
            ->where('menus.is_active', 1);

        if ($mitras_id) {
            $builder->where('menus.mitras_id', $mitras_id);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('menus.name', $search)
                ->orLike('menus.harga_modal', $search)
                ->orLike('menus.harga_jual', $search)
                ->orLike('menus.kategori', $search)
                ->orLike('mitras_id', $search)
                ->groupEnd();
        }

        if ($length != 1) {
            $builder->limit($length, $start);
        }

        $builder->orderBy('menus.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function countAllMenu($mitras_id = null)
    {
        if ($mitras_id) {
            $builder = $this->db->table('menus')
                ->where('mitras_id', $mitras_id)
                ->where('is_active', 1);
            $builder = $builder->countAllResults();

            return $builder;
        }

        return $this->countAll();
    }

    public function countFilteredMenu($search, $mitras_id = null)
    {
        $builder = $this->db->table('menus')
            ->select('
            id,
            menus.name as name_menus,
            menus.foto,
            menus.harga_modal,
            menus.harga_jual,
            menus.kategori,
            menus.is_active,
        ')
            ->where('menus.is_active', 1);

        if ($mitras_id) {
            $builder->where('menus.mitras_id', $mitras_id);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('menus.name', $search)
                ->orLike('menus.harga_modal', $search)
                ->orLike('menus.harga_jual', $search)
                ->orLike('menus.kategori', $search)
                ->orLike('mitras_id', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
