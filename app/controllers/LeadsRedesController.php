<?php
declare(strict_types=1);

final class LeadsRedesController extends Controller
{
    private LeadRedes $leads;
    private SeguimientoRedes $seg;
    private Catalog $cat;

    public function __construct()
    {
        $this->leads = new LeadRedes();
        $this->seg   = new SeguimientoRedes();
        $this->cat   = new Catalog();
    }

    public function index(): void
    {
        $this->requireAuth();
        $rows = $this->leads->listForRole(Auth::role(), Auth::profileId());
        $this->view('leads_redes/index', [
            'leads'   => $rows,
            'catalog' => $this->cat,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('leads_redes/form', [
            'lead' => null, 'catalog' => $this->cat,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        Helpers::csrfCheck();
        $data = $this->validate($_POST);
        $data['operador_id'] = Auth::profileId();
        $this->leads->create($data);
        Helpers::flash('ok', 'Lead Redes creado.');
        Helpers::redirect('/leads-redes');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $lead = $this->leads->find($id);
        if (!$lead) { http_response_code(404); exit('Lead no encontrado'); }
        if (Auth::role() === 'operador' && $lead['operador_id'] !== Auth::profileId()) {
            http_response_code(403); exit('No tienes permisos.');
        }
        $this->view('leads_redes/form', [
            'lead'         => $lead,
            'catalog'      => $this->cat,
            'seguimientos' => $this->seg->forLead($id),
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
        Helpers::flash('ok', 'Lead Redes actualizado.');
        Helpers::redirect('/leads-redes/' . $id . '/editar');
    }

    public function destroy(string $id): void
    {
        $this->requireRole('admin','supervisor');
        Helpers::csrfCheck();
        $this->leads->delete($id);
        Helpers::flash('ok', 'Lead Redes eliminado.');
        Helpers::redirect('/leads-redes');
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

    public function export(): void
    {
        $this->requireAuth();
        $rows = $this->leads->listForRole(Auth::role(), Auth::profileId());
        $ids = array_column($rows, 'id');
        $segs = [];
        if ($ids) {
            $ph = implode(',', array_fill(0, count($ids), '?'));
            $st = Database::pdo()->prepare(
                "SELECT * FROM seguimientos_redes WHERE lead_id IN ($ph) ORDER BY lead_id, iteracion"
            );
            $st->execute($ids);
            foreach ($st->fetchAll() as $s) { $segs[$s['lead_id']][] = $s; }
        }
        foreach ($rows as &$r) { $r['seguimientos'] = $segs[$r['id']] ?? []; }
        $this->json(['leads' => $rows]);
    }

    private function validate(array $in): array
    {
        $nombre = trim((string)($in['nombre'] ?? ''));
        if ($nombre === '') {
            Helpers::flash('error', 'El nombre es obligatorio.');
            Helpers::redirect($_SERVER['HTTP_REFERER'] ?? '/leads-redes');
        }
        return [
            'nombre'      => $nombre,
            'telefono'    => trim((string)($in['telefono'] ?? '')),
            'email'       => trim((string)($in['email'] ?? '')),
            'pais_id'     => $in['pais_id'] ?: null,
            'ciudad'      => trim((string)($in['ciudad'] ?? '')),
            'empresa_id'  => $in['empresa_id']  ?: null,
            'proyecto_id' => $in['proyecto_id'] ?: null,
            'campana_id'  => $in['campana_id']  ?: null,
            'canal_id'    => $in['canal_id']    ?: null,
            'mql_id'      => $in['mql_id']      ?: null,
            'temperatura' => in_array($in['temperatura'] ?? '', ['frio','tibio','caliente'], true) ? $in['temperatura'] : 'frio',
            'tipo_pago'   => trim((string)($in['tipo_pago'] ?? '')),
            'comentario'  => trim((string)($in['comentario'] ?? '')),
            'agente_id'   => $in['agente_id'] ?: null,
            'fecha_lead'  => $in['fecha_lead'] ?: date('Y-m-d'),
        ];
    }
}
