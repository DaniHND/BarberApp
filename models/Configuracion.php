<?php
class Configuracion extends BaseModel {

    public function getAll(): array {
        return $this->db
            ->query('SELECT clave, valor FROM configuracion')
            ->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function get(string $clave, string $default = ''): string {
        $st = $this->db->prepare('SELECT valor FROM configuracion WHERE clave = ?');
        $st->execute([$clave]);
        $v = $st->fetchColumn();
        return $v !== false ? $v : $default;
    }
}
