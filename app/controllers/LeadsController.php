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
        
        $search = trim((string)($_GET['search'] ?? ''));
        $rows = $this->leads->listForRole(Auth::role(), Auth::profileId(), $search);
        
        $this->view('leads/index', [
            'leads'      => $rows,
            'catalog'    => $this->cat,
            'canDerivar' => Auth::hasRole('admin','supervisor','operador'),
            'search'     => $search
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

        if (!empty($data['telefono']) && $this->leads->findByPhone($data['telefono'])) {
            Helpers::flash('error', 'Ya existe un lead con ese teléfono.');
            Helpers::redirect('/leads/nuevo');
            return;
        }

        $data['operador_id'] = Auth::profileId();
        
        // Creamos el lead y obtenemos su ID
        $newLeadId = $this->leads->create($data); 
        
        if (isset($_POST['se_envio_mensaje']) && $_POST['se_envio_mensaje'] == '1') {
            $this->seg->create([
                'lead_id'    => $newLeadId,
                'estado'     => 'se_envio_mensaje',
                'comentario' => 'Mensaje enviado registrado desde el formulario principal.'
            ]);
        }
        
        // ==========================================
        // DERIVACIÓN AL INSTANTE (CON JIT VENDEDOR)
        // ==========================================
        $profileIdVendedor = trim((string)($_POST['vendedor_tabla_id'] ?? ''));
    
    if ($profileIdVendedor !== '') {
        try {
            $db = Database::pdo();
            // 1. Buscamos si el UUID ya tiene un número en la tabla vendedor
            $stmtCheck = $db->prepare("SELECT id FROM vendedor WHERE profile_id = ?");
            $stmtCheck->execute([$profileIdVendedor]);
            $vendRow = $stmtCheck->fetch();

            if ($vendRow && !empty($vendRow['id'])) {
                $vendId = (int)$vendRow['id'];
            } else {
                // 2. Si no existe, sacamos su nombre y lo registramos
                $stmtNom = $db->prepare("SELECT nombre FROM profiles WHERE id = ?");
                $stmtNom->execute([$profileIdVendedor]);
                $nombreVend = $stmtNom->fetchColumn() ?: "Vendedor JIT";

                // Insertamos y dejamos que MySQL le asigne el autoincremento (INT)
                $stmtIns = $db->prepare("INSERT INTO vendedor (nombre, profile_id) VALUES (?, ?)");
                $stmtIns->execute([$nombreVend, $profileIdVendedor]);
                $vendId = (int)$db->lastInsertId();
            }

            // Ejecutamos la derivación automática con el INT validado
            $this->leads->derivar((string)$newLeadId, $vendId);
            Helpers::flash('ok', 'Lead creado y derivado a ventas exitosamente.');
            Helpers::redirect('/leads');
            return; 
        } catch (Throwable $e) {
            Helpers::flash('error', 'Lead creado, pero hubo un error de DB al derivar: ' . $e->getMessage());
            Helpers::redirect('/leads');
            return;
        }
    }
        
        Helpers::flash('ok', 'Lead creado correctamente.');
        Helpers::redirect('/leads');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $lead = $this->leads->find($id);
        if (!$lead) { http_response_code(404); exit('Lead no encontrado'); }

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
    
    $profileIdVendedor = trim((string)($_POST['vendedor_tabla_id'] ?? ''));
    
    if ($profileIdVendedor === '') { 
        Helpers::flash('error', 'Por favor, selecciona un vendedor de la lista antes de presionar Derivar.'); 
        Helpers::redirect('/leads/'.$id.'/editar'); 
        return;
    }
    
    try {
        $db = Database::pdo();
        $stmtCheck = $db->prepare("SELECT id FROM vendedor WHERE profile_id = ?");
        $stmtCheck->execute([$profileIdVendedor]);
        $vendRow = $stmtCheck->fetch();

        if ($vendRow && !empty($vendRow['id'])) {
            $vendId = (int)$vendRow['id'];
        } else {
            $stmtNom = $db->prepare("SELECT nombre FROM profiles WHERE id = ?");
            $stmtNom->execute([$profileIdVendedor]);
            $nombreVend = $stmtNom->fetchColumn() ?: "Vendedor JIT";

            $stmtIns = $db->prepare("INSERT INTO vendedor (nombre, profile_id) VALUES (?, ?)");
            $stmtIns->execute([$nombreVend, $profileIdVendedor]);
            $vendId = (int)$db->lastInsertId();
        }
        
        $this->leads->derivar($id, $vendId);
        Helpers::flash('ok', 'Lead derivado a ventas exitosamente.');
    } catch (Throwable $e) {
        Helpers::flash('error', 'Error en la base de datos al derivar: ' . $e->getMessage());
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

    public function export(): void
    {
        $this->requireAuth();
        $rows = $this->leads->listForRole(Auth::role(), Auth::profileId(), '');
        
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


   /* 
    private function validate(array $in): array
    {
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

        $pais_id = $in['pais_id'] ?? null;
        if ($pais_id) {
            $nombrePais = '';
            foreach ($this->cat->paises() as $p) {
                if ($p['id'] == $pais_id) {
                    $nombrePais = $p['nombre'];
                    break;
                }
            }
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
            if ($nombrePais && isset($prefijos[$nombrePais])) {
                $prefijoRequerido = $prefijos[$nombrePais];
                if (strpos($telefono, $prefijoRequerido) !== 0) {
                    Helpers::flash('error', "Conflicto: Elegiste $nombrePais, por lo tanto el teléfono DEBE comenzar con $prefijoRequerido.");
                    Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
                    exit;
                }
            }
        }

        $empresa_id = $in['empresa_id'] ?? '';
        if ($empresa_id === '') {
            Helpers::flash('error', 'La empresa es obligatoria. Por favor, selecciona una de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }
        $empresaExiste = false;
        foreach ($this->cat->empresas() as $empresa) {
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

        $campana_id = trim((string)($in['campana_id'] ?? ''));
        if ($campana_id === '') {
            Helpers::flash('error', 'La campaña es obligatoria. Por favor, selecciona una de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }
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

        $proyecto_id = trim((string)($in['proyecto_id'] ?? ''));
        if ($proyecto_id === '') {
            Helpers::flash('error', 'El proyecto es obligatorio. Por favor, selecciona uno de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }
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

        $email = trim((string)($in['email'] ?? ''));
        if ($email === '') {
            Helpers::flash('error', 'El correo electrónico es obligatorio.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Helpers::flash('error', 'El formato del correo electrónico no es válido. Debe incluir un "@" y un dominio válido.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }

        $canal_id = trim((string)($in['canal_id'] ?? ''));
        if ($canal_id === '') {
            Helpers::flash('error', 'El canal es obligatorio. Por favor, selecciona uno de la lista.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
            exit;
        }
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

        $mql_id = trim((string)($in['mql_id'] ?? ''));
        $rutaRetorno = $_SERVER['HTTP_REFERER'] ?? '/leads';
        if ($mql_id === '') {
            Helpers::flash('error', 'El Nivel de Interés (MQL) es obligatorio. Por favor, selecciona uno.');
            Helpers::redirect($rutaRetorno);
            exit;
        }
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

        // --- VALIDACIÓN DEL AGENTE (CORREGIDA SIN UUID) ---
        $rolUsuario = Auth::role();
        $perfilId = Auth::profileId();

        if ($rolUsuario === 'admin' || $rolUsuario === 'supervisor') {
            $agente_id = trim((string)($in['agente_id'] ?? ''));
            if ($agente_id === '') {
                Helpers::flash('error', 'El agente es obligatorio. Por favor, selecciona uno de la lista.');
                Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads');
                exit;
            }
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
            // AUTO-ASIGNACIÓN SEGURA PARA OPERADORES
            $db = Database::pdo();
            
            $stmt = $db->prepare("SELECT id FROM agentes WHERE profile_id = ?");
            $stmt->execute([$perfilId]);
            $agenteDB = $stmt->fetch();

            if ($agenteDB && !empty($agenteDB['id'])) {
                $agente_id = $agenteDB['id'];
            } else {
                $stmtNom = $db->prepare("SELECT nombre FROM profiles WHERE id = ?");
                $stmtNom->execute([$perfilId]);
                $perfilInfo = $stmtNom->fetch();
                $nombreReal = $perfilInfo ? $perfilInfo['nombre'] : 'Operador JIT';


                // Usamos autoincremento normal de MySQL (no UUID)
                ///$stmtInsert = $db->prepare("INSERT INTO agentes (nombre, profile_id, activo) VALUES (?, ?, 1)");
                ///$stmtInsert->execute([$nombreReal, $perfilId]);
                
                ////$agente_id = $db->lastInsertId();



                // Generamos el UUID con el Helper del sistema
                $agente_id = Helpers::uuid(); 

                // Inyectamos el ID explícitamente en el INSERT
                $stmtInsert = $db->prepare("INSERT INTO agentes (id, nombre, profile_id, activo) VALUES (?, ?, ?, 1)");
                $stmtInsert->execute([$agente_id, $nombreReal, $perfilId]);

            }
        }

        $tipo_pago = trim((string)($in['tipo_pago'] ?? ''));
        if ($tipo_pago === '') {
            Helpers::flash('error', 'El tipo de pago es obligatorio.');
            Helpers::redirect($rutaRetorno);
            exit;
        }
        $tiposPermitidos = ['Contado', 'Plazo'];
        if (!in_array($tipo_pago, $tiposPermitidos, true)) {
            Helpers::flash('error', 'Error de seguridad: El tipo de pago seleccionado no es válido.');
            Helpers::redirect($rutaRetorno);
            exit;
        }

        $ciudad = trim((string)($in['ciudad'] ?? ''));
        if ($ciudad === '') {
            Helpers::flash('error', 'La ciudad es obligatoria.');
            Helpers::redirect($rutaRetorno);
            exit;
        }

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

    */






    private function validate(array $in): array
    {
        // 1. Recibimos todos los campos de texto permitiendo que vengan en blanco
        $nombre     = trim((string)($in['nombre'] ?? ''));
        $telefono   = trim((string)($in['telefono'] ?? ''));
        $email      = trim((string)($in['email'] ?? ''));
        $ciudad     = trim((string)($in['ciudad'] ?? ''));
        $tipo_pago  = trim((string)($in['tipo_pago'] ?? ''));
        $comentario = trim((string)($in['comentario'] ?? ''));

        // 2. Para las llaves foráneas (listas desplegables), si vienen vacías pasamos NULL 
        // para evitar errores de restricción en MySQL
        $pais_id     = !empty($in['pais_id']) ? $in['pais_id'] : null;
        $empresa_id  = !empty($in['empresa_id']) ? $in['empresa_id'] : null;
        $proyecto_id = !empty($in['proyecto_id']) ? $in['proyecto_id'] : null;
        $campana_id  = !empty($in['campana_id']) ? $in['campana_id'] : null;
        $canal_id    = !empty($in['canal_id']) ? $in['canal_id'] : null;
        $mql_id      = !empty($in['mql_id']) ? $in['mql_id'] : null;
        
        $temperatura = in_array($in['temperatura'] ?? '', ['frio','tibio','caliente'], true) ? $in['temperatura'] : 'frio';
        $fecha_lead  = !empty($in['fecha_lead']) ? $in['fecha_lead'] : date('Y-m-d');

        // 3. Lógica interna de asignación de agentes (Mantuvimos el UUID que arreglamos hoy)
        $rolUsuario = Auth::role();
        $perfilId = Auth::profileId();
        $agente_id = null;

        if ($rolUsuario === 'admin' || $rolUsuario === 'supervisor') {
            $agente_id = !empty($in['agente_id']) ? trim((string)$in['agente_id']) : null;
        } else {
            $db = Database::pdo();
            $stmt = $db->prepare("SELECT id FROM agentes WHERE profile_id = ?");
            $stmt->execute([$perfilId]);
            $agenteDB = $stmt->fetch();

            if ($agenteDB && !empty($agenteDB['id'])) {
                $agente_id = $agenteDB['id'];
            } else {
                $stmtNom = $db->prepare("SELECT nombre FROM profiles WHERE id = ?");
                $stmtNom->execute([$perfilId]);
                $perfilInfo = $stmtNom->fetch();
                $nombreReal = $perfilInfo ? $perfilInfo['nombre'] : 'Operador JIT';

                $agente_id = Helpers::uuid(); 
                $stmtInsert = $db->prepare("INSERT INTO agentes (id, nombre, profile_id, activo) VALUES (?, ?, ?, 1)");
                $stmtInsert->execute([$agente_id, $nombreReal, $perfilId]);
            }
        }

        // 4. Retornamos el arreglo directo a la base de datos sin restricciones
        return [
            'nombre'       => $nombre,
            'telefono'     => $telefono,
            'email'        => $email,
            'pais_id'      => $pais_id,
            'ciudad'       => $ciudad,
            'empresa_id'   => $empresa_id,
            'proyecto_id'  => $proyecto_id,
            'campana_id'   => $campana_id,
            'canal_id'     => $canal_id,
            'mql_id'       => $mql_id,
            'temperatura'  => $temperatura,
            'tipo_pago'    => $tipo_pago,
            'comentario'   => $comentario,
            'agente_id'    => $agente_id,
            'fecha_lead'   => $fecha_lead,
        ];
    }





}