<?php
class Barbero extends BaseModel {

    // ── CRUD barberos ─────────────────────────────────────

    public function getAll(bool $soloActivos = false): array {
        $sql = 'SELECT * FROM barberos';
        if ($soloActivos) $sql .= ' WHERE activo = 1';
        $sql .= ' ORDER BY orden, nombre';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): array|false {
        $st = $this->db->prepare('SELECT * FROM barberos WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(array $d): int {
        $st = $this->db->prepare(
            'INSERT INTO barberos (nombre, descripcion, orden) VALUES (?, ?, ?)'
        );
        $st->execute([$d['nombre'], $d['descripcion'] ?: null, (int)($d['orden'] ?? 0)]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $d): void {
        $st = $this->db->prepare(
            'UPDATE barberos SET nombre = ?, descripcion = ?, orden = ? WHERE id = ?'
        );
        $st->execute([$d['nombre'], $d['descripcion'] ?: null, (int)($d['orden'] ?? 0), $id]);
    }

    public function toggle(int $id): void {
        $this->db->prepare('UPDATE barberos SET activo = NOT activo WHERE id = ?')->execute([$id]);
    }

    public function eliminar(int $id): void {
        $this->db->prepare('DELETE FROM barberos WHERE id = ?')->execute([$id]);
    }

    // ── Horarios semanales ────────────────────────────────

    /** @return array<int, array> Keyed by dia_semana (1-6) */
    public function getHorarios(int $barberoId): array {
        $st = $this->db->prepare(
            'SELECT * FROM barbero_horarios WHERE barbero_id = ? ORDER BY dia_semana'
        );
        $st->execute([$barberoId]);
        $indexed = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $indexed[(int)$r['dia_semana']] = $r;
        }
        return $indexed;
    }

    public function guardarHorarios(int $barberoId, array $horarios): void {
        $this->db->prepare('DELETE FROM barbero_horarios WHERE barbero_id = ?')->execute([$barberoId]);
        $ins = $this->db->prepare(
            'INSERT INTO barbero_horarios (barbero_id, dia_semana, hora_inicio, hora_fin) VALUES (?, ?, ?, ?)'
        );
        foreach ($horarios as $dia => $h) {
            if (!empty($h['activo']) && !empty($h['hora_inicio']) && !empty($h['hora_fin'])) {
                $ins->execute([$barberoId, (int)$dia, $h['hora_inicio'], $h['hora_fin']]);
            }
        }
    }

    // ── Días bloqueados ───────────────────────────────────

    public function getBloqueos(int $barberoId, bool $soloFuturos = false): array {
        $sql = 'SELECT * FROM barbero_bloqueos WHERE barbero_id = ?';
        if ($soloFuturos) $sql .= ' AND fecha >= CURDATE()';
        $sql .= ' ORDER BY fecha';
        $st  = $this->db->prepare($sql);
        $st->execute([$barberoId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarBloqueo(int $barberoId, string $fecha, ?string $motivo): bool {
        try {
            $this->db->prepare(
                'INSERT INTO barbero_bloqueos (barbero_id, fecha, motivo) VALUES (?, ?, ?)'
            )->execute([$barberoId, $fecha, $motivo]);
            return true;
        } catch (\PDOException) {
            return false; // violación de UNIQUE (ya existe ese día)
        }
    }

    public function eliminarBloqueo(int $id, int $barberoId): void {
        $this->db->prepare(
            'DELETE FROM barbero_bloqueos WHERE id = ? AND barbero_id = ?'
        )->execute([$id, $barberoId]);
    }
}
