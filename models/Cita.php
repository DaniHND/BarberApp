<?php
class Cita extends BaseModel {

    // ── Disponibilidad ────────────────────────────────────
    public function getSlotsDisponibles(string $fecha, int $servicioId, array $cfg, ?int $excluirId = null): array {
        // Servicio activo
        $st = $this->db->prepare(
            'SELECT duracion_minutos FROM servicios WHERE id = ? AND activo = 1'
        );
        $st->execute([$servicioId]);
        $svc = $st->fetch(PDO::FETCH_ASSOC);
        if (!$svc) return [];

        // Domingos: sin servicio
        if (date('N', strtotime($fecha)) === '7') return [];

        $duracion  = (int) $svc['duracion_minutos'];
        $tInicio   = strtotime($fecha . ' ' . ($cfg['horario_inicio'] ?? '08:00'));
        $tFin      = strtotime($fecha . ' ' . ($cfg['horario_fin']    ?? '19:00'));
        $intervalo = (int) ($cfg['intervalo_citas'] ?? 30) * 60;

        // Citas activas del día (excluyendo la que se reprograma)
        $sql = "SELECT hora_inicio, hora_fin FROM citas WHERE fecha = ? AND estado IN ('reservado','confirmado')";
        $params = [$fecha];
        if ($excluirId !== null) { $sql .= ' AND id != ?'; $params[] = $excluirId; }
        $st = $this->db->prepare($sql);
        $st->execute($params);
        $ocupados = $st->fetchAll(PDO::FETCH_ASSOC);

        $slots = [];
        for ($t = $tInicio; $t + $duracion * 60 <= $tFin; $t += $intervalo) {
            $sInicio = date('H:i', $t);
            $sFin    = date('H:i', $t + $duracion * 60);

            $libre = true;
            foreach ($ocupados as $o) {
                // Solapamiento: slot empieza antes de que termine ocupado Y termina después de que empiece
                if ($sInicio < $o['hora_fin'] && $sFin > $o['hora_inicio']) {
                    $libre = false;
                    break;
                }
            }

            $slots[] = [
                'hora_inicio' => $sInicio,
                'hora_fin'    => $sFin,
                'disponible'  => $libre,
            ];
        }
        return $slots;
    }

    public function existeConflicto(
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int   $excluirId = null
    ): bool {
        $sql = "SELECT COUNT(*) FROM citas
                WHERE fecha = ? AND estado IN ('reservado','confirmado')
                AND hora_inicio < ? AND hora_fin > ?";
        $params = [$fecha, $horaFin, $horaInicio];

        if ($excluirId !== null) {
            $sql     .= ' AND id != ?';
            $params[] = $excluirId;
        }

        $st = $this->db->prepare($sql);
        $st->execute($params);
        return (int) $st->fetchColumn() > 0;
    }

    // ── CRUD ──────────────────────────────────────────────
    public function crear(array $d): array {
        $token = bin2hex(random_bytes(20));

        $st = $this->db->prepare(
            'INSERT INTO citas
                (cliente_id, servicio_id, fecha, hora_inicio, hora_fin, estado, nombre_cliente, telefono_cliente, token)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $d['cliente_id']       ?? null,
            $d['servicio_id'],
            $d['fecha'],
            $d['hora_inicio'],
            $d['hora_fin'],
            'reservado',
            $d['nombre_cliente'],
            $d['telefono_cliente'] ?? null,
            $token,
        ]);

        return ['id' => (int) $this->db->lastInsertId(), 'token' => $token];
    }

    public function findByToken(string $token): array|false {
        $st = $this->db->prepare(
            'SELECT c.*, s.nombre AS servicio_nombre, s.precio, s.duracion_minutos
             FROM citas c
             JOIN servicios s ON s.id = c.servicio_id
             WHERE c.token = ?'
        );
        $st->execute([$token]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function cancelar(int $id): bool {
        $st = $this->db->prepare(
            "UPDATE citas SET estado = 'cancelado'
             WHERE id = ? AND estado IN ('reservado','confirmado')"
        );
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }

    public function reprogramar(int $id, string $fecha, string $horaInicio, string $horaFin): bool {
        $st = $this->db->prepare(
            "UPDATE citas SET fecha = ?, hora_inicio = ?, hora_fin = ?, estado = 'reservado'
             WHERE id = ? AND estado IN ('reservado','confirmado')"
        );
        $st->execute([$fecha, $horaInicio, $horaFin, $id]);
        return $st->rowCount() > 0;
    }

    // Para el dashboard Fase 2 — horas de citas de hoy
    public function getHoy(): array {
        $st = $this->db->prepare(
            "SELECT c.*, s.nombre AS servicio_nombre
             FROM citas c
             JOIN servicios s ON s.id = c.servicio_id
             WHERE c.fecha = CURDATE() AND c.estado NOT IN ('cancelado','no_presentado')
             ORDER BY c.hora_inicio"
        );
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
