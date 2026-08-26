<header class="app-topbar bg-white border-bottom d-flex align-items-center px-3">
  <button class="btn btn-sm btn-outline-secondary d-md-none me-2" onclick="document.querySelector('.app-sidebar').classList.toggle('show')">
    <i class="bi bi-list"></i>
  </button>
  <div class="ms-auto d-flex align-items-center gap-2">
    <!-- <span class="text-muted small"><?= Helpers::e(Auth::user()['email'] ?? '') ?></span> -->
    <small class="text-dark"><?= Helpers::e(Auth::user()['nombre'] ?? '') ?> · <?= Helpers::e($role ?? '') ?></small>
    <form method="post" action="<?= Helpers::url('/logout') ?>" class="m-0">
      <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
      <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-box-arrow-right"></i> Salir</button>
    </form>
  </div>
</header>
