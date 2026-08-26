<?php
declare(strict_types=1);

final class CalendarioController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $user = Auth::user();
        $role = Auth::role();
        $isAdminSup = in_array($role, ['admin', 'supervisor'], true);

        // Mes visible (YYYY-MM); por defecto mes actual
        $monthParam = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $monthParam)) $monthParam = date('Y-m');
        $first = DateTime::createFromFormat('Y-m-d', $monthParam . '-01');
        $start = $first->format('Y-m-d 00:00:00');
        $end   = (clone $first)->modify('last day of this month')->format('Y-m-d 23:59:59');

        // Filtro por agente (solo admin/supervisor)
        $agenteFilter = $_GET['agente'] ?? 'all';

        // Cargar operadores para el filtro
        $agentes = [];
        if ($isAdminSup) {
            $stmt = Database::pdo()->query("SELECT id, nombre FROM profiles ORDER BY nombre");
            $agentes = $stmt->fetchAll();
        }

        // Traer seguimientos del mes con fecha_proximo_contacto
        $sql = "SELECT s.id, s.lead_id, s.iteracion, s.estado, s.comentario,
                       s.fecha_proximo_contacto,
                       l.nombre AS lead_nombre, l.telefono AS lead_telefono,
                       l.operador_id, po.nombre AS operador_nombre
                FROM seguimientos s
                INNER JOIN leads l ON l.id = s.lead_id
                LEFT JOIN profiles po ON po.id = l.operador_id
                WHERE s.fecha_proximo_contacto IS NOT NULL
                  AND s.fecha_proximo_contacto BETWEEN ? AND ?";
        $params = [$start, $end];

        if (!$isAdminSup) {
            // Operador solo ve los suyos
            $profileId = $this->profileIdFor($user['id']);
            $sql .= " AND l.operador_id = ?";
            $params[] = $profileId;
        } elseif ($agenteFilter !== 'all') {
            $sql .= " AND l.operador_id = ?";
            $params[] = $agenteFilter;
        }

        $sql .= " ORDER BY s.fecha_proximo_contacto ASC";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Agrupar por día (Y-m-d) y calcular hoy / atrasados
        $today = date('Y-m-d');
        $byDate = [];
        $todayTasks = [];
        $overdueTasks = [];
        foreach ($rows as $r) {
            $d = substr($r['fecha_proximo_contacto'], 0, 10);
            $byDate[$d][] = $r;
            if ($d === $today) $todayTasks[] = $r;
            elseif ($d < $today) $overdueTasks[] = $r;
        }

        $this->view('calendario/index', [
            'monthParam'    => $monthParam,
            'first'         => $first,
            'byDate'        => $byDate,
            'todayTasks'    => $todayTasks,
            'overdueTasks'  => $overdueTasks,
            'agentes'       => $agentes,
            'agenteFilter'  => $agenteFilter,
            'isAdminSup'    => $isAdminSup,
        ]);
    }

    private function profileIdFor(string $userId): ?string
    {
        $stmt = Database::pdo()->prepare("SELECT id FROM profiles WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row['id'] ?? null;
    }
}
