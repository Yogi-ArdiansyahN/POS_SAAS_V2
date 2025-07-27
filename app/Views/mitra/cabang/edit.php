<form action="<?= base_url() ?>mitra/cabang/update" id="content-form" method="POST">
    <?= csrf_field() ?>
    <input type="text" name="id" id="id" value="<?= $data['id'] ?>" hidden>
    <div class="mb-3">
        <label for="name" class="form-label">Nama Cabang</label>
        <input type="text" class="form-control" name="name" id="name" value="<?= $data['cabang_name'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat Cabang</label>
        <input type="text" class="form-control" name="alamat" id="alamat" value="<?= $data['alamat'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" name="status" id="status" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
            <option selected value="<?= $data['status'] ?>"> <?= str_replace('_', ' ', ucfirst($data['status'])) ?> </option>
            <?php foreach ($status as $datas) : ?>
                <?php if ($data['status'] != $datas) : ?>
                    <option value="<?= $datas ?>"> <?= str_replace('_', ' ', ucfirst($datas)) ?> </option>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label for="cashier" class="form-label">Kasir</label>
        <select class="form-control" name="cashier" id="cashier" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
            <option selected value="<?= $data['kasir_id'] ?>"> <?= $data['kasir_name'] ?> </option>

            <?php if (empty($kasirs)) : ?>
                <option disabled> Please Add a Cashier First </option>
            <?php endif; ?>

            <?php foreach ($kasirs as $kasir) : ?>
                <option value="<?= $kasir['id'] ?>"> <?= $kasir['name'] ?> </option>
            <?php endforeach ?>
        </select>
        <span class="text-danger"> * Buat kasir apabila diperlukan</span>
    </div>
    <div class="d-flex justify-content-center">
        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
    </div>
</form>