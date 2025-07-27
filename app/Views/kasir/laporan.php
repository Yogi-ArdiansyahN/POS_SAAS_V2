<?= $this->include('kasir/layouts/header') ?>

<!-- Main Content -->
<!-- DataTables + Buttons extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div class="main-content">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('failed')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
            <?= session()->getFlashdata('failed'); ?>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
            <?= session()->getFlashdata('errors'); ?>
        </div>
    <?php endif ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <form action="" method="post">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-12 col-md-2">
                                    <div class="mb-3">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
                                    </div>
                                </div>

                                <div class="col-12 col-md-2">
                                    <div class="mb-3">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="order" class="form-label">Order</label>
                                        <input type="text" class="form-control" name="order" id="order" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button type="submit" id="btn-submit" name="filter" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row <?= $main != null ? '' : 'd-none' ?> mt-3">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <?= $main != null ? $main : '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" tabindex="-1" role="dialog" id="detail" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail <?= $title ?></h5>
                <button type="button" class="close" onclick="close_modal_edit()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body modal-body-detail">

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#datasTable').DataTable({
            lengthMenu: [
                [10, 30, 50, 1],
                [10, 30, 50, "All"]
            ],
            processing: true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    filename: 'Laporan_Transaksi_' + '<?= $date ?>',
                    title: 'Laporan Transaksi ' + '<?= $date ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5], // Kolom yang diekspor
                        format: {
                            body: function(data, row, column, node) {
                                // Format kolom Total dan Margin jadi Rupiah (misalnya kolom 4 dan 5)
                                if (column === 4 || column === 5) {
                                    return data.toString().replace(/[^0-9]/g, '');
                                }
                                return data;
                            }
                        }
                    },
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-danger btn-sm',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    orientation: 'potrait',
                    pageSize: 'A4',
                    filename: 'Laporan_Transaksi_' + '<?= $date ?>',
                    title: 'Laporan Transaksi ' + '<?= $date ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5], // Kolom yang diekspor
                    },
                },
                {
                    extend: 'print',
                    className: 'btn btn-dark btn-sm',
                    text: '<i class="fas fa-print"></i> Print',
                    filename: 'Laporan_Transaksi_' + '<?= $date ?>',
                    title: 'Laporan Transaksi ' + '<?= $date ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5], // Kolom yang diekspor
                    },
                }
            ],
        });
    });

    function formatRupiahExcel(angka) {
        angka = angka.toString().replace(/[^,\d]/g, '');
        let split = angka.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }

    function formatRupiah(angka) {
        angka = angka.toString().replace(/[^,\d]/g, '');
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $(document).ready(function() {
        $('#harga_modal, #harga_jual').on('input', function() {
            let value = $(this).val();
            $(this).val(formatRupiah(value));
        });
    });

    function detail(order) {
        $('.modal-body-detail').html('');

        $.ajax({
            url: '<?= base_url('kasir/detail'); ?>',
            type: 'GET',
            data: {
                order: order
            },
            success: function(response) {
                $('.modal-body-detail').html(response);
                $('#detail').modal('show');
            },
            error: function() {
                console.log('Error');
            }
        });
    }

    function delete_data(id) {
        Swal.fire({
            title: "Hapus Menu?",
            // text: "That thing is still around?",
            icon: "question",
            showDenyButton: true,
            denyButtonText: `Cancel`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('mitra/menu/delete'); ?>',
                    type: 'get',
                    data: {
                        id: id,
                    },
                    success: function(response) {
                        Swal.fire("Menu Terhapus!", "", "success");
                        $('#datasTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        console.log('Error');
                    }
                });
            }
        });
    }
</script>

<?= $this->include('layouts/footer'); ?>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<!-- Export file support -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<?= $this->include('kasir/layouts/footer') ?>