<?php
class Cliente extends BaseModel {

    public function findOrCreate(string $nombre, ?string $telefono): int {
        if ($telefono) {
            $st = $this->db->prepare(
                'SELECT id FROM clientes WHERE nombre = ? AND telefono = ? LIMIT 1'
            );
            $st->execute([$nombre, $telefono]);
        } else {
            $st = $this->db->prepare(
                'SELECT id FROM clientes WHERE nombre = ? AND telefono IS NULL LIMIT 1'
            );
            $st->execute([$nombre]);
        }

        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return (int) $row['id'];
        }

        $st = $this->db->prepare(
            'INSERT INTO clientes (nombre, telefono, primera_visita) VALUES (?, ?, CURDATE())'
        );
        $st->execute([$nombre, $telefono]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): array|false {
        $st = $this->db->prepare('SELECT * FROM clientes WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll(): array {
        $st = $this->db->query(
            "SELECT c.*,
                (SELECT s.nombre FROM historial_visitas hv
                 JOIN servicios s ON s.id = hv.servicio_id
                 WHERE hv.cliente_id = c.id
                 GROUP BY hv.servicio_id
                 ORDER BY COUNT(*) DESC
                 LIMIT 1) AS servicio_favorito
             FROM clientes c
             WHERE c.total_visitas > 0
             ORDER BY c.total_visitas DESC, c.ultima_visita DESC"
        );
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
