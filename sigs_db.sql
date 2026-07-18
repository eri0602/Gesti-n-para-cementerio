-- SQL Script — Base de Datos para el Sistema de Gestión de Expedientes de Fallecidos (SIGS)
-- Incluye estructura de tablas y datos semilla de prueba completos.

CREATE DATABASE IF NOT EXISTS sigs_cementerio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sigs_cementerio;

-- ── 1. Estructura de Tablas ──

-- Tabla: Usuario
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario   INT AUTO_INCREMENT PRIMARY KEY,
    usuario      VARCHAR(50) NOT NULL UNIQUE,
    contrasena   VARCHAR(255) NOT NULL,
    rol          VARCHAR(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Fallecido
CREATE TABLE IF NOT EXISTS fallecido (
    id_fallecido         INT AUTO_INCREMENT PRIMARY KEY,
    dni                  CHAR(8) NOT NULL UNIQUE,
    nombres              VARCHAR(100) NOT NULL,
    apellidos            VARCHAR(100) NOT NULL,
    fecha_nacimiento     DATE NOT NULL,
    fecha_fallecimiento  DATE NOT NULL,
    edad                 INT NOT NULL,
    sexo                 VARCHAR(10) NOT NULL,
    INDEX idx_dni (dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Solicitud_Sepultura
CREATE TABLE IF NOT EXISTS solicitud_sepultura (
    id_solicitud     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_deudo     VARCHAR(100) NOT NULL,
    dni_deudo        CHAR(8) NOT NULL,
    parentesco       VARCHAR(50) NOT NULL,
    fecha_solicitud  DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Expediente
CREATE TABLE IF NOT EXISTS expediente (
    id_expediente      INT AUTO_INCREMENT PRIMARY KEY,
    numero_expediente  VARCHAR(20) NOT NULL UNIQUE,
    id_fallecido       INT NOT NULL,
    id_solicitud       INT NOT NULL,
    fecha_registro     DATE NOT NULL,
    FOREIGN KEY (id_fallecido) REFERENCES fallecido(id_fallecido) ON DELETE RESTRICT,
    FOREIGN KEY (id_solicitud) REFERENCES solicitud_sepultura(id_solicitud) ON DELETE RESTRICT,
    INDEX idx_numero_expediente (numero_expediente),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Reporte_Estadistico
CREATE TABLE IF NOT EXISTS reporte_estadistico (
    id_reporte        INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte      VARCHAR(30) NOT NULL,
    fecha_generacion  DATE NOT NULL,
    generado_por      INT NOT NULL,
    FOREIGN KEY (generado_por) REFERENCES usuario(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Comprobante_Registro
CREATE TABLE IF NOT EXISTS comprobante_registro (
    id_comprobante  INT AUTO_INCREMENT PRIMARY KEY,
    id_expediente   INT NOT NULL,
    fecha_emision   DATE NOT NULL,
    FOREIGN KEY (id_expediente) REFERENCES expediente(id_expediente) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── 2. Datos Semilla de Prueba ──

-- Usuarios Semilla (Contraseña encriptada por defecto: 123456)
INSERT IGNORE INTO usuario (id_usuario, usuario, contrasena, rol) VALUES
(1, 'jefe.cementerio', '$2y$10$2mW747j39a4JKlwEgc7ICOIsX3UkSzLeAfLb7KYM1dX0TQjECcg.W', 'admin'),
(2, 'admin.personal', '$2y$10$2mW747j39a4JKlwEgc7ICOIsX3UkSzLeAfLb7KYM1dX0TQjECcg.W', 'operador');

-- Solicitudes de Deudos
INSERT IGNORE INTO solicitud_sepultura (id_solicitud, nombre_deudo, dni_deudo, parentesco, fecha_solicitud) VALUES
(1, 'Carlos Mendoza Ruiz', '09887766', 'Hijo', '2026-05-10'),
(2, 'María Espinoza Chávez', '41223344', 'Cónyuge', '2026-06-15'),
(3, 'Jorge Flores Alva', '10203040', 'Hermano', '2026-07-02'),
(4, 'Ana Paredes Tello', '45889900', 'Hija', '2026-07-10'),
(5, 'Pedro Beltrán Solís', '07665544', 'Padre', '2026-07-12'),
(6, 'Sofía Valdivia León', '70809010', 'Cónyuge', '2026-07-15');

-- Fallecidos
INSERT IGNORE INTO fallecido (id_fallecido, dni, nombres, apellidos, fecha_nacimiento, fecha_fallecimiento, edad, sexo) VALUES
(1, '10203045', 'Juan Carlos', 'Mendoza Prado', '1945-03-12', '2026-05-09', 81, 'Masculino'),
(2, '08990011', 'Luis Alberto', 'Espinoza Cruz', '1962-08-24', '2026-06-14', 63, 'Masculino'),
(3, '40506070', 'Manuel Augusto', 'Flores Alva', '2010-11-05', '2026-07-01', 15, 'Masculino'),
(4, '02334455', 'Elena Rosa', 'Paredes Rojas', '1998-05-20', '2026-07-09', 28, 'Femenino'),
(5, '72665544', 'Angelito', 'Beltrán Santos', '2024-01-15', '2026-07-11', 2, 'Masculino'),
(6, '06443322', 'Hilda Victoria', 'Valdivia Cáceres', '1975-04-30', '2026-07-14', 51, 'Femenino');

-- Expedientes vinculados
INSERT IGNORE INTO expediente (id_expediente, numero_expediente, id_fallecido, id_solicitud, fecha_registro) VALUES
(1, 'EXP-2026-0001', 1, 1, '2026-05-10'),
(2, 'EXP-2026-0002', 2, 2, '2026-06-15'),
(3, 'EXP-2026-0003', 3, 3, '2026-07-02'),
(4, 'EXP-2026-0004', 4, 4, '2026-07-10'),
(5, 'EXP-2026-0005', 5, 5, '2026-07-12'),
(6, 'EXP-2026-0006', 6, 6, '2026-07-15');

-- Comprobantes de Registro
INSERT IGNORE INTO comprobante_registro (id_comprobante, id_expediente, fecha_emision) VALUES
(1, 1, '2026-05-10'),
(2, 2, '2026-06-15'),
(3, 3, '2026-07-02'),
(4, 4, '2026-07-10'),
(5, 5, '2026-07-12'),
(6, 6, '2026-07-15');
