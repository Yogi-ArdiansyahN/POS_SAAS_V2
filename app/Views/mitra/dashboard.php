<?= $this->include('layouts/header'); ?>
<div class="main-content">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('failed')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('failed'); ?>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('errors'); ?>
        </div>
    <?php endif ?>

    <section class="section">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2 shadow">
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Transaksi</h4>
                        </div>
                        <div class="card-body">
                            <?= $total_transaksi ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2 shadow">
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Transaksi</h4>
                        </div>
                        <div class="card-body">
                            <?= number_format($total_nominal) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2 shadow">
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Diskon </h4>
                        </div>
                        <div class="card-body">
                            <?= number_format($diskon) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2 shadow">
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Margin </h4>
                        </div>
                        <div class="card-body">
                            <?= number_format($total_margin) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>Stok Menu</h4>
                        <div class="card-header-action dropdown">
                            <a href="#" data-toggle="dropdown" class="btn btn-danger dropdown-toggle" id="labelCabang">Cabang</a>
                            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <li class="dropdown-title">Pilih Cabang</li>
                                <?php foreach ($cabangs as $data) : ?>
                                    <li>
                                        <a
                                            class="dropdown-item dropdown-item-cabang"
                                            id="pilih-cabang-<?= $data['id'] ?>"
                                            href="javascript:void(0)"
                                            onclick="searchStokMenuByCabang('<?= $data['id'] ?>', '<?= $data['cabang_name'] ?>')">
                                            <?= $data['cabang_name'] ?>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                                <li>
                                    <a
                                        class="dropdown-item dropdown-item-cabang"
                                        id="pilih-cabang-all"
                                        href="javascript:void(0)"
                                        onclick="searchStokMenuByCabang('all', 'All')">
                                        All
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive px-3">
                            <table class="table table-striped" id="datasTableStokMenu">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Menu</th>
                                        <th>Stok Sekarang</th>
                                        <th>Total Stok</th>
                                    </tr>
                                </thead>
                                <tbody id="BodyDatasTableStokMenu">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>Transaksi</h4>
                        <!-- <div class="card-header-action">
                            <a href="#" class="btn btn-danger">View More <i class="fas fa-chevron-right"></i></a>
                        </div> -->
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive px-3" id="wrapperDataTransaksi">
                            <table class="table table-striped" id="datasTableTransaksi">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Order</th>
                                        <th>Diskon</th>
                                        <th>Total</th>
                                        <th>Total Setelah Diskon</th>
                                        <th>Margin</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody id="BodyDatasTableTransaksi">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    let tableStokMenu;
    let tableTransaksi;

    // TableStokMenu
    $(document).ready(function() {
        tableStokMenu = $('#datasTableStokMenu').DataTable({
            lengthMenu: [
                [10, 30, 50, 1],
                [10, 30, 50, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url() ?>' + "mitra/ajax/getDataTableStokMenuDashboard/all",
                type: "get",
                dataType: "json",
                data: function(d) {
                    d.search.value = $('input[type="search"]').val();
                },
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Menggunakan nomor baris + 1
                    }
                },
                {
                    data: "menu_name"
                },
                {
                    data: "current_quantity",
                },
                {
                    data: "quantity",
                }
            ]
        });
    });

    // TableTransaksi
    $(document).ready(function() {
        tableTransaksi = $('#datasTableTransaksi').DataTable({
            lengthMenu: [
                [10, 30, 50, 1],
                [10, 30, 50, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url() ?>' + "mitra/ajax/getDataTableTransaksiDashboard/all",
                type: "get",
                dataType: "json",
                data: function(a) {
                    // a.search.value = $('input[type="search"]').val();
                    a.search.value = $('#wrapperDataTransaksi input').val();
                },
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Menggunakan nomor baris + 1
                    }
                },
                {
                    data: "created_at"
                },
                {
                    data: "order",
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `
                        <span> ${data.nama_diskon == null ? '-' : data.nama_diskon} </span><br>
                        <sup> ${data.kode_diskon == null ? '-' : '(' + data.kode_diskon + ')'} </sup> <br>
                        <sup class="text-danger">- ${data.kode_diskon == null ? '-' :  data.total - data.total_setelah_diskon} </sup>
                        `;
                    }
                },
                {
                    data: "total",
                },
                {
                    data: "total_setelah_diskon",
                    className: "text-center",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: "margin",
                },
                // {
                //     data: null,
                //     className: "text-center",
                //     render: function(data) {
                //         return `
                //         <div class="d-flex justify-content-center">
                //             <div class="mx-1 d-flex align-items-center justify-content-center gap-3 fs-6">
                //                 <button type="button" class=" mx-1 btn btn-icon btn-info shadow-none" data-toggle="modal" data-target="#detail" onclick="detail('${data.order}')">
                //                     <i class="fas fa-eye"></i>
                //                 </button>
                //             </div>
                //         </div>
                //         `;
                //     }
                // },
            ]
        });
    });

    function searchStokMenuByCabang(id, cabangName) {
        const newUrl = '<?= base_url() ?>' + "mitra/ajax/getDataTableStokMenuDashboard/" + id;

        // Hapus semua class active dari semua item cabang
        $('.dropdown-item-cabang').removeClass('active');

        // Tambahkan class active pada cabang yang diklik
        $('#pilih-cabang-' + id).addClass('active');

        // Ubah label pada tombol dropdown
        $('#labelCabang').text("Cabang: " + cabangName);

        // Update DataTables
        tableStokMenu.ajax.url(newUrl).load();

        searchTransaksiByCabang(id);
    }

    function searchTransaksiByCabang(id) {
        const newUrl = '<?= base_url() ?>' + "mitra/ajax/getDataTableTransaksiDashboard/" + id;

        // Update DataTables
        tableTransaksi.ajax.url(newUrl).load();
    }
</script>

<?= $this->include('layouts/footer'); ?>