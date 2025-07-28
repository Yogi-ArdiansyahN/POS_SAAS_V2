<?= $this->include('layouts/header'); ?>
<div class="main-content">

    <div class="row bg-white shadow rounded p-3 d-flex mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="m-0"><?= $title ?></h3>
        </div>
    </div>

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
    <div class="row">
        <?php foreach ($cabangs as $data) : ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2 shadow-lg">
                    <div class="card-stats">
                        <div class="card-stats-title">
                            <!-- <div class="d-flex justify-content-between align-items-top"> -->
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <span style="font-weight: bold; color: black;"><?= $data['cabang_name'] ?> </span> <br>
                                    <span>(<?= $data['kasir_name'] ?>)</span> <br>
                                </div>
                                <div class="">
                                    <span class="mb-3 badge badge-<?= $data['status'] == 'buka' ? 'success' : (($data['status'] == 'tutup') ? 'danger' : 'primary') ?>"> <?= str_replace('_', ' ', ucfirst($data['status'])) ?> </span> <br>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start">
                                <span><?= $data['alamat'] ?></span>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex align-items-center justify-content-center gap-3 fs-6">
                                    <?php if ($data['has_stoks'] == true) : ?>
                                        <button type="button" class="btn btn-info shadow-none mx-1" data-toggle="modal" data-target="#detail" onclick="detail('<?= $data['id'] ?>')">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        <button type="button" class="btn btn-warning shadow-none mx-1" data-toggle="modal" data-target="#mutasi" onclick="mutasi('<?= $data['id'] ?>')">
                                            <i class="fas fa-pen"></i> Mutasi
                                        </button>
                                    <?php else : ?>
                                        <button type="button" class="btn btn-<?= $data['status'] == 'tidak_aktif' ? 'secondary' : 'info' ?> shadow-none" data-toggle="modal" data-target="#add" onclick="add('<?= $data['id'] ?>')" <?= $data['status'] == 'tidak_aktif' ? 'disabled' : '' ?>>
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header">
                    <h4>Riwayat Mutasi</h4>
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
                        <table class="table table-striped" id="datasRiwayatMutasi">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">No</th>
                                    <th class="text-center align-middle">Tipe Mutasi</th>
                                    <th class="text-center align-middle">Menu</th>
                                    <th class="text-center align-middle">Cabang</th>
                                    <th class="text-center align-middle">Quantity <br> <sup> Mutasi </sup> </th>
                                    <th class="text-center align-middle">Stok Awal <br> <sup> (Sebelum) </sup> </th>
                                    <th class="text-center align-middle">Stok Saat Ini <br> <sup> (Sebelum) </sup> </th>
                                    <th class="text-center align-middle">Stok Awal <br> <sup> (Sesudah) </sup> </th>
                                    <th class="text-center align-middle">Stok Saat Ini <br> <sup> (Sesudah)</sup> </th>
                                    <th class="text-center align-middle">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="BodyDatasTableStokMenu">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit <?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body modal-body-edit">

            </div>
        </div>
    </div>
</div>

<!-- Modal add -->
<div class="modal fade" tabindex="-1" role="dialog" id="add" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body modal-body-add">

            </div>
        </div>
    </div>
</div>

<!-- Modal detail -->
<div class="modal fade" tabindex="-1" role="dialog" id="detail" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body modal-body-detail">

            </div>
        </div>
    </div>
</div>

<!-- Modal mutasi -->
<div class="modal fade" tabindex="-1" role="dialog" id="mutasi" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mutasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body modal-body-mutasi">

            </div>
        </div>
    </div>
</div>

<script>
    function edit(id) {
        $.ajax({
            url: '<?= base_url('mitra/stok/edit'); ?>',
            type: 'POST',
            data: {
                id: id,
            },
            success: function(response) {
                $('.modal-body-edit').html(response);
            },
            error: function() {
                console.log('Error');
            }
        });
    }

    function detail(id) {
        $.ajax({
            url: '<?= base_url('mitra/stok/detail'); ?>',
            type: 'GET',
            data: {
                id: id,
            },
            success: function(response) {
                $('.modal-body-detail').html(response);
            },
            error: function() {
                console.log('Error');
            }
        });
    }

    function mutasi(id) {
        $.ajax({
            url: '<?= base_url('mitra/stok/mutasi'); ?>',
            type: 'GET',
            data: {
                id: id,
            },
            success: function(response) {
                $('.modal-body-mutasi').html(response);
            },
            error: function() {
                console.log('Error');
            }
        });
    }

    function add(id) {
        $.ajax({
            url: '<?= base_url('mitra/stok/tambah'); ?>',
            type: 'GET',
            data: {
                id: id,
            },
            success: function(response) {
                $('.modal-body-add').html(response);
            },
            error: function() {
                console.log('Error');
            }
        });
    }

    // Table Riwayat Mutasi
    let tableRiwayatMutasi;
    $(document).ready(function() {
        tableRiwayatMutasi = $('#datasRiwayatMutasi').DataTable({
            lengthMenu: [
                [10, 30, 50, 1],
                [10, 30, 50, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url() ?>' + "mitra/ajax/getDataRiwayatMutasi/all",
                type: "get",
                dataType: "json",
                data: function(d) {
                    d.search.value = $('input[type="search"]').val();
                },
                dataSrc: 'data'
            },
            columnDefs: [{
                targets: '_all', // semua kolom
                className: 'text-center align-middle'
            }],
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Menggunakan nomor baris + 1
                    }
                },
                {
                    data: "tipe_mutasi"
                },
                {
                    data: "menu_name"
                },
                {
                    data: "cabang_name"
                },
                {
                    data: "quantity"
                },
                {
                    data: "quantity_sebelum",
                },
                {
                    data: "current_quantity_sebelum",
                },
                {
                    data: "quantity_sesudah",
                },
                {
                    data: "current_quantity_sesudah",
                },
                {
                    data: "notes",
                },
            ]
        });
    });

    function searchStokMenuByCabang(id, cabangName) {
        const newUrl = '<?= base_url() ?>' + "mitra/ajax/getDataRiwayatMutasi/" + id;

        // Hapus semua class active dari semua item cabang
        $('.dropdown-item-cabang').removeClass('active');

        // Tambahkan class active pada cabang yang diklik
        $('#pilih-cabang-' + id).addClass('active');

        // Ubah label pada tombol dropdown
        $('#labelCabang').text("Cabang: " + cabangName);

        // Update DataTables
        tableRiwayatMutasi.ajax.url(newUrl).load();
    }
</script>

<?= $this->include('layouts/footer'); ?>