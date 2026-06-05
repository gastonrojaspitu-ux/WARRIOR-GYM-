 CREATE TABLE tipo_documento (
    id_tipo_documento INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL UNIQUE
    );


INSERT INTO tipo_documento (descripcion)
VALUES
('DNI'),
('Pasaporte'),
('CUIL');


CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,

    id_tipo_documento INT NOT NULL,

    numero_documento VARCHAR(20) NOT NULL UNIQUE,

    nombre VARCHAR(50) NOT NULL,

    apellido VARCHAR(50) NOT NULL,

    telefono VARCHAR(20),

    email VARCHAR(100) UNIQUE,

    direccion VARCHAR(100),

    fecha_nacimiento DATE,

    fecha_registro DATE NOT NULL,

    estado ENUM('Activo','Inactivo','Suspendido') NOT NULL,

    usuario VARCHAR(50) NOT NULL UNIQUE,

    contrasena VARCHAR(255) NOT NULL,

    FOREIGN KEY (id_tipo_documento)
        REFERENCES tipo_documento(id_tipo_documento)
);


CREATE TABLE cargos (
    id_cargo INT AUTO_INCREMENT PRIMARY KEY,

    nombre_cargo VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE personal (
    id_personal INT AUTO_INCREMENT PRIMARY KEY,

    id_tipo_documento INT NOT NULL,

    numero_documento VARCHAR(20) NOT NULL UNIQUE,

    nombre VARCHAR(50) NOT NULL,

    apellido VARCHAR(50) NOT NULL,

    telefono VARCHAR(20),

    email VARCHAR(100) UNIQUE,

    usuario VARCHAR(50) NOT NULL UNIQUE,

    contrasena VARCHAR(255) NOT NULL,

    estado ENUM('Activo','Inactivo') NOT NULL,

    FOREIGN KEY (id_tipo_documento)
        REFERENCES tipo_documento(id_tipo_documento)
);


CREATE TABLE personal_cargo (
    id_personal INT NOT NULL,

    id_cargo INT NOT NULL,

    PRIMARY KEY (id_personal, id_cargo),

    FOREIGN KEY (id_personal)
        REFERENCES personal(id_personal),

    FOREIGN KEY (id_cargo)
        REFERENCES cargos(id_cargo)
);



CREATE TABLE membresias (
    id_membresia INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(50) NOT NULL UNIQUE,

    precio DECIMAL(10,2) NOT NULL,

    descripcion VARCHAR(200)
);



CREATE TABLE cliente_membresia (
    id_cliente_membresia INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    id_membresia INT NOT NULL,

    fecha_inicio DATE NOT NULL,

    fecha_fin DATE NOT NULL,

    estado ENUM('Activa','Vencida','Cancelada') NOT NULL,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    FOREIGN KEY (id_membresia)
        REFERENCES membresias(id_membresia)
);



CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    monto DECIMAL(10,2) NOT NULL,

    fecha_pago DATE NOT NULL,

    metodo_pago ENUM('Efectivo','Tarjeta','Transferencia','Mercado Pago') NOT NULL,

    concepto VARCHAR(100) NOT NULL,

    estado ENUM('Pagado','Pendiente','Rechazado') NOT NULL,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);



CREATE TABLE aparatos (
    id_aparato INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    descripcion VARCHAR(200),

    estado ENUM('Disponible','Mantenimiento','Fuera de servicio') NOT NULL
);



CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    id_aparato INT NOT NULL,

    fecha DATE NOT NULL,

    hora_inicio TIME NOT NULL,

    hora_fin TIME NOT NULL,

    estado_reserva ENUM('Activa','Cancelada','Finalizada') NOT NULL,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    FOREIGN KEY (id_aparato)
        REFERENCES aparatos(id_aparato),

    UNIQUE (id_cliente, id_aparato, fecha, hora_inicio)
);



CREATE TABLE rutinas (
    id_rutina INT AUTO_INCREMENT PRIMARY KEY,

    id_personal INT NOT NULL,

    nombre_rutina VARCHAR(100) NOT NULL,

    descripcion TEXT,

    fecha_creacion DATE NOT NULL,

    FOREIGN KEY (id_personal)
        REFERENCES personal(id_personal)
);



CREATE TABLE rutinas_cliente (
    id_rutina_cliente INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    id_rutina INT NOT NULL,

    fecha_inicio DATE NOT NULL,

    fecha_fin DATE,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    FOREIGN KEY (id_rutina)
        REFERENCES rutinas(id_rutina)
);



CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    descripcion VARCHAR(200),

    precio DECIMAL(10,2) NOT NULL,

    stock INT NOT NULL,

    CHECK (precio > 0),

    CHECK (stock >= 0)
);



CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    fecha DATE NOT NULL,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);



CREATE TABLE detalle_ventas (
    id_detalle_venta INT AUTO_INCREMENT PRIMARY KEY,

    id_venta INT NOT NULL,

    id_producto INT NOT NULL,

    cantidad INT NOT NULL,

    precio_unitario DECIMAL(10,2) NOT NULL,

    CHECK (cantidad > 0),

    CHECK (precio_unitario > 0),

    FOREIGN KEY (id_venta)
        REFERENCES ventas(id_venta),

    FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
);



CREATE TABLE asistencias (
    id_asistencia INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    fecha DATE NOT NULL,

    hora TIME NOT NULL,

    modalidad_ingreso ENUM(
        'Membresia',
        'Pase diario',
        'Invitado',
        'Clase grupal'
    ) NOT NULL,

    FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);

