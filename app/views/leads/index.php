<?php 
  /** @var array $leads @var Catalog $catalog @var string $search */ 

  $hoy = date('Y-m-d');
  $leadsHoy = 0;
  $gestionesHoy = 0;

  // Filtrar los leads en PHP si se envían fechas por la URL
  $desde = $_GET['desde'] ?? '';
  $hasta = $_GET['hasta'] ?? '';

  if (!empty($desde) || !empty($hasta)) {
      $leads = array_filter($leads, function($l) use ($desde, $hasta) {
          $fLead = !empty($l['fecha_lead']) ? date('Y-m-d', strtotime($l['fecha_lead'])) : '';
          if (empty($fLead)) return false;
          if (!empty($desde) && $fLead < $desde) return false;
          if (!empty($hasta) && $fLead > $hasta) return false;
          return true;
      });
  }

  if (!empty($leads) && is_array($leads)) {
      foreach ($leads as $l) {
          $fLead = !empty($l['fecha_lead']) ? date('Y-m-d', strtotime($l['fecha_lead'])) : '';
          $fCreate = !empty($l['created_at']) ? date('Y-m-d', strtotime($l['created_at'])) : '';
          if ($fLead === $hoy || $fCreate === $hoy) {
              $leadsHoy++;
          }
          if (!empty($l['seguimientos']) && is_array($l['seguimientos'])) {
              foreach ($l['seguimientos'] as $s) {
                  $fSeg = !empty($s['created_at']) ? date('Y-m-d', strtotime($s['created_at'])) : '';
                  if ($fSeg === $hoy) {
                      $gestionesHoy++;
                  }
              }
          }
      }
  }
?>

