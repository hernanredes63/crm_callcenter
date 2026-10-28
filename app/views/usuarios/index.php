<?php
/** @var array $users @var ?string $error @var ?string $ok */
$roleLabels = [
  'admin'=>'Administrador','supervisor'=>'Supervisor','operador'=>'Operador',
  'vendedor'=>'Vendedor','marketing'=>'Marketing',
];
$roleBadge = [
  'admin'=>'bg-primary','supervisor'=>'bg-info text-dark','operador'=>'bg-secondary',
  'vendedor'=>'bg-success','marketing'=>'bg-warning text-dark',
];
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Usuarios</h4>
    <small class="text-muted">Gestiona los usuarios del sistema</small>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mNuevoUsr">
    <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
  </button>
</div>

<?php if ($error): ?><div class="alert alert-danger"><?= Helpers::e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert alert-success"><?= Helpers::e($ok) ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-header bg-white">
    <strong><i class="bi bi-shield-lock me-1"></i>Usuarios registrados</strong>
    <span class="badge bg-secondary ms-2"><?= count($users) ?></span>
  </div>
  <div class="card-body">
    <?php if (!$users): ?>
      <div class="text-center text-muted py-4">No hay usuarios registrados.</div>
    <?php else: ?>
    <div class="table-responsive">
      <table id="tblUsuarios" class="table table-striped table-hover align-middle w-100">
        <thead class="table-light">
          <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th class="text-end">Acciones</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u):
          $r = $u['role'] ?? null; ?>
          <tr>
            <td class="fw-semibold"><?= Helpers::e($u['nombre']) ?></td>
            <td><?= Helpers::e($u['email'] ?? '') ?></td>
            <td>
              <?php if ($r): ?>
                <span class="badge <?= $roleBadge[$r] ?? 'bg-secondary' ?>"><?= Helpers::e($roleLabels[$r] ?? $r) ?></span>
              <?php else: ?>
                <span class="badge bg-light text-dark border">Sin rol</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge <?= $u['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>

            <td class="text-end">

                <!-- NUEVO BOTÓN DE EDICIÓN -->
              <button type="button" class="btn btn-sm btn-outline-info btn-edit-usr" 
                      title="Editar perfil"
                      data-id="<?= $u['id'] ?>"
                      data-nombre="<?= Helpers::e($u['nombre']) ?>"
                      data-email="<?= Helpers::e($u['email'] ?? '') ?>"
                      data-bs-toggle="modal" 
                      data-bs-target="#mEditarUsr">
                <i class="bi bi-pencil"></i>
              </button>

              <!-- Los formularios que ya tenías de Rol y Activar/Desactivar -->
              <form method="post" action="<?= Helpers::url('/usuarios/'.$u['id'].'/rol') ?>" class="d-inline-flex align-items-center gap-1">




              <form method="post" action="<?= Helpers::url('/usuarios/'.$u['id'].'/rol') ?>" class="d-inline-flex align-items-center gap-1">
                <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                <select name="role" class="form-select form-select-sm" style="width:auto;">
                  <?php foreach ($roleLabels as $rk=>$rl): ?>
                    <option value="<?= $rk ?>" <?= $r===$rk?'selected':'' ?>><?= $rl ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-outline-primary" title="Cambiar rol"><i class="bi bi-check2"></i></button>
              </form>
              <form method="post" action="<?= Helpers::url('/usuarios/'.$u['id'].'/toggle') ?>" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                <button class="btn btn-sm btn-outline-<?= $u['activo']?'secondary':'success' ?>"
                  title="<?= $u['activo']?'Desactivar':'Activar' ?>">
                  <i class="bi bi-<?= $u['activo']?'pause-circle':'play-circle' ?>"></i>
                </button>
              </form>
            </td>


          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="modal fade" id="mNuevoUsr" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="<?= Helpers::url('/usuarios') ?>" class="modal-content">
      <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
      <div class="modal-header"><h5 class="modal-title">Crear Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Nombre</label>
          <input name="nombre" required class="form-control" placeholder="Nombre completo"></div>
        <div class="mb-3"><label class="form-label">Email</label>
          <input name="email" type="email" required class="form-control" placeholder="usuario@email.com"></div>
        <div class="mb-3"><label class="form-label">Contraseña</label>
          <input name="password" type="password" required minlength="6" class="form-control" placeholder="Mínimo 6 caracteres"></div>
        <div class="mb-3"><label class="form-label">Rol</label>
          <select name="role" class="form-select">
            <?php foreach ($roleLabels as $rk=>$rl): ?>
              <option value="<?= $rk ?>" <?= $rk==='operador'?'selected':'' ?>><?= $rl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Crear Usuario</button>
      </div>
    </form>
  </div>
</div>


<!-- MODAL EDITAR USUARIO -->
<div class="modal fade" id="mEditarUsr" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" id="frmEditarUsr" action="" class="modal-content">
      <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
      <div class="modal-header">
        <h5 class="modal-title">Editar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input name="nombre" id="editNombre" required class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input name="email" id="editEmail" type="email" required class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Nueva Contraseña</label>
          <input name="password" type="password" minlength="6" class="form-control" placeholder="Dejar en blanco para conservar la actual">
          <small class="text-muted">Si no quieres cambiar la contraseña, no escribas nada aquí.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-info text-white"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>






<script>
$(function(){
  if ($.fn.DataTable && $('#tblUsuarios').length) {
    $('#tblUsuarios').DataTable({
      pageLength:25, order:[[0,'asc']],
      language:{ url:'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' }
    });
  }

  // Cargar datos en el modal al presionar el botón de editar
  $('.btn-edit-usr').on('click', function() {
    const id = $(this).data('id');
    const nombre = $(this).data('nombre');
    const email = $(this).data('email');

    $('#editNombre').val(nombre);
    $('#editEmail').val(email);
    
    // Construir la URL dinámica para guardar los cambios
    $('#frmEditarUsr').attr('action', '<?= Helpers::url('/usuarios/') ?>' + id + '/editar');
  });
});
</script>
