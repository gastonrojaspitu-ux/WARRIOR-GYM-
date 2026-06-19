-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2026 a las 15:45:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `warrior_gym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aparatos`
--

CREATE TABLE `aparatos` (
  `id_aparato` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `estado` enum('Disponible','Mantenimiento','Fuera de servicio') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aparatos`
--

INSERT INTO `aparatos` (`id_aparato`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Cinta de Correr', 'Cardio', 'Disponible'),
(2, 'Bicicleta Fija', 'Cardio', 'Disponible'),
(3, 'Banco Plano', 'Musculacion', 'Disponible'),
(4, 'Rack de Sentadillas', 'Fuerza', 'Disponible'),
(5, 'Polea Multifuncion', 'Musculacion', 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id_asistencia` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `modalidad_ingreso` enum('Membresia','Pase diario','Invitado','Clase grupal') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL,
  `nombre_cargo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `nombre_cargo`) VALUES
(1, 'Administrador'),
(2, 'Entrenador'),
(3, 'Recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id_clase` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `cupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clases`
--

INSERT INTO `clases` (`id_clase`, `nombre`, `descripcion`, `horario`, `cupo`) VALUES
(1, 'Musculacion', 'Desarrollo muscular, fuerza e hipertrofia', '07:00 y 19:00', 50),
(2, 'Funcional', 'Movilidad, coordinacion y resistencia fisica', '08:30', 30),
(3, 'Cardio Fitness', 'Mejora cardiovascular y quema de calorias', '10:00', 40),
(4, 'Fuerza y Rendimiento', 'Potencia el desempeño fisico y deportivo', '17:00', 25),
(5, 'Preparacion Fisica', 'Entrenamiento para deportistas', '20:00', 20),
(6, 'Plan Personalizado', 'Rutinas adaptadas a objetivos especificos', '20:30', 15),
(7, 'Musculación', 'Desarrollo muscular y fuerza', '07:00 - 22:00', 50),
(8, 'Funcional', 'Movilidad y resistencia', '08:30', 20),
(9, 'Cardio Fitness', 'Entrenamiento cardiovascular', '10:00', 25),
(10, 'Fuerza y Rendimiento', 'Entrenamiento avanzado', '17:00', 20),
(11, 'Preparación Física', 'Preparación deportiva', '20:00', 15),
(12, 'Plan Personalizado', 'Rutinas adaptadas', '20:30', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `id_tipo_documento` int(11) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `estado` enum('Activo','Inactivo','Suspendido') NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `id_tipo_documento`, `numero_documento`, `nombre`, `apellido`, `telefono`, `email`, `direccion`, `fecha_nacimiento`, `fecha_registro`, `estado`, `usuario`, `contrasena`) VALUES
(8, 1, '40111222', 'Juan', 'Perez', '3774555555', 'juan@gmail.com', NULL, NULL, '2026-06-04', 'Activo', '40111222', '1234'),
(9, 1, '46144926', 'cristian ', 'rojas', '3779414232', 'gastonroja@s.gmail.com', NULL, NULL, '2026-06-05', 'Activo', '46144926', '1234'),
(12, 1, '45149925', 'Elena ', 'ramirez', '3774589266', 'elenaramirez@gmail.com.ar', NULL, NULL, '2026-06-19', 'Activo', '45149925', '1234'),
(13, 1, '46144628', 'Camila', 'Vasconcel', '3774623201', 'camivasconcel@gmail.com', NULL, NULL, '2026-06-19', 'Activo', '46144628', '1234'),
(14, 1, '48144326', 'Tiago ian', 'Rojas', '3774457266', 'tiagoinaroja@sgmail.com', NULL, NULL, '2026-06-19', 'Activo', '48144326', '1234'),
(15, 1, '46144828', 'Ignacio', 'avellaneda', '3774413258', 'ignaavell@gmail.com', NULL, NULL, '2026-06-19', 'Activo', '46144828', '1234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_clase`
--

CREATE TABLE `cliente_clase` (
  `id_cliente_clase` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `fecha_inscripcion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_membresia`
--

CREATE TABLE `cliente_membresia` (
  `id_cliente_membresia` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_membresia` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activa','Vencida','Cancelada') NOT NULL
) ;

--
-- Volcado de datos para la tabla `cliente_membresia`
--

INSERT INTO `cliente_membresia` (`id_cliente_membresia`, `id_cliente`, `id_membresia`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(7, 8, 1, '2026-06-05', '2026-06-14', 'Activa'),
(8, 9, 2, '2026-06-01', '2026-06-30', 'Activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_web`
--

CREATE TABLE `contacto_web` (
  `id_contacto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle_venta` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle_venta`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`, `precio`) VALUES
(14, 16, 1, 1, 46999.98, NULL),
(15, 18, 1, 1, 46999.98, NULL),
(16, 18, 2, 1, 79000.00, NULL),
(17, 19, 3, 1, 85000.00, NULL),
(18, 19, 4, 1, 66000.00, NULL),
(19, 20, 1, 1, 46999.98, NULL),
(20, 20, 2, 1, 79000.00, NULL),
(21, 21, 1, 1, 46999.98, NULL),
(22, 21, 2, 1, 79000.00, NULL),
(23, 22, 7, 1, 48000.00, NULL),
(24, 22, 8, 1, 35000.00, NULL),
(25, 23, 1, 1, 47000.00, NULL),
(26, 23, 2, 1, 79000.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresias`
--

CREATE TABLE `membresias` (
  `id_membresia` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `membresias`
--

INSERT INTO `membresias` (`id_membresia`, `nombre`, `precio`, `descripcion`) VALUES
(1, 'Básico', 18000.00, 'Acceso a musculación, vestuarios y horario estándar'),
(2, 'Premium', 28000.00, 'Incluye musculación, clases grupales y seguimiento profesional'),
(3, 'Elite', 45990.00, 'Acceso completo, rutinas personalizadas y reservas prioritarias');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia','Mercado Pago') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `estado` enum('Pagado','Pendiente','Rechazado') NOT NULL
) ;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_cliente`, `monto`, `fecha_pago`, `metodo_pago`, `concepto`, `estado`) VALUES
(1, 9, 28000.00, '2026-06-19', 'Efectivo', 'cuota del gym', 'Pagado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id_personal` int(11) NOT NULL,
  `id_tipo_documento` int(11) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`id_personal`, `id_tipo_documento`, `numero_documento`, `nombre`, `apellido`, `telefono`, `email`, `usuario`, `contrasena`, `estado`) VALUES
(1, 1, '30111222', 'Lucas', 'Fernandez', '3774551001', 'lucas@warriorgym.com', 'lucas', '1234', 'Activo'),
(2, 1, '30222333', 'Sofia', 'Benitez', '3774551002', 'sofia@warriorgym.com', 'sofia', '1234', 'Activo'),
(3, 1, '30333444', 'Martin', 'Gomez', '3774551003', 'martin@warriorgym.com', 'martin', '1234', 'Activo'),
(4, 1, '25000000', 'Admin', 'Warrior', '3774550000', 'admin@warriorgym.com', 'admin', 'admin', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_cargo`
--

CREATE TABLE `personal_cargo` (
  `id_personal` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal_cargo`
--

INSERT INTO `personal_cargo` (`id_personal`, `id_cargo`) VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL
) ;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `precio`, `stock`) VALUES
(1, 'Whey Protein Warrior 1kg', 'Proteína concentrada ideal para recuperación muscular y crecimiento', 47000.00, 8),
(2, 'Whey Protein Warrior 2kg', 'Mayor rendimiento y mejor relación costo-beneficio', 79000.00, 10),
(3, 'Isolate Protein Premium', 'Proteína aislada de rápida absorción y máxima pureza', 85000.00, 8),
(4, 'Mass Gainer 3kg', 'Ideal para aumentar masa muscular y calorías diarias', 66000.00, 12),
(5, 'Creatina Monohidratada 300g', 'Aumenta fuerza, potencia y recuperación muscular', 28000.00, 20),
(6, 'Creatina Monohidratada 500g', 'Mayor duración y excelente relación precio-rendimiento', 43000.00, 15),
(7, 'Creatina Micronizada Premium', 'Disolución superior y absorción optimizada', 48000.00, 8),
(8, 'Pre Workout Energy', 'Impulso energético para entrenamientos intensos', 35000.00, 15),
(9, 'Pre Workout Extreme', 'Máximo enfoque, potencia y resistencia', 42000.00, 10),
(10, 'BCAA 2:1:1', 'Aminoácidos esenciales para proteger la masa muscular', 30000.00, 18),
(11, 'Glutamina 300g', 'Favorece la recuperación muscular y el sistema inmune', 26000.00, 14),
(12, 'Multivitamínico Deportivo', 'Vitaminas y minerales para rendimiento', 18000.00, 25),
(13, 'Omega 3 Premium', 'Salud cardiovascular y recuperación', 22000.00, 20),
(14, 'ThermoFit Fat Burner', 'Suplemento para pérdida de grasa corporal', 38000.00, 12),
(15, 'Recovery Complex', 'Fórmula avanzada de recuperación post-entreno', 32000.00, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_aparato` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado_reserva` enum('Activa','Cancelada','Finalizada') NOT NULL
) ;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_cliente`, `id_aparato`, `fecha`, `hora_inicio`, `hora_fin`, `estado_reserva`) VALUES
(1, 9, 3, '2026-06-09', '07:19:00', '10:20:00', 'Activa'),
(2, 8, 1, '2026-06-17', '11:28:00', '13:30:00', 'Activa'),
(3, 8, 1, '2026-08-28', '09:04:00', '11:05:00', 'Activa'),
(4, 12, 2, '2026-07-12', '08:00:00', '11:00:00', 'Activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutinas`
--

CREATE TABLE `rutinas` (
  `id_rutina` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `nombre_rutina` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutinas`
--

INSERT INTO `rutinas` (`id_rutina`, `id_personal`, `nombre_rutina`, `descripcion`, `fecha_creacion`) VALUES
(2, 1, 'fuerza libre', 'peso libre', '2026-06-19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutinas_cliente`
--

CREATE TABLE `rutinas_cliente` (
  `id_rutina_cliente` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_rutina` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `rutinas_cliente`
--

INSERT INTO `rutinas_cliente` (`id_rutina_cliente`, `id_cliente`, `id_rutina`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 8, 2, '2026-06-20', '2026-10-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_membresia`
--

CREATE TABLE `solicitudes_membresia` (
  `id_solicitud` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `plan_solicitado` varchar(50) DEFAULT NULL,
  `fecha_solicitud` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_membresia`
--

INSERT INTO `solicitudes_membresia` (`id_solicitud`, `nombre`, `apellido`, `email`, `telefono`, `plan_solicitado`, `fecha_solicitud`) VALUES
(1, 'gastón', 'fernandez', 'gastonfernandez@gmail.com.ar', '3774966655', 'Premium', '2026-06-11'),
(2, 'magnecio ', 'Gaston Rojas', 'gastonrojaspitu@gmail.com', '3774966655', 'Elite', '2026-06-12'),
(3, 'Ramiro ', 'Pintos ', 'ramiropintos@gmail.com', '3774526070', 'Premium', '2026-06-18'),
(4, 'Elena ', 'ramirez', 'elenaramirez@gmial.com.ar', '3774885596', 'Elite', '2026-06-19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`id_tipo_documento`, `descripcion`) VALUES
(3, 'CUIL'),
(1, 'DNI'),
(2, 'Pasaporte');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `rol` enum('admin','cliente') NOT NULL,
  `estado` varchar(20) DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password`, `rol`, `estado`) VALUES
(1, 'lucas', 'lucas', '1234', 'admin', 'Activo'),
(2, 'sofia', 'sofia', '1234', 'admin', 'Activo'),
(3, 'martin', 'martin', '1234', 'admin', 'Activo'),
(4, 'admin', 'admin', 'admin', 'admin', 'Activo'),
(9, 'Elena ', 'elenaramirez@gmail.com.ar', '1234', 'cliente', 'Activo'),
(10, 'cristian ', 'gastonroja@s.gmail.com', '1234', 'cliente', 'Activo'),
(11, 'Juan', 'juan@gmail.com', '1234', 'cliente', 'Activo'),
(12, 'Camila', 'camivasconcel@gmail.com', '1234', 'cliente', 'Activo'),
(13, 'Tiago', 'tiagoinaroja@sgmail.com', '1234', 'cliente', 'Activo'),
(14, 'Ignacio', 'ignaavell@gmail.com', '1234', 'cliente', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `fecha`, `id_usuario`) VALUES
(16, '2026-06-12', NULL),
(18, '2026-06-12', 4),
(19, '2026-06-12', 4),
(20, '2026-06-12', 4),
(21, '2026-06-12', 4),
(22, '2026-06-18', 4),
(23, '2026-06-19', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aparatos`
--
ALTER TABLE `aparatos`
  ADD PRIMARY KEY (`id_aparato`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`),
  ADD UNIQUE KEY `nombre_cargo` (`nombre_cargo`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id_clase`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `cliente_clase`
--
ALTER TABLE `cliente_clase`
  ADD PRIMARY KEY (`id_cliente_clase`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `cliente_membresia`
--
ALTER TABLE `cliente_membresia`
  ADD PRIMARY KEY (`id_cliente_membresia`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_membresia` (`id_membresia`);

--
-- Indices de la tabla `contacto_web`
--
ALTER TABLE `contacto_web`
  ADD PRIMARY KEY (`id_contacto`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle_venta`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `membresias`
--
ALTER TABLE `membresias`
  ADD PRIMARY KEY (`id_membresia`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id_personal`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `personal_cargo`
--
ALTER TABLE `personal_cargo`
  ADD PRIMARY KEY (`id_personal`,`id_cargo`),
  ADD KEY `id_cargo` (`id_cargo`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD UNIQUE KEY `id_cliente` (`id_cliente`,`id_aparato`,`fecha`,`hora_inicio`),
  ADD KEY `id_aparato` (`id_aparato`);

--
-- Indices de la tabla `rutinas`
--
ALTER TABLE `rutinas`
  ADD PRIMARY KEY (`id_rutina`),
  ADD KEY `id_personal` (`id_personal`);

--
-- Indices de la tabla `rutinas_cliente`
--
ALTER TABLE `rutinas_cliente`
  ADD PRIMARY KEY (`id_rutina_cliente`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_rutina` (`id_rutina`);

--
-- Indices de la tabla `solicitudes_membresia`
--
ALTER TABLE `solicitudes_membresia`
  ADD PRIMARY KEY (`id_solicitud`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`),
  ADD UNIQUE KEY `descripcion` (`descripcion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aparatos`
--
ALTER TABLE `aparatos`
  MODIFY `id_aparato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `cliente_clase`
--
ALTER TABLE `cliente_clase`
  MODIFY `id_cliente_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_membresia`
--
ALTER TABLE `cliente_membresia`
  MODIFY `id_cliente_membresia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contacto_web`
--
ALTER TABLE `contacto_web`
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `membresias`
--
ALTER TABLE `membresias`
  MODIFY `id_membresia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id_personal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rutinas`
--
ALTER TABLE `rutinas`
  MODIFY `id_rutina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rutinas_cliente`
--
ALTER TABLE `rutinas_cliente`
  MODIFY `id_rutina_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_membresia`
--
ALTER TABLE `solicitudes_membresia`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`);

--
-- Filtros para la tabla `cliente_clase`
--
ALTER TABLE `cliente_clase`
  ADD CONSTRAINT `cliente_clase_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `cliente_clase_ibfk_2` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`id_clase`);

--
-- Filtros para la tabla `cliente_membresia`
--
ALTER TABLE `cliente_membresia`
  ADD CONSTRAINT `cliente_membresia_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `cliente_membresia_ibfk_2` FOREIGN KEY (`id_membresia`) REFERENCES `membresias` (`id_membresia`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `personal_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`);

--
-- Filtros para la tabla `personal_cargo`
--
ALTER TABLE `personal_cargo`
  ADD CONSTRAINT `personal_cargo_ibfk_1` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`),
  ADD CONSTRAINT `personal_cargo_ibfk_2` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_aparato`) REFERENCES `aparatos` (`id_aparato`);

--
-- Filtros para la tabla `rutinas`
--
ALTER TABLE `rutinas`
  ADD CONSTRAINT `rutinas_ibfk_1` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`);

--
-- Filtros para la tabla `rutinas_cliente`
--
ALTER TABLE `rutinas_cliente`
  ADD CONSTRAINT `rutinas_cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `rutinas_cliente_ibfk_2` FOREIGN KEY (`id_rutina`) REFERENCES `rutinas` (`id_rutina`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
