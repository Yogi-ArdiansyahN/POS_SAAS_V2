 <form action="<?= base_url() ?>mitra/kasir/update" id="content-form" method="POST">
     <?= csrf_field() ?>
     <input type="text" name="id" id="id" value="<?= $kasir['id'] ?>" hidden>
     <div class="mb-3">
         <label for="name" class="form-label">Name</label>
         <input type="text" class="form-control" name="name" id="name" value="<?= $kasir['name'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off" readonly>
     </div>
     <div class="mb-3">
         <label for="username" class="form-label">Username</label>
         <input type="text" class="form-control" name="username" id="username" value="<?= $kasir['username'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off" readonly>
     </div>
     <div class="mb-3">
         <label for="email" class="form-label">Email</label>
         <input type="text" class="form-control" name="email" id="email" value="<?= $kasir['email'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off" readonly>
     </div>
     <div class="mb-3">
         <label for="phone" class="form-label">No W.A</label>
         <input type="text" class="form-control" name="phone" id="phone" value="<?= $kasir['phone'] ?>" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off" readonly>
     </div>
     <div class="mb-3">
         <label for="status" class="form-label">Status</label>
         <select class="form-control" name="status" id="status" required oninvalid="this.setCustomValidity('Harap isi kolom ini')" oninput="this.setCustomValidity('')" autocomplete="off">
             <option selected value="<?= $kasir['status'] ?>"> <?= str_replace('_', ' ', ucfirst($kasir['status'])) ?> </option>
             <?php if ($kasir['role'] != 'mitra'): ?>
                 <?php foreach ($status as $datas) : ?>
                     <?php if ($kasir['status'] != $datas) : ?>
                         <option value="<?= $datas ?>"> <?= str_replace('_', ' ', ucfirst($datas)) ?> </option>
                     <?php endif ?>
                 <?php endforeach ?>
             <?php endif ?>
         </select>
     </div>
     <div class="d-flex justify-content-center">
         <button type="submit" id="btn-submit" class="btn btn-primary" <?= $kasir['role'] == 'mitra' ? 'disabled' : '' ?>>Submit</button>
     </div>
 </form>