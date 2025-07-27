<div class="d-flex justify-content-center" style="font-weight: bold;">
    <h6>
        <?= $cabang_name ?>
    </h6>
</div>
<div class="container-fluid p-0">
    <!-- <div class="row no-gutters"> -->
    <form action="<?= base_url() . 'mitra/stok/mutasi' ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="cabang_id" value="<?= $cabang_id ?>">
        <div class="mb-3" id="input_ingredients">
            <div class="d-flex justify-content-between bg-info p-3 rounded">
                <div class="d-flex align-items-center text-white">
                    <span>Menu</span>
                </div>
                <button class="btn btn-icon icon-left btn-primary" id="raw_materials" type="button" onclick="add()">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="pb-1" id="menus">
        </div>
        <div class="d-flex justify-content-center">
            <button type="submit" id="btn-submit" name="mutasi" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

<script>
    var total_menu = <?= $total_menu ?>;
    var uniqueMenu = 0;
    var selected_mn = [];

    function add() {
        // Cek apakah jumlah sudah mencapai batas
        if (uniqueMenu >= total_menu) {
            // alert('Jumlah maksimum menu telah tercapai!');
            Swal.fire({
                position: "top-center",
                icon: "info",
                title: "Jumlah maksimum menu telah tercapai!",
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        $('#menus').append(`
            <div class="row mb-3 alert-info p-2 rounded" id="rawm-${uniqueMenu}">
                <div class="col align-items-center">
                    <select class="form-control mb-1" name="menu[]" data-uid="${uniqueMenu}" onchange="selectMenu(this)" style="width: 100% !important;">
                        <option selected disabled> Pilih Menu </option>
                        <?php foreach ($stoks as $data) : ?>
                            <option value="<?= esc($data['menus_id']) ?>" data-material="<?= $data['menu_name'] ?>"><?= esc($data['menu_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-control mb-1" name="tipe_mutasi[]" required onchange="selectMutasi(this)">
                        <option selected disabled>Pilih Tipe Mutasi</option>
                        <?php foreach ($tipe_mutasi as $tipe) : ?>
                            <option value="<?= $tipe ?>"><?= ucfirst($tipe) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" class="form-control mb-1" name="quantity[]" id="quantity_${uniqueMenu}" placeholder="Quantity">
                   <div class="d-none mb-1" id="mutasi_perpindahan_${uniqueMenu}">
                        <div class="row mt-2">
                            <div class="col-lg-6 mb-2">
                                <label for="">Dari cabang:</label>
                                <input type="text" class="form-control" style="background-color: white !important" value="<?= $cabang_name ?>" placeholder="Dari" required readonly>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="">Ke cabang:</label>
                                <select class="form-control" name="perpindahan_ke[]" required>
                                    <option selected disabled>Pilih cabang</option>
                                    <?php foreach ($cabangs as $data) : ?>
                                        <option value="<?= $data['id'] ?>"><?= ucfirst($data['cabang_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <button class="btn btn-icon icon-left btn-danger shadow-none" type="button" onclick="min('${uniqueMenu}')">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `);

        uniqueMenu++;
    }

    function min(uid) {
        $('#rawm-' + uid).remove();
        selected_mn = selected_mn.filter(item => item.uid !== uid);
        uniqueMenu--; // kurangi jumlah saat elemen dihapus
    }

    function selectMenu(el) {
        const value = el.value;
        const material = el.options[el.selectedIndex].dataset.material;
        const uid = el.dataset.uid;

        // Cek duplikasi
        const isDuplicate = selected_mn.some(item => item.id === value && item.uid !== uid);

        if (isDuplicate) {
            // alert('Menu sudah dipilih. Silakan pilih menu lain.');
            Swal.fire({
                position: "top-center",
                icon: "info",
                title: "Menu sudah dipilih. Silakan pilih menu lain.",
                showConfirmButton: false,
                timer: 1500
            });
            el.selectedIndex = 0; // Kembalikan ke "Pilih Satu"
            return;
        }

        const newItem = {
            uid: uid,
            id: value,
            name: material
        };

        const existingIndex = selected_mn.findIndex(item => item.uid === uid);
        if (existingIndex !== -1) {
            selected_mn[existingIndex] = newItem;
        } else {
            selected_mn.push(newItem);
        }
    }

    function selectMutasi(el) {
        const uid = el.parentElement.parentElement.id.split('-')[1];
        const mutasiType = el.value;

        // Tampilkan atau sembunyikan elemen berdasarkan
        if (mutasiType === 'perpindahan') {
            document.getElementById(`mutasi_perpindahan_${uid}`).classList.remove('d-none');
        } else {
            document.getElementById(`mutasi_perpindahan_${uid}`).classList.add('d-none');
        }
    }
</script>