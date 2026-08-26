<?php
declare(strict_types=1);

final class LeadRedes extends Model
{
    protected string $table = 'leads_redes';

    public function listForRole(?string $role, ?string $profileId): array
    {
        $sql = "SELECT l.*,
                       e.nombre  AS empresa_nombre,
                       c.nombre  AS campana_nombre,
                       ca.nombre AS canal_nombre,
                       m.nivel   AS mql_nivel,
                       po.nombre AS operador_nombre,
                       a.nombre  AS agente_nombre,
                       pa.nombre AS pais_nombre
                FROM leads_redes l
                LEFT JOIN empresas   e  ON e.id  = l.empresa_id
                LEFT JOIN campanas   c  ON c.id  = l.campana_id
                LEFT JOIN canales    ca ON ca.id = l.canal_id
                LEFT JOIN mql_status m  ON m.id  = l.mql_id
                LEFT JOIN profiles   po ON po.id = l.operador_id
                LEFT JOIN agentes    a  ON a.id  = l.agente_id
                LEFT JOIN paises     pa ON pa.id = l.pais_id";
        $params = [];
        if ($role === 'operador') {
            $sql .= ' WHERE l.operador_id = ?';
            $params[] = $profileId;
        }
        $sql .= ' ORDER BY l.created_at DESC';
        return $this->query($sql, $params);
    }

    public function create(array $d): string
    {
        $id = Helpers::uuid();
        $this->exec(
            'INSERT INTO leads_redes
              (id, nombre, telefono, email, pais_id, ciudad, empresa_id, proyecto_id,
               campana_id, canal_id, mql_id, temperatura, tipo_pago, comentario,
               operador_id, agente_id, fecha_lead)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $id, $d['nombre'],
                $d['telefono'] ?: null, $d['email'] ?: null,
                $d['pais_id'] ?: null,  $d['ciudad'] ?: null,
                $d['empresa_id'] ?: null, $d['proyecto_id'] ?: null,
                $d['campana_id'] ?: null, $d['canal_id'] ?: null,
                $d['mql_id'] ?: null,
                $d['temperatura'] ?? 'frio',
                $d['tipo_pago'] ?: null, $d['comentario'] ?: null,
                $d['operador_id'] ?: null, $d['agente_id'] ?: null,
                $d['fecha_lead'] ?: date('Y-m-d'),
            ]
        );
        return $id;
    }

    public function update(string $id, array $d): void
    {
        $this->exec(
            'UPDATE leads_redes SET
                nombre=?, telefono=?, email=?, pais_id=?, ciudad=?,
                empresa_id=?, proyecto_id=?, campana_id=?, canal_id=?,
                mql_id=?, temperatura=?, tipo_pago=?, comentario=?,
                agente_id=?, fecha_lead=?
             WHERE id=?',
            [
                $d['nombre'], $d['telefono'] ?: null, $d['email'] ?: null,
                $d['pais_id'] ?: null, $d['ciudad'] ?: null,
                $d['empresa_id'] ?: null, $d['proyecto_id'] ?: null,
                $d['campana_id'] ?: null, $d['canal_id'] ?: null,
                $d['mql_id'] ?: null,
                $d['temperatura'] ?? 'frio',
                $d['tipo_pago'] ?: null, $d['comentario'] ?: null,
                $d['agente_id'] ?: null,
                $d['fecha_lead'] ?: date('Y-m-d'),
                $id,
            ]
        );
    }
}
