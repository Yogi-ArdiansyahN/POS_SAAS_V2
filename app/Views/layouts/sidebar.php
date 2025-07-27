 <div class="main-sidebar sidebar-style-2">
     <aside id="sidebar-wrapper">
         <div class="sidebar-brand">
             <a href="">POS SaaS</a>
         </div>
         <div class="sidebar-brand sidebar-brand-sm">
             <a href="index.html">PS</a>
         </div>

         <?php
            $role = session()->get('users')['role'];
            ?>

         <?php if ($role == "admin") : ?>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Dashboard") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() ?>admin" class="nav-link">
                         <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Langganan") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() ?>admin/langganan" class="nav-link">
                         <i class="fas fa-calendar-check"></i>
                         <span>Langganan</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Mitra") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() ?>admin/mitra" class="nav-link">
                         <i class="fas fa-handshake"></i>
                         <span>Mitra</span>
                     </a>
                 </li>
             </ul>

         <?php elseif ($role == "mitra") : ?>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Dashboard") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>" class="nav-link">
                         <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Langganan") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/langganan" class="nav-link">
                         <i class="fas fa-calendar-check"></i>
                         <span>Langganan</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Transaksi") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/transaksi" class="nav-link">
                         <i class="fas fa-shopping-cart"></i><span>Transaksi</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Diskon") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/diskon" class="nav-link">
                         <i class="fas fa-percentage"></i><span>Diskon</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Cabang") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/cabang" class="nav-link">
                         <i class="fas fa-store"></i><span>Cabang</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Kasir") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/kasir" class="nav-link">
                         <i class="fas fa-users"></i><span>Kasir</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Menu") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/menu" class="nav-link">
                         <i class="fas fa-utensils"></i><span>Menu</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Stok") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/stok" class="nav-link">
                         <i class="fas fa-boxes"></i><span>Stok</span>
                     </a>
                 </li>
             </ul>
             <ul class="sidebar-menu">
                 <li <?= ($title == "Laporan") ? 'class="active"' : '' ?>>
                     <a href="<?= base_url() . $role ?>/laporan" class="nav-link">
                         <i class="fas fa-chart-line"></i><span>Laporan</span>
                     </a>
                 </li>
             </ul>

         <?php endif; ?>

         <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
             <a href="<?= base_url('logout') ?>" class="btn btn-primary btn-lg btn-block btn-icon-split">
                 <i class="fas fa-rocket"></i> Logout
             </a>
         </div>
     </aside>
 </div>