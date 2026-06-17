-- BarberApp — Migración Fase 3
-- Agrega columna token a citas y valor 'cancelado' al ENUM estado

ALTER TABLE citas
    MODIFY COLUMN estado ENUM(
        'disponible','reservado','confirmado',
        'atendido','no_presentado','en_espera','cancelado'
    ) NOT NULL DEFAULT 'reservado';

ALTER TABLE citas
    ADD COLUMN token VARCHAR(64) DEFAULT NULL UNIQUE AFTER nombre_cliente;

CREATE INDEX IF NOT EXISTS idx_token ON citas(token);
