<?php
class HistorialVisita extends BaseModel {

    public function registrar(int $clienteId, int $citaId, int $servicioId, float $precio, string $fecha): void {
        // Idempotente: evitar doble registro por la misma cita
        $st = $this->db->prepare('SELECT id FROM historial_visitas WHERE cita_id = ?');
        $st->execute([$citaId]);
        if ($st->fetch()) return;

        $st = $this->db->prepare(
            'INSERT INTO historial_visitas (cliente_id, cita_id, servicio_id, fecha, precio_cobrado)
             VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([$clienteId, $citaId, $servicioId, $fecha, $precio]);

        // Actualizar stats del cliente
        $st = $this->db->prepare(
            "UPDATE clientes
             SET total_visitas = total_visitas + 1,
                 ultima_visita = ?,
                 primera_visita = COALESCE(primera_visita, ?)
             WHERE id = ?"
        );
        $st->execute([$fecha, $fecha, $clienteId]);
    }

    public function getByCliente(int $clienteId): array {
        $st = $this->db->prepare(
            "SELECT hv.*, s.nombre AS servicio_nombre
             FROM historial_visitas hv
             LEFT JOIN servicios s ON s.id = hv.servicio_id
             WHERE hv.cliente_id = ?
             ORDER BY hv.fecha DESC, hv.id DESC"
        );
        $st->execute([$clienteId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
