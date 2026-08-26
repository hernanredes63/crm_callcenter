<?php
declare(strict_types=1);

/**
 * Módulo Ventas — resumen del equipo de vendedores.
 * Replica src/pages/Ventas.tsx: métricas por vendedor de leads derivados.
 */
final class VentasController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $role   = Auth::role();
        $userId = Auth::user()['id'] ?? null;

        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';

        // Vendedores (filtrar si el usuario logueado es vendedor)
        $sqlV = "SELECT v.id, v.nombre, v.profile_id
                 FROM vendedor v
                 ORDER BY v.nombre ASC";
        $vendedores = Database::pdo()->query($sqlV)->fetchAll();

        if ($role === 'vendedor' && $userId) {
            $pStmt = Database::pdo()->prepare("SELECT id FROM profiles WHERE user_id = ? LIMIT 1");
            $pStmt->execute([$userId]);
            $profileId = $pStmt->fetchColumn();
            $vendedores = array_values(array_filter(
                $vendedores,
                fn($v) => $profileId && $v['profile_id'] === $profileId
            ));
        }

        // Leads derivados a ventas + estado de seguimientos (para "perdidos")
        $leadsMap = [];
        if ($vendedores) {
            $ids = array_column($vendedores, 'id');
            $in  = implode(',', array_fill(0, count($ids), '?'));
            $params = $ids;

            $where = "l.derivado_ventas = 1 AND l.vendedor_tabla_id IN ($in)";
            if ($desde !== '') { $where .= " AND l.fecha_derivado_ventas >= ?"; $params[] = $desde . ' 00:00:00'; }
            if ($hasta !== '') { $where .= " AND l.fecha_derivado_ventas <= ?"; $params[] = $hasta . ' 23:59:59'; }

            $sqlL = "SELECT l.id, l.vendedor_tabla_id, l.temperatura,
                            MAX(CASE WHEN s.estado='no_le_interesa' THEN 1 ELSE 0 END) AS perdido
                     FROM leads l
                     LEFT JOIN seguimientos s ON s.lead_id = l.id
                     WHERE $where
                     GROUP BY l.id, l.vendedor_tabla_id, l.temperatura";
            $stmt = Database::pdo()->prepare($sqlL);
            $stmt->execute($params);
            foreach ($stmt->fetchAll() as $r) {
                $leadsMap[(string)$r['vendedor_tabla_id']][] = $r;
            }
        }

        // Métricas por vendedor
        $META = 20;
        $rows = [];
        $tot = ['leads'=>0,'visitas'=>0,'negociacion'=>0,'ventas'=>0];
        foreach ($vendedores as $v) {
            $leads = $leadsMap[(string)$v['id']] ?? [];
            $total = count($leads);
            $visitas = count(array_filter($leads, fn($l)=>$l['temperatura']==='caliente'));
            $negoc   = count(array_filter($leads, fn($l)=>$l['temperatura']==='tibio'));
            $perd    = count(array_filter($leads, fn($l)=>(int)$l['perdido']===1));
            $ventas  = $visitas;
            $conv    = $total>0 ? (int)round($ventas/$total*100) : 0;
            $efVis   = $total>0 ? (int)round($visitas/$total*100) : 0;
            $noReal  = $total>0 ? (int)round(($total-$visitas-$negoc)/$total*100) : 0;
            $rows[] = compact('total','visitas','negoc','perd','ventas','conv','efVis','noReal')
                    + ['id'=>$v['id'],'nombre'=>$v['nombre']];
            $tot['leads']       += $total;
            $tot['visitas']     += $visitas;
            $tot['negociacion'] += $negoc;
            $tot['ventas']      += $ventas;
        }

        $this->view('ventas/index', [
            'rows'  => $rows,
            'tot'   => $tot,
            'meta'  => $META,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }
}
