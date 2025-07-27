<?= $this->include('layouts/header'); ?>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
    </section>
    <div class="row">
        <!-- Total Users -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2 shadow">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-users"></i> <!-- Ganti dari fa-archive -->
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Users</h4>
                    </div>
                    <div class="card-body">
                        <?= $total_user ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mitra -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2 shadow">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-handshake"></i> <!-- Ganti dari fa-dollar-sign -->
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Mitra</h4>
                    </div>
                    <div class="card-body">
                        <?= $total_mitra ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cabang -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2 shadow">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-code-branch"></i> <!-- Ganti dari fa-shopping-bag -->
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Cabang</h4>
                    </div>
                    <div class="card-body">
                        <?= $total_cabang ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card card-statistic-2 shadow">
                <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-receipt"></i> <!-- Ganti dari fa-shopping-bag -->
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>
                            Total Transaksi
                            <sup>(Hari Ini)</sup>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?= $total_transaksi ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        
    </div>
</div>
<?= $this->include('layouts/footer'); ?>