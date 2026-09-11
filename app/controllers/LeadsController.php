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
        // --- 1. VALIDACIÓN DEL NOMBRE ---
        $nombre = trim((string)($in['nombre'] ?? ''));
        if ($nombre === '') {
            Helpers::flash('error', 'El nombre es obligatorio.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        $patronNombre = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?:\s+[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)+$/';
        if (!preg_match($patronNombre, $nombre)) {
            Helpers::flash('error', 'El campo debe contener un nombre y un apellido válidos (solo letras).');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // --- 2. VALIDACIÓN DEL TELÉFONO (Obligatorio y formato base) ---
        $telefono = trim((string)($in['telefono'] ?? ''));
        if ($telefono === '') {
            Helpers::flash('error', 'El teléfono es obligatorio.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        $patronTelefono = '/^\+[0-9\s]{7,20}$/';
        if (!preg_match($patronTelefono, $telefono)) {
            Helpers::flash('error', 'El teléfono debe comenzar con + seguido del código de país y el número (solo números y espacios).');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // --- 3. VALIDACIÓN DEL PREFIJO SEGÚN EL PAÍS ---
        $pais_id = $in['pais_id'] ?? null;
        
        if ($pais_id) {
            // Buscamos el nombre del país usando tu catálogo
            $nombrePais = '';
            foreach ($this->cat->paises() as $p) {
                if ($p['id'] == $pais_id) {
                    $nombrePais = $p['nombre'];
                    break;
                }
            }

            // Diccionario estricto de prefijos
            $prefijos = [
                'Bolivia'        => '+591',
                'Argentina'      => '+54',
                'Chile'          => '+56',
                'Colombia'       => '+57',
                'Ecuador'        => '+593',
                'España'         => '+34',
                'Estados Unidos' => '+1',
                'México'         => '+52',
                'Paraguay'       => '+595',
                'Perú'           => '+51',
                'Uruguay'        => '+598'
            ];

            // Verificamos que el teléfono empiece (strpos === 0) con el prefijo correcto
            if ($nombrePais && isset($prefijos[$nombrePais])) {
                $prefijoRequerido = $prefijos[$nombrePais];
                if (strpos($telefono, $prefijoRequerido) !== 0) {
                    Helpers::flash('error', "Conflicto: Elegiste $nombrePais, por lo tanto el teléfono DEBE comenzar con $prefijoRequerido.");
                    Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
                    exit;
                }
            }
        }




        // --- 4. VALIDACIÓN DE LA EMPRESA (Obligatoria y Existente) ---
        $empresa_id = $in['empresa_id'] ?? '';
        
        // 4a. Validar que no esté vacío
        if ($empresa_id === '') {
            Helpers::flash('error', 'La empresa es obligatoria. Por favor, selecciona una de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // 4b. Validar que el ID de la empresa realmente exista en la base de datos
        $empresaExiste = false;
        foreach ($this->cat->empresas() as $empresa) {
            // Comparamos como string por seguridad
            if ((string)$empresa['id'] === (string)$empresa_id) {
                $empresaExiste = true;
                break;
            }
        }

        if (!$empresaExiste) {
            Helpers::flash('error', 'Error de seguridad: La empresa seleccionada no es válida o ya no existe.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }



        // --- 5. VALIDACIÓN DE LA CAMPAÑA (Obligatoria, sin espacios y Existente) ---
        // Aplicamos trim() para eliminar cualquier espacio en blanco accidental o intencional
        $campana_id = trim((string)($in['campana_id'] ?? ''));
        
        // 5a. Validar que no esté vacío
        if ($campana_id === '') {
            Helpers::flash('error', 'La campaña es obligatoria. Por favor, selecciona una de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // 5b. Validar que el ID de la campaña realmente exista en el catálogo
        $campanaExiste = false;
        foreach ($this->cat->campanas() as $campana) {
            if ((string)$campana['id'] === $campana_id) {
                $campanaExiste = true;
                break;
            }
        }

        if (!$campanaExiste) {
            Helpers::flash('error', 'Error de seguridad: La campaña seleccionada no es válida.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }





        // --- 6. VALIDACIÓN DEL PROYECTO (Obligatorio, sin espacios y Existente) ---
        $proyecto_id = trim((string)($in['proyecto_id'] ?? ''));
        
        // 6a. Validar que no esté vacío
        if ($proyecto_id === '') {
            Helpers::flash('error', 'El proyecto es obligatorio. Por favor, selecciona uno de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // 6b. Validar que el ID del proyecto realmente exista en el catálogo
        $proyectoExiste = false;
        foreach ($this->cat->proyectos() as $proyecto) {
            if ((string)$proyecto['id'] === $proyecto_id) {
                $proyectoExiste = true;
                break;
            }
        }

        if (!$proyectoExiste) {
            Helpers::flash('error', 'Error de seguridad: El proyecto seleccionado no es válido.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }




      // --- 7. VALIDACIÓN DEL EMAIL (Obligatorio y con formato estricto) ---
        $email = trim((string)($in['email'] ?? ''));
        
        // 7a. Validar que no esté vacío
        if ($email === '') {
            Helpers::flash('error', 'El correo electrónico es obligatorio.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // 7b. Validar el formato con filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Helpers::flash('error', 'El formato del correo electrónico no es válido. Debe incluir un "@" y un dominio válido (ej. usuario@empresa.com).');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // --- 8. VALIDACIÓN DEL CANAL (Obligatorio y Existente) ---
        $canal_id = trim((string)($in['canal_id'] ?? ''));
        
        // 8a. Validar que no esté vacío
        if ($canal_id === '') {
            Helpers::flash('error', 'El canal es obligatorio. Por favor, selecciona uno de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        // 8b. Validar que el ID del canal realmente exista en el catálogo
        $canalExiste = false;
        foreach ($this->cat->canales() as $canal) {
            if ((string)$canal['id'] === $canal_id) {
                $canalExiste = true;
                break;
            }
        }

        if (!$canalExiste) {
            Helpers::flash('error', 'Error de seguridad: El canal seleccionado no es válido.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }



        // --- 9. VALIDACIÓN DE MQL (Obligatorio y Existente) ---
        $mql_id = trim((string)($in['mql_id'] ?? ''));
        
        // 9a. Validar que no esté vacío
        if ($mql_id === '') {
            Helpers::flash('error', 'El Nivel de Interés (MQL) es obligatorio. Por favor, selecciona uno.');
            Helpers::redirect($rutaRetorno);
            exit;
        }

        // 9b. Validar que el ID del MQL realmente exista en el catálogo
        $mqlExiste = false;
        foreach ($this->cat->mql() as $mql) {
            if ((string)$mql['id'] === $mql_id) {
                $mqlExiste = true;
                break;
            }
        }

        if (!$mqlExiste) {
            Helpers::flash('error', 'Error de seguridad: El Nivel de Interés seleccionado no es válido.');
            Helpers::redirect($rutaRetorno);
            exit;
        }



        // --- 10. VALIDACIÓN DEL AGENTE Y AUTO-ASIGNACIÓN (SOLUCIÓN DEFINITIVA) ---
        $rolUsuario = Auth::role();
        $perfilId = Auth::profileId();

        // Validamos si es Admin o Supervisor para que puedan elegir de la lista
        if ($rolUsuario === 'admin' || $rolUsuario === 'supervisor') {
            $agente_id = trim((string)($in['agente_id'] ?? ''));
            
            // 10a. Validar que no esté vacío
            if ($agente_id === '') {
                Helpers::flash('error', 'El agente es obligatorio. Por favor, selecciona uno de la lista.');
                Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
                exit;
            }

            // 10b. Validar que el ID del agente realmente exista en el catálogo
            $agenteExiste = false;
            foreach ($this->cat->agentes() as $agente) {
                if ((string)$agente['id'] === $agente_id) {
                    $agenteExiste = true;
                    break;
                }
            }

            if (!$agenteExiste) {
                Helpers::flash('error', 'Error de seguridad: El agente seleccionado no es válido.');
                Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
                exit;
            }
        } else {
            // 10c. AUTO-ASIGNACIÓN PARA OPERADORES (Creación JIT)
            $db = Database::pdo();
            
            // 1. Buscamos si este perfil de operador ya está registrado como agente
            $stmt = $db->prepare("SELECT id FROM agentes WHERE profile_id = ?");
            $stmt->execute([$perfilId]);
            $agenteDB = $stmt->fetch();

            if ($agenteDB) {
                // Si ya existe, tomamos su ID oficial de la tabla agentes
                $agente_id = $agenteDB['id'];
            } else {
                // NO EXISTE: Lo insertamos silenciosamente para evitar el Error 1452
                $stmtNom = $db->prepare("SELECT nombre FROM profiles WHERE id = ?");
                $stmtNom->execute([$perfilId]);
                $perfilInfo = $stmtNom->fetch();
                $nombreReal = $perfilInfo ? $perfilInfo['nombre'] : 'Operador JIT';

                // Insertamos en la tabla agentes delegando a MySQL la creación del UUID
                $stmtInsert = $db->prepare("INSERT INTO agentes (id, nombre, profile_id, activo) VALUES (UUID(), ?, ?, 1)");
                $stmtInsert->execute([$nombreReal, $perfilId]);
                
                // Recuperamos el nuevo ID oficial
                $stmtGetId = $db->prepare("SELECT id FROM agentes WHERE profile_id = ?");
                $stmtGetId->execute([$perfilId]);
                $nuevoAgente = $stmtGetId->fetch();
                
                // Asignamos el ID correcto y seguro al lead
                $agente_id = $nuevoAgente['id'];
            }
        }



        // --- 11. VALIDACIÓN DEL TIPO DE PAGO ---
        $tipo_pago = trim((string)($in['tipo_pago'] ?? ''));
        
        // 11a. Validar que no esté vacío
        if ($tipo_pago === '') {
            Helpers::flash('error', 'El tipo de pago es obligatorio. Por favor, selecciona una opción.');
            Helpers::redirect($rutaRetorno);
            exit;
        }

        // 11b. Validar que sea un valor permitido contra la inyección de datos
        $tiposPermitidos = ['Contado', 'Plazo'];
        if (!in_array($tipo_pago, $tiposPermitidos, true)) {
            Helpers::flash('error', 'Error de seguridad: El tipo de pago seleccionado no es válido.');
            Helpers::redirect($rutaRetorno);
            exit;
        }



        // --- 12. VALIDACIÓN DE LA CIUDAD ---
        $ciudad = trim((string)($in['ciudad'] ?? ''));
        if ($ciudad === '') {
            Helpers::flash('error', 'La ciudad es obligatoria. Por favor, selecciona una opción.');
            Helpers::redirect($rutaRetorno);
            exit;
        }



        // --- RETORNO DE DATOS LIMPIOS ---
        return [
            'nombre'       => $nombre,
            'telefono'     => $telefono,
            'email'        => $email,
            'pais_id'      => $pais_id ?: null,
            'ciudad'       => $ciudad,
            'empresa_id'   => $in['empresa_id']  ?: null,
            'proyecto_id'  => $in['proyecto_id'] ?: null,
            'campana_id'   => $in['campana_id']  ?: null,
            'canal_id'     => $canal_id,
            'mql_id'       => $mql_id,
            'temperatura'  => in_array($in['temperatura'] ?? '', ['frio','tibio','caliente'], true) ? $in['temperatura'] : 'frio',
            'tipo_pago'    => $tipo_pago,
            'comentario'   => trim((string)($in['comentario'] ?? '')),
            'agente_id'    => $agente_id,
            'fecha_lead'   => $in['fecha_lead'] ?: date('Y-m-d'),
        ];
    }
}