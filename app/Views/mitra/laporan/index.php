<?= $this->include('layouts/header'); ?>

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
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="m-0"><?= $title ?></h3>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Ringkasan -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Transaksi</small>
                                    <h5 class="fw-bold mb-0"> <?= $total_transaksi ?> </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Pendapatan</small>
                                    <h5 class="fw-bold mb-0">Rp <?= number_format($total_nominal) ?> </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Diskon</small>
                                    <h5 class="fw-bold mb-0">Rp <?= number_format($total_diskon) ?> </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <small style="font-weight: bold;">Total Margin</small>
                                    <h5 class="fw-bold mb-0">Rp <?= number_format($total_margin) ?> </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter -->
                    <a class="btn btn-primary mb-3" href="<?= base_url() ?>mitra/laporan/filter">Filter Laporan</a>

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="datasTable">
                            <thead class="">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Order ID</th>
                                    <th>Cabang</th>
                                    <th>
                                        Diskon
                                    </th>
                                    <th>Total</th>
                                    <th>Total Setelah Diskon</th>
                                    <th>Margin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
            serverSide: true,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    filename: 'Laporan_Transaksi_' + '<?= $date ?>',
                    title: 'Laporan Transaksi ' + '<?= $date ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7], // Kolom yang diekspor
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
                // {
                //     extend: 'pdfHtml5',
                //     className: 'btn btn-danger btn-sm',
                //     text: '<i class="fas fa-file-pdf"></i> PDF',
                //     orientation: 'portrait',
                //     pageSize: 'A4',
                //     filename: 'Laporan_Transaksi_' + '<?= $date ?>',
                //     title: 'Laporan Transaksi ' + '<?= $date ?>',
                //     exportOptions: {
                //         columns: [0, 1, 2, 3, 4, 5, 6, 7],
                //     },
                //     customize: function(doc) {
                //         // Simpan tabel
                //         const table = doc.content[1];

                //         // Bungkus tabel ke dalam kolom tengah agar benar-benar center
                //         doc.content[1] = {
                //             columns: [{
                //                     width: '*',
                //                     text: ''
                //                 },
                //                 {
                //                     width: 'auto',
                //                     text: '',
                //                     alignment: 'center',
                //                     valign: 'middle',
                //                     margin: [0, 0, 0, 10],
                //                     stack: [table]
                //                 },
                //                 {
                //                     width: '*',
                //                     text: ''
                //                 }
                //             ]
                //         };

                //         // Lebar kolom jadi 'auto' biar tidak meluber
                //         table.table.widths = Array(table.table.body[0].length).fill('auto');

                //         // Hitung total dari kolom 5, 6, dan 7 (index 5, 6, 7)
                //         let total5 = 0;
                //         let total6 = 0;
                //         let total7 = 0;

                //         // Set alignment ke kanan untuk kolom Total, Total Setelah Diskon, dan Margin
                //         table.table.body.forEach(function(row, rowIndex) {
                //             // Lewati header (biasanya baris pertama)
                //             if (rowIndex === 0) return;

                //             [5, 6, 7].forEach(function(colIndex) {
                //                 if (row[colIndex]) {
                //                     // Set alignment ke kanan
                //                     row[colIndex].alignment = 'right';

                //                     // Ambil nilai (object .text atau langsung)
                //                     let value = row[colIndex].text || row[colIndex];

                //                     // Hapus karakter non angka dan titik
                //                     value = String(value).replace(/[^0-9]/g, '');

                //                     // Ubah ke number
                //                     let number = parseInt(value);
                //                     if (!isNaN(number)) {
                //                         if (colIndex === 5) total5 += number;
                //                         if (colIndex === 6) total6 += number;
                //                         if (colIndex === 7) total7 += number;
                //                     }
                //                 }
                //             });
                //         });

                //         // Format Rupiah (tanpa Rp)
                //         const formatRupiah = val => {
                //             return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                //         };

                //         // Tambahkan baris total ke body tabel
                //         table.table.body.push([{
                //                 text: 'Total',
                //                 colSpan: 5,
                //                 alignment: 'center',
                //                 fillColor: 'black',
                //                 color: 'white',
                //                 bold: true
                //             }, {}, {}, {}, {},
                //             {
                //                 text: formatRupiah(total5),
                //                 alignment: 'right',
                //                 fillColor: 'black',
                //                 color: 'white',
                //                 bold: true
                //             },
                //             {
                //                 text: formatRupiah(total6),
                //                 alignment: 'right',
                //                 fillColor: 'black',
                //                 color: 'white',
                //                 bold: true
                //             },
                //             {
                //                 text: formatRupiah(total7),
                //                 alignment: 'right',
                //                 fillColor: 'black',
                //                 color: 'white',
                //                 bold: true
                //             }
                //         ]);

                //         doc.content.push({
                //             absolutePosition: {
                //                 x: 300,
                //                 y: 700
                //             }, // x (kanan), y (bawah) → sesuaikan bila perlu
                //             fontSize: 10,
                //             alignment: 'right',
                //             text: [{
                //                     text: '................, <?= $date ?>\n\n',
                //                     bold: true
                //                 },
                //                 {
                //                     text: 'Mengetahui,\n\n\n\n',
                //                     bold: true
                //                 },
                //                 {
                //                     text: '_______________________\n',
                //                     bold: true
                //                 },
                //                 {
                //                     text: 'Owner/Manager',
                //                     italics: true
                //                 }
                //             ]
                //         });
                //     }
                // }
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-danger btn-sm',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    filename: 'Laporan_Penjualan_harian_' + '<?= $date ?>',
                    title: 'Laporan Penjualan Harian ' + '<?= $mitras ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    },
                    customize: function(doc) {
                        // Tambahkan info "Cabang" dan "Tanggal" di bawah title
                        doc.content.splice(1, 0, {
                            margin: [0, 0, 0, 10],
                            text: [{
                                    text: 'Cabang : All\n',
                                    bold: false
                                },
                                {
                                    text: 'Tanggal : <?= $date ?>\n',
                                    bold: false
                                }
                            ],
                            alignment: 'left',
                            fontSize: 10
                        });

                        // Simpan tabel (sekarang pindah ke index 2)
                        const table = doc.content[2];

                        // Bungkus tabel ke tengah
                        doc.content[2] = {
                            columns: [{
                                    width: '*',
                                    text: ''
                                },
                                {
                                    width: 'auto',
                                    text: '',
                                    alignment: 'center',
                                    valign: 'middle',
                                    margin: [0, 0, 0, 10],
                                    stack: [table]
                                },
                                {
                                    width: '*',
                                    text: ''
                                }
                            ]
                        };

                        // Lebar kolom auto agar rapi
                        table.table.widths = Array(table.table.body[0].length).fill('auto');

                        // Hitung total kolom 5, 6, dan 7
                        let total5 = 0;
                        let total6 = 0;
                        let total7 = 0;

                        table.table.body.forEach(function(row, rowIndex) {
                            if (rowIndex === 0) return; // skip header

                            [5, 6, 7].forEach(function(colIndex) {
                                if (row[colIndex]) {
                                    row[colIndex].alignment = 'right';

                                    let value = row[colIndex].text || row[colIndex];
                                    value = String(value).replace(/[^0-9]/g, '');

                                    let number = parseInt(value);
                                    if (!isNaN(number)) {
                                        if (colIndex === 5) total5 += number;
                                        if (colIndex === 6) total6 += number;
                                        if (colIndex === 7) total7 += number;
                                    }
                                }
                            });
                        });

                        // Format angka ke rupiah
                        const formatRupiah = val => {
                            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        };

                        // Tambahkan baris total ke bawah tabel
                        table.table.body.push([{
                                text: 'Total',
                                colSpan: 5,
                                alignment: 'center',
                                fillColor: 'black',
                                color: 'white',
                                bold: true
                            }, {}, {}, {}, {},
                            {
                                text: formatRupiah(total5),
                                alignment: 'right',
                                fillColor: 'black',
                                color: 'white',
                                bold: true
                            },
                            {
                                text: formatRupiah(total6),
                                alignment: 'right',
                                fillColor: 'black',
                                color: 'white',
                                bold: true
                            },
                            {
                                text: formatRupiah(total7),
                                alignment: 'right',
                                fillColor: 'black',
                                color: 'white',
                                bold: true
                            }
                        ]);

                        // Tambahkan tanda tangan di bawah
                        doc.content.push({
                            absolutePosition: {
                                x: 300,
                                y: 700
                            },
                            fontSize: 10,
                            alignment: 'right',
                            text: [{
                                    text: '................, <?= $date ?>\n\n',
                                    bold: true
                                },
                                {
                                    text: 'Mengetahui,\n\n\n\n',
                                    bold: true
                                },
                                {
                                    text: '_______________________\n',
                                    bold: true
                                },
                                {
                                    text: 'Owner/Manager',
                                    italics: true
                                }
                            ]
                        });
                    }
                }
            ],
            ajax: {
                url: '<?= base_url() ?>' + "mitra/ajax/getDataTableLaporans",
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
                    data: "created_at",
                },
                {
                    data: "order",
                },
                {
                    data: "cabang_name",
                    className: "text-center",
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        if (!data.nama_diskon) return "-";

                        const diskonNama = data.nama_diskon;
                        const diskonKode = data.kode_diskon ? `(${data.kode_diskon})` : "-";
                        const nilaiDiskon = data.total && data.total_setelah_diskon ?
                            formatRupiah(data.total - data.total_setelah_diskon) :
                            "-";

                        return `
                            <div style="font-size: 13px;">
                                ${diskonNama} <span class="text-muted">${diskonKode}</span>  <span class="text-danger">-${nilaiDiskon}</span>
                            </div>
                        `;
                    }
                },
                {
                    data: "total",
                    className: "text-center",
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: "total_setelah_diskon",
                    className: "text-center",
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: "margin",
                    className: "text-center",
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `
                        <div class="d-flex justify-content-center">
                            <div class="mx-1 d-flex align-items-center justify-content-center gap-3 fs-6">
                                <button type="button" class=" mx-1 btn btn-icon btn-info shadow-none" data-toggle="modal" data-target="#detail" onclick="detail('${data.order}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                },
            ]
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
            url: '<?= base_url('mitra/laporan/detail'); ?>',
            type: 'GET',
            data: {
                order: order
            },
            success: function(response) {
                $('.modal-body-detail').html(response);
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

<script src="<?= base_url() ?>assets/modules/select2/dist/js/select2.full.min.js"></script>