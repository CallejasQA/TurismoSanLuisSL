-- Script SQL (español) para turismo_sanluis_db
CREATE DATABASE IF NOT EXISTS turismo_sanluis_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE turismo_sanluis_db;

-- tabla usuarios
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin','propietario','cliente') NOT NULL DEFAULT 'cliente',
  estado ENUM('activo','inactivo') DEFAULT 'activo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- perfiles de clientes
DROP TABLE IF EXISTS clientes_perfiles;
CREATE TABLE clientes_perfiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  primer_nombre VARCHAR(50) NOT NULL,
  segundo_nombre VARCHAR(50),
  primer_apellido VARCHAR(50) NOT NULL,
  cedula VARCHAR(40),
  telefono_codigo VARCHAR(8) DEFAULT '+57',
  telefono_numero VARCHAR(40) NOT NULL,
  municipio_origen VARCHAR(100),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_cedula_cliente (cedula),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- usuarios base (contraseña: 123456)
INSERT INTO usuarios (nombre,email,password,rol,estado) VALUES
('Administrador','admin@turismosl.com','$2y$12$737fzBthKgaZ1uKYhiSZcOYj3SvNdLLKp2ucvvIeI2Yb5S7.Sqmzq','admin','activo'),
('Propietario Demo','propietario@turismosl.com','$2y$12$737fzBthKgaZ1uKYhiSZcOYj3SvNdLLKp2ucvvIeI2Yb5S7.Sqmzq','propietario','activo'),
('Cliente Demo','cliente@turismosl.com','$2y$12$737fzBthKgaZ1uKYhiSZcOYj3SvNdLLKp2ucvvIeI2Yb5S7.Sqmzq','cliente','activo');

INSERT INTO clientes_perfiles (usuario_id, primer_nombre, segundo_nombre, primer_apellido, cedula, telefono_codigo, telefono_numero, municipio_origen)
VALUES (3,'Cliente','Demo','Turismo','123456789','+57','3001234567','San Luis');

-- perfiles de propietarios
DROP TABLE IF EXISTS propietarios_perfiles;
CREATE TABLE propietarios_perfiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  telefono VARCHAR(30),
  direccion VARCHAR(255),
  descripcion TEXT,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- afiliados (solicitudes)
DROP TABLE IF EXISTS afiliados;
CREATE TABLE afiliados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre_negocio VARCHAR(200) NOT NULL,
  tipo ENUM('Finca','Glamping','Hotel','Cabaña','Ecohotel') DEFAULT 'Finca',
  descripcion TEXT,
  direccion VARCHAR(255),
  estado ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  solicitado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- alojamientos
DROP TABLE IF EXISTS alojamientos;
CREATE TABLE alojamientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  propietario_id INT NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  descripcion TEXT,
  ubicacion VARCHAR(255),
  precio_noche DECIMAL(10,2) NOT NULL DEFAULT 0,
  rango_precio VARCHAR(50),
  imagen VARCHAR(255),
  estado ENUM('pendiente','aprobado','rechazado','inactivo','activo') DEFAULT 'pendiente',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (propietario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- servicios de alojamientos
DROP TABLE IF EXISTS servicios;
CREATE TABLE servicios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL UNIQUE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO servicios (nombre) VALUES
('WiFi'),('Parqueadero'),('Piscina'),('Cocina'),('Baño privado'),('Agua caliente'),('Televisión'),('Aire acondicionado'),('Ventilador'),
('Desayuno incluido'),('Recepción 24 horas'),('Zona BBQ'),('Jacuzzi'),('Sauna'),('Restaurante'),('Bar'),('Terraza'),('Jardín'),('Servicio de transporte'),
('Permitido fumar (zonas designadas)'),('Zona de juegos infantiles'),('Senderos ecológicos'),('Actividades guiadas'),('Hamacas'),('Zona de camping'),('Mascotas permitidas (con/sin costo)'),('Cocina compartida');

DROP TABLE IF EXISTS alojamiento_servicio;
CREATE TABLE alojamiento_servicio (
  alojamiento_id INT NOT NULL,
  servicio_id INT NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (alojamiento_id, servicio_id),
  FOREIGN KEY (alojamiento_id) REFERENCES alojamientos(id) ON DELETE CASCADE,
  FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- reservas
DROP TABLE IF EXISTS reservas;
CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  alojamiento_id INT NOT NULL,
  cliente_id INT NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  total DECIMAL(12,2) NOT NULL,
  estado ENUM('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (alojamiento_id) REFERENCES alojamientos(id) ON DELETE CASCADE,
  FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
