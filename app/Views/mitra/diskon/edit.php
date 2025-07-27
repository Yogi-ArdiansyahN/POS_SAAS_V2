  <form action="<?= base_url() ?>mitra/diskon/update" id="content-form" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $diskons['id'] ?>">
      <div class="mb-3">
          <label for="name" class="form-label">Nama</label>
          <input type="text" class="form-control" id="name" value="<?= $diskons['name'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off" readonly>
      </div>
      <div class="mb-3">
          <label for="name" class="form-label">Kode</label>
          <input type="text" class="form-control" id="kode" value="<?= $diskons['kode'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off" readonly>
      </div>
      <div class="mb-3">
          <label for="">Tipe</label>
          <select class="form-control" name="type" style="width: 100% !important;">
              <option value="nominal" <?= $diskons['type'] == 'nominal' ? 'selected' : '' ?>> Nominal </option>
              <option value="persen" <?= $diskons['type'] == 'persen' ? 'selected' : '' ?>> Persen </option>
          </select>
      </div>
      <div class="mb-3">
          <label for="">Nilai Diskon</label>
          <input type="number" class="form-control" name="nilai_diskon" id="nilai_diskon" value="<?= $diskons['value'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" step="0.5" autocomplete="off">
      </div>
      <div class="mb-3">
          <label for="">Status</label>
          <select class="form-control" name="status" style="width: 100% !important;">
              <option value="aktif" <?= $diskons['is_active'] == '1' ? 'selected' : '' ?>> Aktif </option>
              <option value="tidak_aktif" <?= $diskons['is_active'] == '0' ? 'selected' : '' ?>> Tidak Aktif </option>
          </select>
      </div>
      <div class="mb-3">
          <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
          <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai"
              value="<?= $diskons['start_date'] ?>">
      </div>
      <div class="mb-3">
          <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
          <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai" value="<?= $diskons['end_date'] ?>">
      </div>
      <div class="d-flex justify-content-center">
          <button type="submit" id="btn-submit" class="btn btn-primary">Submit</button>
      </div>
  </form>