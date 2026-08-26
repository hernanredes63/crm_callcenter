<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= Helpers::e(APP_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="<?= Helpers::url('/assets/app.css') ?>" rel="stylesheet">

<!-- Moviendo jQuery aquí arriba para que cargue antes que las vistas -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>
<body>
<div class="app-shell d-flex">
  <?php require ROOT_PATH . '/app/views/layout/sidebar.php'; ?>
  <div class="app-main flex-grow-1">
    <?php require ROOT_PATH . '/app/views/layout/topbar.php'; ?>
    <main class="p-4">
      <?php if ($msg = Helpers::flash('ok')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= Helpers::e($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?php if ($msg = Helpers::flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= Helpers::e($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      
      <!-- Aquí se inyectan las vistas como leads/index.php -->
      <?= $content ?>
      
    </main>
  </div>
</div>

<!-- El resto de los scripts se quedan al final -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="<?= Helpers::url('/assets/app.js') ?>"></script>
</body>
</html>