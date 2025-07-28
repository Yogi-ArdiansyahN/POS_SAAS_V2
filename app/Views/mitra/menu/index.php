<?= $this->include('layouts/header'); ?>

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
                        <div class="ml-3">
                            <a href="#" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#tambah">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datasTable">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        #
                                    </th>
                                    <th>Foto</th>
                                    <th>Name Menu</th>
                                    <th>Harga Modal</th>
                                    <th>Harga Jual</th>
                                    <th>Kategori</th>
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

<!-- Modal Tambah -->
<div class="modal fade" tabindex="-1" role="dialog" id="tambah" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah <?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url() ?>mitra/menu/create" id="content-form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= old('name') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="">Harga Modal</label>
                        <input type="number" class="form-control" name="harga_modal" id="harga_modal" value="<?= old('harga_modal') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" step="0.5" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="">Harga Jual</label>
                        <input type="number" class="form-control" name="harga_jual" id="harga_jual" value="<?= old('harga_jual') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" step="0.5" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="">Kategori</label>
                        <select class="form-control" name="kategori" style="width: 100% !important;">
                            <option disabled selected> Pilih salah satu </option>
                            <?php foreach ($kategori as $data) : ?>
                                <option value="<?= $data ?>"> <?= str_replace('_', ' ', ucfirst($data)) ?> </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Foto Produk</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                        <span>Ukuran rekomendasi : 100x100 pixel. Biarkan kosong untuk menggunakan gambar default.</span>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
                <button type="button" class="close" onclick="close_modal_edit()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body modal-body-edit">

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
            ajax: {
                url: '<?= base_url() ?>' + "mitra/ajax/getDataTableMenus",
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
                    data: "foto",
                    render: function(data, type, row) {
                        // Pastikan data adalah URL gambar yang valid
                        if (data) {
                            return '<img src="<?= base_url() ?>uploads/image/' + data + '" style="max-height: 100px; max-width: 100px;" />';
                        } else {
                            return '<img src="<?= base_url() ?>uploads/image/default-image-menus.png" style="max-height: 100px; max-width: 100px;" />';
                        }
                    }
                },
                {
                    data: "name_menus",
                },
                {
                    data: "harga_modal",
                    className: "text-center",
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: "harga_jual",
                    className: "text-center",
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: "kategori",
                    className: "text-center",
                    render: function(data, type, row) {
                        if (typeof data !== 'string' || !data.trim()) return data;

                        return data.charAt(0).toUpperCase() + data.slice(1).toLowerCase();
                    }
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `
                        <div class="d-flex justify-content-center">
                            <div class="mx-1 d-flex align-items-center justify-content-center gap-3 fs-6">
                                <button type="button" class=" mx-1 btn btn-icon btn-warning shadow-none" data-toggle="modal" data-target="#edit" onclick="edit('${data.id}')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class=" mx-1 btn btn-icon btn-danger shadow-none" onclick="delete_data('${data.id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                },
            ]
        });
    });

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

    function edit(id) {
        $('.modal-body-edit').html('');

        $.ajax({
            url: '<?= base_url('mitra/menu/edit'); ?>',
            type: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                $('.modal-body-edit').html(response);
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
                        if (response.status === 'errors') {
                            Swal.fire("Gagal menghapus menu!", response.message, "error");
                            return;
                        }

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
<script src="<?= base_url() ?>assets/modules/select2/dist/js/select2.full.min.js"></script>