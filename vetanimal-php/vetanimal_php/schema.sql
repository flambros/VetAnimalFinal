-- ==========================================
-- VetAnimal - Esquema de base de datos MySQL
-- ==========================================

CREATE DATABASE IF NOT EXISTS vetanimal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vetanimal;

-- ---------- USERS ----------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('cliente','veterinario') NOT NULL DEFAULT 'cliente',
  telefono VARCHAR(50) DEFAULT NULL,
  especialidad VARCHAR(150) DEFAULT NULL,
  foto VARCHAR(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- PETS ----------
CREATE TABLE pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  especie VARCHAR(50) NOT NULL,
  raza VARCHAR(100) DEFAULT NULL,
  edad INT DEFAULT NULL,
  peso DECIMAL(6,2) DEFAULT NULL,
  foto VARCHAR(500) DEFAULT NULL,
  estado_salud VARCHAR(100) DEFAULT 'Estable',
  alergias TEXT DEFAULT NULL,
  condiciones_cronicas TEXT DEFAULT NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- SERVICES ----------
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  duracion_min INT DEFAULT 30,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  icono VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- TURNOS ----------
CREATE TABLE turnos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mascota_id INT NOT NULL,
  servicio_id INT NOT NULL,
  veterinario_id INT DEFAULT NULL,
  fecha DATE NOT NULL,
  hora VARCHAR(10) NOT NULL,
  estado ENUM('pendiente','confirmado','cancelado','completado') NOT NULL DEFAULT 'confirmado',
  notas TEXT DEFAULT NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mascota_id) REFERENCES pets(id) ON DELETE CASCADE,
  FOREIGN KEY (servicio_id) REFERENCES services(id),
  FOREIGN KEY (veterinario_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- CONSULTAS (historial clínico) ----------
CREATE TABLE consultas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mascota_id INT NOT NULL,
  veterinario_id INT DEFAULT NULL,
  fecha DATE NOT NULL,
  tipo ENUM('CONTROL','EMERGENCIA','VACUNA','CIRUGIA','DIAGNOSTICO') NOT NULL DEFAULT 'CONTROL',
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT NOT NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mascota_id) REFERENCES pets(id) ON DELETE CASCADE,
  FOREIGN KEY (veterinario_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- VACUNAS ----------
CREATE TABLE vacunas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mascota_id INT NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  fecha_aplicacion DATE DEFAULT NULL,
  fecha_refuerzo DATE DEFAULT NULL,
  estado ENUM('AL_DIA','VENCIDA','PENDIENTE') NOT NULL DEFAULT 'PENDIENTE',
  FOREIGN KEY (mascota_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- ESTUDIOS ----------
CREATE TABLE estudios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mascota_id INT NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  tipo VARCHAR(100) DEFAULT NULL,
  fecha DATE NOT NULL,
  resultado_url VARCHAR(500) DEFAULT NULL,
  FOREIGN KEY (mascota_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- PRODUCTS ----------
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(200) NOT NULL,
  categoria ENUM('Medicamentos','Bienestar y Estética','Nutrición y Alimento','Pulgas y Garrapatas') NOT NULL,
  etiqueta VARCHAR(50) DEFAULT NULL,
  descripcion TEXT DEFAULT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  imagen VARCHAR(500) DEFAULT NULL,
  requiere_receta TINYINT(1) NOT NULL DEFAULT 0,
  stock INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- ORDERS ----------
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  estado ENUM('pendiente','pagado','enviado','entregado') NOT NULL DEFAULT 'pendiente',
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (producto_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- DATOS DE PRUEBA (seed)
-- Contraseñas en texto plano "123456" -> hasheadas con password_hash (bcrypt)
-- ==========================================

INSERT INTO users (nombre, email, password, rol, telefono, especialidad, foto) VALUES
('Dr. Santiago Méndez', 'santiago.mendez@vetanimal.com', '$2b$10$5FRuVWoZ0KWO//2zj.nCuus3m4e61xHpXXT3O95gEJrA0LlTR8Yf6', 'veterinario', '11-4000-1000', 'Medicina General', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=200&q=80'),
('Dra. Martina Paz', 'martina.paz@vetanimal.com', '$2b$10$5FRuVWoZ0KWO//2zj.nCuus3m4e61xHpXXT3O95gEJrA0LlTR8Yf6', 'veterinario', '11-4000-1001', 'Emergencias', 'https://images.unsplash.com/photo-1594824813566-78809a712f5a?w=200&q=80'),
('Agustina Gómez', 'agustina.gomez@example.com', '$2b$10$5FRuVWoZ0KWO//2zj.nCuus3m4e61xHpXXT3O95gEJrA0LlTR8Yf6', 'cliente', '11-5555-2222', NULL, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&q=80');
-- Nota: las 3 contraseñas de prueba de arriba son "123456"

INSERT INTO pets (usuario_id, nombre, especie, raza, edad, peso, foto, estado_salud, alergias, condiciones_cronicas, creado_en) VALUES
(3, 'Bella', 'Perro', 'Golden Retriever', 4, 28.5, 'https://images.unsplash.com/photo-1552053831-71594a27632d?w=500&q=80', 'Estable', 'Picaduras de Pulga, Polen', 'Ninguna diagnosticada a la fecha.', '2024-01-10 10:00:00');

INSERT INTO services (nombre, descripcion, duracion_min, precio, icono) VALUES
('Chequeo General', 'Consulta estándar de 30 minutos', 30, 25.00, 'heart'),
('Cirugía Avanzada', 'Salas quirúrgicas de última generación', 90, 250.00, 'scalpel'),
('Diagnósticos', 'Laboratorio propio e imágenes digitales', 45, 60.00, 'flask'),
('Vacunación', 'Aplicación de vacunas y refuerzos', 20, 18.00, 'syringe');

INSERT INTO turnos (mascota_id, servicio_id, veterinario_id, fecha, hora, estado, notas, creado_en) VALUES
(1, 1, 1, '2026-08-25', '10:00', 'confirmado', 'Consulta de rutina preventiva.', '2026-08-01 12:00:00'),
(1, 4, 2, '2026-08-30', '11:30', 'confirmado', 'Refuerzo anual.', '2026-08-05 14:30:00');

INSERT INTO consultas (mascota_id, veterinario_id, fecha, tipo, titulo, descripcion, creado_en) VALUES
(1, 1, '2024-05-15', 'CONTROL', 'Control Anual Preventivo', 'Paciente presenta excelente condición física. Se realizó examen físico completo, limpieza dental superficial y actualización de peso.', '2024-05-15 11:00:00'),
(1, 2, '2024-03-02', 'EMERGENCIA', 'Urgencia: Dermatitis Aguda', 'Reacción alérgica en zona abdominal. Se administró antihistamínico vía oral y se recetó pomada calmante por 7 días.', '2024-03-02 16:20:00'),
(1, 1, '2023-11-20', 'VACUNA', 'Vacunación Cuádruple', 'Aplicación de refuerzo anual sin complicaciones posteriores.', '2023-11-20 10:15:00');

INSERT INTO vacunas (mascota_id, nombre, fecha_aplicacion, fecha_refuerzo, estado) VALUES
(1, 'Antirrábica', '2025-05-15', '2026-05-15', 'AL_DIA'),
(1, 'DHPP (Quíntuple)', '2025-11-20', '2026-11-20', 'AL_DIA'),
(1, 'Giardia', '2023-04-10', '2024-04-10', 'VENCIDA');

INSERT INTO estudios (mascota_id, nombre, tipo, fecha, resultado_url) VALUES
(1, 'Hemograma Completo', 'Laboratorio', '2024-05-15', '#'),
(1, 'Radiografía Cadera', 'Imágenes', '2024-01-05', '#'),
(1, 'Ecocardiograma', 'Cardiología', '2023-11-20', '#');

INSERT INTO products (nombre, categoria, etiqueta, descripcion, precio, imagen, requiere_receta, stock) VALUES
('Preventivo de Dirofilaria Canina', 'Medicamentos', 'RECETADO', 'Tableta masticable mensual para perros de 11 a 23 kg. Requiere receta válida.', 45.00, 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80', 1, 40),
('Champú Suave de Avena', 'Bienestar y Estética', 'BIENESTAR', 'Fórmula hipoalergénica para pieles sensibles. Libre de parabenos.', 22.50, 'https://images.unsplash.com/photo-1583947581924-860bda6a26df?w=400&q=80', 0, 60),
('Dieta de Soporte de Movilidad Avanzada', 'Nutrición y Alimento', 'ALIMENTO', 'Bolsa de 6.8 kg. Clínicamente probado para mejorar la salud articular en 21 días.', 78.00, 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?w=400&q=80', 0, 25),
('Multivitamínico Diario Masticable', 'Bienestar y Estética', 'BIENESTAR', '60 bocaditos blandos. Vitaminas y minerales esenciales para perros adultos.', 34.99, 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=400&q=80', 0, 55);
