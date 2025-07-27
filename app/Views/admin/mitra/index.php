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
                                    <th>
                                        Name <br>
                                        <sup>(Username)</sup>
                                    </th>
                                    <th>
                                        Mitra <br>
                                        <sup>(Jumlah Cabang)</sup>
                                    </th>
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
    let mitras = <?= $mitras ?>;
    $(document).ready(function() {
        $('#datasTable').DataTable({
            lengthMenu: [
                [10, 30, 50, 1],
                [10, 30, 50, "All"]
            ],
            data: mitras,
            processing: true,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    className: "text-center"
                },
                {
                    data: 'name',
                    render: function(data, type, row) {
                        return `${data}<br><sup>(${row.username})</sup>`;
                    }
                },
                {
                    data: 'mitra',
                    render: function(data, type, row) {
                        return `${data}<br><sup>(${row.jumlah_cabang} Cabang)</sup>`;
                    }
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
                    render: function(data, type, row) {
                        return `<a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit" onClick="edit('${data.id}')">Edit</a>`;
                    }
                }
            ]
        });
    });

    function edit(id) {
        $('.modal-body-edit').html('');

        $.ajax({
            url: '<?= base_url('admin/mitra/edit'); ?>',
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
<script src="<?= base_url() ?>assets/modules/select2/dist/js/select2.full.min.js"></script>