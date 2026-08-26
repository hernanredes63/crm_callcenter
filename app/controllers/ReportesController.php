<?php
declare(strict_types=1);

final class ReportesController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $pdo = Database::pdo();

        // Leads (para todos los reportes de leads tradicionales)
        $leads = $pdo->query("
            SELECT l.id, l.nombre, l.telefono, l.temperatura, l.fecha_lead, l.created_at,
                   l.derivado_ventas, l.fecha_derivado_ventas,
                   l.empresa_id, e.nombre  AS empresa_nombre,
                   c.nombre  AS campana_nombre,
                   ca.nombre AS canal_nombre,
                   m.nivel   AS mql_nivel,
                   po.nombre AS operador_nombre,
                   v.nombre  AS vendedor_tabla_nombre
              FROM leads l
              LEFT JOIN empresas   e  ON e.id  = l.empresa_id
              LEFT JOIN campanas   c  ON c.id  = l.campana_id
              LEFT JOIN canales    ca ON ca.id = l.canal_id
              LEFT JOIN mql_status m  ON m.id  = l.mql_id
              LEFT JOIN profiles   po ON po.id = l.operador_id
              LEFT JOIN vendedor   v  ON v.id  = l.vendedor_tabla_id
        ")->fetchAll();

        // Leads Redes
        $leadsRedes = $pdo->query("
            SELECT lr.id, lr.nombre, lr.temperatura, lr.fecha_lead, lr.created_at,
                   lr.empresa_id, e.nombre  AS empresa_nombre,
                   c.nombre  AS campana_nombre,
                   ca.nombre AS canal_nombre,
                   po.nombre AS operador_nombre
              FROM leads_redes lr
              LEFT JOIN empresas e  ON e.id  = lr.empresa_id
              LEFT JOIN campanas c  ON c.id  = lr.campana_id
              LEFT JOIN canales  ca ON ca.id = lr.canal_id
              LEFT JOIN profiles po ON po.id = lr.operador_id
        ")->fetchAll();

        // Contactabilidad: nº de seguimientos por lead + primer estado
        $seg = $pdo->query("
            SELECT s.lead_id, s.iteracion, s.estado
              FROM seguimientos s
             ORDER BY s.lead_id, s.iteracion
        ")->fetchAll();

        $empresas = $pdo->query("SELECT id, nombre FROM empresas ORDER BY nombre")->fetchAll();

        $this->view('reportes/index', [
            'leads'       => $leads,
            'leadsRedes'  => $leadsRedes,
            'seguimientos'=> $seg,
            'empresas'    => $empresas,
        ]);
    }
}
