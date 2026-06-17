<?php
class Dashboard extends BaseModel {

    // Stats del día actual
    public function getStatsHoy(): array {
        $stmt = $this->db->prepare("
            SELECT
                (SELECT COUNT(*)        FROM citas       WHERE fecha = CURDATE())                                      AS citas_hoy,
                (SELECT COUNT(*)        FROM lista_espera WHERE DATE(fecha_llegada) = CURDATE() AND estado='esperando') AS en_espera,
                (SELECT COUNT(*)        FROM servicios   WHERE activo = 1)                                             AS servicios_activos,
                (SELECT COALESCE(SUM(monto),0) FROM ingresos WHERE fecha = CURDATE())                                  AS ingresos_hoy
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    // Citas agrupadas por día para los últimos N días (rellena huecos con 0)
    public function getCitasUltimosDias(int $dias = 7): array {
        $resultado = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $resultado[date('Y-m-d', strtotime("-{$i} days"))] = 0;
        }

        $stmt = $this->db->prepare("
            SELECT fecha, COUNT(*) AS total
            FROM citas
            WHERE fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL :dias DAY) AND CURDATE()
            GROUP BY fecha
            ORDER BY fecha ASC
        ");
        $stmt->execute([':dias' => $dias - 1]);

        foreach ($stmt->fetchAll() as $row) {
            if (isset($resultado[$row['fecha']])) {
                $resultado[$row['fecha']] = (int) $row['total'];
            }
        }
        return $resultado;
    }

    // Top servicios por número de citas
    public function getTopServicios(int $limite = 5): array {
        $stmt = $this->db->prepare("
            SELECT s.nombre, COUNT(c.id) AS total, s.precio
            FROM servicios s
            LEFT JOIN citas c ON c.servicio_id = s.id
            WHERE s.activo = 1
            GROUP BY s.id, s.nombre, s.precio
            ORDER BY total DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
