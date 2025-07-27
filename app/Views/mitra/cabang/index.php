<?= $this->include('layouts/header'); ?>
<div class="main-content">

    <div class="row bg-white shadow rounded p-3 d-flex mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="m-0"><?= $title ?></h3>
            <div class="ml-3">
                <a href="#" class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#tambah">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
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
                            <div class="d-flex justify-content-between align-items-top">
                                <div class="">
                                    <span class="mb-3 badge badge-<?= $data['status'] == 'buka' ? 'success' : (($data['status'] == 'tutup') ? 'danger' : 'primary') ?>"> <?= str_replace('_', ' ', ucfirst($data['status'])) ?> </span> <br>
                                    <span style="font-weight: bold; color: black;"><?= $data['cabang_name'] ?></span>(<?= $data['kasir_name'] ?>)
                                </div>
                                <div class="">
                                    <div class="d-flex align-items-center justify-content-center gap-3 fs-6">
                                        <button type="button" class="btn btn-warning shadow-none" data-toggle="modal" data-target="#edit" onclick="edit('<?= $data['id'] ?>')">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <span>
                                <?= $data['alamat'] ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
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
                <form action="<?= base_url() ?>mitra/cabang/create" id="content-form" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Cabang</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= old('name') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Cabang</label>
                        <input type="text" class="form-control" name="alamat" id="alamat" value="<?= old('alamat') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" name="status" id="status" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                            <option selected disabled>Select one</option>
                            <?php foreach ($status as $data) : ?>
                                <option value="<?= $data ?>"> <?= str_replace('_', ' ', ucfirst($data)) ?> </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cashier" class="form-label">Kasir</label>
                        <select class="form-control" name="cashier" id="cashier" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                            <option selected disabled>Pilih Satu</option>

                            <?php if (empty($kasirs)) : ?>
                                <option disabled> Please Add a Cashier First </option>
                            <?php endif; ?>

                            <?php foreach ($kasirs as $data) : ?>
                                <option value="<?= $data['id'] ?>"> <?= $data['name'] ?> </option>
                            <?php endforeach ?>
                        </select>
                        <span class="text-danger"> * Buat kasir apabila diperlukan</span>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
        $.ajax({
            url: '<?= base_url('mitra/cabang/edit'); ?>',
            type: 'GET',
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
</script>

<?= $this->include('layouts/footer'); ?>