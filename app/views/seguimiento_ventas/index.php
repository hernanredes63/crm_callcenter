<?php
/** @var array $leads */
/** @var array $stats */
/** @var string $desde */
/** @var string $hasta */
/** @var string $estado */
/** @var bool $canManage */

$estadoLabels = [
  'pendiente' => ['Pendiente', 'warning text-dark', 'bi-clock'],
  'visita'    => ['Visita',    'primary',   'bi-eye'],
  'venta'     => ['Venta',     'success',   'bi-cart-check'],
  'perdido'   => ['Perdido',   'danger',    'bi-x-circle'],
];
$estFiltros = ['todos'=>'Todos'] + array_map(fn($e)=>$e[0], $estadoLabels);
?>

<!-- MODIFICACIÓN 1: Estilos CSS personalizados para igualar a Lovable -->
<style>
  .bg-lovable-blue { background-color: #4a75c6 !important; }
  .btn-lovable-purple { background-color: #702eb5; color: white; border: none; }
  .btn-lovable-purple:hover { background-color: #5b2496; color: white; }
  .filter-pill { border-radius: 20px; border: 1px solid #e0e0e0; color: #6c757d; background: white; padding: 0.3rem 1.2rem; text-decoration: none; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; }
  .filter-pill:hover { background-color: #f8f9fa; }
  .filter-pill.active { background-color: #702eb5; color: white; border-color: #702eb5; }
  .metric-box { background-color: rgba(255,255,255,0.15); border-radius: 8px; padding: 0.8rem; height: 100%; }
</style>

<?php if (!empty($_SESSION['flash_ok'])): ?>
  <div class="alert alert-success"><?= Helpers::e($_SESSION['flash_ok']) ?></div>
  <?php unset($_SESSION['flash_ok']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger"><?= Helpers::e($_SESSION['flash_error']) ?></div>
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- MODIFICACIÓN 2: Todo el bloque superior (Filtros y Métricas) ahora está dentro de un contenedor azul -->
<form method="get" id="formFiltros">
  <input type="hidden" name="estado" id="inputEstado" value="<?= Helpers::e($estado) ?>">

  <div class="card border-0 mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body bg-lovable-blue p-4 text-white">
      <h3 class="fw-bold mb-1">Seguimiento de Ventas</h3>
      <p class="mb-4" style="color: rgba(255,255,255,0.8);">Gestiona el seguimiento de leads derivados</p>

     <!-- Fechas inline con librería Flatpickr (Estilo Lovable) -->
      <div class="d-flex flex-wrap gap-3 mb-4">
        
        <!-- Desde -->
        <div class="position-relative d-inline-block hover-shadow rounded">
          <div class="d-flex align-items-center rounded px-3 py-2 border border-white border-opacity-50 shadow-sm" style="background: rgba(255,255,255,0.1); cursor: pointer; transition: all 0.2s;">
            <i class="bi bi-calendar3 text-white me-2"></i>
            <span class="text-white small fw-semibold" id="label-desde">
              <?= !empty($desde) ? date('d/m/Y', strtotime($desde)) : 'Desde' ?>
            </span>
          </div>
          <!-- Input ahora es tipo text y transparente -->
          <input type="text" name="desde" id="input-desde" value="<?= Helpers::e($desde) ?>" 
                 class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
        </div>

        <!-- Hasta -->
        <div class="position-relative d-inline-block hover-shadow rounded">
          <div class="d-flex align-items-center rounded px-3 py-2 border border-white border-opacity-50 shadow-sm" style="background: rgba(255,255,255,0.1); cursor: pointer; transition: all 0.2s;">
            <i class="bi bi-calendar3 text-white me-2"></i>
            <span class="text-white small fw-semibold" id="label-hasta">
              <?= !empty($hasta) ? date('d/m/Y', strtotime($hasta)) : 'Hasta' ?>
            </span>
          </div>
          <input type="text" name="hasta" id="input-hasta" value="<?= Helpers::e($hasta) ?>" 
                 class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
        </div>
        
      </div>

      <!-- Métricas semi-transparentes -->
      <div class="row g-2">
        <?php foreach ([
          ['Total Leads', $stats['total'], 'bi-funnel'],
          ['Pendientes', $stats['pendiente'], 'bi-clock'],
          ['Visitas',    $stats['visita'], 'bi-eye'],
          ['Ventas',     $stats['venta'], 'bi-cart-check'],
          ['Perdidos',   $stats['perdido'], 'bi-x-circle'],
        ] as $c): ?>
          <div class="col-6 col-md">
            <div class="metric-box">
              <div class="small mb-1" style="color: rgba(255,255,255,0.7);"><i class="bi <?= $c[2] ?> me-1"></i><?= $c[0] ?></div>
              <div class="fs-4 fw-bold"><?= (int)$c[1] ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- MODIFICACIÓN 3: Botones tipo Píldora para filtrar estados (CORREGIDO) -->
  <div class="d-flex align-items-center flex-wrap gap-2 mb-4">
    <span class="text-muted small fw-semibold me-1">Filtrar:</span>
    <?php foreach ($estFiltros as $k=>$lab):
      $isActive = ($estado === $k || (empty($estado) && $k === 'todos'));
    ?>
      <!-- El cambio clave está aquí en el onclick, ahora envía $k directamente -->
      <button type="button" class="filter-pill <?= $isActive ? 'active' : '' ?>"
              onclick="document.getElementById('inputEstado').value='<?= $k ?>'; document.getElementById('formFiltros').submit();">
        <?= Helpers::e($lab) ?>
      </button>
    <?php endforeach; ?>
  </div>
</form>

<!-- MODIFICACIÓN 4: Rediseño del listado en tarjetas horizontales -->
<?php if (!$leads): ?>
  <div class="alert alert-info border-0 shadow-sm" style="background-color: #e3f2fd; color: #0c5460;">
    No hay leads derivados para mostrar con los filtros actuales.
  </div>
<?php else: ?>
  <div class="d-flex flex-column gap-2">
    <?php foreach ($leads as $l):
        $ult = $l['ultimo_estado'] ?? 'pendiente';
        $cfg = $estadoLabels[$ult] ?? $estadoLabels['pendiente'];
    ?>
    <div class="card shadow-sm border-0 rounded-3 mb-2 hover-shadow">
      <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
          
          <!-- Bloque Izquierdo: Información del Lead -->
          <div class="flex-grow-1 row align-items-center g-2 m-0">
            <!-- Columna 1: Nombre y Teléfono -->
            <div class="col-12 col-md-3">
              <div class="d-flex align-items-center gap-2 mb-1">
                <h6 class="mb-0 fw-bold"><?= Helpers::e($l['nombre']) ?></h6>
                <!-- Píldora de estado del lead -->
                <span class="badge rounded-pill bg-warning text-dark border border-warning bg-opacity-25 fw-normal" style="font-size: 0.75rem;">
                  <i class="bi bi-clock me-1"></i>Pendiente
                </span>
              </div>
              <div class="small text-muted fw-semibold">
                <?php if ($l['telefono']): ?><i class="bi bi-telephone-fill text-danger me-1"></i><?= Helpers::e($l['telefono']) ?><?php endif; ?>
              </div>
              <?php if (!empty($l['fecha_derivado_ventas'])): ?>
                <div class="small text-muted mt-1"><i class="bi bi-calendar3 text-danger me-1"></i>Derivado: <?= Helpers::e(date('d/m/Y', strtotime($l['fecha_derivado_ventas']))) ?></div>
              <?php endif; ?>
            </div>

            <!-- Columna 2: Ciudad -->
            <div class="col-12 col-md-2 text-md-center">
              <?php if ($l['ciudad']): ?>
                <div class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= Helpers::e($l['ciudad']) ?></div>
              <?php endif; ?>
            </div>

            <!-- Columna 3: Empresa -->
            <div class="col-12 col-md-2 text-md-center">
              <?php if (!empty($l['empresa_nombre'])): ?>
                <div class="small text-muted"><i class="bi bi-building text-info me-1"></i><?= Helpers::e($l['empresa_nombre']) ?></div>
              <?php else: ?>
                 <div class="small text-muted"><i class="bi bi-grid-3x3-gap text-info me-1"></i>NM</div>
              <?php endif; ?>
            </div>

            <!-- Columna 4: Campaña -->
            <div class="col-12 col-md-4">
              <?php if (!empty($l['campana_nombre'])): ?>
                <div class="small text-muted text-truncate" title="<?= Helpers::e($l['campana_nombre']) ?>">
                  <i class="bi bi-megaphone-fill text-danger me-1"></i><?= Helpers::e($l['campana_nombre']) ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Bloque Derecho: Botón de Registrar -->
          <?php if ($canManage): ?>
          <div class="text-end" style="min-width: 140px;">
            <button class="btn btn-lovable-purple rounded-pill px-4 shadow-sm" type="button"
                    data-bs-toggle="collapse" data-bs-target="#form-<?= Helpers::e($l['id']) ?>">
              <i class="bi bi-plus-circle me-1"></i> Registrar
            </button>
          </div>
          <?php endif; ?>
        </div>

        <!-- Formulario Colapsable (Oculto hasta hacer clic en Registrar) -->
        <?php if ($canManage): ?>
        <div class="collapse mt-3" id="form-<?= Helpers::e($l['id']) ?>">
          <div class="card border-0 bg-light rounded-3">
             <div class="card-body">
              <form method="post" action="<?= Helpers::url('/seguimiento-ventas/' . $l['id']) ?>" class="sv-form row g-2 align-items-end">
                <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                
                <div class="col-md-3">
                  <label class="form-label small mb-1 fw-bold text-muted">Estado del seguimiento</label>
                  <select name="estado" class="form-select form-select-sm sv-estado" required>
                    <option value="">Seleccione...</option>
                    <option value="visita">Visita</option>
                    <option value="venta">Venta</option>
                    <option value="perdido">Perdido</option>
                    <option value="pendiente">Pendiente</option>
                  </select>
                </div>
                
                <div class="col-md-3 sv-fecha-visita d-none">
                  <label class="form-label small mb-1 fw-bold text-muted">Fecha visita</label>
                  <input type="date" name="fecha_visita" class="form-control form-control-sm">
                </div>
                
                <div class="col-md-3 sv-fecha-venta d-none">
                  <label class="form-label small mb-1 fw-bold text-muted">Fecha venta</label>
                  <input type="date" name="fecha_venta" class="form-control form-control-sm">
                </div>
                
                <div class="col-md-4 flex-grow-1">
                  <label class="form-label small mb-1 fw-bold text-muted">Comentario (opcional)</label>
                  <input type="text" name="comentario" class="form-control form-control-sm" placeholder="Escribe un detalle corto...">
                </div>
                
                <div class="col-md-2 text-end">
                  <button class="btn btn-sm btn-success w-100"><i class="bi bi-save me-1"></i> Guardar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>






<!-- Importar Librería de Calendario (Flatpickr) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<!-- Estilos para igualar el diseño del calendario a Lovable -->
<style>
  /* Color del día seleccionado */
  .flatpickr-day.selected, .flatpickr-day.selected:hover {
      background: #34d399 !important; 
      border-color: #34d399 !important;
  }
  
  /* Diseño de los botones de las flechas (Mes anterior y siguiente) */
  .flatpickr-calendar .flatpickr-prev-month,
  .flatpickr-calendar .flatpickr-next-month {
      background-color: #34d399 !important; /* Fondo turquesa */
      border-radius: 6px !important; /* Bordes un poco redondeados */
      top: 10px !important; /* Posición para centrarlo */
      height: 28px !important;
      width: 28px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 0 !important;
  }
  
  /* Posición específica para separarlos de los bordes */
  .flatpickr-calendar .flatpickr-prev-month {
      left: 10px !important;
  }
  .flatpickr-calendar .flatpickr-next-month {
      right: 10px !important;
  }
  
  /* Flechitas en color blanco */
  .flatpickr-calendar .flatpickr-prev-month svg,
  .flatpickr-calendar .flatpickr-next-month svg {
      fill: #ffffff !important;
      width: 14px !important;
      height: 14px !important;
  }
  
  /* Efecto al pasar el ratón por encima (Hover) */
  .flatpickr-calendar .flatpickr-prev-month:hover,
  .flatpickr-calendar .flatpickr-next-month:hover {
      background-color: #2cae7e !important; /* Un poco más oscuro */
  }
  .flatpickr-calendar .flatpickr-prev-month:hover svg,
  .flatpickr-calendar .flatpickr-next-month:hover svg {
      fill: #ffffff !important;
  }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Configuración común del calendario
    const config = {
        locale: "es", // En español
        dateFormat: "Y-m-d", // Formato para la base de datos
        disableMobile: "true", // Fuerza el diseño de PC incluso en pantallas chicas
        onChange: function(selectedDates, dateStr, instance) {
            // Cuando eliges fecha, enviamos el formulario automáticamente
            document.getElementById('formFiltros').submit();
        }
    };

    // Inicializar calendario Desde
    flatpickr("#input-desde", config);
    
    // Inicializar calendario Hasta
    flatpickr("#input-hasta", config);
});
</script>




<script>
// Mantengo tu misma lógica de Javascript pero adaptada al nuevo diseño
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