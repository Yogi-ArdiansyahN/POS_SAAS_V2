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
                        <div class="ml-3">
                            <a href="#" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#tambah">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="datasTable">
                            <thead class="">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kode</th>
                                    <th>Tipe</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $iteration = 1; ?>
                                <?php foreach ($diskons as $data) : ?>
                                    <tr>
                                        <td><?= $iteration ?></td>
                                        <td><?= $data['name'] ?></td>
                                        <td><?= $data['kode'] ?></td>
                                        <td><?= $data['type'] ?></td>
                                        <td><?= $data['value'] ?></td>
                                        <td>
                                            <span class="mb-3 badge badge-<?= $data['is_active'] == '1' ? 'success' : (($data['is_active'] == '0') ? 'danger' : 'primary') ?>">
                                                <?= ($data['is_active'] == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                            </span>
                                        </td>
                                        <td><?= $data['start_date'] ?></td>
                                        <td><?= $data['end_date'] ?></td>
                                        <td><?= $data['created_at'] ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <div class="mx-1 d-flex align-items-center justify-content-center gap-3 fs-6">
                                                    <button type="button" class=" mx-1 btn btn-icon btn-warning shadow-none" data-toggle="modal" data-target="#edit" onclick="edit('<?= $data['id'] ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $iteration++ ?>
                                <?php endforeach; ?>
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
                <form action="<?= base_url() ?>mitra/diskon/create" id="content-form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= old('name') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="">Tipe</label>
                        <select class="form-control" name="tipe" style="width: 100% !important;">
                            <option disabled selected> Pilih salah satu </option>
                            <option value="nominal"> Nominal </option>
                            <option value="persen"> Persen </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Nilai Diskon</label>
                        <input type="number" class="form-control" name="nilai_diskon" id="nilai_diskon" value="<?= old('nilai_diskon') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" step="0.5" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered" role="document">
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
    function edit(id) {
        $('.modal-body-edit').html('');

        $.ajax({
            url: '<?= base_url('mitra/diskon/edit'); ?>',
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