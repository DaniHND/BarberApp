-- ============================================================
-- Fase 6: Barberos con horarios y disponibilidad
-- Ejecutar en HeidiSQL o phpMyAdmin sobre la BD barberapp
-- ============================================================

CREATE TABLE IF NOT EXISTS barberos (
  id          INT          AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  orden       INT          NOT NULL DEFAULT 0,
  created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Horario semanal: qué días trabaja y en qué rango de horas
CREATE TABLE IF NOT EXISTS barbero_horarios (
  id          INT     AUTO_INCREMENT PRIMARY KEY,
  barbero_id  INT     NOT NULL,
  dia_semana  TINYINT NOT NULL COMMENT '1=Lunes 2=Martes 3=Miércoles 4=Jueves 5=Viernes 6=Sábado',
  hora_inicio TIME    NOT NULL,
  hora_fin    TIME    NOT NULL,
  UNIQUE KEY uq_barbero_dia (barbero_id, dia_semana),
  CONSTRAINT fk_bh_barbero FOREIGN KEY (barbero_id) REFERENCES barberos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Días bloqueados: vacaciones, ausencias, imprevistos
CREATE TABLE IF NOT EXISTS barbero_bloqueos (
  id         INT          AUTO_INCREMENT PRIMARY KEY,
  barbero_id INT          NOT NULL,
  fecha      DATE         NOT NULL,
  motivo     VARCHAR(255) DEFAULT NULL,
  UNIQUE KEY uq_barbero_fecha (barbero_id, fecha),
  CONSTRAINT fk_bb_barbero FOREIGN KEY (barbero_id) REFERENCES barberos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vincular citas con barbero (nullable para citas existentes y sin preferencia)
ALTER TABLE citas
  ADD COLUMN barbero_id INT DEFAULT NULL AFTER servicio_id,
  ADD CONSTRAINT fk_citas_barbero FOREIGN KEY (barbero_id) REFERENCES barberos(id) ON DELETE SET NULL;
