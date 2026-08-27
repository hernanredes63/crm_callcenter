<?php /** @var array $leads @var Catalog $catalog */ ?>
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-share me-2"></i>Leads Redes
    <span class="badge bg-secondary ms-2"><?= count($leads) ?></span>
  </h4>

  <!-- MODIFICACIÓN 1: Agregamos el Buscador Principal Superior -->
  <div class="mx-auto" style="width: 350px; max-width: 100%;">
    <div class="input-group shadow-sm">
      <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="text" id="miBuscadorRedes" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre, teléfono o email..." value="">
      <button class="btn btn-outline-danger d-none" id="btnLimpiarBuscador" title="Limpiar">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>
  <!-- FIN MODIFICACIÓN 1 -->

  <div class="ms-auto d-flex gap-2">
    <button id="btnExportRedes" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-excel"></i> Exportar Excel</button>
    <a href="<?= Helpers::url('/leads-redes/nuevo') ?>" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> Nuevo lead redes</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblLeadsRedes" class="table table-striped table-hover align-middle w-100">
        <thead class="table-light">
          <tr>
            <th>Fecha lead</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <!-- MODIFICACIÓN 2: Columna oculta de Email para las búsquedas -->
            <th class="d-none">Email</th>
            <th>Ciudad</th>
            <th>Empresa</th>
            <th>Campaña</th>
            <th>Canal</th>
            <th>MQL</th>
            <th>Temp.</th>
            <th>Operador</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $l):
          $tempBadge = ['frio'=>'info','tibio'=>'warning','caliente'=>'danger'][$l['temperatura']] ?? 'secondary'; ?>
          <tr>
            <td><?= Helpers::e($l['fecha_lead']) ?></td>
            <td class="fw-semibold"><?= Helpers::e($l['nombre']) ?></td>
            <td><?php if ($l['telefono']): ?>
              <a href="tel:<?= Helpers::e($l['telefono']) ?>"><?= Helpers::e($l['telefono']) ?></a>
            <?php endif; ?></td>
            <!-- MODIFICACIÓN 2: Dato oculto de Email -->
            <td class="d-none"><?= Helpers::e($l['email'] ?? '') ?></td>
            <td><?= Helpers::e($l['ciudad']) ?></td>
            <td><?= Helpers::e($l['empresa_nombre']) ?></td>
            <td><?= Helpers::e($l['campana_nombre']) ?></td>
            <td><?= Helpers::e($l['canal_nombre']) ?></td>
            <td><?= Helpers::e($l['mql_nivel']) ?></td>
            <td><span class="badge bg-<?= $tempBadge ?>"><?= Helpers::e($l['temperatura']) ?></span></td>
            <td><?= Helpers::e($l['operador_nombre']) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary"
                 href="<?= Helpers::url('/leads-redes/'.$l['id'].'/editar') ?>">
                <i class="bi bi-pencil"></i></a>
              <?php if (Auth::isSupervisor()): ?>
              <form method="post" action="<?= Helpers::url('/leads-redes/'.$l['id'].'/eliminar') ?>"
                    class="d-inline" onsubmit="return confirm('¿Eliminar este lead?')">
                <input type="hidden" name="_csrf" value="<?= Helpers::csrf() ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
$(function () {
  // MODIFICACIÓN 3: Configuramos DataTables (ocultamos el buscador viejo y bloqueamos columnas)
  var tabla = $('#tblLeadsRedes').DataTable({
    pageLength: 25,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
    dom: 'lrtip', // Oculta el buscador por defecto
    columnDefs: [
      // Habilitar búsqueda solo en Nombre(1), Teléfono(2) y Email oculto(3)
      { targets: [1, 2, 3], searchable: true },
      // Bloquear búsqueda en Fecha(0), Ciudad(4), Empresa(5)... etc.
      { targets: [0, 4, 5, 6, 7, 8, 9, 10, 11], searchable: false }
    ]
  });

  // MODIFICACIÓN 4: Conectamos el buscador nuevo con DataTables
  $('#miBuscadorRedes').on('keyup', function () {
    tabla.search(this.value).draw();
    
    // Mostrar u ocultar botón de limpiar (la X roja)
    if (this.value.trim() !== '') {
      $('#btnLimpiarBuscador').removeClass('d-none');
    } else {
      $('#btnLimpiarBuscador').addClass('d-none');
    }
  });

  // Funcionalidad para limpiar el buscador
  $('#btnLimpiarBuscador').on('click', function() {
    $('#miBuscadorRedes').val('');
    tabla.search('').draw();
    $(this).addClass('d-none');
  });

  $('#btnExportRedes').on('click', async function () {
    const btn = $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generando...');
    try {
      const res  = await fetch('<?= Helpers::url('/leads-redes/export') ?>', { credentials:'same-origin' });
      const data = await res.json();
      const leads = data.leads || [];
      const wb = XLSX.utils.book_new();

      // MODIFICACIÓN 5: Inyectamos la función autoEncuadrar que tenías en los leads normales
      const autoEncuadrar = (datos, ws) => {
        if (datos.length > 0) {
          ws['!cols'] = Object.keys(datos[0]).map(llave => ({
            wch: Math.max(
              llave.length, 
              ...datos.map(d => d[llave] ? d[llave].toString().length : 0) 
            ) + 2 
          }));
        }
        return ws;
      };

      const sheet1 = leads.map(l => ({
        'Fecha Lead': l.fecha_lead, 'Fecha Creación': l.created_at,
        'Nombre': l.nombre, 'Teléfono': l.telefono, 'Email': l.email,
        'Ciudad': l.ciudad,
        'Empresa': l.empresa_nombre, 'Campaña': l.campana_nombre, 'Canal': l.canal_nombre,
        'MQL': l.mql_nivel, 'Temperatura': l.temperatura,
        'Operador': l.operador_nombre, 'Comentario': l.comentario
      }));
      // Aplicamos el auto-encuadre a la hoja 1
      let ws1 = XLSX.utils.json_to_sheet(sheet1);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(sheet1, ws1), 'Leads Redes');

      const segs = [];
      leads.forEach(l => (l.seguimientos||[]).forEach(s => segs.push({
        'Lead': l.nombre, 'Canal': l.canal_nombre,
        'Iteración': s.iteracion, 'Estado': s.estado, 'Comentario': s.comentario,
        'Próx. contacto': s.fecha_proximo_contacto, 'Fecha': s.created_at
      })));
      // Aplicamos el auto-encuadre a la hoja 2
      let ws2 = XLSX.utils.json_to_sheet(segs);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(segs, ws2), 'Seguimientos');

      XLSX.writeFile(wb, 'leads_redes_' + new Date().toISOString().slice(0,10) + '.xlsx');
    } finally {
      btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel"></i> Exportar Excel');
    }
  });
});
</script>