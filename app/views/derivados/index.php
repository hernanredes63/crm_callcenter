<?php /** @var array $derivados */
$hoy = date('Y-m-d');
$mesIni = date('Y-m-01');
?>
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-arrow-right-circle me-2"></i>Derivados
    <span class="badge bg-secondary ms-2"><?= count($derivados) ?></span>
  </h4>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header bg-white">
    <strong><i class="bi bi-people me-1"></i> Derivados por agente</strong>
    <small class="text-muted">(según fecha de derivación)</small>
  </div>
  <div class="card-body">
    <div class="row g-2 align-items-end mb-3">
      <div class="col-sm-3">
        <label class="form-label small mb-1">Desde</label>
        <input type="date" id="fDesde" class="form-control form-control-sm" value="<?= $mesIni ?>">
      </div>
      <div class="col-sm-3">
        <label class="form-label small mb-1">Hasta</label>
        <input type="date" id="fHasta" class="form-control form-control-sm" value="<?= $hoy ?>">
      </div>
      <div class="col-sm-6 d-flex flex-wrap gap-2">
        <button class="btn btn-sm btn-outline-secondary" data-atajo="hoy">Hoy</button>
        <button class="btn btn-sm btn-outline-secondary" data-atajo="mes">Este mes</button>
        <button class="btn btn-sm btn-outline-secondary" data-atajo="mes_anterior">Mes anterior</button>
        <button class="btn btn-sm btn-outline-secondary" data-atajo="todo">Todo</button>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="border rounded p-3 h-100">
          <div class="text-muted small">Total derivados en el rango</div>
          <div class="display-5 fw-bold text-primary" id="totalRango">0</div>
          <div class="small text-muted" id="rangoLbl"></div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="border rounded p-3">
          <canvas id="chartAgentes" height="120"></canvas>
        </div>
      </div>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-sm table-striped mb-0" id="tblAgentes">
        <thead class="table-light">
          <tr><th>Agente</th><th class="text-end">Derivados</th></tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblDerivados" class="table table-striped table-hover align-middle w-100">
        <thead class="table-light">
          <tr>
            <th>Fecha derivación</th>
            <th>Línea</th>
            <th>Nombre</th>
            <th>Empresa</th>
            <th>Campaña</th>
            <th>Gestionado por</th>
            <th>Derivado a</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($derivados as $d): ?>
          <tr>
            <td><?= Helpers::e($d['fecha_derivado_ventas'] ?? '') ?></td>
            <td><?php if ($d['telefono']): ?>
              <a href="tel:<?= Helpers::e($d['telefono']) ?>"><?= Helpers::e($d['telefono']) ?></a>
            <?php endif; ?></td>
            <td><?= Helpers::e($d['nombre']) ?></td>
            <td><?= Helpers::e($d['empresa_nombre'] ?? '') ?></td>
            <td><?= Helpers::e($d['campana_nombre'] ?? '') ?></td>
            <td><?= Helpers::e($d['operador_nombre'] ?? '-') ?></td>
            <td><?= Helpers::e($d['vendedor_tabla_nombre'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const DERIVADOS = <?= json_encode(array_map(fn($d)=>[
  'nombre'=>$d['nombre'],
  'telefono'=>$d['telefono'],
  'operador'=>$d['operador_nombre'] ?? 'Sin asignar',
  'fecha'=>$d['fecha_derivado_ventas'],
], $derivados), JSON_UNESCAPED_UNICODE) ?>;

$(function(){
  $('#tblDerivados').DataTable({
    pageLength:25, order:[[0,'desc']],
    language:{ url:'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' }
  });

  let chart = null;

  function recalcular(){
    const desde = $('#fDesde').val();
    const hasta = $('#fHasta').val();
    const dMs = desde ? new Date(desde+'T00:00:00-04:00').getTime() : null;
    const hMs = hasta ? new Date(hasta+'T23:59:59-04:00').getTime() : null;

    const enRango = DERIVADOS.filter(d => {
      if (!d.fecha) return false;
      const t = new Date(d.fecha).getTime();
      if (dMs !== null && t < dMs) return false;
      if (hMs !== null && t > hMs) return false;
      return true;
    });

    $('#totalRango').text(enRango.length);
    $('#rangoLbl').text((desde||'—') + ' → ' + (hasta||'—'));

    const map = {};
    enRango.forEach(d => { map[d.operador] = (map[d.operador]||0) + 1; });
    const arr = Object.entries(map).map(([n,t])=>({nombre:n,total:t}))
                      .sort((a,b)=>b.total-a.total);

    const tb = $('#tblAgentes tbody').empty();
    if (!arr.length) tb.append('<tr><td colspan="2" class="text-center text-muted">Sin derivaciones en el rango</td></tr>');
    arr.forEach(r => tb.append(`<tr><td>${r.nombre}</td><td class="text-end fw-bold">${r.total}</td></tr>`));

    const ctx = document.getElementById('chartAgentes');
    if (chart) chart.destroy();
    chart = new Chart(ctx, {
      type:'bar',
      data:{ labels:arr.map(r=>r.nombre), datasets:[{ label:'Derivados', data:arr.map(r=>r.total), backgroundColor:'rgba(13,110,253,0.7)' }] },
      options:{ scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } }, plugins:{ legend:{ display:false } } }
    });
  }

  $('#fDesde, #fHasta').on('change', recalcular);
  $('[data-atajo]').on('click', function(){
    const t = $(this).data('atajo');
    const hoy = new Date(); const iso = d => d.toISOString().slice(0,10);
    if (t==='hoy'){ $('#fDesde').val(iso(hoy)); $('#fHasta').val(iso(hoy)); }
    else if (t==='mes'){ $('#fDesde').val(iso(new Date(hoy.getFullYear(),hoy.getMonth(),1))); $('#fHasta').val(iso(hoy)); }
    else if (t==='mes_anterior'){
      $('#fDesde').val(iso(new Date(hoy.getFullYear(),hoy.getMonth()-1,1)));
      $('#fHasta').val(iso(new Date(hoy.getFullYear(),hoy.getMonth(),0)));
    } else { $('#fDesde').val(''); $('#fHasta').val(''); }
    recalcular();
  });

  recalcular();
});
</script>
