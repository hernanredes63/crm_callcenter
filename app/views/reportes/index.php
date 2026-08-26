<?php
/** @var array $leads @var array $leadsRedes @var array $seguimientos @var array $empresas */
$hoy = date('Y-m-d');
$mesIni = date('Y-m-01');
?>
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Reportes</h4>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">
      <div class="col-sm-3">
        <label class="form-label small mb-1">Desde</label>
        <input type="date" id="fDesde" class="form-control form-control-sm" value="<?= $mesIni ?>">
      </div>
      <div class="col-sm-3">
        <label class="form-label small mb-1">Hasta</label>
        <input type="date" id="fHasta" class="form-control form-control-sm" value="<?= $hoy ?>">
      </div>
      <div class="col-sm-3">
        <label class="form-label small mb-1">Empresa</label>
        <select id="fEmpresa" class="form-select form-select-sm">
          <option value="">Todas</option>
          <?php foreach ($empresas as $e): ?>
            <option value="<?= Helpers::e($e['id']) ?>"><?= Helpers::e($e['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-3 d-flex flex-wrap gap-2">
        <button class="btn btn-sm btn-outline-secondary" data-atajo="hoy">Hoy</button>
        <button class="btn btn-sm btn-outline-secondary" data-atajo="mes">Este mes</button>
        <button class="btn btn-sm btn-outline-secondary" data-atajo="mes_anterior">Mes anterior</button>
        <button class="btn btn-sm btn-outline-secondary" data-atajo="todo">Todo</button>
      </div>
      <div class="col-12">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="swDerivFecha">
          <label class="form-check-label small" for="swDerivFecha">
            Contar derivados por <strong>fecha de derivación</strong> (en lugar de fecha de lead)
          </label>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Totales -->
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
    <div class="text-muted small">Total Leads</div>
    <div class="display-6 fw-bold text-primary" id="kpiLeads">0</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
    <div class="text-muted small">Derivados</div>
    <div class="display-6 fw-bold text-success" id="kpiDeriv">0</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
    <div class="text-muted small">Leads Redes</div>
    <div class="display-6 fw-bold text-info" id="kpiRedes">0</div>
  </div></div></div>
  <div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
    <div class="text-muted small">Contactabilidad</div>
    <div class="display-6 fw-bold text-warning"><span id="kpiContact">0</span>%</div>
    <div class="small text-muted">leads con al menos 1 seguimiento</div>
  </div></div></div>
</div>

<div class="row g-3">
  <div class="col-lg-6"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Leads por operador</strong></div>
    <div class="card-body"><canvas id="chOperador" height="180"></canvas></div>
  </div></div>
  <div class="col-lg-6"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Leads por empresa</strong></div>
    <div class="card-body"><canvas id="chEmpresa" height="180"></canvas></div>
  </div></div>
  <div class="col-lg-6"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Leads por campaña</strong></div>
    <div class="card-body"><canvas id="chCampana" height="180"></canvas></div>
  </div></div>
  <div class="col-lg-6"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Leads por canal</strong></div>
    <div class="card-body"><canvas id="chCanal" height="180"></canvas></div>
  </div></div>
  <div class="col-lg-4"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Temperatura</strong></div>
    <div class="card-body"><canvas id="chTemp" height="200"></canvas></div>
  </div></div>
  <div class="col-lg-4"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>MQL</strong></div>
    <div class="card-body"><canvas id="chMql" height="200"></canvas></div>
  </div></div>
  <div class="col-lg-4"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Contactabilidad por N° seguimiento</strong></div>
    <div class="card-body"><canvas id="chContact" height="200"></canvas></div>
  </div></div>

  <div class="col-lg-6"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Derivados por agente</strong>
      <small class="text-muted">(fecha de derivación)</small></div>
    <div class="card-body"><canvas id="chDeriv" height="180"></canvas></div>
  </div></div>
  <div class="col-lg-6"><div class="card shadow-sm h-100">
    <div class="card-header bg-white"><strong>Leads Redes por operador</strong></div>
    <div class="card-body"><canvas id="chRedes" height="180"></canvas></div>
  </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const LEADS = <?= json_encode(array_map(fn($l)=>[
    'empresa_id'=>$l['empresa_id'],
    'empresa'=>$l['empresa_nombre'] ?? 'Sin empresa',
    'campana'=>$l['campana_nombre'] ?? 'Sin campaña',
    'canal'=>$l['canal_nombre'] ?? 'Sin canal',
    'mql'=>$l['mql_nivel'] ?? 'Sin MQL',
    'operador'=>$l['operador_nombre'] ?? 'Sin asignar',
    'vendedor'=>$l['vendedor_tabla_nombre'] ?? 'Sin asignar',
    'temperatura'=>$l['temperatura'],
    'fecha_lead'=>$l['fecha_lead'],
    'derivado'=>(int)$l['derivado_ventas'],
    'fecha_deriv'=>$l['fecha_derivado_ventas'],
    'id'=>$l['id'],
], $leads), JSON_UNESCAPED_UNICODE) ?>;

const REDES = <?= json_encode(array_map(fn($l)=>[
    'empresa_id'=>$l['empresa_id'],
    'operador'=>$l['operador_nombre'] ?? 'Sin asignar',
    'fecha_lead'=>$l['fecha_lead'],
], $leadsRedes), JSON_UNESCAPED_UNICODE) ?>;

const SEGS = <?= json_encode($seguimientos, JSON_UNESCAPED_UNICODE) ?>;

const charts = {};
function drawBar(id, labels, data, color='rgba(13,110,253,0.7)'){
  const el = document.getElementById(id);
  if (charts[id]) charts[id].destroy();
  charts[id] = new Chart(el, {
    type:'bar',
    data:{ labels, datasets:[{ data, backgroundColor:color }] },
    options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, ticks:{precision:0}}} }
  });
}
function drawPie(id, labels, data){
  const el = document.getElementById(id);
  if (charts[id]) charts[id].destroy();
  const colors = ['#6f42c1','#0d6efd','#20c997','#fd7e14','#dc3545','#d63384','#ffc107','#198754'];
  charts[id] = new Chart(el, {
    type:'doughnut',
    data:{ labels, datasets:[{ data, backgroundColor: labels.map((_,i)=>colors[i%colors.length]) }] },
    options:{ plugins:{legend:{position:'bottom'}} }
  });
}
function grpCount(arr, key){
  const m={}; arr.forEach(x => { const k = x[key]||'Sin dato'; m[k]=(m[k]||0)+1; });
  return Object.entries(m).sort((a,b)=>b[1]-a[1]);
}
function inRango(fechaStr, dMs, hMs){
  if (!fechaStr) return false;
  const t = new Date(fechaStr+'T00:00:00-04:00').getTime();
  if (dMs!==null && t<dMs) return false;
  if (hMs!==null && t>hMs) return false;
  return true;
}

