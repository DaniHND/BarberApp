<?php
class Ingreso extends BaseModel {

    public function registrar(int $citaId, int $servicioId, float $monto, string $fecha): void {
        $st = $this->db->prepare('SELECT id FROM ingresos WHERE cita_id = ?');
        $st->execute([$citaId]);
        if ($st->fetch()) return;

        $st = $this->db->prepare(
            'INSERT INTO ingresos (cita_id, servicio_id, monto, fecha) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$citaId, $servicioId, $monto, $fecha]);
    }

    public function getResumenHoy(): array {
        $st = $this->db->query(
            "SELECT COALESCE(SUM(monto), 0) AS total, COUNT(*) AS citas
             FROM ingresos WHERE fecha = CURDATE()"
        );
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getResumenMes(int $anio, int $mes): array {
        $st = $this->db->prepare(
            "SELECT COALESCE(SUM(monto), 0) AS total, COUNT(*) AS citas
             FROM ingresos WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?"
        );
        $st->execute([$anio, $mes]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getPorDiaEnMes(int $anio, int $mes): array {
        $diasEnMes = (int) date('t', mktime(0, 0, 0, $mes, 1, $anio));
        $result    = array_fill(1, $diasEnMes, 0.0);

        $st = $this->db->prepare(
            "SELECT DAY(fecha) AS dia, SUM(monto) AS total
             FROM ingresos WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?
             GROUP BY DAY(fecha)"
        );
        $st->execute([$anio, $mes]);
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[(int)$row['dia']] = (float)$row['total'];
        }
        return $result;
    }

    public function getTopServiciosMes(int $anio, int $mes, int $limite = 5): array {
        $st = $this->db->prepare(
            "SELECT s.nombre, COUNT(i.id) AS total_citas, SUM(i.monto) AS total_monto
             FROM ingresos i
             JOIN servicios s ON s.id = i.servicio_id
             WHERE YEAR(i.fecha) = ? AND MONTH(i.fecha) = ?
             GROUP BY i.servicio_id, s.nombre
             ORDER BY total_citas DESC
             LIMIT ?"
        );
        $st->bindValue(1, $anio, PDO::PARAM_INT);
        $st->bindValue(2, $mes,  PDO::PARAM_INT);
        $st->bindValue(3, $limite, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimasCitas(int $limite = 15): array {
        $st = $this->db->prepare(
            "SELECT i.id, i.monto, i.fecha,
                    s.nombre AS servicio_nombre,
                    c.nombre_cliente, c.hora_inicio
             FROM ingresos i
             JOIN servicios s ON s.id = i.servicio_id
             LEFT JOIN citas c ON c.id = i.cita_id
             ORDER BY i.id DESC
             LIMIT ?"
        );
        $st->bindValue(1, $limite, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
