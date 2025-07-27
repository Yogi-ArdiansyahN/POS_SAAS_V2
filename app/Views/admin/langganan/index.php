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
        <?php if (!empty($langganan)) : ?>
            <?php foreach ($langganan as $data) : ?>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="pricing shadow pb-3">
                        <div class="pricing-title">
                            <?= $data['name'] ?>
                        </div>
                        <div class="pricing-title text-<?= $data['status'] == 1 ? 'success' : 'danger' ?>">
                            <?= $data['status'] == 1 ? 'Aktif' : 'Tidak Aktif' ?>
                        </div>
                        <div class="pricing-padding">
                            <div class="pricing-price">
                                <div>
                                    <sup> Rp </sup> <?= number_format($data['harga']) ?>
                                </div>
                                <div>per <?= ucfirst($data['kategori']) ?></div>
                            </div>
                            <div class="pricing-details">
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label">Multi Kasir</div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label">Multi Cabang</div>
                                </div>
                                <div class="pricing-item">
                                    <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                    <div class="pricing-item-label">Laporan Otomatis</div>
                                </div>
                            </div>
                        </div>
                        <div class="pricing-cta mb-3">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#edit" onclick="edit('<?= $data['id'] ?>')"> Edit <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>
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
                <form action="<?= base_url() ?>admin/langganan/create" id="content-form" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= old('name') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Kategori</label>
                        <select class="form-control" name="kategori" id="kategori" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                            <option selected disabled>Select one</option>
                            <option value="bulan">Bulan</option>
                            <option value="tahun">Tahun</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Durasi</label>
                        <input type="number" class="form-control" name="durasi" id="name" value="<?= old('durasi') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" id="name" value="<?= old('harga') ?>" oninput="this.setCustomValidity('')" autocomplete="off">
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
                    <span aria-hidden="true" onclick="close_modal_edit()">×</span>
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
            url: '<?= base_url('admin/langganan/edit'); ?>',
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