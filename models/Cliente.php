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
}
