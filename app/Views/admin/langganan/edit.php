<form action="<?= base_url() ?>admin/langganan/update" id="content-form" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $langganan['id'] ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control" name="name" id="name" value="<?= $langganan['name'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Kategori</label>
        <select class="form-control" name="kategori" id="kategori" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
            <option value="minggu" <?= ($langganan['kategori'] == 'minggu' ? 'selected' : '') ?>>Minggu</option>
            <option value="bulan" <?= ($langganan['kategori'] == 'bulan' ? 'selected' : '') ?>>Bulan</option>
            <option value="tahun" <?= ($langganan['kategori'] == 'tahun' ? 'selected' : '') ?>>Tahun</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" name="status" id="status" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
            <option value="1" <?= ($langganan['status'] == 1 ? 'selected' : '') ?>>Aktif</option>
            <option value="0" <?= ($langganan['status'] == 0 ? 'selected' : '') ?>>Tidak Aktif</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Durasi</label>
        <input type="number" class="form-control" name="durasi" id="name" value="<?= $langganan['durasi'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Harga</label>
        <input type="number" class="form-control" name="harga" id="name" value="<?= $langganan['harga'] ?>" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="d-flex justify-content-center">
        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
    </div>
</form>