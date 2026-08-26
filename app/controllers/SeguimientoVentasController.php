<?php
declare(strict_types=1);

/**
 * Módulo Seguimiento Ventas.
 * Replica src/pages/SeguimientoVentas.tsx: gestión de seguimientos
 * (pendiente/visita/venta/perdido) sobre leads derivados a ventas.
 */
final class SeguimientoVentasController extends Controller
{
    /** Roles permitidos: vendedor ve solo lo suyo; admin/supervisor ven todo. */
    private function ensureAccess(): void
    {
        $this->requireRole('admin', 'supervisor', 'vendedor');
    }

    public function index(): void
    {
        $this->ensureAccess();

        $role   = Auth::role();
        $userId = Auth::user()['id'] ?? null;

        $desde  = $_GET['desde']  ?? '';
        $hasta  = $_GET['hasta']  ?? '';
        $estado = $_GET['estado'] ?? 'todos';

        // Vendedores disponibles
        $vendedores = Database::pdo()->query(
            "SELECT id, nombre, profile_id FROM vendedor ORDER BY nombre ASC"
        )->fetchAll();

        // Si es vendedor, filtrar al suyo
        if ($role === 'vendedor' && $userId) {
            $pStmt = Database::pdo()->prepare("SELECT id FROM profiles WHERE user_id = ? LIMIT 1");
            $pStmt->execute([$userId]);
            $profileId = $pStmt->fetchColumn();
            $vendedores = array_values(array_filter(
                $vendedores,
                fn($v) => $profileId && $v['profile_id'] === $profileId
            ));
        }

        $leads = [];
        $stats = ['total'=>0,'pendiente'=>0,'visita'=>0,'venta'=>0,'perdido'=>0];

        if ($vendedores) {
            $ids = array_column($vendedores, 'id');
            $in  = implode(',', array_fill(0, count($ids), '?'));
            $params = $ids;

            $where = "l.derivado_ventas = 1 AND l.vendedor_tabla_id IN ($in)";
            if ($desde !== '') { $where .= " AND l.fecha_derivado_ventas >= ?"; $params[] = $desde . ' 00:00:00'; }
            if ($hasta !== '') { $where .= " AND l.fecha_derivado_ventas <= ?"; $params[] = $hasta . ' 23:59:59'; }

            $sql = "SELECT l.id, l.nombre, l.telefono, l.email, l.ciudad, l.pais,
                           l.temperatura, l.fecha_lead, l.created_at,
                           l.ejecutivo_ventas, l.vendedor_tabla_id,
                           l.fecha_derivado_ventas,
                           v.nombre AS vendedor_nombre,
                           e.nombre AS empresa_nombre,
                           p.nombre AS proyecto_nombre,
                           c.nombre AS campana_nombre
                    FROM leads l
                    LEFT JOIN vendedor  v ON v.id = l.vendedor_tabla_id
                    LEFT JOIN empresas  e ON e.id = l.empresa_id
                    LEFT JOIN proyectos p ON p.id = l.proyecto_id
                    LEFT JOIN campanas  c ON c.id = l.campana_id
                    WHERE $where
                    ORDER BY l.fecha_derivado_ventas DESC";
            $stmt = Database::pdo()->prepare($sql);
            $stmt->execute($params);
            $leads = $stmt->fetchAll();

            if ($leads) {
                $leadIds = array_column($leads, 'id');
                $inL = implode(',', array_fill(0, count($leadIds), '?'));
                $stmt = Database::pdo()->prepare(
                    "SELECT * FROM seguimiento_ventas
                     WHERE lead_id IN ($inL)
                     ORDER BY created_at DESC"
                );
                $stmt->execute($leadIds);
                $segs = $stmt->fetchAll();

                $segMap = [];
                foreach ($segs as $s) { $segMap[$s['lead_id']][] = $s; }

                foreach ($leads as &$l) {
                    $l['seguimientos'] = $segMap[$l['id']] ?? [];
                    $l['ultimo_estado'] = $l['seguimientos'][0]['estado'] ?? 'pendiente';
                }
                unset($l);
            }

            foreach ($leads as $l) {
                $stats['total']++;
                $st = $l['ultimo_estado'] ?? 'pendiente';
                if (isset($stats[$st])) $stats[$st]++;
            }

            if ($estado !== 'todos') {
                $leads = array_values(array_filter(
                    $leads,
                    fn($l) => ($l['ultimo_estado'] ?? 'pendiente') === $estado
                ));
            }
        }

        $this->view('seguimiento_ventas/index', [
            'leads'      => $leads,
            'stats'      => $stats,
            'desde'      => $desde,
            'hasta'      => $hasta,
            'estado'     => $estado,
            'canManage'  => in_array($role, ['admin','supervisor','vendedor'], true),
        ]);
    }

    /** Registrar un nuevo seguimiento (POST). */
    public function store(string $leadId): void
    {
        $this->ensureAccess();
        Helpers::csrfCheck();

        $estado       = $_POST['estado']        ?? '';
        $comentario   = trim((string)($_POST['comentario'] ?? ''));
        $fechaVisita  = $_POST['fecha_visita']  ?? '';
        $fechaVenta   = $_POST['fecha_venta']   ?? '';

        $validos = ['pendiente','visita','venta','perdido'];
        if (!in_array($estado, $validos, true)) {
            $_SESSION['flash_error'] = 'Estado inválido.';
            Helpers::redirect('/seguimiento-ventas');
        }

        // vendedor_tabla_id del lead
        $stmt = Database::pdo()->prepare(
            "SELECT vendedor_tabla_id FROM leads WHERE id = ? AND derivado_ventas = 1 LIMIT 1"
        );
        $stmt->execute([$leadId]);
        $vt = $stmt->fetchColumn();
        if (!$vt) {
            $_SESSION['flash_error'] = 'Lead no derivado o inexistente.';
            Helpers::redirect('/seguimiento-ventas');
        }

        // Si es vendedor, verificar que sea el suyo
        if (Auth::role() === 'vendedor') {
            $userId = Auth::user()['id'] ?? null;
            $p = Database::pdo()->prepare("SELECT id FROM profiles WHERE user_id = ? LIMIT 1");
            $p->execute([$userId]);
            $profileId = $p->fetchColumn();
            $ok = Database::pdo()->prepare(
                "SELECT 1 FROM vendedor WHERE id = ? AND profile_id = ? LIMIT 1"
            );
            $ok->execute([(int)$vt, $profileId]);
            if (!$ok->fetchColumn()) {
                http_response_code(403);
                exit('No puedes registrar seguimientos para este lead.');
            }
        }

        $stmt = Database::pdo()->prepare(
            "INSERT INTO seguimiento_ventas
             (id, lead_id, vendedor_tabla_id, estado, comentario, fecha_visita, fecha_venta)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            Helpers::uuid(),
            $leadId,
            (int)$vt,
            $estado,
            $comentario !== '' ? $comentario : null,
            $fechaVisita !== '' ? $fechaVisita : null,
            $fechaVenta  !== '' ? $fechaVenta  : null,
        ]);

        $_SESSION['flash_ok'] = 'Seguimiento registrado.';
        Helpers::redirect('/seguimiento-ventas');
    }
}
