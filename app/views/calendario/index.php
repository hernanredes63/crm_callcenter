<?php
/** @var string $monthParam */
/** @var DateTime $first */
/** @var array $byDate */
/** @var array $todayTasks */
/** @var array $overdueTasks */
/** @var array $agentes */
/** @var string $agenteFilter */
/** @var bool $isAdminSup */

$mesesEs = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$diasEs  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

$year  = (int)$first->format('Y');
$month = (int)$first->format('n');
$daysInMonth = (int)$first->format('t');
$firstDow    = (int)$first->format('w'); // 0=Dom

$prev = (clone $first)->modify('-1 month')->format('Y-m');
$next = (clone $first)->modify('+1 month')->format('Y-m');
$curr = date('Y-m');
$today = date('Y-m-d');

$qs = function(array $extra) use ($agenteFilter) {
    $base = ['agente' => $agenteFilter];
    return http_build_query(array_merge($base, $extra));
};
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
  <div>
    <h3 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Calendario</h3>
    <small class="text-muted">Seguimientos programados</small>
  </div>
  <?php if ($isAdminSup): ?>
    <form method="get" class="d-flex gap-2">
      <input type="hidden" name="month" value="<?= Helpers::e($monthParam) ?>">
      <select name="agente" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="all" <?= $agenteFilter==='all'?'selected':'' ?>>Todos los agentes</option>
        <?php foreach ($agentes as $a): ?>
          <option value="<?= Helpers::e($a['id']) ?>" <?= $agenteFilter===$a['id']?'selected':'' ?>>
            <?= Helpers::e($a['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  <?php endif; ?>
</div>

<div class="row g-3 mb-3">
  <?php if (!empty($todayTasks)): ?>
    <div class="col-md-6">
      <div class="card border-primary">
        <div class="card-body">
          <h6 class="card-title text-primary mb-2">
            <i class="bi bi-bell me-1"></i>Hoy tienes <?= count($todayTasks) ?> seguimiento(s)
          </h6>
          <ul class="list-unstyled mb-0 small">
            <?php foreach (array_slice($todayTasks, 0, 5) as $t): ?>
              <li><i class="bi bi-telephone me-1"></i><?= Helpers::e($t['lead_nombre']) ?>
                <span class="text-muted">— <?= Helpers::e(substr($t['fecha_proximo_contacto'], 11, 5)) ?></span>
              </li>
            <?php endforeach; ?>
            <?php if (count($todayTasks) > 5): ?>
              <li class="text-muted">+<?= count($todayTasks)-5 ?> más...</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <?php if (!empty($overdueTasks)): ?>
    <div class="col-md-6">
      <div class="card border-danger">
        <div class="card-body">
          <h6 class="card-title text-danger mb-2">
            <i class="bi bi-exclamation-triangle me-1"></i><?= count($overdueTasks) ?> seguimiento(s) atrasado(s)
          </h6>
          <ul class="list-unstyled mb-0 small">
            <?php foreach (array_slice($overdueTasks, 0, 5) as $t): ?>
              <li><i class="bi bi-telephone me-1"></i><?= Helpers::e($t['lead_nombre']) ?>
                <span class="text-muted">— <?= Helpers::e(substr($t['fecha_proximo_contacto'], 0, 16)) ?></span>
              </li>
            <?php endforeach; ?>
            <?php if (count($overdueTasks) > 5): ?>
              <li class="text-muted">+<?= count($overdueTasks)-5 ?> más...</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <strong><?= $mesesEs[$month] ?> <?= $year ?></strong>
    <div class="btn-group btn-group-sm">
      <a class="btn btn-outline-secondary" href="?<?= $qs(['month'=>$prev]) ?>"><i class="bi bi-chevron-left"></i></a>
      <a class="btn btn-outline-secondary" href="?<?= $qs(['month'=>$curr]) ?>">Hoy</a>
      <a class="btn btn-outline-secondary" href="?<?= $qs(['month'=>$next]) ?>"><i class="bi bi-chevron-right"></i></a>
    </div>
  </div>
  <div class="card-body">
    <div class="cal-grid">
      <?php foreach ($diasEs as $d): ?>
        <div class="cal-head"><?= $d ?></div>
      <?php endforeach; ?>
      <?php for ($i = 0; $i < $firstDow; $i++): ?>
        <div class="cal-cell empty"></div>
      <?php endfor; ?>
      <?php for ($day = 1; $day <= $daysInMonth; $day++):
        $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $items = $byDate[$dateKey] ?? [];
        $isToday = $dateKey === $today;
      ?>
        <div class="cal-cell <?= $isToday ? 'today' : '' ?>">
          <div class="cal-day"><?= $day ?></div>
          <?php foreach (array_slice($items, 0, 3) as $it): ?>
            <div class="cal-event" title="<?= Helpers::e($it['lead_nombre']) ?> · Iter <?= (int)$it['iteracion'] ?>">
              <span class="cal-time"><?= Helpers::e(substr($it['fecha_proximo_contacto'], 11, 5)) ?></span>
              <span class="cal-name"><?= Helpers::e($it['lead_nombre']) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (count($items) > 3): ?>
            <div class="cal-more">+<?= count($items)-3 ?> más</div>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header"><strong>Detalle del mes</strong></div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead class="table-light">
          <tr>
            <th>Fecha</th><th>Lead</th><th>Teléfono</th><th>Operador</th>
            <th class="text-center">Iter.</th><th>Estado</th><th>Comentario</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $flat = [];
          foreach ($byDate as $arr) foreach ($arr as $r) $flat[] = $r;
          usort($flat, fn($a,$b)=>strcmp($a['fecha_proximo_contacto'],$b['fecha_proximo_contacto']));
        ?>
        <?php if (empty($flat)): ?>
          <tr><td colspan="7" class="text-center text-muted py-3">Sin seguimientos programados este mes.</td></tr>
        <?php else: foreach ($flat as $r): ?>
          <tr>
            <td><?= Helpers::e(substr($r['fecha_proximo_contacto'], 0, 16)) ?></td>
            <td><?= Helpers::e($r['lead_nombre']) ?></td>
            <td>
              <?php if (!empty($r['lead_telefono'])): ?>
                <a href="tel:<?= Helpers::e($r['lead_telefono']) ?>"><?= Helpers::e($r['lead_telefono']) ?></a>
              <?php else: ?><span class="text-muted">-</span><?php endif; ?>
            </td>
            <td><?= Helpers::e($r['operador_nombre'] ?? '-') ?></td>
            <td class="text-center"><?= (int)$r['iteracion'] ?></td>
            <td><?= Helpers::e($r['estado']) ?></td>
            <td class="text-muted small"><?= Helpers::e($r['comentario'] ?? '') ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
.cal-head { text-align:center; font-weight:600; color:#6c757d; padding:6px 0; font-size:.85rem; }
.cal-cell { min-height:100px; border:1px solid #e9ecef; border-radius:6px; padding:4px; background:#fff; display:flex; flex-direction:column; gap:2px; }
.cal-cell.empty { background:transparent; border:none; }
.cal-cell.today { background:#eef4ff; border-color:#0d6efd; }
.cal-day { font-weight:600; font-size:.85rem; color:#495057; }
.cal-event { font-size:.72rem; background:#e7f1ff; color:#084298; border-radius:4px; padding:2px 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cal-time { font-weight:600; margin-right:4px; }
.cal-more { font-size:.7rem; color:#6c757d; }
</style>
