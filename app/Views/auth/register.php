<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Pendaftaran - POS SaaS</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/fontawesome/css/all.min.css">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/jquery-selectric/selectric.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js" integrity="sha512-+k1pnlgt4F1H8L7t3z95o3/KO+o78INEcXTbnoJQ/F2VqDVhWoaiVml/OEHv9HsVgxUaVW+IbiZPUJQfF/YxZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/components.css">
    <!-- Start GA -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- /END GA -->
</head>

<body>
    <div id="app" class="">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-12 mx-auto">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Pendaftaran</h4>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="<?= base_url('daftar') ?>">
                                    <?= csrf_field(); ?>
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
                                        <div class="col-12 col-md-4 col-lg-4">
                                            <div class="form-group" class="d-block">
                                                <label for="name">Nama</label>
                                                <input id="name" type="text" class="form-control" name="name" value="<?= old('name') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                            </div>
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input id="username" type="username" class="form-control" name="username" value="<?= old('username') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                                <div class="invalid-feedback">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input id="email" type="email" class="form-control" name="email" value="<?= old('email') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                                <div class="invalid-feedback">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="nowa" class="d-block">No W.A</label>
                                                <input id="phone" type="number" class="form-control pwstrength" name="phone" value="<?= old('phone') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                            </div>
                                            <div class="form-group">
                                                <label for="password" class="d-block">Kata Sandi</label>
                                                <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                                <div id="pwindicator" class="pwindicator">
                                                    <div class="bar"></div>
                                                    <div class="label"></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="password2" class="d-block">Konfirmasi Kata Sandi</label>
                                                <input id="password2" type="password" class="form-control" name="password-confirm" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 col-lg-4">
                                            <div class="form-group" class="d-block">
                                                <label for="name_umkm">Nama UMKM</label>
                                                <input id="name_umkm" type="text" class="form-control" name="name_umkm" value="<?= old('name_umkm') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                            </div>
                                            <div class="form-group" class="d-block">
                                                <label for="alamat_umkm">Alamat UMKM</label>
                                                <input id="alamat_umkm" type="text" class="form-control" name="alamat_umkm" value="<?= old('alamat_umkm') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="d-flex justify-content-end"> -->
                                    <div class="col-3 mx-auto">
                                        <button name="daftar" class="btn btn-primary btn-lg btn-block">
                                            Daftar
                                        </button>
                                    </div>
                                    <!-- </div> -->
                                </form>
                            </div>
                        </div>
                        <div class="simple-footer">
                            Copyright &copy; POS SaaS 2025
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            $('#phone').on('input', function() {
                let phoneNumber = $(this).val();

                // Jika inputan pertama bukan 62, ubah menjadi 62
                if (phoneNumber.length === 1 && phoneNumber !== '6') {
                    $(this).val('62');
                }
            });
        });
    </script>

    <!-- General JS Scripts -->
    <script src="<?= base_url() ?>assets/modules/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/modules/popper.js"></script>
    <script src="<?= base_url() ?>assets/modules/tooltip.js"></script>
    <script src="<?= base_url() ?>assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="<?= base_url() ?>assets/modules/moment.min.js"></script>

    <!-- JS Libraies -->
    <script src="<?= base_url() ?>assets/modules/jquery-pwstrength/jquery.pwstrength.min.js"></script>
    <script src="<?= base_url() ?>assets/modules/jquery-selectric/jquery.selectric.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="<?= base_url() ?>assets/js/page/auth-register.js"></script>

    <!-- Template JS File -->
    <script src="<?= base_url() ?>assets/js/scripts.js"></script>
    <script src="<?= base_url() ?>assets/js/custom.js"></script>
</body>

</html>