function recalcular(){
  const desde = $('#fDesde').val();
  const hasta = $('#fHasta').val();
  const emp = $('#fEmpresa').val();
  const porFechaDeriv = $('#swDerivFecha').is(':checked');
  const dMs = desde ? new Date(desde+'T00:00:00-04:00').getTime() : null;
  const hMs = hasta ? new Date(hasta+'T23:59:59-04:00').getTime() : null;

  const filterEmp = x => !emp || x.empresa_id === emp;

  // Leads (por fecha de lead)
  const leadsF = LEADS.filter(l => filterEmp(l) && inRango(l.fecha_lead, dMs, hMs));
  // Derivados: si switch => por fecha_deriv, si no => de los leads del rango
  const derivF = porFechaDeriv
    ? LEADS.filter(l => filterEmp(l) && l.derivado===1 && l.fecha_deriv && (() => {
        const t = new Date(l.fecha_deriv).getTime();
        return (dMs===null||t>=dMs) && (hMs===null||t<=hMs);
      })())
    : leadsF.filter(l => l.derivado===1);

  const redesF = REDES.filter(r => filterEmp(r) && inRango(r.fecha_lead, dMs, hMs));

  // KPIs
  $('#kpiLeads').text(leadsF.length);
  $('#kpiDeriv').text(derivF.length);
  $('#kpiRedes').text(redesF.length);

  const idsF = new Set(leadsF.map(l=>l.id));
  const conSeg = new Set(SEGS.filter(s=>idsF.has(s.lead_id)).map(s=>s.lead_id));
  const pct = leadsF.length ? Math.round(conSeg.size*100/leadsF.length) : 0;
  $('#kpiContact').text(pct);

  // Charts
  const op = grpCount(leadsF,'operador').slice(0,15);
  drawBar('chOperador', op.map(x=>x[0]), op.map(x=>x[1]));

  const em = grpCount(leadsF,'empresa').slice(0,15);
  drawBar('chEmpresa', em.map(x=>x[0]), em.map(x=>x[1]), 'rgba(111,66,193,0.7)');

  const cm = grpCount(leadsF,'campana').slice(0,15);
  drawBar('chCampana', cm.map(x=>x[0]), cm.map(x=>x[1]), 'rgba(32,201,151,0.7)');

  const cn = grpCount(leadsF,'canal').slice(0,15);
  drawBar('chCanal', cn.map(x=>x[0]), cn.map(x=>x[1]), 'rgba(253,126,20,0.7)');

  const tp = grpCount(leadsF,'temperatura');
  drawPie('chTemp', tp.map(x=>x[0]), tp.map(x=>x[1]));

  const mq = grpCount(leadsF,'mql');
  drawPie('chMql', mq.map(x=>x[0]), mq.map(x=>x[1]));

  // Contactabilidad por N° seguimiento (de los leads del rango)
  const conteo = {1:0,2:0,3:0,4:0,5:0,6:0,7:0};
  SEGS.forEach(s => { if (idsF.has(s.lead_id) && conteo[s.iteracion]!==undefined) conteo[s.iteracion]++; });
  drawBar('chContact',
    ['S1','S2','S3','S4','S5','S6','S7'],
    [1,2,3,4,5,6,7].map(i=>conteo[i]),
    'rgba(255,193,7,0.8)');

  const dv = grpCount(derivF,'vendedor').slice(0,15);
  drawBar('chDeriv', dv.map(x=>x[0]), dv.map(x=>x[1]), 'rgba(25,135,84,0.7)');

  const ro = grpCount(redesF,'operador').slice(0,15);
  drawBar('chRedes', ro.map(x=>x[0]), ro.map(x=>x[1]), 'rgba(13,202,240,0.7)');
}

$(function(){
  $('#fDesde, #fHasta, #fEmpresa, #swDerivFecha').on('change', recalcular);
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
