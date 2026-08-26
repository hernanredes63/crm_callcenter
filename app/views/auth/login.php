<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
  <div class="card shadow-lg border-0" style="max-width:420px;width:100%">
    <div class="card-body p-4">
      <div class="text-center mb-4">
        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
             style="width:64px;height:64px"><i class="bi bi-people-fill fs-3"></i></div>
        <h4 class="mb-1"><?= Helpers::e(APP_NAME) ?></h4>
        <p class="text-muted small">Inicia sesión para acceder al sistema</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small"><?= Helpers::e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= Helpers::url('/login') ?>">
        <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" required autofocus class="form-control" placeholder="tu@email.com">
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" required minlength="6" class="form-control" placeholder="••••••••">
        </div>
        <button class="btn btn-primary w-100">Iniciar sesión</button>
      </form>
      <p class="text-center text-muted small mt-3 mb-0">¿No tienes cuenta? Contacta al administrador.</p>
    </div>
  </div>
</div>
