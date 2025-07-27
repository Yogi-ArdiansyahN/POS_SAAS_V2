<div class="d-flex justify-content-center" style="font-weight: bold;">
    <h6>
        <?= $cabang_name ?>
    </h6>
</div>
<div class="container-fluid p-0">
    <div class="row">
        <!-- Kolom Stock Menus -->
        <div class="col-lg-12 col-md-12 p-2">
            <div class="rounded p-3 h-100">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="">Menu</th>
                                <th scope="col" class="text-center" style="">Current Quantity</th>
                                <th scope="col" class="text-center" style="">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stok as $data) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['menu_name']) ?></td>
                                    <td class="text-center">
                                        <?= $data['current_quantity'] ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $data['quantity'] ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>