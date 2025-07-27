 <div class="table-responsive">
     <table class="table table-striped" id="datasTable">
         <thead class="">
             <tr>
                 <th>Menu</th>
                 <th>Harga Modal</th>
                 <th>Harga Jual</th>
                 <th>Qty</th>
                 <th>Total</th>
             </tr>
         </thead>
         <tbody>
             <?php foreach ($menus as $data) : ?>
                 <tr>
                     <td><?= $data['name'] ?></td>
                     <td><?= $data['harga_modal'] ?></td>
                     <td><?= $data['harga_jual'] ?></td>
                     <td><?= $data['quantity'] ?></td>
                     <td><?= $data['total'] ?></td>
                 </tr>
             <?php endforeach ?>
         </tbody>
     </table>
 </div>