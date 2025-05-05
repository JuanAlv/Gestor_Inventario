CREATE DATABASE gestor_inventario;
USE gestor_inventario;

/*TABLAS AUXILIARES*/
-- Tabla para almacenar tipos de documento (CC, TI, CE, etc.)
CREATE TABLE tipo_documentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(20) NOT NULL UNIQUE
);

-- Tabla para almacenar tipos de roles (administrador, vendedor, cliente, etc.)
CREATE TABLE tipo_roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(20) NOT NULL UNIQUE
);

-- Tabla para el estado de un usuario (activo, inactivo, suspendido, etc.)
CREATE TABLE estado (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(20)
);

-- Tabla para clasificar productos por categoría (alimentos, bebidas, etc.)
CREATE TABLE tipo_categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL UNIQUE
);

-- Tipos de factura (ej: Factura A, B, C)
CREATE TABLE tipo_factura (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL UNIQUE
);

-- Tipos de pago aceptados (efectivo, tarjeta, transferencia, etc.)
CREATE TABLE tipo_pagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL UNIQUE
);

-- Tabla de usuarios del sistema (clientes, vendedores, administradores)
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL,
  documento VARCHAR(20) NOT NULL UNIQUE,
  id_tipo_documento INT NOT NULL,
  id_rol INT NOT NULL,
  contrasena VARCHAR(255) NOT NULL,
  id_estado INT NOT NULL,
  -- Relaciones con tablas auxiliares
  FOREIGN KEY (id_tipo_documento) REFERENCES tipo_documentos(id),
  FOREIGN KEY (id_rol) REFERENCES tipo_roles(id),
  FOREIGN KEY (id_estado) REFERENCES estado(id)
);

-- Tabla de proveedores de productos
CREATE TABLE proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  razon_social VARCHAR(100) NOT NULL,        -- Nombre de la empresa
  contacto_nombre VARCHAR(100),              -- Nombre de la persona de contacto
  contacto_apellido VARCHAR(100),            -- Apellido de contacto
  nit VARCHAR(20) NOT NULL UNIQUE,           -- Número de identificación tributaria
  telefono VARCHAR(20) NOT NULL,
  correo VARCHAR(100) NOT NULL
);

-- Registro de compras hechas a proveedores
CREATE TABLE compras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_proveedor INT NOT NULL,
  fecha_compra DATE NOT NULL,
  precio_compra DECIMAL(10,2) NOT NULL,       -- Total de la compra
  id_usuario INT NOT NULL,                    -- Usuario que realizó el registro
  FOREIGN KEY (id_proveedor) REFERENCES proveedores(id),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Catálogo de productos registrados
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(20) NOT NULL UNIQUE,         -- Código interno
  nombre VARCHAR(50) NOT NULL UNIQUE,
  id_categoria INT NOT NULL,
  id_proveedor INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  fecha_fabricacion DATE NOT NULL,
  fecha_vencimiento DATE NOT NULL,
  fecha_registro DATE NOT NULL,
  lote VARCHAR(50) NOT NULL,
  FOREIGN KEY (id_categoria) REFERENCES tipo_categorias(id),
  FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

-- Control de existencias de productos
CREATE TABLE inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_producto INT NOT NULL,
  cantidad_actual INT NOT NULL,              -- Stock disponible
  fecha_ultima_entrada DATE NOT NULL,        -- Último ingreso al inventario
  FOREIGN KEY (id_producto) REFERENCES productos(id)
);

CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario_cliente INT NOT NULL,       -- Cliente que realizó la compra (usuario)
  id_usuario_vendedor INT NOT NULL,      -- Vendedor (usuario del sistema)
  id_tipo_factura INT NOT NULL,
  id_tipo_pago INT NOT NULL,
  fecha_venta DATE NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_usuario_cliente) REFERENCES usuarios(id),
  FOREIGN KEY (id_usuario_vendedor) REFERENCES usuarios(id),
  FOREIGN KEY (id_tipo_factura) REFERENCES tipo_factura(id),
  FOREIGN KEY (id_tipo_pago) REFERENCES tipo_pagos(id)
);

-- Detalle de productos vendidos por cada venta
CREATE TABLE detalle_ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,                     -- Venta a la que pertenece
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,           -- cantidad * precio_unitario
  FOREIGN KEY (id_venta) REFERENCES ventas(id),
  FOREIGN KEY (id_producto) REFERENCES productos(id)
);

