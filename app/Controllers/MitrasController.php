<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cabangs;
use App\Models\DetailTransaksis;
use App\Models\Mitras;
use App\Models\Stoks;
use App\Models\Transaksis;
use CodeIgniter\HTTP\ResponseInterface;

class MitrasController extends BaseController
{
    protected $transaksis, $detail_transaksis, $cabangs, $mitras, $stoks;

    public function __construct()
    {
        $this->transaksis = new Transaksis();
        $this->detail_transaksis = new DetailTransaksis();
        $this->cabangs = new Cabangs();
        $this->mitras = new Mitras();
        $this->stoks = new Stoks();
    }

    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('id', session()->get('users')['mitras_id'])->first();
        $cabangs = $this->cabangs->getCabangs($mitras['id'], null, null);

        $transaksis = $this->transaksis
            ->select('
                transaksis.order,
                cabangs.name AS cabang_name,
                transaksis.total,
                transaksis.created_at,
                transaksis.margin AS margin,
                diskons.name as nama_diskon,
                diskons.kode as kode_diskon,
                transaksis.total_setelah_diskon
            ')
            ->where('transaksis.mitras_id', $mitras['id'])
            ->where('DATE(transaksis.created_at)', date('Y-m-d'))
            ->join('cabangs', 'cabangs.id = transaksis.cabangs_id')
            ->join('diskons', 'diskons.id = transaksis.diskons_id', 'left')
            ->groupBy('transaksis.id')
            ->orderBy('transaksis.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // total nominal diskon
        $total_diskon = 0;
        // total nominal transaksi
        $total_nominal = 0;
        // total margin
        $total_margin = 0;
        foreach ($transaksis as $data) {
            $total_nominal += $data['total'];
            $total_margin += $data['margin'];
            $total_diskon += ($data['total_setelah_diskon'] != 0 ? $data['total'] : 0) - $data['total_setelah_diskon'];
        }

        // get data stok menu
        $data_stok_menu = $this->stoks->select('
                menus.name AS menu_name,
                SUM(stoks.current_quantity) AS current_quantity,
                SUM(stoks.quantity) AS quantity
            ')
            ->join('menus', 'menus.id = stoks.menus_id')
            ->where('stoks.mitras_id', $mitras['id'])
            ->where('DATE(stoks.created_at)', date('Y-m-d'))
            ->groupBy('menus.name')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Dashboard',
            'total_transaksi' => count($transaksis),
            'total_nominal' => $total_nominal,
            'total_margin' => $total_margin,
            'cabangs' => $cabangs,
            'data_stok_menu' => $data_stok_menu,
            'diskon' => $total_diskon
        ];

        return view('mitra/dashboard', $data);
    }

    public function getDataTableStokMenuDashboard($cabangs_id)
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $draw = $this->request->getVar('draw', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $start = $this->request->getVar('start', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $length = $this->request->getVar('length', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $search = $this->request->getVar('search', FILTER_SANITIZE_FULL_SPECIAL_CHARS)['value'];

        if ($cabangs_id == "all") {
            $data = $this->mitras->getDataTableStokMenuDashboard($start, $length, $search, $mitras['id'], null, date('Y-m-d'));
            $recordsTotal = $this->mitras->countAllgetDataTableStokMenuDashboard($mitras['id'], null, date('Y-m-d'));
            $recordsFiltered = $this->mitras->countFilteredgetDataTableStokMenuDashboard($search, $mitras['id'], null, date('Y-m-d'));
        } else {
            $data = $this->mitras->getDataTableStokMenuDashboard($start, $length, $search, $mitras['id'], $cabangs_id, date('Y-m-d'));
            $recordsTotal = $this->mitras->countAllgetDataTableStokMenuDashboard($mitras['id'], $cabangs_id, date('Y-m-d'));
            $recordsFiltered = $this->mitras->countFilteredgetDataTableStokMenuDashboard($search, $mitras['id'], $cabangs_id, date('Y-m-d'));
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }

    public function getDataTableTransaksiDashboard($cabangs_id)
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $draw = $this->request->getVar('draw', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $start = $this->request->getVar('start', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $length = $this->request->getVar('length', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $search = $this->request->getVar('search', FILTER_SANITIZE_FULL_SPECIAL_CHARS)['value'];

        if ($cabangs_id == "all") {
            $data = $this->mitras->getDataTableTransaksiDashboard($start, $length, $search, $mitras['id'], null, date('Y-m-d'));
            $recordsTotal = $this->mitras->countAllgetDataTableTransaksiDashboard($mitras['id'], null, date('Y-m-d'));
            $recordsFiltered = $this->mitras->countFilteredgetDataTableTransaksiDashboard($search, $mitras['id'], null, date('Y-m-d'));
        } else {
            $data = $this->mitras->getDataTableTransaksiDashboard($start, $length, $search, $mitras['id'], $cabangs_id, date('Y-m-d'));
            $recordsTotal = $this->mitras->countAllgetDataTableTransaksiDashboard($mitras['id'], $cabangs_id, date('Y-m-d'));
            $recordsFiltered = $this->mitras->countFilteredgetDataTableTransaksiDashboard($search, $mitras['id'], $cabangs_id, date('Y-m-d'));
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'search' => $search
        ];

        return $this->response->setJSON($response);
    }
}
