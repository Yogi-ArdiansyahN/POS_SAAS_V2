<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Cabangs;
use App\Models\DetailTransaksis;
use App\Models\Mitras;
use App\Models\Stoks;
use App\Models\Transaksis;
use CodeIgniter\HTTP\ResponseInterface;

class LaporansController extends BaseController
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

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

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

        // dd($transaksis, $detail_transaksis);
        $data = [
            'title' => 'Laporan',
            'total_transaksi' => count($transaksis),
            'total_nominal' => $total_nominal,
            'total_margin' => $total_margin,
            'total_diskon' => $total_diskon,
            'date' => date('Y-m-d'),
            'mitras' => $mitras['name'],
        ];

        return view('mitra/laporan/index', $data);
    }

    public function detail()
    {
        $order = $this->request->getVar('order', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $detail_transaksis = $this->detail_transaksis
            ->select('
                menus.name,
                detail_transaksis.harga_modal,
                detail_transaksis.harga_jual,
                detail_transaksis.quantity,
                detail_transaksis.total,
            ')
            ->where('order', $order)
            ->join('menus', 'menus.id = detail_transaksis.menus_id')
            ->get()->getResultArray();

        // dd($detail_transaksis, $order);  

        $data = [
            'menus' => $detail_transaksis
        ];

        return view('mitra/laporan/detail', $data);
    }

    public function filter()
    {
        date_default_timezone_set('Asia/Jakarta');

        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $cabangs = $this->cabangs->getCabangs($mitras['id'], null, null);

        // get cabang
        $cabangss = [];
        foreach ($cabangs as $data) {
            $cabangss[] = [
                'cabang_name' => $data['cabang_name'],
                'id' => $data['id']
            ];
        }

        $tanggal_transaksis = $this->transaksis
            ->select('
                DATE(transaksis.created_at) as tanggal,
            ')
            ->where('transaksis.mitras_id', session()->get('users')['mitras_id'])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();

        if (isset($_POST['filter'])) {
            $cabang = $this->request->getPost('cabang', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $tanggal_mulai = $this->request->getPost('tanggal_mulai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $tanggal_selesai = $this->request->getPost('tanggal_selesai', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $order = $this->request->getPost('order', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $tipe = $this->request->getPost('tipe_laporan', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

            // tanggal untuk penamaan export
            $tanggals = null;

            $transaksis = $this->transaksis
                ->select('
                    transaksis.order,
                    cabangs.name AS cabang_name,
                    transaksis.total,
                    transaksis.created_at,
                    transaksis.margin AS margin,
                    transaksis.total_setelah_diskon
                ')
                ->where('transaksis.mitras_id', $mitras['id'])
                ->join('cabangs', 'cabangs.id = transaksis.cabangs_id')
                ->join('detail_transaksis', 'detail_transaksis.transaksis_id = transaksis.id');

            // Filter berdasarkan cabang
            if (!empty($cabang)) {
                $transaksis->where('transaksis.cabangs_id', $cabang);
            }

            // Filter berdasarkan order
            if (!empty($order)) {
                $transaksis->where('transaksis.order', $order);
            }

            // Filter berdasarkan tanggal mulai & selesai
            if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
                $transaksis->where('DATE(transaksis.created_at) >=', $tanggal_mulai);
                $transaksis->where('DATE(transaksis.created_at) <=', $tanggal_selesai);
                $tanggals = $tanggal_mulai . ' - ' . $tanggal_selesai;
            }

            // Jika hanya tanggal_mulai yang diisi
            if (!empty($tanggal_mulai) && empty($tanggal_selesai)) {
                $transaksis->where('DATE(transaksis.created_at)', $tanggal_mulai);
                $tanggals = $tanggal_mulai;
            }

            // Filter berdasarkan tipe (harian, mingguan, bulanan, tahunan)
            if (!empty($tipe)) {
                switch ($tipe) {
                    case 'harian':
                        $transaksis->groupBy('DATE(transaksis.created_at)');
                        break;
                    case 'mingguan':
                        $transaksis->groupBy('YEARWEEK(transaksis.created_at, 1)');
                        break;
                    case 'bulanan':
                        $transaksis->groupBy('YEAR(transaksis.created_at), MONTH(transaksis.created_at)');
                        break;
                    case 'tahunan':
                        $transaksis->groupBy('YEAR(transaksis.created_at)');
                        break;
                }
            }

            // Group by transaksi ID dan urutkan
            $transaksis = $transaksis->groupBy('transaksis.id')
                ->orderBy('transaksis.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // total diskon
            $total_diskon = 0;

            // total transaksi 
            $total_transaksi = count($transaksis);
            // total nominal transaksi
            $total_nominal = 0;
            // total margin
            $total_margin = 0;
            foreach ($transaksis as $data) {
                $total_nominal += $data['total'];
                $total_margin += $data['margin'];
                $total_diskon += ($data['total_setelah_diskon'] != 0 ? $data['total'] : 0) - $data['total_setelah_diskon'];
            }

            $total_nominal = number_format($total_nominal);
            $total_margin = number_format($total_margin);
            $total_diskon = number_format($total_diskon);

            $tbody = '';
            $no = 1;
            foreach ($transaksis as $data) {
                $tanggal = date('d-m-Y', strtotime($data['created_at']));
                $total = number_format($data['total']);
                $margin = number_format($data['margin']);
                $diskon = number_format(($data['total_setelah_diskon'] != 0 ? $data['total'] : 0) - $data['total_setelah_diskon']);

                $tbody .= "
                            <tr>
                                <td>{$no}</td>
                                <td>{$tanggal}</td>
                                <td>{$data['order']}</td>
                                <td>{$data['cabang_name']}</td>
                                <td>{$diskon}</td>
                                <td>Rp {$total}</td>
                                <td>Rp {$margin}</td>
                                <td>
                                    <button class='btn btn-sm btn-info' onclick=\"detail('{$data['order']}')\">Detail</button>
                                </td>
                            </tr>
                        ";
                $no++;
            }

            $main = <<<HTML
                <div class="card-body">
                    <!-- Ringkasan -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Transaksi</small>
                                    <h5 class="fw-bold mb-0"> $total_transaksi </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Pendapatan</small>
                                    <h5 class="fw-bold mb-0">Rp $total_nominal </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Diskon</small>
                                    <h5 class="fw-bold mb-0">Rp $total_diskon </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Margin</small>
                                    <h5 class="fw-bold mb-0">Rp $total_margin </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="datasTable">
                            <thead class="">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Order ID</th>
                                    <th>Cabang</th>
                                    <th>Diskon</th>
                                    <th>Total</th>
                                    <th>Margin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               $tbody
                            </tbody>
                        </table>
                    </div>
                </div>
            HTML;

            $data = [
                'title' => 'Laporan',
                'main' => $main,
                'date' => $tanggals,
                'tanggal' => $tanggal_transaksis,
                'cabangs' => $cabangss
            ];

            return view('mitra/laporan/filter', $data);
        }

        $data = [
            'title' => 'Laporan',
            'main' => null,
            'date' => date('Y-m-d'),
            'tanggal' => $tanggal_transaksis,
            'cabangs' => $cabangss
        ];

        return view('mitra/laporan/filter', $data);
    }

    public function getDataTable()
    {
        $mitras = $this->mitras->where('users_id', session()->get('users')['id'])->first();

        $draw = $this->request->getVar('draw', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $start = $this->request->getVar('start', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $length = $this->request->getVar('length', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $search = $this->request->getVar('search', FILTER_SANITIZE_FULL_SPECIAL_CHARS)['value'];

        $data = $this->dataTableLaporans($start, $length, $search, $mitras['id']);
        $recordsTotal = $this->countAllLaporans($mitras['id'], 'kasir');
        $recordsFiltered = $this->countFilteredLaporans($search, $mitras['id']);

        $response = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }

    public function dataTableLaporans($start, $length, $search, $mitras_id = null)
    {
        date_default_timezone_set('Asia/Jakarta');

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
            ->where('DATE(transaksis.created_at)', date('Y-m-d'))
            ->join('cabangs', 'cabangs.id = transaksis.cabangs_id')
            ->join('detail_transaksis', 'detail_transaksis.transaksis_id = transaksis.id')
            ->join('diskons', 'diskons.id = transaksis.diskons_id', 'left');

        if ($mitras_id) {
            $transaksis->where('transaksis.mitras_id', $mitras_id);
        }

        if (!empty($search)) {
            $transaksis->groupStart()
                ->like('transaksis.order', $search)
                ->orLike('cabangs.name', $search)
                ->orLike('diskons.name', $search)
                ->orLike('diskons.kode', $search)
                ->orLike('transaksis.total', $search)
                ->orLike('transaksis.created_at', $search)
                ->groupEnd();
        }

        if ($length != 1) {
            $transaksis->limit($length, $start);
        }

        $transaksis->groupBy('transaksis.id')->orderBy('transaksis.created_at', 'DESC');

        return $transaksis->get()->getResultArray();
    }

    public function countAllLaporans($mitras_id = null)
    {
        date_default_timezone_set('Asia/Jakarta');

        $transaksis = $this->transaksis
            ->select('
                transaksis.order,
                transaksis.total,
                transaksis.created_at,
            ')
            ->where('DATE(transaksis.created_at)', date('Y-m-d'))
            ->where('transaksis.mitras_id', $mitras_id)
            ->get()
            ->getResultArray();

        return count($transaksis);
    }

    public function countFilteredLaporans($search, $mitras_id = null)
    {
        date_default_timezone_set('Asia/Jakarta');

        $transaksis = $this->transaksis
            ->where('DATE(transaksis.created_at)', date('Y-m-d'));

        if ($mitras_id) {
            $transaksis->where('transaksis.mitras_id', $mitras_id);
        }

        if (!empty($search)) {
            $transaksis->groupStart()
                ->like('transaksis.order', $search)
                ->orLike('cabangs.name', $search)
                ->orLike('diskons.name', $search)
                ->orLike('diskons.kode', $search)
                ->orLike('transaksis.total', $search)
                ->orLike('transaksis.created_at', $search)
                ->groupEnd();
        }

        return $transaksis->countAllResults();
    }
}
