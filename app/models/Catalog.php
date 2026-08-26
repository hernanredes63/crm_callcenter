<?php
declare(strict_types=1);

/**
 * Acceso rápido a los catálogos que los formularios necesitan.
 */
final class Catalog extends Model
{
    public function empresas(): array { return $this->query('SELECT id, nombre FROM empresas ORDER BY nombre'); }
    public function campanas(): array { return $this->query('SELECT id, nombre, empresa_id FROM campanas ORDER BY nombre'); }
    public function proyectos(): array { return $this->query('SELECT id, nombre, empresa_id FROM proyectos ORDER BY nombre'); }
    public function canales(): array  { return $this->query('SELECT id, nombre FROM canales ORDER BY nombre'); }
    public function mql(): array      { return $this->query('SELECT id, nivel  FROM mql_status ORDER BY nivel'); }
    public function paises(): array   { return $this->query('SELECT id, nombre FROM paises ORDER BY nombre'); }
    public function agentes(): array  { return $this->query('SELECT id, nombre FROM agentes WHERE activo = 1 ORDER BY nombre'); }
    public function vendedores(): array { return $this->query('SELECT id, nombre FROM vendedor ORDER BY nombre'); }
    public function operadores(): array
    {
        return $this->query(
            "SELECT p.id, p.nombre
             FROM profiles p
             JOIN user_roles r ON r.user_id = p.user_id
             WHERE r.role = 'operador' AND p.activo = 1
             ORDER BY p.nombre"
        );
    }
}
