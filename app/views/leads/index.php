<?php /** @var array $leads @var Catalog $catalog @var string $search */ ?>
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
  <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Leads
    <span class="badge bg-secondary ms-2"><?= count($leads) ?></span>
  </h4>

  <!-- --- CAMBIO 1: BUSCADOR BACKEND --- -->
  <!-- Se agregó la etiqueta <form> para enviar la variable "search" por GET al controlador -->
  <div class="mx-auto" style="width: 350px; max-width: 100%;">
    <form method="GET" action="<?= Helpers::url('/leads') ?>" class="m-0">
      <div class="input-group">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, teléfono o email..." value="<?= Helpers::e($search ?? '') ?>">
        <!-- Botón para limpiar la búsqueda si hay un texto escrito -->
        <?php if (!empty($search)): ?>
          <a href="<?= Helpers::url('/leads') ?>" class="btn btn-outline-danger" title="Limpiar"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
      </div>
    </form>
  </div>
  <!-- FIN DEL CAMBIO 1 -->

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
          $tempBadge = ['frio'=>'info','tibio'=>'warning','caliente'=>'danger'][$l['temperatura']] ?? 'secondary'; ?>
          <tr>
            <td><?= Helpers::e($l['fecha_lead']) ?></td>
            <td><?= Helpers::e($l['nombre']) ?></td>
            <td><?php if ($l['telefono']): ?>
              <a href="tel:<?= Helpers::e($l['telefono']) ?>"><?= Helpers::e($l['telefono']) ?></a>
            <?php endif; ?></td>
            <td><?= Helpers::e($l['empresa_nombre']) ?></td>
            <td><?= Helpers::e($l['campana_nombre']) ?></td>
            <td><?= Helpers::e($l['canal_nombre']) ?></td>
            <td><?= Helpers::e($l['mql_nivel']) ?></td>
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
  // Inicializamos DataTables normalmente
  $('#tblLeads').DataTable({
    pageLength: 25,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' }
  });

  // --- CAMBIO 2: Eliminamos la lógica keyup en JavaScript ---
  // (Ya no es necesaria porque la búsqueda se procesa en el servidor al presionar Enter)

  $('#btnExport').on('click', async function () {
    const btn = $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generando...');
    try {
      const res  = await fetch('<?= Helpers::url('/leads/export') ?>', { credentials:'same-origin' });
      const data = await res.json();
      const leads = data.leads || [];
      const wb = XLSX.utils.book_new();

      // Función para calcular el ancho perfecto de las columnas
      const autoEncuadrar = (datos, ws) => {
        if (datos.length > 0) {
          ws['!cols'] = Object.keys(datos[0]).map(llave => ({
            wch: Math.max(
              llave.length, // Mide el título
              ...datos.map(d => d[llave] ? d[llave].toString().length : 0) // Mide el contenido más largo
            ) + 2 // Suma 2 espacios de margen para que respire
          }));
        }
        return ws;
      };

      // 1. Hoja de Leads
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

      // 2. Hoja de Seguimientos
      const segs = [];
      leads.forEach(l => (l.seguimientos||[]).forEach(s => segs.push({
        'Lead': l.nombre, 'Teléfono': l.telefono,
        'Iteración': s.iteracion, 'Estado': s.estado, 'Comentario': s.comentario,
        'Próx. contacto': s.fecha_proximo_contacto, 'Fecha': s.created_at
      })));
      let ws2 = XLSX.utils.json_to_sheet(segs);
      XLSX.utils.book_append_sheet(wb, autoEncuadrar(segs, ws2), 'Seguimientos');

      // 3. Hoja de Derivados
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