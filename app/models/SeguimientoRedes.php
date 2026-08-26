<?php
declare(strict_types=1);

final class SeguimientoRedes extends Model
{
    protected string $table = 'seguimientos_redes';

    public function forLead(string $leadId): array
    {
        return $this->query(
            'SELECT * FROM seguimientos_redes WHERE lead_id = ? ORDER BY iteracion ASC',
            [$leadId]
        );
    }

    public function create(array $d): string
    {
        $rows = $this->forLead($d['lead_id']);
        $iter = count($rows) + 1;
        if ($iter > 7) throw new RuntimeException('Máximo 7 seguimientos por lead.');

        $id = Helpers::uuid();
        $this->exec(
            'INSERT INTO seguimientos_redes
              (id, lead_id, iteracion, estado, comentario, fecha_proximo_contacto)
             VALUES (?,?,?,?,?,?)',
            [
                $id, $d['lead_id'], $iter, $d['estado'],
                $d['comentario'] ?: null,
                $d['fecha_proximo_contacto'] ?: null,
            ]
        );
        return $id;
    }
}
