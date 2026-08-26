<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$active = fn(string $p) => strpos($path, $p) ? 'active' : '';  // str_starts_with($path, $p)
$role = Auth::role();
?>
<aside class="app-sidebar text-white" style="background:#471d63;">
  <div class="p-3 border-bottom border-secondary">
    <h5 class="mb-0"><i class="bi bi-telephone-fill me-2"></i><?= Helpers::e(APP_NAME) ?></h5>
    <small class="text-info">Telemarketing</small>    
  </div>
  <nav class="nav flex-column p-2">
   <!-- && !str_contains($path,'redes') -->
    <a class="nav-link text-white <?= $active('/leads') && !strpos($path,'redes') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/leads') ?>"><i class="bi bi-person-lines-fill me-2"></i>Leads</a>
    <a class="nav-link text-white <?= $active('/leads-redes') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/leads-redes') ?>"><i class="bi bi-person-lines-fill me-2"></i>Leads Redes</a>
    <a class="nav-link text-white <?= $active('/derivados') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/derivados') ?>"><i class="bi bi-share me-2"></i>Derivados</a>
    <a class="nav-link text-white <?= $active('/ventas') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/ventas') ?>"><i class="bi bi-cart-check me-2"></i>Ventas</a>
    <a class="nav-link text-white <?= $active('/seguimiento-ventas') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/seguimiento-ventas') ?>"><i class="bi bi-graph-up-arrow me-2"></i>Seguimiento Ventas</a>
    <a class="nav-link text-white <?= $active('/calendario') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/calendario') ?>"><i class="bi bi-calendar3 me-2"></i>Calendario</a>
    <a class="nav-link text-white <?= $active('/reportes') ? 'active bg-primary rounded' : '' ?>"
       href="<?= Helpers::url('/reportes') ?>"><i class="bi bi-bar-chart me-2"></i>Reportes</a>
    <?php if (in_array($role, ['admin','supervisor'], true)): ?>
      <hr class="text-secondary">
      <a class="nav-link text-white <?= $active('/usuarios') ? 'active bg-primary rounded' : '' ?>"
         href="<?= Helpers::url('/usuarios') ?>"><i class="bi bi-person-badge me-2"></i>Usuarios</a>
      <a class="nav-link text-white <?= $active('/base') ? 'active bg-primary rounded' : '' ?>"
         href="<?= Helpers::url('/base') ?>"><i class="bi bi-database me-2"></i>Base / Catálogos</a>
    <?php endif; ?>
  </nav>
</aside>
