<?= $this->include('kasir/layouts/header') ?>

<!-- Main Content -->
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Report Filters -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Report Filters</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option value="daily">Daily Sales</option>
                                <option value="weekly">Weekly Sales</option>
                                <option value="monthly">Monthly Sales</option>
                                <option value="product">Product Sales</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="generateReport">
                                <i class="bi bi-search"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Summary -->
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Total Sales</h6>
                                    <h3 id="totalSales">Rp 8,250,000</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Transactions</h6>
                                    <h3 id="totalTransactions">165</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Avg. Sale</h6>
                                    <h3 id="avgSale">Rp 50,000</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h6>Items Sold</h6>
                                    <h3 id="itemsSold">412</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Data -->
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sales Data</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2" id="exportCSV">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="printReport">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Items</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data - would be dynamically generated in a real app -->
                                <tr>
                                    <td>2023-04-23</td>
                                    <td>TRX-001</td>
                                    <td>5</td>
                                    <td>Cash</td>
                                    <td>Rp 125,000</td>
                                </tr>
                                <tr>
                                    <td>2023-04-23</td>
                                    <td>TRX-002</td>
                                    <td>3</td>
                                    <td>Card</td>
                                    <td>Rp 78,000</td>
                                </tr>
                                <tr>
                                    <td>2023-04-23</td>
                                    <td>TRX-003</td>
                                    <td>2</td>
                                    <td>QRIS</td>
                                    <td>Rp 45,000</td>
                                </tr>
                                <tr>
                                    <td>2023-04-22</td>
                                    <td>TRX-004</td>
                                    <td>7</td>
                                    <td>Cash</td>
                                    <td>Rp 175,000</td>
                                </tr>
                                <tr>
                                    <td>2023-04-22</td>
                                    <td>TRX-005</td>
                                    <td>4</td>
                                    <td>Card</td>
                                    <td>Rp 98,000</td>
                                </tr>
                                <tr>
                                    <td>2023-04-22</td>
                                    <td>TRX-006</td>
                                    <td>1</td>
                                    <td>Cash</td>
                                    <td>Rp 25,000</td>
                                </tr>
                                <tr>
                                    <td>2023-04-21</td>
                                    <td>TRX-007</td>
                                    <td>6</td>
                                    <td>QRIS</td>
                                    <td>Rp 150,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Sales Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('kasir/layouts/footer') ?>