<?php
declare(strict_types=1);

final class Lead extends Model
{
    protected string $table = 'leads';

    /** Lista todos los leads visibles para el rol dado, con nombres relacionados. */
    // --- CAMBIO 1: Se agregó el parámetro opcional "$search = ''" a la función ---
    public function listForRole(?string $role, ?string $profileId, string $search = ''): array
    {
        $sql = "SELECT l.*,
                       e.nombre AS empresa_nombre,
                       c.nombre AS campana_nombre,
                       ca.nombre AS canal_nombre,
                       m.nivel  AS mql_nivel,
                       po.nombre AS operador_nombre,
                       pv.nombre AS vendedor_nombre,
                       v.nombre  AS vendedor_tabla_nombre,
                       a.nombre  AS agente_nombre
                FROM leads l
                LEFT JOIN empresas   e  ON e.id  = l.empresa_id
                LEFT JOIN campanas   c  ON c.id  = l.campana_id
                LEFT JOIN canales    ca ON ca.id = l.canal_id
                LEFT JOIN mql_status m  ON m.id  = l.mql_id
                LEFT JOIN profiles   po ON po.id = l.operador_id
                LEFT JOIN profiles   pv ON pv.id = l.vendedor_id
                LEFT JOIN vendedor   v  ON v.id  = l.vendedor_tabla_id
                LEFT JOIN agentes    a  ON a.id  = l.agente_id";
        
        // --- CAMBIO 2: Creamos un arreglo $where para apilar dinámicamente las condiciones SQL ---
        $where = [];
        $params = [];

        // RBAC en la capa de aplicación (equivalente a las RLS de Postgres)
        // --- CAMBIO 3: Cambiamos la concatenación directa de "$sql .= ' WHERE...'" por "$where[] =" ---
        if ($role === 'operador') {
            $where[] = 'l.operador_id = ?';
            $params[] = $profileId;
        } elseif ($role === 'vendedor') {
            $where[] = '(l.vendedor_id = ? OR l.derivado_ventas = 1)';
            $params[] = $profileId;
        }

        // --- CAMBIO 4: NUEVO BLOQUE. Si el usuario escribió algo en el buscador, armamos el "LIKE" ---
        if ($search !== '') {
            $where[] = '(l.telefono LIKE ? OR l.nombre LIKE ? OR l.email LIKE ?)';
            $likeSearch = '%' . $search . '%'; // Envuelve la palabra en % para buscar coincidencias parciales
            array_push($params, $likeSearch, $likeSearch, $likeSearch);
        }

        // --- CAMBIO 5: NUEVO BLOQUE. Si hay condiciones en $where, las unimos con " AND " y las agregamos al $sql ---
        if (count($where) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY l.created_at DESC';

        return $this->query($sql, $params);
    }

    /** Verificación global de duplicado por teléfono. */
    public function findByPhone(string $telefono): ?array
    {
        if ($telefono === '') return null;
        return $this->queryOne(
            'SELECT id, nombre, operador_id, derivado_ventas FROM leads WHERE telefono = ? LIMIT 1',
            [$telefono]
        );
    }

    public function create(array $data): string
    {
        $id = Helpers::uuid();
        $sql = 'INSERT INTO leads
                (id, nombre, telefono, email, pais_id, ciudad, empresa_id, proyecto_id,
                 campana_id, canal_id, mql_id, temperatura, tipo_pago, comentario,
                 operador_id, agente_id, fecha_lead)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $this->exec($sql, [
            $id,
            $data['nombre'],
            $data['telefono']   ?: null,
            $data['email']      ?: null,
            $data['pais_id']    ?: null,
            $data['ciudad']     ?: null,
            $data['empresa_id'] ?: null,
            $data['proyecto_id']?: null,
            $data['campana_id'] ?: null,
            $data['canal_id']   ?: null,
            $data['mql_id']     ?: null,
            $data['temperatura'] ?? 'frio',
            $data['tipo_pago']  ?: null,
            $data['comentario'] ?: null,
            $data['operador_id']?: null,
            $data['agente_id']  ?: null,
            $data['fecha_lead'] ?: date('Y-m-d'),
        ]);
        return $id;
    }

    public function update(string $id, array $data): void
    {
        $sql = 'UPDATE leads SET
                    nombre = ?, telefono = ?, email = ?, pais_id = ?, ciudad = ?,
                    empresa_id = ?, proyecto_id = ?, campana_id = ?, canal_id = ?,
                    mql_id = ?, temperatura = ?, tipo_pago = ?, comentario = ?,
                    agente_id = ?, fecha_lead = ?
                WHERE id = ?';
        $this->exec($sql, [
            $data['nombre'],
            $data['telefono']   ?: null,
            $data['email']      ?: null,
            $data['pais_id']    ?: null,
            $data['ciudad']     ?: null,
            $data['empresa_id'] ?: null,
            $data['proyecto_id']?: null,
            $data['campana_id'] ?: null,
            $data['canal_id']   ?: null,
            $data['mql_id']     ?: null,
            $data['temperatura'] ?? 'frio',
            $data['tipo_pago']  ?: null,
            $data['comentario'] ?: null,
            $data['agente_id']  ?: null,
            $data['fecha_lead'] ?: date('Y-m-d'),
            $id,
        ]);
    }

    /** Deriva un lead a un vendedor. Lanza excepción si ya está derivado (trigger de BD). */
    public function derivar(string $id, int $vendedorTablaId): void
    {
        // Comprobación global (además del trigger)
        $existing = $this->queryOne(
            'SELECT derivado_ventas, vendedor_tabla_id FROM leads WHERE id = ?',
            [$id]
        );
        if (!$existing) throw new RuntimeException('Lead no encontrado');
        if ((int)$existing['derivado_ventas'] === 1) {
            throw new RuntimeException('Este lead ya fue derivado a ventas por otro operador.');
        }
        $this->exec(
            'UPDATE leads
                SET derivado_ventas = 1,
                    vendedor_tabla_id = ?,
                    fecha_derivado_ventas = NOW()
              WHERE id = ?',
            [$vendedorTablaId, $id]
        );
    }
}