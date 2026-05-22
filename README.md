# WARRIOR-GYM-
Tipo de documentó; 
USE warrior_gym;
CREATE TABLE tipo_documento (
    id_tipo_documento INT NOT NULL AUTO_INCREMENT,
    descripcion VARCHAR(50) NOT NULL,

    PRIMARY KEY (id_tipo_documento)
);

Tabla de cargos;
CREATE TABLE cargos (
    id_cargo INT NOT NULL AUTO_INCREMENT,
    nombre_cargo VARCHAR(50) NOT NULL,

    PRIMARY KEY (id_cargo)
);

Tabla Clientes;
CREATE TABLE clientes (
    id_cliente INT NOT NULL AUTO_INCREMENT,
    id_tipo_documento INT NOT NULL,
    numero_documento VARCHAR(20) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion VARCHAR(100),
    fecha_nacimiento DATE,
    fecha_registro DATE,
    estado VARCHAR(20),
    usuario VARCHAR(50) NOT NULL,
    contraseña VARCHAR(100) NOT NULL,

    PRIMARY KEY (id_cliente),

    FOREIGN KEY (id_tipo_documento)
        REFERENCES tipo_documento(id_tipo_documento)
);

Tabla Personal;
CREATE TABLE personal (
    id_personal INT NOT NULL AUTO_INCREMENT,
    id_tipo_documento INT NOT NULL,
    numero_documento VARCHAR(20) NOT NULL,
    id_cargo INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(20),
    usuario VARCHAR(50) NOT NULL,
    contraseña VARCHAR(100) NOT NULL,

    PRIMARY KEY (id_personal),

    FOREIGN KEY (id_tipo_documento)
        REFERENCES tipo_documento(id_tipo_documento),

    FOREIGN KEY (id_cargo)
        REFERENCES cargos(id_cargo)
);

Tabla de membresía;
CREATE TABLE membresias (
    id_membresia INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    duracion VARCHAR(30) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    descripcion VARCHAR(200),

    PRIMARY KEY (id_membresia)
);

Tabla Clientes_Membresía ;
CREATE TABLE cliente_membresia (
    id_cliente_membresia INT NOT NULL AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_membresia INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado VARCHAR(30),

    PRIMARY KEY (id_cliente_membresia),

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    FOREIGN KEY (id_membresia)
        REFERENCES membresias(id_membresia)
);

Tabla de pagos;
CREATE TABLE pagos (
    id_pago INT NOT NULL AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    metodo_pago VARCHAR(50),
    concepto VARCHAR(100),
    estado VARCHAR(30),

    PRIMARY KEY (id_pago),

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);

Tabla de Aparatos;
CREATE TABLE aparatos (
    id_aparato INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200),
    estado VARCHAR(30),

    PRIMARY KEY (id_aparato)
);

Tabla de Rutinas;
CREATE TABLE rutinas (
    id_rutina INT NOT NULL AUTO_INCREMENT,
    id_personal INT NOT NULL,
    nombre_rutina VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATE,

    PRIMARY KEY (id_rutina),

    FOREIGN KEY (id_personal)
        REFERENCES personal(id_personal)
);

Tabla de Rutinas_Clientes ;
CREATE TABLE rutinas_cliente (
    id_rutina_cliente INT NOT NULL AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_rutina INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,

    PRIMARY KEY (id_rutina_cliente),

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    FOREIGN KEY (id_rutina)
        REFERENCES rutinas(id_rutina)
)

Tabla de Productos;
CREATE TABLE productos (
    id_producto INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200),
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,

    PRIMARY KEY (id_producto)
);

Tabla de ventas;
CREATE TABLE ventas (
    id_venta INT NOT NULL AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    fecha DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_venta),

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);

Tabla de detalle _ ventas ;
CREATE TABLE detalle_ventas (
    id_detalle_venta INT NOT NULL AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_detalle_venta),

    FOREIGN KEY (id_venta)
        REFERENCES ventas(id_venta),

    FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
);

Tabla de asistencias;
CREATE TABLE asistencias (
    id_asistencia INT NOT NULL AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    tipo_ingreso VARCHAR(50),

    PRIMARY KEY (id_asistencia),

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);
