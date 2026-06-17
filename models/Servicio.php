<?php
class Servicio extends BaseModel {

    public function getAll(bool $soloActivos = false): array {
        $sql = 'SELECT * FROM servicios';
        if ($soloActivos) $sql .= ' WHERE activo = 1';
        $sql .= ' ORDER BY nombre ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM servicios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear(array $d): int {
        $stmt = $this->db->prepare(
            'INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos, activo)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $d['nombre'],
            $d['descripcion'] ?: null,
            (float) $d['precio'],
            (int)   $d['duracion_minutos'],
            (int)   ($d['activo'] ?? 1),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $d): bool {
        $stmt = $this->db->prepare(
            'UPDATE servicios
             SET nombre = ?, descripcion = ?, precio = ?, duracion_minutos = ?, activo = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $d['nombre'],
            $d['descripcion'] ?: null,
            (float) $d['precio'],
            (int)   $d['duracion_minutos'],
            (int)   ($d['activo'] ?? 0),
            $id,
        ]);
    }

    public function toggleActivo(int $id): bool {
        $stmt = $this->db->prepare(
            'UPDATE servicios SET activo = IF(activo = 1, 0, 1) WHERE id = ?'
        );
        return $stmt->execute([$id]);
    }

    public function eliminar(int $id): string {
        // Si tiene citas asociadas, solo desactivar
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM citas WHERE servicio_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() > 0) {
            $this->db->prepare('UPDATE servicios SET activo = 0 WHERE id = ?')->execute([$id]);
            return 'desactivado';
        }
        $this->db->prepare('DELETE FROM servicios WHERE id = ?')->execute([$id]);
        return 'eliminado';
    }

    public function countActivos(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM servicios WHERE activo = 1')->fetchColumn();
    }
}
