<?php
declare(strict_types=1);

final class DerivadosController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $sql = "SELECT l.id, l.nombre, l.telefono, l.fecha_derivado_ventas, l.created_at,
                       po.nombre AS operador_nombre,
                       v.nombre  AS vendedor_tabla_nombre,
                       e.nombre  AS empresa_nombre,
                       c.nombre  AS campana_nombre
                FROM leads l
                LEFT JOIN profiles po ON po.id = l.operador_id
                LEFT JOIN vendedor v  ON v.id  = l.vendedor_tabla_id
                LEFT JOIN empresas e  ON e.id  = l.empresa_id
                LEFT JOIN campanas c  ON c.id  = l.campana_id
                WHERE l.derivado_ventas = 1
                ORDER BY l.fecha_derivado_ventas DESC";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $this->view('derivados/index', ['derivados' => $rows]);
    }
}
