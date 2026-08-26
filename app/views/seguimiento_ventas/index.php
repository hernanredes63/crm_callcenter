<?php
/** @var array $leads */
/** @var array $stats */
/** @var string $desde */
/** @var string $hasta */
/** @var string $estado */
/** @var bool $canManage */

$estadoLabels = [
  'pendiente' => ['Pendiente', 'warning',   'bi-clock'],
  'visita'    => ['Visita',    'primary',   'bi-eye'],
  'venta'     => ['Venta',     'success',   'bi-cart-check'],
  'perdido'   => ['Perdido',   'danger',    'bi-x-circle'],
];
$estFiltros = ['todos'=>'Todos'] + array_map(fn($e)=>$e[0], $estadoLabels);
?>
<div class="d-flex align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Seguimiento de Ventas</h4>
</div>

<?php if (!empty($_SESSION['flash_ok'])): ?>
  <div class="alert alert-success"><?= Helpers::e($_SESSION['flash_ok']) ?></div>
  <?php unset($_SESSION['flash_ok']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger"><?= Helpers::e($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small mb-1">Desde</label>
        <input type="date" name="desde" value="<?= Helpers::e($desde) ?>" class="form-control form-control-sm">
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Hasta</label>
        <input type="date" name="hasta" value="<?= Helpers::e($hasta) ?>" class="form-control form-control-sm">
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1">Estado</label>
        <select name="estado" class="form-select form-select-sm">
          <?php foreach ($estFiltros as $k=>$lab): ?>
            <option value="<?= Helpers::e($k) ?>" <?= $estado===$k?'selected':'' ?>><?= Helpers::e($lab) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filtrar</button>
        <a href="<?= Helpers::url('/seguimiento-ventas') ?>" class="btn btn-outline-secondary btn-sm">Limpiar</a>
      </div>
    </form>
  </div>
</div>

<div class="row g-2 mb-3">
  <?php foreach ([
    ['Total',      $stats['total'],     'secondary','bi-funnel'],
    ['Pendientes', $stats['pendiente'], 'warning',  'bi-clock'],
    ['Visitas',    $stats['visita'],    'primary',  'bi-eye'],
    ['Ventas',     $stats['venta'],     'success',  'bi-cart-check'],
    ['Perdidos',   $stats['perdido'],   'danger',   'bi-x-circle'],
  ] as $c): ?>
    <div class="col-6 col-md">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body py-2">
          <div class="text-muted small"><i class="bi <?= $c[3] ?> me-1"></i><?= $c[0] ?></div>
          <div class="fs-4 fw-bold text-<?= $c[2] ?>"><?= (int)$c[1] ?></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if (!$leads): ?>
  <div class="alert alert-info mb-0">No hay leads derivados para mostrar con los filtros actuales.</div>
<?php else: ?>
  <div class="d-flex flex-column gap-3">
    <?php foreach ($leads as $l):
        $ult = $l['ultimo_estado'] ?? 'pendiente';
        $cfg = $estadoLabels[$ult] ?? $estadoLabels['pendiente'];
    ?>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
          <div class="flex-grow-1" style="min-width:260px">
            <div class="d-flex align-items-center gap-2 mb-1">
              <h6 class="mb-0"><?= Helpers::e($l['nombre']) ?></h6>
              <span class="badge bg-<?= $cfg[1] ?>"><i class="bi <?= $cfg[2] ?> me-1"></i><?= $cfg[0] ?></span>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-1 small text-muted">
              <?php if ($l['telefono']): ?><div>📞 <?= Helpers::e($l['telefono']) ?></div><?php endif; ?>
              <?php if ($l['email']):    ?><div>✉️ <?= Helpers::e($l['email']) ?></div><?php endif; ?>
              <?php if ($l['ciudad']):   ?><div>📍 <?= Helpers::e($l['ciudad']) ?></div><?php endif; ?>
              <?php if (!empty($l['empresa_nombre'])):  ?><div>🏢 <?= Helpers::e($l['empresa_nombre']) ?></div><?php endif; ?>
              <?php if (!empty($l['proyecto_nombre'])): ?><div>📋 <?= Helpers::e($l['proyecto_nombre']) ?></div><?php endif; ?>
              <?php if (!empty($l['campana_nombre'])):  ?><div>📢 <?= Helpers::e($l['campana_nombre']) ?></div><?php endif; ?>
              <?php if (!empty($l['vendedor_nombre'])): ?><div>👤 <?= Helpers::e($l['vendedor_nombre']) ?></div><?php endif; ?>
              <?php if (!empty($l['fecha_derivado_ventas'])): ?>
                <div>📅 Derivado: <?= Helpers::e(date('d/m/Y', strtotime($l['fecha_derivado_ventas']))) ?></div>
              <?php endif; ?>
            </div>

            <?php if (!empty($l['seguimientos'])): ?>
              <div class="mt-3">
                <div class="small fw-semibold text-muted mb-1">
                  <i class="bi bi-chat-square-text me-1"></i>Historial de seguimiento
                </div>
                <?php foreach ($l['seguimientos'] as $s):
                    $sc = $estadoLabels[$s['estado']] ?? $estadoLabels['pendiente'];
                ?>
                  <div class="d-flex gap-2 bg-light rounded p-2 mb-1 small">
                    <span class="badge bg-<?= $sc[1] ?> align-self-start"><?= $sc[0] ?></span>
                    <div class="flex-grow-1">
                      <?php if (!empty($s['comentario'])): ?>
                        <div><?= nl2br(Helpers::e($s['comentario'])) ?></div>
                      <?php endif; ?>
                      <div class="text-muted">
                        <?= Helpers::e(date('d/m/Y H:i', strtotime($s['created_at']))) ?>
                        <?php if (!empty($s['fecha_visita'])): ?> · Visita: <?= Helpers::e($s['fecha_visita']) ?><?php endif; ?>
                        <?php if (!empty($s['fecha_venta'])):  ?> · Venta: <?= Helpers::e($s['fecha_venta'])  ?><?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($canManage): ?>
          <div style="min-width:260px">
            <button class="btn btn-sm btn-primary" type="button"
                    data-bs-toggle="collapse" data-bs-target="#form-<?= Helpers::e($l['id']) ?>">
              <i class="bi bi-check2-circle me-1"></i>Registrar
            </button>
            <div class="collapse mt-2" id="form-<?= Helpers::e($l['id']) ?>">
              <form method="post"
                    action="<?= Helpers::url('/seguimiento-ventas/' . $l['id']) ?>"
                    class="border rounded p-2 bg-light sv-form">
                <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                <div class="mb-2">
                  <select name="estado" class="form-select form-select-sm sv-estado" required>
                    <option value="">Estado…</option>
                    <option value="visita">Visita</option>
                    <option value="venta">Venta</option>
                    <option value="perdido">Perdido</option>
                    <option value="pendiente">Pendiente</option>
                  </select>
                </div>
                <div class="mb-2 sv-fecha-visita d-none">
                  <label class="form-label small mb-1">Fecha visita</label>
                  <input type="date" name="fecha_visita" class="form-control form-control-sm">
                </div>
                <div class="mb-2 sv-fecha-venta d-none">
                  <label class="form-label small mb-1">Fecha venta</label>
                  <input type="date" name="fecha_venta" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                  <textarea name="comentario" rows="2" class="form-control form-control-sm"
                            placeholder="Comentario (opcional)"></textarea>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-success flex-grow-1"><i class="bi bi-save"></i> Guardar</button>
                </div>
              </form>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<script>
document.querySelectorAll('.sv-form').forEach(f => {
  const sel = f.querySelector('.sv-estado');
  const vis = f.querySelector('.sv-fecha-visita');
  const ven = f.querySelector('.sv-fecha-venta');
  sel.addEventListener('change', () => {
    vis.classList.toggle('d-none', sel.value !== 'visita');
    ven.classList.toggle('d-none', sel.value !== 'venta');
  });
});
</script>
