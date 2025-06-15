-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS tienda_productos;
USE tienda_productos;

-- Tabla de usuarios (para el login y administración)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- Tabla de productos (para el CRUD principal)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    codigo_producto VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- Insertar un usuario administrador por defecto
-- Password: admin123 (hasheada con password_hash())
INSERT INTO usuarios (nombre, email, telefono, password) VALUES 
('Administrador', 'admin@tienda.com', '123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oStkLpDdXTgs4YNjHqA3a9wDo/QMOi');

-- Insertar algunos productos de ejemplo
INSERT INTO productos (nombre, codigo_producto, descripcion, precio, imagen) VALUES 
('Laptop HP Pavilion', 'LAP001', 'Laptop HP Pavilion 15 pulgadas, 8GB RAM, 256GB SSD', 899.99, 'laptop_hp.jpg'),
('Mouse Inalámbrico', 'MOU001', 'Mouse inalámbrico ergonómico con batería de larga duración', 29.99, 'mouse_wireless.jpg'),
('Teclado Mecánico', 'TEC001', 'Teclado mecánico RGB para gaming y trabajo profesional', 79.99, 'teclado_mecanico.jpg'),
('Monitor 24 pulgadas', 'MON001', 'Monitor Full HD 24 pulgadas, perfecto para oficina', 199.99, 'monitor_24.jpg');