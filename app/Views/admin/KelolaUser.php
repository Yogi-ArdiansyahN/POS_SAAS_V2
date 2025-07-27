<?= $this->include('layouts/header'); ?>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Kelola User</h1>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <!-- <h4>Advanced Table</h4> -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">Tambah</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            No
                                        </th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Phone</th>
                                        <th class="text-center">Balance</th>
                                        <th>Email</th>
                                        <th class="text-center">Role</th>
                                        <th class="text-center">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $itteration = 1; ?>
                                    <?php foreach ($users as $user) : ?>
                                        <tr>
                                            <td class="align-middle text-center">
                                                <?= $itteration++; ?>
                                            </td>
                                            <td><?= $user['name']; ?></td>
                                            <td><?= $user['username']; ?></td>
                                            <td><?= $user['phone']; ?></td>
                                            <td><?= $user['balance']; ?></td>
                                            <td><?= $user['email']; ?></td>
                                            <td class="text-center"><?= $user['role']; ?></td>
                                            <td class="text-center">
                                                <?php if ($user['status']  == 'active') : ?>
                                                    <div class="badge badge-success"><?= $user['status']; ?></div>
                                                <?php elseif ($user['status'] == 'suspend') : ?>
                                                    <div class="badge badge-danger"><?= $user['status']; ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit" onclick="edit('<?= $user['id'] ?>')">Edit</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- Modal Edit -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="content-form" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" class="form-control" name="id" id="editId" autocomplete="off">

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="Phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" id="email" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="balance" class="form-label">Balance</label>
                        <input type="text" class="form-control" name="balance" id="balance" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" name="role" id="role">
                            <option value="admin">Admin</option>
                            <option value="member">Member</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="active">Active</option>
                            <option value="suspend">Suspend</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="button" onclick="update()" id="btn-submit" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" tabindex="-1" role="dialog" id="tambah" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="content-form" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" class="form-control" name="id" id="idTambah" autocomplete="off">

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="namaTambah" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="usernameTambah" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="Phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" placeholder="6285XXXXXXXXX" id="phoneTambah" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" id="emailTambah" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="balance" class="form-label">Balance</label>
                        <input type="text" class="form-control" name="balance" id="balanceTambah" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" name="role" id="roleTambah">
                            <option value="admin">Admin</option>
                            <option value="member">Member</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Status</label>
                        <select class="form-control" name="status" id="statusTambah">
                            <option value="active">Active</option>
                            <option value="suspend">Suspend</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="button" onclick="tambah()" id="btn-submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var idd = document.getElementById('editId');
    var nama = document.getElementById('nama');
    var phone = document.getElementById('phone');
    var email = document.getElementById('email');
    var role = document.getElementById('role');
    var status = document.getElementById('status');
    var username = document.getElementById('username');
    var balance = document.getElementById('balance');

    function edit(id) {
        $.ajax({
            url: '<?= base_url('admin/getuser'); ?>',
            type: 'POST',
            data: {
                id: id,
            },
            success: function(response) {
                var data = response;
                idd.value = id;
                nama.value = data['name'];
                phone.value = data['phone'];
                email.value = data['email'];
                role.value = data['role'];
                status.value = data['status'];
                username.value = data['username'];
                balance.value = data['balance'];
            },
            error: function() {
                console.log('Error');
            }
        });
    }

    function update() {
        idd = $('#editId').val();
        nama = $('#nama').val();
        phone = $('#phone').val();
        email = $('#email').val();
        role = $('#role').val();
        status = $('#status').val();
        balance = $('#balance').val();
        username = $('#username').val();

        $.ajax({
            url: '<?= base_url('admin/updateuser'); ?>',
            type: 'POST',
            data: {
                id: idd,
                nama: nama,
                phone: phone,
                email: email,
                role: role,
                status: status,
                username: username,
                balance: balance
            },
            success: function(response) {
                var data = response;
                Swal.fire({
                    title: data.status,
                    text: data.message,
                    icon: "success"
                }).then((result) => {
                    window.location.href = '<?= base_url(); ?>' + 'admin/kelolauser';
                });

            },
            error: function() {
                console.log('Error');
            }
        });
    }

    function tambah() {
        idd = $('#idTambah').val();
        username = $('#usernameTambah').val();
        balance = $('#balanceTambah').val();
        nama = $('#namaTambah').val();
        phone = $('#phoneTambah').val();
        email = $('#emailTambah').val();
        role = $('#roleTambah').val();
        status = $('#statusTambah').val();

        $.ajax({
            url: '<?= base_url('admin/tambahuser'); ?>',
            type: 'POST',
            data: {
                id: idd,
                nama: nama,
                phone: phone,
                email: email,
                role: role,
                status: status,
                username: username,
                balance: balance
            },
            success: function(response) {
                var data = response;
                Swal.fire({
                    title: data.status,
                    text: data.message,
                    icon: "success"
                }).then((result) => {
                    window.location.href = '<?= base_url(); ?>' + 'admin/kelolauser';
                });

            },
            error: function() {
                console.log('Error');
            }
        });
    }
</script>
<?= $this->include('layouts/footer'); ?>