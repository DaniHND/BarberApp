<?php
class ListaEspera extends BaseModel {

    public function getEsperando(): array {
        $st = $this->db->query(
            "SELECT le.*, s.nombre AS servicio_nombre
             FROM lista_espera le
             LEFT JOIN servicios s ON s.id = le.servicio_id
             WHERE le.estado = 'esperando' AND DATE(le.fecha_llegada) = CURDATE()
             ORDER BY le.fecha_llegada ASC"
        );
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $d): int {
        $st = $this->db->prepare(
            'INSERT INTO lista_espera (nombre, telefono, servicio_id) VALUES (?, ?, ?)'
        );
        $st->execute([$d['nombre'], $d['telefono'] ?? null, $d['servicio_id'] ?? null]);
        return (int) $this->db->lastInsertId();
    }

    public function atender(int $id): bool {
        $st = $this->db->prepare(
            "UPDATE lista_espera SET estado = 'atendido' WHERE id = ?"
        );
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }

    public function cancelar(int $id): bool {
        $st = $this->db->prepare(
            "UPDATE lista_espera SET estado = 'cancelado' WHERE id = ?"
        );
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }

    public function countEsperando(): int {
        $st = $this->db->query(
            "SELECT COUNT(*) FROM lista_espera
             WHERE estado = 'esperando' AND DATE(fecha_llegada) = CURDATE()"
        );
        return (int) $st->fetchColumn();
    }
}
