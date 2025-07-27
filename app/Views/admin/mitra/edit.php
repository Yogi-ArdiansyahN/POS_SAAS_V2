<form action="<?= base_url() ?>admin/mitra/update" id="content-form" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $mitra['id'] ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control" name="name" id="name" value="<?= $mitra['name'] ?>" readonly required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Username</label>
        <input type="text" class="form-control" name="name" id="name" value="<?= $mitra['username'] ?>" readonly required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" name="status" id="status" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
            <option value="aktif" <?= ($mitra['status'] == 'aktif' ? 'selected' : '') ?>>Aktif</option>
            <option value="tidak_aktif" <?= ($mitra['status'] == 'tidak_aktif' ? 'selected' : '') ?>>Tidak Aktif</option>
        </select>
    </div>
    <div class="d-flex justify-content-center">
        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
    </div>
</form>