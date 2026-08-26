<?php
/** @var array $rows @var array $tot @var int $meta @var string $desde @var string $hasta */
$initials = function(string $n): string {
    $parts = preg_split('/\s+/', trim($n)) ?: [];
    $ini = '';
    foreach ($parts as $p) { if ($p !== '') $ini .= mb_substr($p, 0, 1); }
    return mb_strtoupper(mb_substr($ini, 0, 2));
};
?>
<div class="rounded-4 p-4 mb-4 text-white" style="background:linear-gradient(90deg,#6a2ea8,#8a4bbf);">
  <h4 class="mb-1"><i class="bi bi-cart-check me-2"></i>Resumen del Equipo</h4>
  <div class="small opacity-75 mb-3">Rendimiento general de ventas</div>

  <form method="get" class="row g-2 align-items-end mb-3">
    <div class="col-sm-3">
      <label class="form-label small mb-1">Desde</label>
      <input type="date" name="desde" value="<?= Helpers::e($desde) ?>" class="form-control form-control-sm">
    </div>
    <div class="col-sm-3">
      <label class="form-label small mb-1">Hasta</label>
      <input type="date" name="hasta" value="<?= Helpers::e($hasta) ?>" class="form-control form-control-sm">
    </div>
    <div class="col-sm-6 d-flex gap-2">
      <button class="btn btn-sm btn-light">Filtrar</button>
      <?php if ($desde || $hasta): ?>
        <a class="btn btn-sm btn-outline-light" href="<?= Helpers::url('/ventas') ?>">Limpiar</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="row g-3">
    <?php
      $cards = [
        ['Total Leads',        $tot['leads'],       'people'],
        ['Visitas Realizadas', $tot['visitas'],     'eye'],
        ['En Negociación',     $tot['negociacion'], 'handshake'],
        ['Ventas Cerradas',    $tot['ventas'],      'graph-up'],
      ];
      foreach ($cards as [$l,$v,$ic]): ?>
      <div class="col-6 col-md-3">
        <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.15);">
          <div class="small opacity-75"><i class="bi bi-<?= $ic ?> me-1"></i><?= $l ?></div>
          <div class="fs-3 fw-bold"><?= (int)$v ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php if (!$rows): ?>
  <div class="text-center text-muted py-5">No hay vendedores asignados para mostrar.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($rows as $r):
      $pct = $meta>0 ? min(100, round($r['ventas']/$meta*100)) : 0; ?>
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
              <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center"
                   style="width:44px;height:44px;"><?= Helpers::e($initials($r['nombre'])) ?></div>
              <div>
                <div class="fw-semibold"><?= Helpers::e($r['nombre']) ?></div>
                <div class="small text-muted">Ejecutivo de Ventas</div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-baseline mb-1">
              <span class="fs-4 fw-bold"><?= (int)$r['ventas'] ?></span>
              <small class="text-muted">Meta: <?= (int)$meta ?></small>
            </div>
            <div class="progress mb-3" style="height:8px;">
              <div class="progress-bar bg-primary" style="width:<?= $pct ?>%;"></div>
            </div>

            <div class="row g-2 mb-3 text-center">
              <div class="col-6"><div class="bg-light rounded-2 p-2">
                <div class="small text-muted">Leads</div><div class="fw-bold"><?= (int)$r['total'] ?></div>
              </div></div>
              <div class="col-6"><div class="bg-light rounded-2 p-2">
                <div class="small text-muted">Visitas</div><div class="fw-bold"><?= (int)$r['visitas'] ?></div>
              </div></div>
              <div class="col-6"><div class="bg-light rounded-2 p-2">
                <div class="small text-muted">Negociación</div><div class="fw-bold"><?= (int)$r['negoc'] ?></div>
              </div></div>
              <div class="col-6"><div class="bg-light rounded-2 p-2">
                <div class="small text-muted"><i class="bi bi-graph-down"></i> Perdidos</div>
                <div class="fw-bold text-danger"><?= (int)$r['perd'] ?></div>
              </div></div>
            </div>

            <div class="border-top pt-2 small">
              <div class="d-flex justify-content-between"><span class="text-muted">Conversión</span><span class="fw-semibold"><?= (int)$r['conv'] ?>%</span></div>
              <div class="d-flex justify-content-between"><span class="text-muted">Efectividad visitas</span><span class="fw-semibold"><?= (int)$r['efVis'] ?>%</span></div>
              <div class="d-flex justify-content-between"><span class="text-muted">No realizadas</span><span class="fw-semibold"><?= (int)$r['noReal'] ?>%</span></div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
