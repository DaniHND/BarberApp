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

    // ── Fase 4: Agenda admin ──────────────────────────────

    public function getAgendaFecha(string $fecha): array {
        $st = $this->db->prepare(
            "SELECT c.*, s.nombre AS servicio_nombre, s.precio, s.duracion_minutos
             FROM citas c
             JOIN servicios s ON s.id = c.servicio_id
             WHERE c.fecha = ? AND c.estado != 'cancelado'
             ORDER BY c.hora_inicio"
        );
        $st->execute([$fecha]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstado(int $id, string $estado): bool {
        $permitidos = ['reservado','confirmado','atendido','no_presentado','en_espera','cancelado'];
        if (!in_array($estado, $permitidos, true)) return false;
        $st = $this->db->prepare('UPDATE citas SET estado = ? WHERE id = ?');
        $st->execute([$estado, $id]);
        return $st->rowCount() > 0;
    }

    // Marca como no_presentado las citas reservadas cuya hora ya pasó
    public function autoLiberarExpirados(): int {
        $st = $this->db->prepare(
            "UPDATE citas SET estado = 'no_presentado'
             WHERE estado = 'reservado'
             AND fecha = CURDATE()
             AND CONCAT(fecha, ' ', hora_inicio) < NOW()"
        );
        $st->execute();
        return $st->rowCount();
    }

    // Eventos para FullCalendar (JSON)
    public function getEventosFC(string $fecha): array {
        $colores = [
            'reservado'      => '#3b82f6',
            'confirmado'     => '#22c55e',
            'atendido'       => '#10b981',
            'no_presentado'  => '#ef4444',
            'en_espera'      => '#a855f7',
            'cancelado'      => '#9ca3af',
        ];
        $eventos = [];
        foreach ($this->getAgendaFecha($fecha) as $c) {
            $color = $colores[$c['estado']] ?? '#6b7280';
            $eventos[] = [
                'id'              => $c['id'],
                'title'           => $c['nombre_cliente'] . ' · ' . $c['servicio_nombre'],
                'start'           => $c['fecha'] . 'T' . $c['hora_inicio'],
                'end'             => $c['fecha'] . 'T' . $c['hora_fin'],
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'estado'   => $c['estado'],
                    'cliente'  => $c['nombre_cliente'],
                    'servicio' => $c['servicio_nombre'],
                    'precio'   => $c['precio'],
                ],
            ];
        }
        return $eventos;
    }
}
