CREATE DATABASE IF NOT EXISTS sigs_cementerio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sigs_cementerio;

-- Tabla: Usuario
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario   INT AUTO_INCREMENT PRIMARY KEY,
    usuario      VARCHAR(50) NOT NULL UNIQUE,
    contrasena   VARCHAR(255) NOT NULL,
    rol          VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

-- Tabla: Solicitud_Sepultura
CREATE TABLE IF NOT EXISTS solicitud_sepultura (
    id_solicitud     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_deudo     VARCHAR(100) NOT NULL,
    dni_deudo        CHAR(8) NOT NULL,
    parentesco       VARCHAR(50) NOT NULL,
    fecha_solicitud  DATE NOT NULL
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

-- Tabla: Reporte_Estadistico
CREATE TABLE IF NOT EXISTS reporte_estadistico (
    id_reporte        INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte      VARCHAR(30) NOT NULL,
    fecha_generacion  DATE NOT NULL,
    generado_por      INT NOT NULL,
    FOREIGN KEY (generado_por) REFERENCES usuario(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla: Comprobante_Registro
CREATE TABLE IF NOT EXISTS comprobante_registro (
    id_comprobante  INT AUTO_INCREMENT PRIMARY KEY,
    id_expediente   INT NOT NULL,
    fecha_emision   DATE NOT NULL,
    FOREIGN KEY (id_expediente) REFERENCES expediente(id_expediente) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Usuarios semilla
INSERT IGNORE INTO usuario (usuario, contrasena, rol) VALUES
('jefe.cementerio', '$2y$10$2mW747j39a4JKlwEgc7ICOIsX3UkSzLeAfLb7KYM1dX0TQjECcg.W', 'admin'),
('admin.personal', '$2y$10$2mW747j39a4JKlwEgc7ICOIsX3UkSzLeAfLb7KYM1dX0TQjECcg.W', 'operador');
