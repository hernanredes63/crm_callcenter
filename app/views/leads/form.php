<?php
/** @var ?array $lead @var Catalog $catalog @var array $seguimientos */
$isEdit = !empty($lead);
$action = $isEdit ? Helpers::url('/leads/'.$lead['id']) : Helpers::url('/leads');
$v = fn(string $k, $d='') => Helpers::e($lead[$k] ?? $d);
$seguimientos = $seguimientos ?? [];
?>
<div class="d-flex align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
    <?= $isEdit ? 'Editar lead' : 'Nuevo lead' ?></h4>
  <a href="<?= Helpers::url('/leads') ?>" class="btn btn-sm btn-outline-secondary ms-auto">
    <i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="post" action="<?= $action ?>">
          <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre *</label>
              <input required name="nombre" class="form-control" value="<?= $v('nombre') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Teléfono</label>
              <input name="telefono" class="form-control" value="<?= $v('telefono') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha lead</label>
              <input type="date" name="fecha_lead" class="form-control" value="<?= $v('fecha_lead', date('Y-m-d')) ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= $v('email') ?>">
            </div>
            
            <div class="col-md-3">
              <label class="form-label">País</label>
              <select name="pais_id" id="paisSelect" class="form-select">
                <option value="">—</option>
                <?php foreach ($catalog->paises() as $p): ?>
                  <option value="<?= $p['id'] ?>" <?= ($lead['pais_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                    <?= Helpers::e($p['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Ciudad</label>
              <select name="ciudad" id="ciudadSelect" class="form-select" data-ciudad-guardada="<?= $v('ciudad') ?>">
                <option value="">— Seleccionar —</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Empresa</label>
              <select name="empresa_id" id="empresaSelect" class="form-select">
                <option value="">—</option>
                <?php foreach ($catalog->empresas() as $e): ?>
                  <option value="<?= $e['id'] ?>" <?= ($lead['empresa_id'] ?? '') === $e['id'] ? 'selected' : '' ?>>
                    <?= Helpers::e($e['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Campaña</label>
              <select name="campana_id" id="campanaSelect" class="form-select" data-campana-guardada="<?= $v('campana_id') ?>">
                <option value="" data-empresa-id="">— Seleccionar Empresa Primero —</option>
                <?php foreach ($catalog->campanas() as $c): ?>
                  <option value="<?= $c['id'] ?>" data-empresa-id="<?= $c['empresa_id'] ?? '' ?>" <?= ($lead['campana_id'] ?? '') === $c['id'] ? 'selected' : '' ?>>
                    <?= Helpers::e($c['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Proyecto</label>
              <select name="proyecto_id" id="proyectoSelect" class="form-select" data-proyecto-guardado="<?= $v('proyecto_id') ?>">
                <option value="" data-empresa-id="">— Seleccionar Empresa Primero —</option>
                <?php foreach ($catalog->proyectos() as $c): ?>
                  <option value="<?= $c['id'] ?>" data-empresa-id="<?= $c['empresa_id'] ?? '' ?>" <?= ($lead['proyecto_id'] ?? '') === $c['id'] ? 'selected' : '' ?>>
                    <?= Helpers::e($c['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="col-md-3">
              <label class="form-label">Canal</label>
              <select name="canal_id" class="form-select">
                <option value="">—</option>
                <?php foreach ($catalog->canales() as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= ($lead['canal_id'] ?? '') === $c['id'] ? 'selected' : '' ?>>
                    <?= Helpers::e($c['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Nivel de Interés (MQL)</label>
              <select name="mql_id" class="form-select">
                <option value="">— Sin MQL —</option>
                <?php foreach ($catalog->mql() as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= ($lead['mql_id'] ?? '') === $c['id'] ? 'selected' : '' ?>>
                    <?= Helpers::e($c['nivel']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Temperatura</label>
              <select name="temperatura" class="form-select">
                <?php foreach (['frio','tibio','caliente'] as $t): ?>
                  <option value="<?= $t ?>" <?= ($lead['temperatura'] ?? 'frio') === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Tipo pago</label>
              <select name="tipo_pago" class="form-select">
                <option value="">— Seleccionar —</option>
                <?php foreach (['Contado', 'Plazo'] as $pago): ?>
                  <option value="<?= $pago ?>" <?= ($lead['tipo_pago'] ?? '') === $pago ? 'selected' : '' ?>>
                    <?= $pago ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Agente</label>
              <?php if (Auth::isSupervisor()): ?>
                  <select name="agente_id" class="form-select">
                    <option value="">—</option>
                    <?php foreach ($catalog->agentes() as $a): ?>
                      <option value="<?= $a['id'] ?>" <?= ($lead['agente_id'] ?? '') === $a['id'] ? 'selected' : '' ?>>
                        <?= Helpers::e($a['nombre']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
              <?php else: ?>
                  <input type="text" class="form-control bg-light" value="<?= Helpers::e(Auth::user()['nombre']) ?>" readonly disabled>
                  <input type="hidden" name="agente_id" value="<?= Auth::userId() ?>">
              <?php endif; ?>
            </div>
          
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check form-switch p-3 border rounded w-100 bg-light">
                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="switchMensaje" name="se_envio_mensaje" value="1" style="width: 2.5em; height: 1.25em;">
                <label class="form-check-label d-block w-100" for="switchMensaje" style="cursor: pointer;">
                  <strong>Se envió mensaje</strong><br>
                  <small class="text-muted">Marca si ya se envió un mensaje al lead (cuenta como 1 seguimiento)</small>
                </label>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Comentario</label>
              <textarea name="comentario" rows="3" class="form-control"><?= $v('comentario') ?></textarea>
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php if ($isEdit): ?>
  <div class="col-lg-4">
    <!-- Derivación -->
    <?php if (Auth::hasRole('admin','supervisor','operador') && (int)$lead['derivado_ventas'] === 0): ?>
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-warning-subtle"><strong>Derivar a ventas</strong></div>
      <div class="card-body">
        <form method="post" action="<?= Helpers::url('/leads/'.$lead['id'].'/derivar') ?>">
          <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
          <select name="vendedor_tabla_id" class="form-select mb-2" required>
            <option value="">Selecciona vendedor…</option>
            <?php foreach ($catalog->vendedores() as $v): ?>
              <option value="<?= $v['id'] ?>"><?= Helpers::e($v['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-warning w-100"><i class="bi bi-send"></i> Derivar</button>
        </form>
      </div>
    </div>
    <?php else: ?>
      <div class="alert alert-success"><i class="bi bi-check-circle"></i>
        Ya derivado a <strong><?= Helpers::e($lead['vendedor_tabla_id']) ?></strong>
        el <?= Helpers::e($lead['fecha_derivado_ventas']) ?></div>
    <?php endif; ?>

    <!-- Seguimientos -->
    <div class="card shadow-sm">
      <div class="card-header d-flex align-items-center">
        <strong>Seguimientos</strong>
        <span class="badge bg-secondary ms-2"><?= count($seguimientos) ?>/7</span>
        <?php if (count($seguimientos) < 7): ?>
        <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="collapse" data-bs-target="#segForm">
          <i class="bi bi-plus"></i> Agregar</button>
        <?php endif; ?>
      </div>
      <div class="card-body p-0">
        <div id="segForm" class="collapse border-bottom p-3">
          <form id="frmSeg" data-lead="<?= $lead['id'] ?>">
            <div class="mb-2">
              <label class="form-label small">Estado</label>
              <select name="estado" class="form-select form-select-sm">
                <?php foreach (['contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje'] as $s): ?>
                  <option value="<?= $s ?>"><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Comentario</label>
              <textarea name="comentario" rows="2" class="form-control form-control-sm"></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label small">Próximo contacto</label>
              <input type="datetime-local" name="fecha_proximo_contacto" class="form-control form-control-sm">
            </div>
            <button class="btn btn-sm btn-primary w-100">Guardar seguimiento</button>
          </form>
        </div>
        <ul class="list-group list-group-flush">
          <?php foreach ($seguimientos as $s): ?>
            <li class="list-group-item">
              <div class="d-flex">
                <span class="badge bg-primary me-2 align-self-start">#<?= $s['iteracion'] ?></span>
                <div class="flex-grow-1">
                  <div class="fw-semibold"><?= Helpers::e($s['estado']) ?></div>
                  <?php if ($s['comentario']): ?><div class="small text-muted"><?= Helpers::e($s['comentario']) ?></div><?php endif; ?>
                  <div class="small text-muted"><?= Helpers::e($s['created_at']) ?>
                    <?php if ($s['fecha_proximo_contacto']): ?>
                      · próx: <?= Helpers::e($s['fecha_proximo_contacto']) ?>
                    <?php endif; ?></div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- SCRIPT PRINCIPAL: PAÍSES, CIUDADES, EMPRESAS, CAMPAÑAS Y PROYECTOS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. LÓGICA DE PAÍSES Y CIUDADES
    // ==========================================
    const paisSelect = document.getElementById('paisSelect');
    const ciudadSelect = document.getElementById('ciudadSelect');
    const ciudadGuardada = ciudadSelect ? ciudadSelect.getAttribute('data-ciudad-guardada') : null;

    const ciudadesPorPais = {
        'Bolivia': ['Santa Cruz', 'La Paz', 'Cochabamba', 'Chuquisaca', 'Tarija', 'Potosí', 'Oruro', 'Beni', 'Pando'],
        'Argentina': ['Buenos Aires'],
        'Chile': ['Santiago'],
        'Colombia': ['Bogotá'],
        'Ecuador': ['Quito'],
        'España': ['Madrid'],
        'Estados Unidos': ['Miami', 'New York', 'Washington D.C.'],
        'México': ['Ciudad de México'],
        'Paraguay': ['Asunción'],
        'Perú': ['Lima'],
        'Uruguay': ['Montevideo']
    };

    if (paisSelect && ciudadSelect) {
        function actualizarCiudades() {
            let paisNombre = '';
            if (paisSelect.selectedIndex > 0) {
                paisNombre = paisSelect.options[paisSelect.selectedIndex].text.trim();
            }
            ciudadSelect.innerHTML = '<option value="">— Seleccionar —</option>';
            if (ciudadesPorPais[paisNombre]) {
                ciudadesPorPais[paisNombre].forEach(function(ciudad) {
                    const option = document.createElement('option');
                    option.value = ciudad;
                    option.textContent = ciudad;
                    if (ciudad === ciudadGuardada) option.selected = true;
                    ciudadSelect.appendChild(option);
                });
            }
        }
        paisSelect.addEventListener('change', actualizarCiudades);
        actualizarCiudades();
    }

    // ==========================================
    // 2. LÓGICA DE EMPRESAS, CAMPAÑAS Y PROYECTOS
    // ==========================================
    const empresaSelect = document.getElementById('empresaSelect');
    const campanaSelect = document.getElementById('campanaSelect');
    const proyectoSelect = document.getElementById('proyectoSelect');

    if (empresaSelect) {
        // A. Guardar en memoria las campañas originales
        const campanasOriginales = [];
        if (campanaSelect) {
            for (let i = 0; i < campanaSelect.options.length; i++) {
                campanasOriginales.push({
                    value: campanaSelect.options[i].value,
                    text: campanaSelect.options[i].text,
                    empresaId: campanaSelect.options[i].getAttribute('data-empresa-id') || ''
                });
            }
        }

        // B. Guardar en memoria los proyectos originales
        const proyectosOriginales = [];
        if (proyectoSelect) {
            for (let i = 0; i < proyectoSelect.options.length; i++) {
                proyectosOriginales.push({
                    value: proyectoSelect.options[i].value,
                    text: proyectoSelect.options[i].text,
                    empresaId: proyectoSelect.options[i].getAttribute('data-empresa-id') || ''
                });
            }
        }

        const campanaGuardada = campanaSelect ? campanaSelect.getAttribute('data-campana-guardada') : null;
        const proyectoGuardado = proyectoSelect ? proyectoSelect.getAttribute('data-proyecto-guardado') : null;

        function actualizarDependientes() {
            const empresaIdSeleccionada = empresaSelect.value;
            
            // --- FILTRAR CAMPAÑAS ---
            if (campanaSelect) {
                campanaSelect.options.length = 0; // Vaciar
                if (!empresaIdSeleccionada) {
                    campanaSelect.add(new Option("— Seleccionar Empresa Primero —", ""));
                } else {
                    campanaSelect.add(new Option("— Seleccionar Campaña —", ""));
                    campanasOriginales.forEach((camp, index) => {
                        if (index === 0) return;
                        if (camp.empresaId === empresaIdSeleccionada) {
                            const nuevaOpcion = new Option(camp.text, camp.value);
                            nuevaOpcion.setAttribute('data-empresa-id', camp.empresaId);
                            campanaSelect.add(nuevaOpcion);
                        }
                    });
                    if (campanaGuardada && campanaSelect.querySelector(`option[value="${campanaGuardada}"]`)) {
                        campanaSelect.value = campanaGuardada;
                    }
                }
            }

            // --- FILTRAR PROYECTOS ---
            if (proyectoSelect) {
                proyectoSelect.options.length = 0; // Vaciar
                if (!empresaIdSeleccionada) {
                    proyectoSelect.add(new Option("— Seleccionar Empresa Primero —", ""));
                } else {
                    proyectoSelect.add(new Option("— Seleccionar Proyecto —", ""));
                    proyectosOriginales.forEach((proy, index) => {
                        if (index === 0) return;
                        if (proy.empresaId === empresaIdSeleccionada) {
                            const nuevaOpcion = new Option(proy.text, proy.value);
                            nuevaOpcion.setAttribute('data-empresa-id', proy.empresaId);
                            proyectoSelect.add(nuevaOpcion);
                        }
                    });
                    if (proyectoGuardado && proyectoSelect.querySelector(`option[value="${proyectoGuardado}"]`)) {
                        proyectoSelect.value = proyectoGuardado;
                    }
                }
            }
        }

        // Escuchar cambios de la empresa
        empresaSelect.addEventListener('change', actualizarDependientes);
        
        // Ejecutar la primera vez para que limpie si está en "Nuevo Lead"
        actualizarDependientes();
    }
});
</script>

<?php if ($isEdit): ?>
<script>
document.getElementById('frmSeg')?.addEventListener('submit', async function (e) {
  e.preventDefault();
  const fd = new FormData(this);
  fd.append('_csrf', '<?= Helpers::csrf() ?>');
  const r = await fetch('<?= Helpers::url('/leads/'.$lead['id'].'/seguimientos') ?>', {
    method: 'POST', body: fd, credentials: 'same-origin'
  });
  const j = await r.json();
  if (j.ok) location.reload();
  else alert(j.error || 'Error al guardar');
});
</script>
<?php endif; ?>