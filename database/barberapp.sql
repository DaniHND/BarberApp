-- ============================================================
--  BarberApp — Script SQL completo
--  Base de datos: barberapp
--  PHP 8.3 | MySQL 8+ | utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS barberapp
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE barberapp;

-- ──────────────────────────────────────────────────────────
--  1. ADMINISTRADORES
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS administradores (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    usuario     VARCHAR(50)     NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,
    email       VARCHAR(100)    DEFAULT NULL,
    activo      TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  2. CONFIGURACION
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS configuracion (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    clave       VARCHAR(50)     NOT NULL UNIQUE,
    valor       VARCHAR(255)    NOT NULL,
    descripcion VARCHAR(200)    DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  3. SERVICIOS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS servicios (
    id                  INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre              VARCHAR(100)    NOT NULL,
    descripcion         TEXT            DEFAULT NULL,
    precio              DECIMAL(8,2)    NOT NULL DEFAULT 0.00,
    duracion_minutos    INT             NOT NULL DEFAULT 30,
    activo              TINYINT(1)      NOT NULL DEFAULT 1,
    created_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  4. CLIENTES
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS clientes (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100)    NOT NULL,
    telefono        VARCHAR(20)     DEFAULT NULL,
    email           VARCHAR(100)    DEFAULT NULL,
    total_visitas   INT             NOT NULL DEFAULT 0,
    primera_visita  DATE            DEFAULT NULL,
    ultima_visita   DATE            DEFAULT NULL,
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre    (nombre),
    INDEX idx_telefono  (telefono)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  5. CITAS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS citas (
    id                  INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    cliente_id          INT UNSIGNED    DEFAULT NULL,
    servicio_id         INT UNSIGNED    NOT NULL,
    fecha               DATE            NOT NULL,
    hora_inicio         TIME            NOT NULL,
    hora_fin            TIME            NOT NULL,
    estado              ENUM(
                            'disponible',
                            'reservado',
                            'confirmado',
                            'atendido',
                            'no_presentado',
                            'en_espera',
                            'cancelado'
                        )               NOT NULL DEFAULT 'reservado',
    nombre_cliente      VARCHAR(100)    NOT NULL,
    telefono_cliente    VARCHAR(20)     DEFAULT NULL,
    token               VARCHAR(64)     DEFAULT NULL UNIQUE,
    notas               TEXT            DEFAULT NULL,
    created_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id)    REFERENCES clientes(id)  ON DELETE SET NULL,
    FOREIGN KEY (servicio_id)   REFERENCES servicios(id) ON DELETE RESTRICT,
    INDEX idx_fecha         (fecha),
    INDEX idx_estado        (estado),
    INDEX idx_fecha_hora    (fecha, hora_inicio),
    INDEX idx_token         (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  6. LISTA_ESPERA
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS lista_espera (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100)    NOT NULL,
    telefono        VARCHAR(20)     DEFAULT NULL,
    servicio_id     INT UNSIGNED    DEFAULT NULL,
    fecha_llegada   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado          ENUM(
                        'esperando',
                        'atendido',
                        'cancelado'
                    )               NOT NULL DEFAULT 'esperando',
    notas           TEXT            DEFAULT NULL,
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL,
    INDEX idx_estado        (estado),
    INDEX idx_fecha_llegada (fecha_llegada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  7. HISTORIAL_VISITAS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS historial_visitas (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT UNSIGNED    NOT NULL,
    cita_id         INT UNSIGNED    DEFAULT NULL,
    servicio_id     INT UNSIGNED    DEFAULT NULL,
    fecha           DATE            NOT NULL,
    precio_cobrado  DECIMAL(8,2)    DEFAULT NULL,
    notas           TEXT            DEFAULT NULL,
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id)    REFERENCES clientes(id)  ON DELETE CASCADE,
    FOREIGN KEY (cita_id)       REFERENCES citas(id)     ON DELETE SET NULL,
    FOREIGN KEY (servicio_id)   REFERENCES servicios(id) ON DELETE SET NULL,
    INDEX idx_cliente   (cliente_id),
    INDEX idx_fecha     (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────
--  8. INGRESOS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS ingresos (
    id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    cita_id         INT UNSIGNED    DEFAULT NULL,
    servicio_id     INT UNSIGNED    DEFAULT NULL,
    monto           DECIMAL(8,2)    NOT NULL,
    fecha           DATE            NOT NULL,
    tipo            ENUM('cita','espera') NOT NULL DEFAULT 'cita',
    descripcion     VARCHAR(200)    DEFAULT NULL,
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cita_id)       REFERENCES citas(id)     ON DELETE SET NULL,
    FOREIGN KEY (servicio_id)   REFERENCES servicios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  DATOS INICIALES
-- ============================================================

-- Administrador por defecto
-- Usuario: admin | Contraseña: Admin123
INSERT INTO administradores (nombre, usuario, password, email) VALUES
('Administrador', 'admin', '$2y$10$M60yc3Lm50g.AyIsoy.0bOPk6z1R4/DwZR1TnavyuyqatPrhndieO', 'admin@barberapp.com');

-- Configuración inicial de la barbería
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_barberia',     'BarberApp',    'Nombre de la barbería'),
('horario_inicio',      '08:00',        'Hora de apertura (HH:MM)'),
('horario_fin',         '19:00',        'Hora de cierre (HH:MM)'),
('intervalo_citas',     '30',           'Duración mínima de slot en minutos'),
('minutos_confirmacion','10',           'Minutos antes de la cita para confirmar llegada'),
('moneda',              'L.',           'Símbolo de moneda');

-- Servicios de ejemplo
INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos) VALUES
('Corte Clásico',       'Corte tradicional con tijera y máquina',            150.00, 30),
('Degradado',           'Fade degradado en los costados con blend',          180.00, 45),
('Corte + Barba',       'Corte completo más arreglo y perfilado de barba',   220.00, 60),
('Arreglo de Barba',    'Perfilado, hidratación y arreglo de barba',         100.00, 30),
('Corte Niño',          'Corte para niños menores de 12 años',               100.00, 30);
