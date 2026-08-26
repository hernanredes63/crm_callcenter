<?php
/**
 * @var array  $tabs
 * @var string $currentTab
 * @var array  $cfg
 * @var array  $items
 * @var array  $empresas
 * @var array  $empresasMap
 * @var ?string $error
 * @var ?string $ok
 */
$field = $cfg['field'];
$needsEmpresa = $cfg['needsEmpresa'];
$labelField = ($field === 'nivel') ? 'Nivel' : 'Nombre';
$singular = rtrim($cfg['label'], 's');
if ($currentTab === 'mql_status') $singular = 'Nivel MQL';
if ($currentTab === 'campanas')   $singular = 'Campaña';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0"><i class="bi bi-database me-2"></i>Base de Datos</h4>
    <small class="text-muted">Gestiona empresas, proyectos, campañas, canales, agentes y niveles MQL</small>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mBaseNuevo">
    <i class="bi bi-plus-lg me-1"></i> Nuevo
  </button>
</div>

<?php if ($error): ?><div class="alert alert-danger"><?= Helpers::e($error) ?></div><?php endif; ?>
<?php if ($ok):    ?><div class="alert alert-success"><?= Helpers::e($ok) ?></div><?php endif; ?>

<ul class="nav nav-tabs mb-3">
  <?php foreach ($tabs as $key => $t): ?>
    <li class="nav-item">
      <a class="nav-link <?= $key === $currentTab ? 'active' : '' ?>"
         href="<?= Helpers::url('/base?tab='.$key) ?>">
        <i class="bi <?= $t['icon'] ?> me-1"></i><?= Helpers::e($t['label']) ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <strong><i class="bi <?= $cfg['icon'] ?> me-1"></i><?= Helpers::e($cfg['label']) ?></strong>
    <span class="badge bg-secondary"><?= count($items) ?></span>
  </div>
  <div class="card-body">
    <?php if (!$items): ?>
      <div class="text-center text-muted py-4">No hay registros. Crea el primero.</div>
    <?php else: ?>
    <div class="table-responsive">
      <table id="tblBase" class="table table-striped table-hover align-middle w-100">
        <thead class="table-light">
          <tr>
            <th><?= $labelField ?></th>
            <?php if ($needsEmpresa): ?><th>Empresa</th><?php endif; ?>
            <th style="width:140px" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td class="fw-semibold"><?= Helpers::e($it[$field] ?? '') ?></td>
            <?php if ($needsEmpresa): ?>
              <td><?= Helpers::e($empresasMap[$it['empresa_id'] ?? ''] ?? '-') ?></td>
            <?php endif; ?>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary btn-edit"
                      data-id="<?= Helpers::e($it['id']) ?>"
                      data-val="<?= Helpers::e($it[$field] ?? '') ?>"
                      data-empresa="<?= Helpers::e($it['empresa_id'] ?? '') ?>"
                      data-bs-toggle="modal" data-bs-target="#mBaseEditar">
                <i class="bi bi-pencil"></i>
              </button>
              <form method="post"
                    action="<?= Helpers::url('/base/'.$it['id'].'/eliminar') ?>"
                    class="d-inline" onsubmit="return confirm('¿Eliminar este registro?');">
                <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                <input type="hidden" name="_tab" value="<?= Helpers::e($currentTab) ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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

<!-- Modal Nuevo -->
<div class="modal fade" id="mBaseNuevo" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="<?= Helpers::url('/base') ?>">
      <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
      <input type="hidden" name="_tab"  value="<?= Helpers::e($currentTab) ?>">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo <?= Helpers::e($singular) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label"><?= $labelField ?></label>
          <input type="text" name="<?= $field ?>" class="form-control" required
                 placeholder="<?= $field === 'nivel' ? 'Ej: Alto, Medio, Bajo' : 'Nombre' ?>">
        </div>
        <?php if ($needsEmpresa): ?>
        <div class="mb-3">
          <label class="form-label">Empresa</label>
          <select name="empresa_id" class="form-select" required>
            <option value="">Seleccionar empresa</option>
            <?php foreach ($empresas as $e): ?>
              <option value="<?= Helpers::e($e['id']) ?>"><?= Helpers::e($e['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Crear</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="mBaseEditar" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" id="formBaseEditar">
      <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
      <input type="hidden" name="_tab"  value="<?= Helpers::e($currentTab) ?>">
      <div class="modal-header">
        <h5 class="modal-title">Editar <?= Helpers::e($singular) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label"><?= $labelField ?></label>
          <input type="text" name="<?= $field ?>" id="editVal" class="form-control" required>
        </div>
        <?php if ($needsEmpresa): ?>
        <div class="mb-3">
          <label class="form-label">Empresa</label>
          <select name="empresa_id" id="editEmpresa" class="form-select" required>
            <option value="">Seleccionar empresa</option>
            <?php foreach ($empresas as $e): ?>
              <option value="<?= Helpers::e($e['id']) ?>"><?= Helpers::e($e['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  const baseUrl = <?= json_encode(Helpers::url('/base/')) ?>;
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      document.getElementById('formBaseEditar').action = baseUrl + id;
      document.getElementById('editVal').value = btn.dataset.val || '';
      const emp = document.getElementById('editEmpresa');
      if (emp) emp.value = btn.dataset.empresa || '';
    });
  });
  if (window.jQuery && jQuery.fn.DataTable) {
    jQuery('#tblBase').DataTable({
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      pageLength: 25, order: [[0,'asc']]
    });
  }
})();
</script>
