<?php
declare(strict_types=1);

final class LeadsController extends Controller
{
    private Lead $leads;
    private Seguimiento $seg;
    private Catalog $cat;

    public function __construct()
    {
        $this->leads = new Lead();
        $this->seg   = new Seguimiento();
        $this->cat   = new Catalog();
    }

    public function index(): void
    {
        $this->requireAuth();
        
        // --- AQUÍ HICIMOS UN CAMBIO: Capturamos la variable de búsqueda ---
        $search = trim((string)($_GET['search'] ?? ''));
        
        // Pasamos la variable $search al modelo
        $rows = $this->leads->listForRole(Auth::role(), Auth::profileId(), $search);
        
        $this->view('leads/index', [
            'leads'      => $rows,
            'catalog'    => $this->cat,
            'canDerivar' => Auth::hasRole('admin','supervisor','operador'),
            'search'     => $search // Pasamos la variable a la vista
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('leads/form', [
            'lead'    => null,
            'catalog' => $this->cat,
        ]);
    }

   public function store(): void
    {
        $this->requireAuth();
        Helpers::csrfCheck();
        $data = $this->validate($_POST);

        // Duplicado global por teléfono
        if (!empty($data['telefono']) && $this->leads->findByPhone($data['telefono'])) {
            Helpers::flash('error', 'Ya existe un lead con ese teléfono.');
            Helpers::redirect('/leads/nuevo');
        }

        $data['operador_id'] = Auth::profileId();  // fuerza el operador actual
        
        // Creamos el lead y obtenemos su ID
        $newLeadId = $this->leads->create($data); 
        
        // --- Seguimiento: Verificamos si el switch de mensaje está activo ---
        if (isset($_POST['se_envio_mensaje']) && $_POST['se_envio_mensaje'] == '1') {
            $this->seg->create([
                'lead_id'    => $newLeadId,
                'estado'     => 'se_envio_mensaje',
                'comentario' => 'Mensaje enviado registrado desde el formulario principal.'
            ]);
        }
        
        // ==========================================
        // NUEVO CAMBIO: Derivar al instante si se eligió un vendedor
        // ==========================================
        // Leemos el ID numérico del vendedor que vino oculto en el formulario de la derecha
        $vendId = (int)($_POST['vendedor_tabla_id'] ?? 0);
        
        if ($vendId > 0) {
            try {
                // Ejecutamos la derivación automática
                $this->leads->derivar((string)$newLeadId, $vendId);
                Helpers::flash('ok', 'Lead creado y derivado a ventas exitosamente.');
                Helpers::redirect('/leads');
                return; 
            } catch (Throwable $e) {
                // Si el vendedor tiene error en la BD, avisamos sin romper la pantalla
                Helpers::flash('error', 'Lead creado, pero hubo un error de base de datos al derivar: ' . $e->getMessage());
                Helpers::redirect('/leads');
                return;
            }
        }
        // ==========================================
        
        // Si no se eligió vendedor, termina el guardado normal
        Helpers::flash('ok', 'Lead creado correctamente.');
        Helpers::redirect('/leads');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $lead = $this->leads->find($id);
        if (!$lead) { http_response_code(404); exit('Lead no encontrado'); }

        // Un operador solo edita sus propios leads
        if (Auth::role() === 'operador' && $lead['operador_id'] !== Auth::profileId()) {
            http_response_code(403); exit('No tienes permisos.');
        }

        $this->view('leads/form', [
            'lead'          => $lead,
            'catalog'       => $this->cat,
            'seguimientos'  => $this->seg->forLead($id),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        Helpers::csrfCheck();
        $lead = $this->leads->find($id);
        if (!$lead) { http_response_code(404); exit('Lead no encontrado'); }
        if (Auth::role() === 'operador' && $lead['operador_id'] !== Auth::profileId()) {
            http_response_code(403); exit('No tienes permisos.');
        }
        
        $this->leads->update($id, $this->validate($_POST));
        
        // --- CAMBIO: Verificamos si el switch de mensaje está activo ---
        if (isset($_POST['se_envio_mensaje']) && $_POST['se_envio_mensaje'] == '1') {
            $this->seg->create([
                'lead_id'    => $id,
                'estado'     => 'se_envio_mensaje',
                'comentario' => 'Mensaje enviado registrado desde la edición del lead.'
            ]);
        }
        
        Helpers::flash('ok', 'Lead actualizado.');
        Helpers::redirect('/leads/' . $id . '/editar');
    }

    public function destroy(string $id): void
    {
        $this->requireRole('admin','supervisor');
        Helpers::csrfCheck();
        $this->leads->delete($id);
        Helpers::flash('ok', 'Lead eliminado.');
        Helpers::redirect('/leads');
    }

    public function derivar(string $id): void
    {
        $this->requireAuth();
        Helpers::csrfCheck();
        
        // CORRECCIÓN: Volvemos a tratarlo como número (int) para respetar el modo estricto de PHP
        $vendId = (int)($_POST['vendedor_tabla_id'] ?? 0);
        
        // Si es 0 (eligió la opción "Selecciona vendedor..."), le pedimos amablemente que elija uno
        if ($vendId <= 0) { 
            Helpers::flash('error', 'Por favor, selecciona un vendedor de la lista antes de presionar Derivar.'); 
            Helpers::redirect('/leads/'.$id.'/editar'); 
            return;
        }
        
        try {
            // Ahora sí le pasamos el número entero correcto a la función
            $this->leads->derivar($id, $vendId);
            Helpers::flash('ok', 'Lead derivado a ventas exitosamente.');
        } catch (Throwable $e) {
            // Si el vendedor tiene error en la BD (como Juan de Dios), atrapamos el error aquí
            Helpers::flash('error', 'Error en la base de datos al derivar (¿El vendedor fue borrado?): ' . $e->getMessage());
        }
        
        Helpers::redirect('/leads/' . $id . '/editar');
    }


    public function storeSeguimiento(string $id): void
    {
        $this->requireAuth();
        Helpers::csrfCheck();
        try {
            $this->seg->create([
                'lead_id'                => $id,
                'estado'                 => $_POST['estado'] ?? 'no_contesta',
                'comentario'             => trim((string)($_POST['comentario'] ?? '')),
                'fecha_proximo_contacto' => $_POST['fecha_proximo_contacto'] ?? null,
            ]);
            $this->json(['ok' => true]);
        } catch (Throwable $e) {
            $this->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteSeguimiento(string $id): void
    {
        $this->requireRole('admin','supervisor','operador');
        Helpers::csrfCheck();
        (new Seguimiento())->delete($id);
        $this->json(['ok' => true]);
    }

    /** Export XLSX en cliente: entregamos JSON con todo el dataset y seguimientos. */
    public function export(): void
    {
        $this->requireAuth();
        // Para exportar normalmente queremos todos los datos, por lo que pasamos string vacío
        $rows = $this->leads->listForRole(Auth::role(), Auth::profileId(), '');
        
        // Cargamos todos los seguimientos de esos leads en una sola query.
        $ids = array_column($rows, 'id');
        $segs = [];
        if ($ids) {
            $ph  = implode(',', array_fill(0, count($ids), '?'));
            $st  = Database::pdo()->prepare(
                "SELECT * FROM seguimientos WHERE lead_id IN ($ph) ORDER BY lead_id, iteracion"
            );
            $st->execute($ids);
            foreach ($st->fetchAll() as $s) { $segs[$s['lead_id']][] = $s; }
        }
        foreach ($rows as &$r) { $r['seguimientos'] = $segs[$r['id']] ?? []; }
        $this->json(['leads' => $rows]);
    }

    /* ------------------------------------------------------------------ */
    private function validate(array $in): array
    {
        $nombre = trim((string)($in['nombre'] ?? ''));
        if ($nombre === '') {
            Helpers::flash('error', 'El nombre es obligatorio.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
        }

        return [
            'nombre'       => $nombre,
            'telefono'     => trim((string)($in['telefono'] ?? '')),
            'email'        => trim((string)($in['email'] ?? '')),
            'pais_id'      => $in['pais_id'] ?: null,
            'ciudad'       => trim((string)($in['ciudad'] ?? '')),
            'empresa_id'   => $in['empresa_id']  ?: null,
            'proyecto_id'  => $in['proyecto_id'] ?: null,
            'campana_id'   => $in['campana_id']  ?: null,
            'canal_id'     => $in['canal_id']    ?: null,
            'mql_id'       => $in['mql_id']      ?: null,
            'temperatura'  => in_array($in['temperatura'] ?? '', ['frio','tibio','caliente'], true) ? $in['temperatura'] : 'frio',
            'tipo_pago'    => trim((string)($in['tipo_pago'] ?? '')),
            'comentario'   => trim((string)($in['comentario'] ?? '')),
            'agente_id'    => $in['agente_id'] ?? null, // Regresamos a la validación original
            'fecha_lead'   => $in['fecha_lead'] ?: date('Y-m-d'),
        ];
    }
}