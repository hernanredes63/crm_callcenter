<?php
declare(strict_types=1);

/**
 * Modelo base. Provee helpers PDO comunes.
 * Los modelos concretos extienden esta clase y declaran $table.
 */
abstract class Model
{
    protected string $table = '';

    protected function db(): PDO { return Database::pdo(); }

    /** Ejecuta una SELECT y devuelve todas las filas. */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Ejecuta una SELECT y devuelve la primera fila o null. */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Ejecuta INSERT/UPDATE/DELETE. Devuelve nº de filas afectadas. */
    protected function exec(string $sql, array $params = []): int
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function find(string $id): ?array
    {
        return $this->queryOne("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1", [$id]);
    }

    public function all(): array
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY nombre ASC");
    }

    public function delete(string $id): int
    {
        return $this->exec("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}
