<?php
class Administrador extends BaseModel {

    public function findByUsuario(string $usuario): array|false {
        $stmt = $this->db->prepare(
            'SELECT * FROM administradores WHERE usuario = ? AND activo = 1 LIMIT 1'
        );
        $stmt->execute([$usuario]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, usuario, email FROM administradores WHERE id = ? AND activo = 1 LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
