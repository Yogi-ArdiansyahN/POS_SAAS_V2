<div class="d-flex justify-content-center" style="font-weight: bold;">
    <h6>
        <?= $cabang['name'] ?>
    </h6>
</div>
<div class="container-fluid p-0">
    <!-- <div class="row no-gutters"> -->
    <form action="<?= base_url() . 'mitra/stok/create' ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="cabang_id" value="<?= $cabang_id ?>">
        <div class="row">
            <!-- Kolom Stock Menus -->
            <div class="col-lg-12 col-md-12 p-2">
                <div class="rounded p-3 h-100">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" style="width:70%">Menu</th>
                                    <th scope="col" style="width:30%">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menus as $data) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($data['name']) ?></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                name="<?= htmlspecialchars($data['id']) ?>"
                                                value="" min="0">
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>