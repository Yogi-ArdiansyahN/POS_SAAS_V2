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
        <?php if (!empty($langganan)) : ?>
            <?php foreach ($langganan as $data) : ?>
                <?php if ($is_trial == 1 && $data['name'] == 'Trial') continue; ?>
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="pricing shadow pb-3">
                        <div class="pricing-title">
                            <?= $data['name'] ?>
                        </div>
                        <div class="pricing-padding">
                            <div class="pricing-price">
                                <div style="font-size:25px !important;">
                                    <sup> Rp </sup> <?= number_format($data['harga']) ?>
                                </div>
                                <div>per <?= $data['durasi']  . ' ' . ucfirst($data['kategori']) ?></div>
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
                            <button class="btn btn-info" onclick="berlangganan('<?= $data['id'] ?>')">Berlangganan <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif ?>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="m-0">Riwayat <?= $title ?></h3>
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
                                    <th>Langganan</th>
                                    <th>Harga</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Akhir</th>
                                    <th>Expired At</th>
                                    <th>Status</th>
                                    <th>Created At</th>
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

<script>
    let riwayat_langganan = <?= $riwayatLangganan ?>;

    $(document).ready(function() {
        $('#datasTable').DataTable({
            lengthMenu: [
                [10, 30, 50, 1],
                [10, 30, 50, "All"]
            ],
            processing: true,
            data: riwayat_langganan,
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
                    data: "harga",
                },
                {
                    data: "tanggal_mulai",
                },
                {
                    data: "tanggal_selesai",
                },
                {
                    data: "expired_at",
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        return `<span class="badge rounded-pill  text-white bg-${data.status == 'lunas' ? 'success' : (data.status == 'belum_lunas' ? 'danger' : 'primary') }"> ${data.status.replace('_', ' ')} </span>`;
                    }
                },
                {
                    data: "created_at",
                },
            ]
        });
    });

    function berlangganan(id) {
        Swal.fire({
            title: "Mohon ditunggu...",
            html: "Berlangganan sedang diproses.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();

                // Tunggu 3 detik sebelum jalankan AJAX
                setTimeout(() => {
                    $.ajax({
                        url: '<?= base_url('mitra/langganan/berlangganan'); ?>',
                        type: 'GET',
                        data: {
                            id: id,
                        },
                        success: function(response) {
                            Swal.close(); // tutup loading

                            if (response.status === "success") {
                                Swal.fire({
                                    title: 'Langkah-langkah Pembayaran Langganan',
                                    html: `
                                        <ul style="text-align: left;">
                                        <li>Transfer sesuai nominal langganan.</li>
                                        <li>Transfer ke Dana: 0851-5651-1121 <br> (a.n. Yogi Ardiansyah Nugraha).</li>
                                        <li>Konfirmasi ke admin: 0851-5651-1121.</li>
                                        </ul>
                                    `,
                                    icon: 'info',
                                    confirmButtonText: 'Selesai'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload(); // 🔄 Reload halaman
                                    }
                                });
                            }

                            if (response.status === "errors") {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: response.message,
                                    confirmButtonText: 'Selesai'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload(); // 🔄 Reload halaman
                                    }
                                });
                            }

                            if (response.status === "success_trial") {
                                Swal.fire({
                                    icon: "success",
                                    title: "Langganan Trial",
                                    text: "Selamat anda telah berlangganan",
                                    confirmButtonText: 'Selesai'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload(); // 🔄 Reload halaman
                                    }
                                });
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat memproses permintaan.'
                            });
                        }
                    });
                }, 2000); // <-- 3 detik delay
            }
        });
    }
</script>

<?= $this->include('layouts/footer'); ?>