<!-- Estilos para clonar la estética de Lovable -->
<style>
  .text-lovable-purple { color: #702eb5 !important; }
  .bg-lovable-purple { background-color: #702eb5 !important; }
  .btn-lovable-purple { background-color: #702eb5; color: white; border: none; }
  .btn-lovable-purple:hover { background-color: #5b2496; color: white; }
  .btn-outline-purple { color: #702eb5; border-color: #702eb5; background-color: transparent; }
  .btn-outline-purple:hover { background-color: #702eb5; color: white; }
  
  /* Tabla limpia y con efecto hover al pasar el cursor */
  .table-lovable th { background-color: white !important; color: #1f2937; font-weight: 700; font-size: 0.9rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 1rem; }
  .table-lovable td { vertical-align: middle; font-size: 0.9rem; color: #374151; border-bottom: 1px solid #f3f4f6; padding: 0.8rem 0.5rem; }
  .table-lovable tbody tr:hover { background-color: #faf5ff !important; }
  
  /* Píldoras suaves */
  .badge-soft-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
  .badge-soft-secondary { background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }

  /* Menús Desplegables estilo Lovable */
  .form-select-lovable {
      border-radius: 8px; border: 1px solid #e5e7eb; color: #4b5563; font-size: 0.85rem; 
      background-color: #ffffff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer; padding-top: 0.35rem; padding-bottom: 0.35rem;
  }
  .form-select-lovable:focus { border-color: #702eb5; box-shadow: 0 0 0 0.2rem rgba(112, 46, 181, 0.25); }

  /* Calendario Flatpickr morado */
  .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #702eb5 !important; border-color: #702eb5 !important; }
  .flatpickr-calendar .flatpickr-prev-month, .flatpickr-calendar .flatpickr-next-month {
      background-color: #702eb5 !important; border-radius: 6px !important; top: 10px !important; height: 28px !important; width: 28px !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 0 !important;
  }
  .flatpickr-calendar .flatpickr-prev-month { left: 10px !important; }
  .flatpickr-calendar .flatpickr-next-month { right: 10px !important; }
  .flatpickr-calendar .flatpickr-prev-month svg, .flatpickr-calendar .flatpickr-next-month svg { fill: #ffffff !important; width: 14px !important; height: 14px !important; }
  .flatpickr-calendar .flatpickr-prev-month:hover, .flatpickr-calendar .flatpickr-next-month:hover { background-color: #5b2496 !important; }
</style>

<!-- 1. CABECERA -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
  <div>
    <h3 class="fw-bold mb-1 text-dark">Leads</h3>
  </div>
  
  <div class="d-flex align-items-center gap-3 flex-wrap">
    <!-- Contadores Dinámicos -->
    <div class="d-flex align-items-center bg-white border rounded px-3 py-1 shadow-sm small text-muted">
      <i class="bi bi-plus text-secondary me-2"></i> Leads hoy: <span class="fw-bold text-dark ms-2"><?= $leadsHoy ?></span>
    </div>
    <div class="d-flex align-items-center bg-white border rounded px-3 py-1 shadow-sm small text-muted">
      <i class="bi bi-telephone text-secondary me-2"></i> Gestiones hoy: <span class="fw-bold text-dark ms-2"><?= $gestionesHoy ?></span>
    </div>
    <!-- Botones de Acción -->
    <button id="btnExport" class="btn btn-white border shadow-sm small fw-semibold"><i class="bi bi-download me-2"></i> Exportar Excel</button>
    <a href="<?= Helpers::url('/leads/nuevo') ?>" class="btn btn-lovable-purple shadow-sm small fw-semibold"><i class="bi bi-plus-lg me-2"></i> Nuevo Lead</a>
  </div>
</div>

<!-- 2. BARRA DE BÚSQUEDA Y FILTROS -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
  <div class="card-body p-3">
    <form method="get" id="formFiltrosLeads">
      
      <!-- Mantener valores de temperatura, mql y derivado al enviar el formulario de fechas si es necesario -->
      <input type="hidden" name="temperatura" value="<?= Helpers::e($_GET['temperatura'] ?? '') ?>">
      <input type="hidden" name="mql" value="<?= Helpers::e($_GET['mql'] ?? '') ?>">
      <input type="hidden" name="derivado" value="<?= Helpers::e($_GET['derivado'] ?? '') ?>">

      <!-- Búsqueda Gigante -->
      <div class="input-group mb-3 border rounded-3 p-1">
        <span class="input-group-text bg-transparent border-0 text-muted px-3"><i class="bi bi-search"></i></span>
        <input type="text" id="miBuscadorPersonalizado" class="form-control border-0 shadow-none bg-transparent ps-0" placeholder="Buscar por nombre, teléfono o email..." value="<?= Helpers::e($search ?? '') ?>">
        <button type="button" class="btn border-0 text-muted <?= empty($search) ? 'd-none' : '' ?>" id="btnLimpiarBuscador" title="Limpiar"><i class="bi bi-x-circle-fill"></i></button>
      </div>

      <!-- Fila de Controles (Dropdowns + Fechas) -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        
        <!-- Bloque Izquierdo: Los 3 Menús Desplegables de Lovable -->
        <div class="d-flex flex-wrap gap-2">
          <select id="filtroTemperaturaSelect" class="form-select form-select-lovable w-auto">
            <option value="">Todas las temperaturas</option>
            <option value="caliente" <?= ($_GET['temperatura'] ?? '') === 'caliente' ? 'selected' : '' ?>>Caliente</option>
            <option value="tibio" <?= ($_GET['temperatura'] ?? '') === 'tibio' ? 'selected' : '' ?>>Tibio</option>
            <option value="frio" <?= ($_GET['temperatura'] ?? '') === 'frio' ? 'selected' : '' ?>>Frío</option>
          </select>

          <select id="filtroMQLSelect" class="form-select form-select-lovable w-auto">
            <option value="">Todos los MQL</option>
            <option value="Alto" <?= ($_GET['mql'] ?? '') === 'Alto' ? 'selected' : '' ?>>Alto</option>
            <option value="Medio" <?= ($_GET['mql'] ?? '') === 'Medio' ? 'selected' : '' ?>>Medio</option>
            <option value="Malo" <?= ($_GET['mql'] ?? '') === 'Malo' ? 'selected' : '' ?>>Malo</option>
          </select>

          <select id="filtroDerivadoSelect" class="form-select form-select-lovable w-auto">
            <option value="">Todos</option>
            <option value="Sí" <?= ($_GET['derivado'] ?? '') === 'Sí' ? 'selected' : '' ?>>Derivado a ventas</option>
            <option value="No" <?= ($_GET['derivado'] ?? '') === 'No' ? 'selected' : '' ?>>No derivado</option>
          </select>
        </div>

        <!-- Bloque Derecho: Filtros de Fecha (Flatpickr con recarga por formulario) -->
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <div class="position-relative hover-shadow rounded">
            <div class="d-flex align-items-center rounded px-3 py-1 border shadow-sm bg-white" style="cursor: pointer; height: 34px;">
              <span class="text-muted small me-2">Desde</span>
              <span class="text-dark small fw-semibold" id="label-desde"><?= !empty($_GET['desde']) ? date('d/m/Y', strtotime($_GET['desde'])) : 'dd/mm/aaaa' ?></span>
              <i class="bi bi-chevron-down text-muted ms-2 small"></i>
            </div>
            <input type="text" name="desde" id="input-desde" value="<?= Helpers::e($_GET['desde'] ?? '') ?>" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
          </div>

          <div class="position-relative hover-shadow rounded">
            <div class="d-flex align-items-center rounded px-3 py-1 border shadow-sm bg-white" style="cursor: pointer; height: 34px;">
              <span class="text-muted small me-2">Hasta</span>
              <span class="text-dark small fw-semibold" id="label-hasta"><?= !empty($_GET['hasta']) ? date('d/m/Y', strtotime($_GET['hasta'])) : 'dd/mm/aaaa' ?></span>
              <i class="bi bi-chevron-down text-muted ms-2 small"></i>
            </div>
            <input type="text" name="hasta" id="input-hasta" value="<?= Helpers::e($_GET['hasta'] ?? '') ?>" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
          </div>

          <?php if (!empty($_GET['desde']) || !empty($_GET['hasta'])): ?>
            <a href="<?= Helpers::url('/leads') ?>" class="btn btn-sm btn-outline-secondary" title="Limpiar fechas"><i class="bi bi-x-lg"></i></a>
          <?php endif; ?>
        </div>
        
      </div>
    </form>
  </div>
</div>

<!-- 3. TABLA DE LEADS -->
<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="table-responsive px-3 pb-3">
      <table id="tblLeads" class="table table-lovable table-hover align-middle w-100 mb-0">
        <thead>
          <tr>
            <th>Fecha Lead</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th class="d-none">Email</th>
            <th>Empresa</th>
            <th>Campaña</th>
            <th>Canal</th>
            <th>MQL</th>
            <th>Último Estado</th>
            <th>Operador</th>
            <th>Derivado</th>
            <th class="text-center">Acción</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $l):
          $mqlRaw = strtolower(trim($l['mql_nivel'] ?? ''));
          $mqlBadge = [
              'alto'  => 'success',
              'medio' => 'warning text-dark',
              'malo'  => 'danger',
          ][$mqlRaw] ?? 'secondary';

          $tempVal = strtolower(trim($l['temperatura'] ?? ''));
          $tempBadge = [
              'caliente' => 'danger',
              'tibio'    => 'warning text-dark',
              'frio'     => 'info',
          ][$tempVal] ?? 'secondary';
        ?>
          <tr>
            <td class="text-muted small"><?= Helpers::e(date('Y-m-d', strtotime($l['fecha_lead']))) ?></td>
            <td class="fw-semibold text-dark"><?= Helpers::e($l['nombre']) ?: 'Sin nombre' ?></td>
            <td><?php if ($l['telefono']): ?>
              <a href="tel:<?= Helpers::e($l['telefono']) ?>" class="text-decoration-none text-dark"><?= Helpers::e($l['telefono']) ?></a>
            <?php endif; ?></td>
            <td class="d-none"><?= Helpers::e($l['email'] ?? '') ?></td>
            <td class="small"><?= Helpers::e($l['empresa_nombre']) ?: 'NM' ?></td>
            <td class="small">
                <?php if (!empty($l['campana_nombre'])): ?>
                    <i class="bi bi-megaphone-fill text-muted me-1"></i><?= Helpers::e($l['campana_nombre']) ?>
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>
            <td class="small"><?= Helpers::e($l['canal_nombre']) ?></td>
            
            <td>
              <?php if (!empty($l['mql_nivel'])): ?>
                <span class="badge rounded-pill bg-<?= $mqlBadge ?> px-2 fw-normal"><?= Helpers::e($l['mql_nivel']) ?></span>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
            
            <td>
              <?php if (!empty($l['temperatura'])): ?>
                <span class="badge rounded-pill bg-<?= $tempBadge ?> px-2 fw-normal"><?= Helpers::e($l['temperatura']) ?></span>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
            
            <td class="small"><?= Helpers::e($l['operador_nombre']) ?></td>
            
            <td>
              <?php if ((int)$l['derivado_ventas'] === 1): ?>
                <span class="badge rounded-pill bg-success px-2 fw-normal">Sí</span>
              <?php else: ?>
                <span class="badge rounded-pill bg-danger px-2 fw-normal">No</span>
              <?php endif; ?>
            </td>
            
            <td class="text-center">
              <div class="d-inline-flex gap-1 align-items-center justify-content-center">
                <!-- Botón de llamada rápida (Protocolo tel:) -->
                <?php if (!empty($l['telefono'])): ?>
                <a class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" href="tel:<?= Helpers::e($l['telefono']) ?>" title="Llamar a <?= Helpers::e($l['telefono']) ?>">
                  <i class="bi bi-telephone"></i>
                </a>
                <?php endif; ?>

                <a class="btn btn-sm btn-outline-purple rounded-pill px-3 py-1" href="<?= Helpers::url('/leads/'.$l['id'].'/editar') ?>" title="Editar lead">
                  <i class="bi bi-pencil"></i>
                </a>

                <?php if (Auth::isSupervisor()): ?>
                <form method="post" action="<?= Helpers::url('/leads/'.$l['id'].'/eliminar') ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este lead?')">
                  <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                  <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" title="Eliminar lead">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- LIBRERÍAS Y SCRIPTS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const config = { 
        locale: "es", 
        dateFormat: "Y-m-d", 
        disableMobile: "true", 
        onChange: function() { 
            document.getElementById('formFiltrosLeads').submit(); 
        } 
    };
    flatpickr("#input-desde", config);
    flatpickr("#input-hasta", config);
});

$(function () {
  var tabla = $('#tblLeads').DataTable({
    pageLength: 25,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
    dom: 'rt<"d-flex justify-content-between align-items-center pt-3"ip>',
    columnDefs: [
      { targets: [1, 2, 3, 7, 8, 10], searchable: true },
      { targets: [0, 4, 5, 6, 9, 11], searchable: false, orderable: false }
    ]
  });

  const aplicarFiltrosDesplegables = () => {
    let temp = $('#filtroTemperaturaSelect').val();
    let mql = $('#filtroMQLSelect').val();
    let derivadoSelect = $('#filtroDerivadoSelect').val();

    tabla.column(8).search(temp);
    tabla.column(7).search(mql);
    tabla.column(10).search(derivadoSelect);
    tabla.draw();
  };

  $('#filtroTemperaturaSelect, #filtroMQLSelect, #filtroDerivadoSelect').on('change', function() {
    aplicarFiltrosDesplegables();
  });

  aplicarFiltrosDesplegables();

  $('#miBuscadorPersonalizado').on('keyup', function () {
    tabla.search(this.value).draw();
    if (this.value.trim() !== '') {
      $('#btnLimpiarBuscador').removeClass('d-none');
    } else {
      $('#btnLimpiarBuscador').addClass('d-none');
    }
  });

  $('#btnLimpiarBuscador').on('click', function() {
    $('#miBuscadorPersonalizado').val(''); 
    tabla.search('').draw(); 
    $(this).addClass('d-none'); 
  });
  
  if ($('#miBuscadorPersonalizado').val() !== '') {
      tabla.search($('#miBuscadorPersonalizado').val()).draw();
  }

  // Exportar Excel
  $('#btnExport').on('click', async function () {
    const btn = $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generando...');
    try {
      const res  = await fetch('<?= Helpers::url('/leads/export') ?>', { credentials:'same-origin' });
      const data = await res.json();
      const leads = data.leads || [];
      const wb = XLSX.utils.book_new();

      const autoEncuadrar = (datos, ws) => {
        if (datos.length > 0) {
          ws['!cols'] = Object.keys(datos[0]).map(llave => ({
            wch: Math.max( llave.length, ...datos.map(d => d[llave] ? d[llave].toString().length : 0) ) + 2 
          }));
        }
        return ws;
      };

      const sheet1 = leads.map(l => ({
        'Fecha Lead': l.fecha_lead, 'Fecha Creación': l.created_at,
        'Nombre': l.nombre, 'Teléfono': l.telefono, 'Email': l.email,
        'Empresa': l.empresa_nombre, 'Campaña': l.campana_nombre, 'Canal': l.canal_nombre,
        'MQL': l.mql_nivel, 'Temperatura': l.temperatura,
        'Operador': l.operador_nombre, 'Vendedor': l.vendedor_tabla_nombre,
        'Derivado': l.derivado_ventas == 1 ? 'Sí' : 'No',
        'Fecha derivación': l.fecha_derivado_ventas || '', 'Comentario': l.comentario
      }));
      let ws1 = XLSX.utils.json_to_sheet(sheet1);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(sheet1, ws1), 'Leads');

      const segs = [];
      leads.forEach(l => (l.seguimientos||[]).forEach(s => segs.push({
        'Lead': l.nombre, 'Teléfono': l.telefono, 'Iteración': s.iteracion, 'Estado': s.estado, 'Comentario': s.comentario,
        'Próx. contacto': s.fecha_proximo_contacto, 'Fecha': s.created_at
      })));
      let ws2 = XLSX.utils.json_to_sheet(segs);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(segs, ws2), 'Seguimientos');

      const derivados = leads.filter(l => l.derivado_ventas == 1)
        .sort((a,b) => (b.fecha_derivado_ventas||'').localeCompare(a.fecha_derivado_ventas||''))
        .map(l => ({
          'Fecha derivación': l.fecha_derivado_ventas, 'Fecha creación': l.created_at,
          'Teléfono': l.telefono, 'Nombre': l.nombre, 'Campaña': l.campana_nombre, 'Empresa': l.empresa_nombre,
          'Operador': l.operador_nombre, 'Vendedor': l.vendedor_tabla_nombre
        }));
      let ws3 = XLSX.utils.json_to_sheet(derivados);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(derivados, ws3), 'Derivados');

      XLSX.writeFile(wb, 'leads_' + new Date().toISOString().slice(0,10) + '.xlsx');
    } finally {
      btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel"></i> Exportar Excel');
    }
  });
});
</script>