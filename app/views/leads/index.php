<?php /** @var array $leads @var Catalog $catalog @var string $search */ ?>
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Leads
    <span class="badge bg-secondary ms-2"><?= count($leads) ?></span>
  </h4>

  <div class="mx-auto" style="width: 350px; max-width: 100%;">
    <div class="input-group shadow-sm">
      <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="text" id="miBuscadorPersonalizado" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre, teléfono o email..." value="<?= Helpers::e($search ?? '') ?>">
      <button class="btn btn-outline-danger <?= empty($search) ? 'd-none' : '' ?>" id="btnLimpiarBuscador" title="Limpiar">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>

  <div class="ms-auto d-flex gap-2">
    <button id="btnExport" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</button>
    <a href="<?= Helpers::url('/leads/nuevo') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo lead</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblLeads" class="table table-striped table-hover align-middle w-100">
        <thead class="table-light">
          <tr>
            <th>Fecha lead</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <!-- MODIFICACIÓN 5: Columna oculta de Email para que DataTables la pueda buscar -->
            <th class="d-none">Email</th>
            <th>Empresa</th>
            <th>Campaña</th>
            <th>Canal</th>
            <th>MQL</th>
            <th>Temp.</th>
            <th>Operador</th>
            <th>Derivado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $l):
          $tempBadge = ['frio'=>'info','tibio'=>'warning','caliente'=>'danger'][$l['temperatura']] ?? 'secondary';
          // MODIFICACIÓN 7 (Parte A): Lógica para detectar el color del MQL
          $mqlBadge = ['alto'=>'success','medio'=>'warning text-dark','malo'=>'danger'][strtolower(trim($l['mql_nivel'] ?? ''))] ?? 'secondary';
        ?>
          <tr>
            <td><?= Helpers::e($l['fecha_lead']) ?></td>
            <td><?= Helpers::e($l['nombre']) ?></td>
            <td><?php if ($l['telefono']): ?>
              <a href="tel:<?= Helpers::e($l['telefono']) ?>"><?= Helpers::e($l['telefono']) ?></a>
            <?php endif; ?></td>
            <!-- MODIFICACIÓN 5: Dato oculto de Email -->
            <td class="d-none"><?= Helpers::e($l['email'] ?? '') ?></td>
            <td><?= Helpers::e($l['empresa_nombre']) ?></td>
            <td><?= Helpers::e($l['campana_nombre']) ?></td>
            <td><?= Helpers::e($l['canal_nombre']) ?></td>
            
            <!-- MODIFICACIÓN 7 (Parte B): Mostrar la etiqueta redondeada del MQL -->
            <td>
              <?php if (!empty($l['mql_nivel'])): ?>
                <span class="badge rounded-pill bg-<?= $mqlBadge ?>"><?= Helpers::e($l['mql_nivel']) ?></span>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
            
            <td><span class="badge bg-<?= $tempBadge ?>"><?= Helpers::e($l['temperatura']) ?></span></td>
            <td><?= Helpers::e($l['operador_nombre']) ?></td>
            <td>
              <?php if ((int)$l['derivado_ventas'] === 1): ?>
                <span class="badge bg-success">Sí · <?= Helpers::e($l['vendedor_tabla_nombre'] ?? '-') ?></span>
              <?php else: ?><span class="text-muted">No</span><?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?= Helpers::url('/leads/'.$l['id'].'/editar') ?>">
                <i class="bi bi-pencil"></i></a>
              <?php if (Auth::isSupervisor()): ?>
              <form method="post" action="<?= Helpers::url('/leads/'.$l['id'].'/eliminar') ?>" class="d-inline"
                    onsubmit="return confirm('¿Eliminar este lead?')">
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
  var tabla = $('#tblLeads').DataTable({
    pageLength: 25,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
    dom: 'lrtip',
    // MODIFICACIÓN 6: Restringimos la búsqueda SÓLO a las columnas específicas
    columnDefs: [
      // Columna 1 (Nombre), 2 (Teléfono) y 3 (Email oculto) SÍ se pueden buscar
      { targets: [1, 2, 3], searchable: true },
      // Todas las demás columnas (Fecha, Empresa, Campaña, etc.) se ignoran en el buscador
      { targets: [0, 4, 5, 6, 7, 8, 9, 10, 11], searchable: false }
    ]
  });

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
        'Empresa': l.empresa_nombre, 'Campaña': l.campana_nombre, 'Canal': l.canal_nombre,
        'MQL': l.mql_nivel, 'Temperatura': l.temperatura,
        'Operador': l.operador_nombre, 'Vendedor': l.vendedor_tabla_nombre,
        'Derivado': l.derivado_ventas == 1 ? 'Sí' : 'No',
        'Fecha derivación': l.fecha_derivado_ventas || '',
        'Comentario': l.comentario
      }));
      let ws1 = XLSX.utils.json_to_sheet(sheet1);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(sheet1, ws1), 'Leads');

      const segs = [];
      leads.forEach(l => (l.seguimientos||[]).forEach(s => segs.push({
        'Lead': l.nombre, 'Teléfono': l.telefono,
        'Iteración': s.iteracion, 'Estado': s.estado, 'Comentario': s.comentario,
        'Próx. contacto': s.fecha_proximo_contacto, 'Fecha': s.created_at
      })));
      let ws2 = XLSX.utils.json_to_sheet(segs);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(segs, ws2), 'Seguimientos');

      const derivados = leads.filter(l => l.derivado_ventas == 1)
        .sort((a,b) => (b.fecha_derivado_ventas||'').localeCompare(a.fecha_derivado_ventas||''))
        .map(l => ({
          'Fecha derivación': l.fecha_derivado_ventas,
          'Fecha creación': l.created_at,
          'Teléfono': l.telefono, 'Nombre': l.nombre,
          'Campaña': l.campana_nombre, 'Empresa': l.empresa_nombre,
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