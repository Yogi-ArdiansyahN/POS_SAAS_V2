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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>No W.A</th>
                                    <th>Cabang</th>
                                    <th>Status</th>
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
                <form action="<?= base_url() ?>mitra/kasir/create" id="content-form" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= old('name') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?= old('username') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" id="email" value="<?= old('email') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">No W.A</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="<?= old('phone') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
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
    $(document).ready(function() {
        $('#datasTable').DataTable({
            lengthMenu: [
                [10, 30, 50, 500, 800, 1000, -1],
                [10, 30, 50, 500, 800, 1000, "All"]
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url() ?>' + "mitra/ajax/getDataTable",
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
                    data: "name",
                },
                {
                    data: "email",
                },
                {
                    data: "phone",
                },
                {
                    data: "cabang_name",
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `<span class="badge rounded-pill  text-white bg-${data.status == 'aktif' ? 'success' : (data.status == 'tidak_aktif' ? 'danger' : 'primary') }"> ${data.status.replace('_', ' ')} </span>`;
                    }
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `
                        <div class="d-flex justify-content-center">
                           <div class="mx-1 d-flex align-items-center justify-content-center gap-3 fs-6">                                
                                <button type="button" class="btn btn-icon btn-warning shadow-none" data-toggle="modal" data-target="#edit" onclick="edit('${data.id}')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                },
            ]
        });
    });

    $(document).ready(function() {
        $('#phone').on('input', function() {
            let phoneNumber = $(this).val();

            // Jika inputan pertama bukan 62, ubah menjadi 62
            if (phoneNumber.length === 1 && phoneNumber !== '6') {
                $(this).val('62');
            }
        });
    });

    function edit(id) {
        $.ajax({
            url: '<?= base_url('mitra/kasir/edit'); ?>',
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