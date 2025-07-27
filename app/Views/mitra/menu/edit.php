<form action="<?= base_url() ?>mitra/menu/update" id="content-form" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <input type="hidden" name="id" value="<?= $menu['id'] ?>">
    <div class="mb-3 d-flex justify-content-center">
        <?php if ($menu['foto']) : ?>
            <img src="<?= base_url() ?>uploads/image/<?= $menu['foto'] ?>" style="max-height: 100px; max-width: 100px;" />
        <?php else : ?>
            <img src="<?= base_url() ?>uploads/image/default-image-menus.png" style="max-height: 100px; max-width: 100px;" />
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="">Image</label>
        <input type="file" class="form-control" name="foto" accept="image/*">
        <input type="hidden" name="old_image" value="<?= $menu['foto'] ?>">
        <span style="color: red;">*Untuk mengubah gambar menu, unggah file baru menggunakan field di atas</span>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control" name="name" id="name" value="<?= $menu['name'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="">Harga Modal</label>
        <input type="number" class="form-control" name="harga_modal" id="harga_modal" value="<?= number_format($menu['harga_modal'], 0, ',', '.') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="">Harga Jual</label>
        <input type="number" class="form-control" name="harga_jual" id="harga_jual" value="<?= number_format($menu['harga_jual'], 0, ',', '.') ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="">Kategori</label>
        <select class="form-control" name="kategori" style="width: 100% !important;">
            <option selected value="<?= $menu['kategori'] ?>"> <?= ucfirst($menu['kategori']) ?> </option>
            <?php foreach ($kategori as $data) : ?>
                <?php if ($menu['kategori'] != $data) : ?>
                    <option value="<?= $data ?>"> <?= str_replace('_', ' ', ucfirst($data)) ?> </option>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    </div>
    <div class="d-flex justify-content-center">
        <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
    </div>
</form>

<script>
    function formatRupiah(angka) {
        angka = angka.toString().replace(/[^,\d]/g, '');
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $(document).ready(function() {
        $('#harga_modal, #harga_jual').on('input', function() {
            let value = $(this).val();
            $(this).val(formatRupiah(value));
        });
    });
</script>