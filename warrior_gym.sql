-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: warrior_gym
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aparatos`
--

DROP TABLE IF EXISTS `aparatos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aparatos` (
  `id_aparato` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `estado` enum('Disponible','Mantenimiento','Fuera de servicio') NOT NULL,
  PRIMARY KEY (`id_aparato`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aparatos`
--

LOCK TABLES `aparatos` WRITE;
/*!40000 ALTER TABLE `aparatos` DISABLE KEYS */;
INSERT INTO `aparatos` VALUES (1,'Cinta de Correr','Cardio','Disponible'),(2,'Bicicleta Fija','Cardio','Disponible'),(3,'Banco Plano','Musculacion','Disponible'),(4,'Rack de Sentadillas','Fuerza','Disponible'),(5,'Polea Multifuncion','Musculacion','Disponible');
/*!40000 ALTER TABLE `aparatos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargos`
--

DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cargo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_cargo`),
  UNIQUE KEY `nombre_cargo` (`nombre_cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargos`
--

LOCK TABLES `cargos` WRITE;
/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` VALUES (1,'Administrador'),(2,'Entrenador'),(3,'Recepcionista');
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clases`
--

DROP TABLE IF EXISTS `clases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clases` (
  `id_clase` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `cupo` int(11) NOT NULL,
  PRIMARY KEY (`id_clase`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clases`
--

LOCK TABLES `clases` WRITE;
/*!40000 ALTER TABLE `clases` DISABLE KEYS */;
INSERT INTO `clases` VALUES (1,'Musculacion','Desarrollo muscular, fuerza e hipertrofia','07:00 y 19:00',50),(2,'Funcional','Movilidad, coordinacion y resistencia fisica','08:30',30),(3,'Cardio Fitness','Mejora cardiovascular y quema de calorias','10:00',40),(4,'Fuerza y Rendimiento','Potencia el desempeño fisico y deportivo','17:00',25),(5,'Preparacion Fisica','Entrenamiento para deportistas','20:00',20),(6,'Plan Personalizado','Rutinas adaptadas a objetivos especificos','20:30',15),(7,'Musculación','Desarrollo muscular y fuerza','07:00 - 22:00',50),(8,'Funcional','Movilidad y resistencia','08:30',20),(9,'Cardio Fitness','Entrenamiento cardiovascular','10:00',25),(10,'Fuerza y Rendimiento','Entrenamiento avanzado','17:00',20),(11,'Preparación Física','Preparación deportiva','20:00',15),(12,'Plan Personalizado','Rutinas adaptadas','20:30',10);
/*!40000 ALTER TABLE `clases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente_membresia`
--

DROP TABLE IF EXISTS `cliente_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_membresia` (
  `id_cliente_membresia` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_membresia` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('Activa','Vencida','Cancelada') NOT NULL,
  PRIMARY KEY (`id_cliente_membresia`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_membresia` (`id_membresia`),
  CONSTRAINT `cliente_membresia_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `cliente_membresia_ibfk_2` FOREIGN KEY (`id_membresia`) REFERENCES `membresias` (`id_membresia`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`fecha_fin` >= `fecha_inicio`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente_membresia`
--

LOCK TABLES `cliente_membresia` WRITE;
/*!40000 ALTER TABLE `cliente_membresia` DISABLE KEYS */;
INSERT INTO `cliente_membresia` VALUES (7,8,1,'2026-06-05','2026-06-14','Activa'),(8,9,2,'2026-06-01','2026-06-30','Activa'),(10,35,3,'2026-06-29','2026-07-29','Activa'),(11,36,3,'2026-06-29','2026-07-29','Activa'),(12,37,3,'2026-07-01','2026-07-31','Activa'),(13,39,2,'2026-07-02','2026-08-01','Activa'),(14,40,1,'2026-07-03','2026-08-02','Activa'),(15,42,2,'2026-07-15','2026-08-14','Activa');
/*!40000 ALTER TABLE `cliente_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
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
  `contrasena` varchar(255) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `unico_tipo_numero_documento` (`id_tipo_documento`,`numero_documento`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (8,1,'40111222','Juan','Perez','3774555555','juan@gmail.com',NULL,NULL,'2026-06-04','Inactivo','40111222','1234',NULL),(9,1,'46144926','cristian ','rojas','3779414232','gastonroja@s.gmail.com',NULL,NULL,'2026-06-05','Inactivo','46144926','1234',NULL),(12,1,'45149925','Elena ','ramirez','3774589266','elenaramirez@gmail.com.ar',NULL,NULL,'2026-06-19','Inactivo','45149925','1234',NULL),(13,1,'46144628','Camila','Vasconcel','3774623201','camivasconcel@gmail.com',NULL,NULL,'2026-06-19','Inactivo','46144628','1234',NULL),(14,1,'48144326','Tiago ian','Rojas','3774457266','tiagoinaroja@sgmail.com',NULL,NULL,'2026-06-19','Inactivo','48144326','1234',NULL),(15,1,'46144828','Ignacio','avellaneda','3774413258','ignaavell@gmail.com',NULL,NULL,'2026-06-19','Inactivo','46144828','1234',NULL),(16,1,'46149992','fernando ','gomez','3774458280','fernando@gmail.com',NULL,NULL,'2026-06-23','Inactivo','46149992','1234',NULL),(17,1,'','luciano','','','luciano@gmail.com.ar','',NULL,'2026-06-23','Inactivo','luciano','1234',NULL),(18,1,'46144778','ian','rojas','3774986655','ianrojas@gmail.com',NULL,NULL,'2026-06-23','Inactivo','46144778','1234',NULL),(19,1,'46779855','cecillo','rojas','3774526586','cecillorojas@gmail.com.ar','',NULL,'2026-06-23','Inactivo','cecillo','1234',NULL),(20,1,'46555989','fabian','gauto','3774555219','fabigauto@gmail.com.ar',NULL,NULL,'2026-06-23','Inactivo','46555989','1234',NULL),(21,1,'45555896','samuel','proz','3774821993','samuel@gmail.com.ar','',NULL,'2026-06-24','Inactivo','Samuel','1234',NULL),(22,1,'45799887','roman','riquelme','3774462581','romanriquelme@gmail.com',NULL,NULL,'2026-06-24','Inactivo','45799887','1234',NULL),(23,1,'47895666','mario','Vasconcel','3774825763','mariovasco@gmial.com.ar',NULL,NULL,'2026-06-24','Inactivo','47895666','1234',NULL),(24,1,'48666333','dalmiro','avalos','3779545852','dalmi@gmail.com.ar',NULL,NULL,'2026-06-24','Inactivo','48666333','1234',NULL),(25,1,'46528966','lucia','avellaneda','3778633200','luci@gmail.com.ar','',NULL,'2026-06-26','Inactivo','lucia','1234',NULL),(26,1,'48999666','luis','zanetti','3778954822','luis@gmail.com.ar','',NULL,'2026-06-26','Inactivo','luis','1234',NULL),(28,1,'46144852','fabricio','pedemonte','3774958233','fabri@gmail.com','Castelli 1661','2026-06-23','2026-06-26','Activo','','',26),(35,1,'45779822','Oscar','Martinez','3775665547','oscar@gmail.com.ar','','2004-09-07','2026-06-29','Activo','Oscar','$2y$10$ML8kquw4Lf6t9r52wPoRVOR3EAwP3yEr8nrfkH/.GX5MBAxqxRpCy',30),(36,1,'47899654','Antonio','Sanabria','3775664422','anto@gmail.com.ar','Castelli 1661','2026-06-29','2026-06-29','Activo','antonio','$2y$10$ckW7ZMHUuLFFTYSYY2wPh.306TYWzimGccEo0LKx22Xay985qpmBa',31),(37,1,'28284977','Ester','alvarez','3774504970','esterlucia@gmail.com.ar','Castelli 1662','1981-03-25','2026-07-01','Activo','Ester','$2y$10$CUT1tWOVMzqpg/via/Ki4eUoTwXP.MCS6qalML4qpqvEB2cAOzSD2',32),(38,1,'20144629','Sebastián','Rivas','3778633200','seba@gmail.com','Castelli 1661','2026-07-02','2026-07-02','Activo','Sebastián','$2y$10$8Q6K4ZiG8qwnyv2qlfuUUe7ljRki8K03XnpXBRSq3v8e.kJNkj2Lm',33),(39,1,'48774635241','Ezequiel','Rajoy','37764588999','eze@gmail.com','soler1232','2004-09-02','2026-07-02','Activo','Ezequil','$2y$10$CCbNDSgJxGE0E2.lnz/HxOc3cbMueVU0h8YOqG7Ai8FEORrRZJJQS',34),(40,1,'42666222','martina','gonzalez','3775919163','martina@gmail.com','vieites 1425','2016-08-19','2026-07-02','Activo','matina','$2y$10$xEhe.gvf4eydv/5S.MYGpObTvIhZntD.9d6U7b7/SqxOsj2lpIW4u',35),(41,1,'46144853','Raquel','Vera','3779524551','raquelita@gmail.com.ar','posadas 1212','2003-07-23','2026-07-14','Activo','Raquel','$2y$10$D3MhI0cndGXrspR9SwASie7LSNxCnsvxi9KYFdtVSga7/LgAtXPga',36),(42,1,'46144855','Aron','frenelli','3774999291','aron@gmail.com.ar','Verón de astrada  1643','2002-11-18','2026-07-14','Activo','Aron','$2y$10$7/pPxP.HRj9jR4U2DwWgKe46ZheXAV7jpAmw1apEtlKqdKYIEXIue',37);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacto_web`
--

DROP TABLE IF EXISTS `contacto_web`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacto_web` (
  `id_contacto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_contacto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacto_web`
--

LOCK TABLES `contacto_web` WRITE;
/*!40000 ALTER TABLE `contacto_web` DISABLE KEYS */;
INSERT INTO `contacto_web` VALUES (1,'cristian Rojas','gastonrojaspitu@gmail.com','Membresías','Teléfono: 3774465250\n\nMensaje:\ncuanto se abona.','2026-06-29 17:19:42'),(2,'felipe caseres','gastonrojaspitu@gmail.com','Membresías','Teléfono: 3774966655\n\nMensaje:\ndsddeffrf','2026-07-03 03:00:38'),(3,'Aron frenelli','aron@gmail.com.ar','Membresías','Teléfono: 3774232120\n\nMensaje:\nson seguras ?','2026-07-14 22:47:32');
/*!40000 ALTER TABLE `contacto_web` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (1,1,1,1,47000.00),(2,1,2,1,79000.00),(3,2,1,1,47000.00),(4,2,2,1,79000.00),(5,3,1,1,47000.00),(6,3,3,1,85000.00),(7,4,1,1,47000.00),(8,4,2,1,79000.00),(9,5,1,1,47000.00),(10,5,2,1,79000.00),(11,6,1,2,47000.00),(12,6,2,1,10.00),(13,7,1,2,47000.00),(14,7,3,1,85000.00),(15,8,1,1,47000.00),(16,8,2,2,10.00),(17,9,2,1,10.00),(18,10,1,1,47000.00),(19,11,6,1,43000.00),(20,11,14,2,38000.00),(21,12,6,2,43000.00),(22,12,14,1,38000.00),(23,13,11,2,26000.00),(24,13,13,1,22000.00),(25,14,1,1,47000.00),(26,14,2,1,40000.00),(27,15,5,1,28000.00),(28,15,3,1,85000.00),(29,16,1,1,47000.00),(30,16,2,1,40000.00),(31,17,1,1,47000.00),(32,17,3,1,85000.00);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresias`
--

DROP TABLE IF EXISTS `membresias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membresias` (
  `id_membresia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_membresia`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresias`
--

LOCK TABLES `membresias` WRITE;
/*!40000 ALTER TABLE `membresias` DISABLE KEYS */;
INSERT INTO `membresias` VALUES (1,'Básico',18000.00,'Acceso a musculación, vestuarios y horario estándar'),(2,'Premium',28000.00,'Incluye musculación, clases grupales y seguimiento profesional'),(3,'Elite',45990.00,'Acceso completo, rutinas personalizadas y reservas prioritarias');
/*!40000 ALTER TABLE `membresias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia','Mercado Pago') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `estado` enum('Pagado','Pendiente','Rechazado') NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`monto` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,9,28000.00,'2026-06-19','Efectivo','cuota del gym','Pagado'),(3,35,45.99,'2026-06-29','Efectivo','Membresía mensual','Pagado'),(4,36,45.99,'2026-06-29','Transferencia','Membresía mensual','Pagado'),(5,28,18000.00,'2026-06-29','Tarjeta','Membresía mensual','Pendiente'),(6,37,45.99,'2026-07-01','Mercado Pago','Membresía mensual','Pagado'),(7,39,28.00,'2026-07-02','Tarjeta','Membresía mensual','Pagado'),(8,40,18.00,'2026-07-03','Mercado Pago','Membresía mensual','Pagado'),(9,42,28.00,'2026-07-15','Tarjeta','Membresía mensual','Pagado');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal`
--

DROP TABLE IF EXISTS `personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal` (
  `id_personal` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipo_documento` int(11) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL,
  PRIMARY KEY (`id_personal`),
  UNIQUE KEY `numero_documento` (`numero_documento`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_tipo_documento` (`id_tipo_documento`),
  CONSTRAINT `personal_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal`
--

LOCK TABLES `personal` WRITE;
/*!40000 ALTER TABLE `personal` DISABLE KEYS */;
INSERT INTO `personal` VALUES (1,1,'30111222','Lucas','Fernandez','3774551001','lucas@warriorgym.com','lucas','1234','Activo'),(2,1,'30222333','Sofia','Benitez','3774551002','sofia@warriorgym.com','sofia','1234','Activo'),(3,1,'30333444','Martin','Gomez','3774551003','martin@warriorgym.com','martin','1234','Activo'),(4,1,'25000000','Admin','Warrior','3774550000','admin@warriorgym.com','admin','admin','Activo'),(7,1,'47899657','Emiliano','Monzón','3778566332','emi@gmail.com.ar','Emiliano','$2y$10$dsTHvHpcuyAbZVomrqWAeewM0ZUoWnubPK65ZPX490VSGteNZkwdK','Activo');
/*!40000 ALTER TABLE `personal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_cargo`
--

DROP TABLE IF EXISTS `personal_cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_cargo` (
  `id_personal` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  PRIMARY KEY (`id_personal`,`id_cargo`),
  KEY `id_cargo` (`id_cargo`),
  CONSTRAINT `personal_cargo_ibfk_1` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`),
  CONSTRAINT `personal_cargo_ibfk_2` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_cargo`
--

LOCK TABLES `personal_cargo` WRITE;
/*!40000 ALTER TABLE `personal_cargo` DISABLE KEYS */;
INSERT INTO `personal_cargo` VALUES (1,2),(2,2),(3,2),(4,1);
/*!40000 ALTER TABLE `personal_cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `nombre` (`nombre`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`precio` > 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`stock` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'Whey Protein Warrior 1kg','Proteína concentrada ideal para recuperación muscular y crecimiento',47000.00,7),(2,'Whey Protein Warrior 2kg','Mayor rendimiento y mejor relación costo-beneficio',40000.00,6),(3,'Isolate Protein Premium','Proteína aislada de rápida absorción y máxima pureza',85000.00,6),(4,'Mass Gainer 3kg','Ideal para aumentar masa muscular y calorías diarias',66000.00,12),(5,'Creatina Monohidratada 300g','Aumenta fuerza, potencia y recuperación muscular',28000.00,19),(6,'Creatina Monohidratada 500g','Mayor duración y excelente relación precio-rendimiento',43000.00,12),(7,'Creatina Micronizada Premium','Disolución superior y absorción optimizada',48000.00,8),(8,'Pre Workout Energy','Impulso energético para entrenamientos intensos',35000.00,15),(9,'Pre Workout Extreme','Máximo enfoque, potencia y resistencia',42000.00,10),(10,'BCAA 2:1:1','Aminoácidos esenciales para proteger la masa muscular',30000.00,18),(11,'Glutamina 300g','Favorece la recuperación muscular y el sistema inmune',26000.00,12),(12,'Multivitamínico Deportivo','Vitaminas y minerales para rendimiento',18000.00,25),(13,'Omega 3 Premium','Salud cardiovascular y recuperación',22000.00,19),(14,'ThermoFit Fat Burner','Suplemento para pérdida de grasa corporal',38000.00,9),(15,'Recovery Complex','Fórmula avanzada de recuperación post-entreno',32000.00,10);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_aparato` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado_reserva` enum('Pendiente','Activa','Cancelada','Finalizada') NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`id_reserva`),
  UNIQUE KEY `id_cliente` (`id_cliente`,`id_aparato`,`fecha`,`hora_inicio`),
  KEY `id_aparato` (`id_aparato`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_aparato`) REFERENCES `aparatos` (`id_aparato`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`hora_inicio` < `hora_fin`),
  CONSTRAINT `CONSTRAINT_2` CHECK (`hora_inicio` < `hora_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (1,9,3,'2026-06-09','07:19:00','10:20:00','Cancelada'),(2,8,1,'2026-06-17','11:28:00','13:30:00','Activa'),(3,8,1,'2026-08-28','09:04:00','11:05:00','Activa'),(4,12,2,'2026-07-12','08:00:00','11:00:00','Cancelada'),(5,9,4,'2026-06-18','18:04:00','19:03:00','Activa'),(6,19,1,'2026-06-04','14:00:00','16:00:00','Cancelada'),(7,19,5,'2026-06-26','06:00:00','09:00:00','Cancelada'),(8,19,3,'2026-06-25','09:00:00','11:00:00','Cancelada'),(16,19,2,'2026-06-14','14:00:00','15:00:00','Activa'),(18,18,5,'2026-06-13','08:00:00','09:00:00','Cancelada'),(21,9,5,'2026-06-09','18:00:00','19:00:00','Cancelada'),(22,24,1,'2026-06-28','14:00:00','15:00:00','Cancelada'),(23,9,4,'2026-06-28','14:00:00','15:00:00','Cancelada'),(24,13,4,'2026-06-11','10:00:00','23:00:00','Cancelada'),(25,35,2,'2026-06-30','21:00:00','23:00:00','Cancelada'),(26,36,5,'2026-07-09','08:00:00','10:00:00','Cancelada'),(27,36,3,'2026-07-10','15:00:00','17:00:00','Cancelada'),(28,36,1,'2026-06-29','19:00:00','21:00:00','Activa'),(29,28,4,'2026-06-29','16:00:00','18:00:00','Activa'),(30,37,1,'2026-07-06','06:00:00','07:00:00','Activa'),(31,39,1,'2026-07-02','10:00:00','11:00:00','Activa'),(32,40,4,'2026-07-03','14:00:00','15:00:00','Activa'),(33,42,1,'2026-07-29','10:00:00','11:00:00','Activa');
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutina_asignada`
--

DROP TABLE IF EXISTS `rutina_asignada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rutina_asignada` (
  `id_rutina_asignada` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `nombre_rutina` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_asignacion` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Activa',
  PRIMARY KEY (`id_rutina_asignada`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutina_asignada`
--

LOCK TABLES `rutina_asignada` WRITE;
/*!40000 ALTER TABLE `rutina_asignada` DISABLE KEYS */;
INSERT INTO `rutina_asignada` VALUES (1,18,NULL,'Rutina personalizada','Objetivo: Fuerza | Nivel: Avanzado | Días: 6 días | Duración: 90 minutos | Preferencias: pesas | Lesiones: rodilla | Extra: ganar','2026-06-24','Activa'),(8,13,NULL,'Rutina personalizada','Objetivo: Definición | Nivel: Intermedio | Días: 5 días | Duración: 60 minutos | Preferencias: cardio | Lesiones: nada | Extra: mejorar ','2026-06-25','Activa'),(11,9,NULL,'Rutina personalizada','Objetivo: Fuerza | Nivel: Avanzado | Días: 6 días | Duración: 60 minutos | Preferencias: pesas | Lesiones: rodilla | Extra: quiero llegar en 3 meses ','2026-06-25','Activa'),(12,9,NULL,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-06-15','Activa'),(13,9,NULL,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-06-01','Activa'),(14,25,NULL,'Rutina personalizada','Objetivo: Ganar masa muscular | Nivel: Intermedio | Días: 6 días | Duración: 90 minutos | Preferencias: mixto | Lesiones: nada | Extra: llegar a fin de año.','2026-06-26','Activa'),(15,25,NULL,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-06-27','Activa'),(16,35,NULL,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-06-29','Activa'),(17,36,NULL,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-06-29','Activa'),(18,28,NULL,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-06-29','Activa'),(19,37,NULL,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-07-01','Activa'),(20,37,NULL,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-07-01','Activa'),(21,39,NULL,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-07-02','Inactiva'),(22,39,NULL,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-07-02','Inactiva'),(23,39,NULL,'fuerza libre','peso libre','2026-07-02','Activa'),(24,38,NULL,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-07-02','Activa'),(25,40,NULL,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-07-02','Activa'),(26,41,7,'PHP Warrior Fuerza Intermedia','Objetivo: mejorar fuerza, técnica y resistencia muscular en nivel intermedio.\r\n\r\nDía 1: Pecho y tríceps\r\n- Press banca: 4 series de 8 a 10 repeticiones\r\n- Press inclinado con mancuernas: 3 series de 10 repeticiones\r\n- Aperturas en máquina: 3 series de 12 repeticiones\r\n- Fondos asistidos: 3 series de 10 repeticiones\r\n- Extensión de tríceps en polea: 3 series de 12 repeticiones\r\n\r\nDía 2: Espalda y bíceps\r\n- Jalón al pecho: 4 series de 10 repeticiones\r\n- Remo sentado: 4 series de 10 repeticiones\r\n- Remo con mancuerna: 3 series de 10 repeticiones por lado\r\n- Curl de bíceps con barra: 3 series de 12 repeticiones\r\n- Curl martillo: 3 series de 12 repeticiones\r\n\r\nDía 3: Piernas\r\n- Sentadilla: 4 series de 10 repeticiones\r\n- Prensa: 4 series de 12 repeticiones\r\n- Peso muerto rumano: 3 series de 10 repeticiones\r\n- Camilla femoral: 3 series de 12 repeticiones\r\n- Gemelos: 4 series de 15 repeticiones\r\n\r\nDía 4: Hombros y abdomen\r\n- Press militar: 4 series de 10 repeticiones\r\n- Elevaciones laterales: 3 series de 12 repeticiones\r\n- Pájaros para hombro posterior: 3 series de 12 repeticiones\r\n- Plancha abdominal: 3 series de 40 segundos\r\n- Crunch abdominal: 3 series de 20 repeticiones\r\n\r\nFrecuencia recomendada: 4 días por semana.\r\nDescanso entre series: 60 a 90 segundos.\r\nNivel: Intermedio.\r\nResponsable: entrenador del gimnasio.','2026-07-14','Activa'),(27,42,7,'Warrior Avanzada Fuerza e Hipertrofia','Objetivo: aumentar fuerza, masa muscular y rendimiento general en clientes con experiencia.\r\n\r\nDía 1: Pecho y tríceps\r\n- Press banca pesado: 5 series de 5 repeticiones\r\n- Press inclinado con mancuernas: 4 series de 8 repeticiones\r\n- Aperturas en polea: 3 series de 12 repeticiones\r\n- Fondos en paralelas: 4 series de 8 a 10 repeticiones\r\n- Press cerrado: 3 series de 8 repeticiones\r\n- Extensión de tríceps en polea: 3 series de 12 repeticiones\r\n\r\nDía 2: Espalda y bíceps\r\n- Dominadas o jalón al pecho: 5 series de 6 a 8 repeticiones\r\n- Remo con barra: 4 series de 8 repeticiones\r\n- Remo en máquina: 4 series de 10 repeticiones\r\n- Peso muerto: 4 series de 5 repeticiones\r\n- Curl de bíceps con barra: 4 series de 10 repeticiones\r\n- Curl martillo: 3 series de 12 repeticiones\r\n\r\nDía 3: Piernas\r\n- Sentadilla libre: 5 series de 5 repeticiones\r\n- Prensa: 4 series de 10 repeticiones\r\n- Peso muerto rumano: 4 series de 8 repeticiones\r\n- Estocadas con mancuernas: 3 series de 10 repeticiones por pierna\r\n- Camilla femoral: 3 series de 12 repeticiones\r\n- Gemelos parado: 4 series de 15 repeticiones\r\n\r\nDía 4: Hombros y abdomen\r\n- Press militar: 5 series de 6 repeticiones\r\n- Elevaciones laterales: 4 series de 12 repeticiones\r\n- Pájaros para deltoide posterior: 4 series de 12 repeticiones\r\n- Encogimientos de trapecio: 4 series de 10 repeticiones\r\n- Plancha abdominal: 4 series de 45 segundos\r\n- Elevación de piernas: 4 series de 15 repeticiones\r\n\r\nDía 5: Full Body técnico\r\n- Sentadilla frontal: 4 series de 6 repeticiones\r\n- Press banca liviano técnico: 4 series de 8 repeticiones\r\n- Remo con barra: 4 series de 8 repeticiones\r\n- Dominadas: 3 series al fallo controlado\r\n- Bicicleta o caminata inclinada: 20 minutos\r\n\r\nFrecuencia recomendada: 5 días por semana.\r\nDescanso entre series pesadas: 2 a 3 minutos.\r\nDescanso entre accesorios: 60 a 90 segundos.\r\nNivel: Avanzado.\r\nRecomendación: realizar entrada en calor antes de cada sesión y cuidar la técnica en ejercicios pesados.','2026-07-14','Activa');
/*!40000 ALTER TABLE `rutina_asignada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutinas`
--

DROP TABLE IF EXISTS `rutinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rutinas` (
  `id_rutina` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL,
  `nombre_rutina` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` date NOT NULL,
  PRIMARY KEY (`id_rutina`),
  KEY `id_personal` (`id_personal`),
  CONSTRAINT `rutinas_ibfk_1` FOREIGN KEY (`id_personal`) REFERENCES `personal` (`id_personal`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutinas`
--

LOCK TABLES `rutinas` WRITE;
/*!40000 ALTER TABLE `rutinas` DISABLE KEYS */;
INSERT INTO `rutinas` VALUES (2,1,'fuerza libre','peso libre','2026-06-19'),(3,1,'Hipertrofia Inicial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales','2026-06-26'),(4,1,'Hipertrofia secuencial','Lunes: Pecho y tríceps\r\nMartes: Espalda y bíceps\r\nMiércoles: Piernas\r\nJueves: Hombros\r\nViernes: Cardio y abdominales\r\nsábado: pierna full','2026-06-26'),(30,1,'Warrior Full Body Principiante','Objetivo: adaptación inicial, fuerza básica y mejora de resistencia.\r\n\r\nDía 1: Tren superior\r\n- Press banca: 3 series de 10 repeticiones\r\n- Jalón al pecho: 3 series de 10 repeticiones\r\n- Press de hombros: 3 series de 10 repeticiones\r\n- Curl de bíceps: 3 series de 12 repeticiones\r\n- Extensión de tríceps: 3 series de 12 repeticiones\r\n\r\nDía 2: Tren inferior\r\n- Sentadillas: 3 series de 12 repeticiones\r\n- Prensa: 3 series de 12 repeticiones\r\n- Camilla femoral: 3 series de 12 repeticiones\r\n- Gemelos: 4 series de 15 repeticiones\r\n- Abdominales: 3 series de 15 repeticiones\r\n\r\nDía 3: Full Body + Cardio\r\n- Remo en máquina: 3 series de 10 repeticiones\r\n- Estocadas: 3 series de 10 repeticiones por pierna\r\n- Peso muerto rumano: 3 series de 10 repeticiones\r\n- Plancha abdominal: 3 series de 30 segundos\r\n- Caminata o bicicleta: 20 minutos\r\n\r\nFrecuencia recomendada: 3 veces por semana.\r\nDescanso entre series: 60 a 90 segundos.\r\nNivel: Principiante.','2026-07-14'),(31,1,'Warrior Hipertrofia Intermedia','Objetivo: aumento de masa muscular, mejora de fuerza y trabajo por grupos musculares.\r\n\r\nDía 1: Pecho y tríceps\r\n- Press banca: 4 series de 10 repeticiones\r\n- Press inclinado con mancuernas: 3 series de 10 repeticiones\r\n- Aperturas con mancuernas: 3 series de 12 repeticiones\r\n- Fondos asistidos o en banco: 3 series de 10 repeticiones\r\n- Extensión de tríceps en polea: 3 series de 12 repeticiones\r\n\r\nDía 2: Espalda y bíceps\r\n- Jalón al pecho: 4 series de 10 repeticiones\r\n- Remo en máquina: 4 series de 10 repeticiones\r\n- Remo con mancuerna: 3 series de 10 repeticiones por lado\r\n- Curl de bíceps con barra: 3 series de 12 repeticiones\r\n- Curl martillo: 3 series de 12 repeticiones\r\n\r\nDía 3: Piernas y glúteos\r\n- Sentadillas: 4 series de 10 repeticiones\r\n- Prensa: 4 series de 12 repeticiones\r\n- Peso muerto rumano: 3 series de 10 repeticiones\r\n- Camilla femoral: 3 series de 12 repeticiones\r\n- Gemelos: 4 series de 15 repeticiones\r\n\r\nDía 4: Hombros y abdomen\r\n- Press militar: 4 series de 10 repeticiones\r\n- Elevaciones laterales: 3 series de 12 repeticiones\r\n- Pájaros para posterior: 3 series de 12 repeticiones\r\n- Encogimientos de trapecio: 3 series de 12 repeticiones\r\n- Plancha abdominal: 3 series de 40 segundos\r\n- Crunch abdominal: 3 series de 20 repeticiones\r\n\r\nFrecuencia recomendada: 4 días por semana.\r\nDescanso entre series: 60 a 90 segundos.\r\nNivel: Intermedio.','2026-07-14'),(32,7,'PHP Warrior Fuerza Intermedia','Objetivo: mejorar fuerza, técnica y resistencia muscular en nivel intermedio.\r\n\r\nDía 1: Pecho y tríceps\r\n- Press banca: 4 series de 8 a 10 repeticiones\r\n- Press inclinado con mancuernas: 3 series de 10 repeticiones\r\n- Aperturas en máquina: 3 series de 12 repeticiones\r\n- Fondos asistidos: 3 series de 10 repeticiones\r\n- Extensión de tríceps en polea: 3 series de 12 repeticiones\r\n\r\nDía 2: Espalda y bíceps\r\n- Jalón al pecho: 4 series de 10 repeticiones\r\n- Remo sentado: 4 series de 10 repeticiones\r\n- Remo con mancuerna: 3 series de 10 repeticiones por lado\r\n- Curl de bíceps con barra: 3 series de 12 repeticiones\r\n- Curl martillo: 3 series de 12 repeticiones\r\n\r\nDía 3: Piernas\r\n- Sentadilla: 4 series de 10 repeticiones\r\n- Prensa: 4 series de 12 repeticiones\r\n- Peso muerto rumano: 3 series de 10 repeticiones\r\n- Camilla femoral: 3 series de 12 repeticiones\r\n- Gemelos: 4 series de 15 repeticiones\r\n\r\nDía 4: Hombros y abdomen\r\n- Press militar: 4 series de 10 repeticiones\r\n- Elevaciones laterales: 3 series de 12 repeticiones\r\n- Pájaros para hombro posterior: 3 series de 12 repeticiones\r\n- Plancha abdominal: 3 series de 40 segundos\r\n- Crunch abdominal: 3 series de 20 repeticiones\r\n\r\nFrecuencia recomendada: 4 días por semana.\r\nDescanso entre series: 60 a 90 segundos.\r\nNivel: Intermedio.\r\nResponsable: entrenador del gimnasio.','2026-07-14'),(33,7,'Warrior Avanzada Fuerza e Hipertrofia','Objetivo: aumentar fuerza, masa muscular y rendimiento general en clientes con experiencia.\r\n\r\nDía 1: Pecho y tríceps\r\n- Press banca pesado: 5 series de 5 repeticiones\r\n- Press inclinado con mancuernas: 4 series de 8 repeticiones\r\n- Aperturas en polea: 3 series de 12 repeticiones\r\n- Fondos en paralelas: 4 series de 8 a 10 repeticiones\r\n- Press cerrado: 3 series de 8 repeticiones\r\n- Extensión de tríceps en polea: 3 series de 12 repeticiones\r\n\r\nDía 2: Espalda y bíceps\r\n- Dominadas o jalón al pecho: 5 series de 6 a 8 repeticiones\r\n- Remo con barra: 4 series de 8 repeticiones\r\n- Remo en máquina: 4 series de 10 repeticiones\r\n- Peso muerto: 4 series de 5 repeticiones\r\n- Curl de bíceps con barra: 4 series de 10 repeticiones\r\n- Curl martillo: 3 series de 12 repeticiones\r\n\r\nDía 3: Piernas\r\n- Sentadilla libre: 5 series de 5 repeticiones\r\n- Prensa: 4 series de 10 repeticiones\r\n- Peso muerto rumano: 4 series de 8 repeticiones\r\n- Estocadas con mancuernas: 3 series de 10 repeticiones por pierna\r\n- Camilla femoral: 3 series de 12 repeticiones\r\n- Gemelos parado: 4 series de 15 repeticiones\r\n\r\nDía 4: Hombros y abdomen\r\n- Press militar: 5 series de 6 repeticiones\r\n- Elevaciones laterales: 4 series de 12 repeticiones\r\n- Pájaros para deltoide posterior: 4 series de 12 repeticiones\r\n- Encogimientos de trapecio: 4 series de 10 repeticiones\r\n- Plancha abdominal: 4 series de 45 segundos\r\n- Elevación de piernas: 4 series de 15 repeticiones\r\n\r\nDía 5: Full Body técnico\r\n- Sentadilla frontal: 4 series de 6 repeticiones\r\n- Press banca liviano técnico: 4 series de 8 repeticiones\r\n- Remo con barra: 4 series de 8 repeticiones\r\n- Dominadas: 3 series al fallo controlado\r\n- Bicicleta o caminata inclinada: 20 minutos\r\n\r\nFrecuencia recomendada: 5 días por semana.\r\nDescanso entre series pesadas: 2 a 3 minutos.\r\nDescanso entre accesorios: 60 a 90 segundos.\r\nNivel: Avanzado.\r\nRecomendación: realizar entrada en calor antes de cada sesión y cuidar la técnica en ejercicios pesados.','2026-07-14');
/*!40000 ALTER TABLE `rutinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutinas_cliente`
--

DROP TABLE IF EXISTS `rutinas_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rutinas_cliente` (
  `id_rutina_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_rutina` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  PRIMARY KEY (`id_rutina_cliente`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_rutina` (`id_rutina`),
  CONSTRAINT `rutinas_cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `rutinas_cliente_ibfk_2` FOREIGN KEY (`id_rutina`) REFERENCES `rutinas` (`id_rutina`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`fecha_fin` >= `fecha_inicio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutinas_cliente`
--

LOCK TABLES `rutinas_cliente` WRITE;
/*!40000 ALTER TABLE `rutinas_cliente` DISABLE KEYS */;
INSERT INTO `rutinas_cliente` VALUES (1,8,2,'2026-06-20','2026-10-23'),(2,9,2,'2026-06-19','2026-06-25');
/*!40000 ALTER TABLE `rutinas_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes_membresia`
--

DROP TABLE IF EXISTS `solicitudes_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_membresia` (
  `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `plan_solicitado` varchar(50) DEFAULT NULL,
  `fecha_solicitud` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  PRIMARY KEY (`id_solicitud`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_membresia`
--

LOCK TABLES `solicitudes_membresia` WRITE;
/*!40000 ALTER TABLE `solicitudes_membresia` DISABLE KEYS */;
INSERT INTO `solicitudes_membresia` VALUES (1,'gastón','fernandez','gastonfernandez@gmail.com.ar','3774966655','Premium','2026-06-11','Aprobada'),(2,'magnecio ','Gaston Rojas','gastonrojaspitu@gmail.com','3774966655','Elite','2026-06-12','Rechazada'),(3,'Ramiro ','Pintos ','ramiropintos@gmail.com','3774526070','Premium','2026-06-18','Rechazada'),(4,'Elena ','ramirez','elenaramirez@gmial.com.ar','3774885596','Elite','2026-06-19','Rechazada'),(5,'norberto','rivas','nor@gmail.com','3772458862','Elite','2026-06-25','Rechazada'),(6,'','','','','','2026-06-25','Rechazada'),(7,'cristian ','','','','','2026-06-25','Rechazada'),(8,'cristian ','','','','','2026-06-25','Rechazada'),(9,'cristian ','','','','Basico','2026-06-25','Rechazada'),(10,'admin','','','','Basico','2026-06-25','Rechazada'),(11,'admin','','admin','','Basico','2026-06-25','Rechazada'),(12,'Camila','No definido','camivasconcel@gmail.com','No definido','Elite','2026-06-25','Aprobada'),(13,'Camila','Vasconcel','camivasconcel@gmail.com','3774966655','Elite','2026-06-26','Rechazada'),(14,'cecillo','rojas','cecillorojas@gmail.com.ar','3774555555','Basico','2026-06-26','Rechazada'),(15,'cecillo','rojas','cecillorojas@gmail.com.ar','3774555555','Elite','2026-06-26','Aprobada'),(16,'Oscar','Martinez','oscar@gmail.com.ar','3775665547','Elite','2026-06-29','Aprobada'),(17,'Antonio','Sanabria','anto@gmail.com.ar','3775664422','Elite','2026-06-29','Aprobada'),(18,'fabricio','pedemonte','fabri@gmail.com','3774958233','Basico','2026-06-29','Aprobada'),(19,'Ester','alvarez','esterlucia@gmail.com.ar','3774504970','Elite','2026-07-01','Aprobada'),(20,'Sebastián','Rivas','seba@gmail.com','3778633200','Premium','2026-07-02','Aprobada'),(21,'Ezequiel','Rajoy','eze@gmail.com','37764588999','Premium','2026-07-02','Aprobada'),(22,'martina','gonzalez','martina@gmail.com','3775919163','Basico','2026-07-02','Aprobada'),(23,'Aron','frenelli','aron@gmail.com.ar','3774999291','Premium','2026-07-14','Aprobada');
/*!40000 ALTER TABLE `solicitudes_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes_rutina`
--

DROP TABLE IF EXISTS `solicitudes_rutina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_rutina` (
  `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `fecha_solicitud` date DEFAULT NULL,
  PRIMARY KEY (`id_solicitud`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_rutina`
--

LOCK TABLES `solicitudes_rutina` WRITE;
/*!40000 ALTER TABLE `solicitudes_rutina` DISABLE KEYS */;
INSERT INTO `solicitudes_rutina` VALUES (1,15,'quiero ganar masa muscular\r\n','Aprobada','2026-06-22'),(2,10,'quiero fortalecer el tricep','Pendiente','2026-06-22'),(3,12,'quiero ganar musculo en 2 meses ','Rechazada','2026-06-22'),(4,12,'\r\n🎯 Objetivo: Definición\r\n💪 Nivel: Intermedio\r\n📅 Días: 5 días\r\n⏱ Duración: 90 minutos\r\n🏋 Preferencias: pesas\r\n⚠ Lesiones: nada\r\n📝 Extra: quiero llegar en 3 meses \r\n','Pendiente','2026-06-24'),(5,10,'\r\n🎯 Objetivo: Fuerza\r\n💪 Nivel: Avanzado\r\n📅 Días: 6 días\r\n⏱ Duración: 90 minutos\r\n🏋 Preferencias: pesas\r\n⚠ Lesiones: rodilla\r\n📝 Extra: cumplir mis objetivos\r\n','Pendiente','2026-06-24'),(6,18,'Objetivo: Fuerza | Nivel: Avanzado | Días: 6 días | Duración: 90 minutos | Preferencias: pesas | Lesiones: rodilla | Extra: ganar','Aprobada','2026-06-24'),(7,18,'Objetivo: Bajar de peso | Nivel: Intermedio | Días: 5 días | Duración: 60 minutos | Preferencias: cardio | Lesiones: nada | Extra: bajar lo antes posible','Pendiente','2026-06-24'),(8,18,'Objetivo: Resistencia | Nivel: Intermedio | Días: 4 días | Duración: 60 minutos | Preferencias: mixto | Lesiones: nada | Extra: mas oxigeno ','Pendiente','2026-06-24'),(9,16,'Objetivo: Fuerza | Nivel: Avanzado | Días: 6 días | Duración: 90 minutos | Preferencias: pesas | Lesiones: nada | Extra: ser musculoso \r\n','Pendiente','2026-06-24'),(10,18,'Objetivo: Ganar masa muscular | Nivel: Principiante | Días: 2 días | Duración: 90 minutos | Preferencias: cardio | Lesiones: nada | Extra: wdedede','Pendiente','2026-06-24'),(11,10,'Objetivo: Ganar masa muscular | Nivel: Principiante | Días: 2 días | Duración: 45 minutos | Preferencias: pesas | Lesiones: nada | Extra: musculo','Pendiente','2026-06-24'),(12,10,'Objetivo: Resistencia | Nivel: Intermedio | Días: 6 días | Duración: 60 minutos | Preferencias: mixto | Lesiones: nada | Extra: tener mas oxigeno ','Pendiente','2026-06-25'),(13,13,'Objetivo: Definición | Nivel: Intermedio | Días: 5 días | Duración: 60 minutos | Preferencias: cardio | Lesiones: nada | Extra: mejorar ','Aprobada','2026-06-25'),(14,9,'Objetivo: Fuerza | Nivel: Avanzado | Días: 6 días | Duración: 60 minutos | Preferencias: pesas | Lesiones: rodilla | Extra: quiero llegar en 3 meses ','Aprobada','2026-06-25'),(15,25,'Objetivo: Ganar masa muscular | Nivel: Intermedio | Días: 6 días | Duración: 90 minutos | Preferencias: mixto | Lesiones: nada | Extra: llegar a fin de año.','Aprobada','2026-06-26'),(16,35,'Objetivo: Ganar masa muscular\nNivel: Avanzado\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: mixto\nLesiones: nada\nComentarios extra: llegar en 2 meses','Aprobada','2026-06-29'),(17,36,'Objetivo: Bajar de peso\nNivel: Intermedio\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: cardio\nLesiones: ninguna\nComentarios extra: objetivo; bajar de peso en 6 meses','Aprobada','2026-06-29'),(18,28,'Objetivo: Fuerza\nNivel: Avanzado\nDías de entrenamiento: 6 días\nDuración por sesión: 90 minutos\nPreferencias: pesas\nLesiones: ninguna\nComentarios extra: seguimiento exclusivo','Aprobada','2026-06-29'),(19,37,'Objetivo: Bajar de peso\nNivel: Avanzado\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: cardio\nLesiones: fémur\nComentarios extra: bajar de peso lo masa antes posible.','Aprobada','2026-07-01'),(20,37,'Objetivo: Definición\nNivel: Avanzado\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: mixto\nLesiones: ninguna\nComentarios extra: ganar resistencia','Rechazada','2026-07-02'),(21,37,'Objetivo: Ganar masa muscular\nNivel: Principiante\nDías de entrenamiento: 5 días\nDuración por sesión: 90 minutos\nPreferencias: cardio\nLesiones: ninguna\nComentarios extra: nose','Rechazada','2026-07-02'),(22,36,'Objetivo: Ganar masa muscular\nNivel: Principiante\nDías de entrenamiento: 6 días\nDuración por sesión: 90 minutos\nPreferencias: pesas\nLesiones: rodilla\nComentarios extra: ganar musculo','Pendiente','2026-07-02'),(23,36,'Objetivo: Fuerza\nNivel: Avanzado\nDías de entrenamiento: 6 días\nDuración por sesión: 60 minutos\nPreferencias: pesas\nLesiones: fémur\nComentarios extra: tener cuidado con mi rodilla','Pendiente','2026-07-02'),(24,38,'Objetivo: Ganar masa muscular\nNivel: Avanzado\nDías de entrenamiento: 6 días\nDuración por sesión: 90 minutos\nPreferencias: pesas\nLesiones: rodilla\nComentarios extra: ganar lo antes posible','Aprobada','2026-07-02'),(25,39,'Objetivo: Ganar masa muscular\nNivel: Principiante\nDías de entrenamiento: 3 días\nDuración por sesión: 60 minutos\nPreferencias: pesas\nLesiones: ninguna\nComentarios extra: ganar masa en menos de 2 meses','Aprobada','2026-07-02'),(26,39,'Objetivo: Bajar de peso\nNivel: Intermedio\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: cardio\nLesiones: ninguna\nComentarios extra: nada','Aprobada','2026-07-02'),(27,39,'Objetivo: Definición\nNivel: Intermedio\nDías de entrenamiento: 5 días\nDuración por sesión: 45 minutos\nPreferencias: mixto\nLesiones: nada\nComentarios extra: definición pro','Pendiente','2026-07-02'),(28,40,'Objetivo: Resistencia\nNivel: Avanzado\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: cardio\nLesiones: ninguna\nComentarios extra: ganar resistencia lo antes posible','Aprobada','2026-07-02'),(29,42,'Objetivo: Definición\nNivel: Intermedio\nDías de entrenamiento: 5 días\nDuración por sesión: 60 minutos\nPreferencias: mixto\nLesiones: ninguna\nComentarios extra: quiero volverme mas definido y fuerte','Aprobada','2026-07-14');
/*!40000 ALTER TABLE `solicitudes_rutina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_documento`
--

DROP TABLE IF EXISTS `tipo_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tipo_documento`),
  UNIQUE KEY `descripcion` (`descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_documento`
--

LOCK TABLES `tipo_documento` WRITE;
/*!40000 ALTER TABLE `tipo_documento` DISABLE KEYS */;
INSERT INTO `tipo_documento` VALUES (3,'CUIL'),(1,'DNI'),(7,'LC'),(2,'Pasaporte');
/*!40000 ALTER TABLE `tipo_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `rol` enum('admin','cliente') NOT NULL,
  `estado` varchar(20) DEFAULT 'Activo',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'lucas','lucas','1234','admin','Activo'),(2,'sofia','sofia','1234','admin','Activo'),(3,'martin','martin','1234','admin','Activo'),(4,'admin','admin','admin','admin','Activo'),(9,'Elena ','elenaramirez@gmail.com.ar','1234','cliente','Activo'),(10,'cristian ','gastonroja@s.gmail.com','1234','cliente','Activo'),(11,'Juan','juan@gmail.com','1234','cliente','Activo'),(12,'Camila','camivasconcel@gmail.com','1234','cliente','Activo'),(13,'Tiago','tiagoinaroja@sgmail.com','1234','cliente','Activo'),(14,'Ignacio','ignaavell@gmail.com','1234','cliente','Activo'),(15,'daniel','dani@gmail.com','1234','cliente','Activo'),(16,'Ramon','ramonnoriega@gmail.com','1234','cliente','Activo'),(17,'luciano','luciano@gmail.com.ar','1234','cliente','Activo'),(18,'cecillo','cecillorojas@gmail.com.ar','1234','cliente','Activo'),(19,'Samuel','samuel@gmail.com.ar','1234','cliente','Activo'),(20,'47895666','mariovasco@gmial.com.ar','1234','cliente','Activo'),(21,'dalmiro','dalmi@gmail.com.ar','1234','cliente','Activo'),(22,'lucia','luci@gmail.com.ar','1234','cliente','Activo'),(23,'luis','luis@gmail.com.ar','1234','cliente','Activo'),(24,'chacho ','chacho@gmail.com.ar','1234','cliente','Activo'),(25,'fabio','fabios@gmail.com.ar','1234','cliente','Activo'),(26,'fabricio','fabri@gmail.com','1234','cliente','Activo'),(27,'pepe','pepe@gmail.com.ar','1234','cliente','Activo'),(29,'pepe','pepe@gmail.com','1234','cliente','Activo'),(30,'Oscar','oscar@gmail.com.ar','$2y$10$ML8kquw4Lf6t9r52wPoRVOR3EAwP3yEr8nrfkH/.GX5MBAxqxRpCy','cliente','Activo'),(31,'antonio','anto@gmail.com.ar','$2y$10$ckW7ZMHUuLFFTYSYY2wPh.306TYWzimGccEo0LKx22Xay985qpmBa','cliente','Activo'),(32,'Ester','esterlucia@gmail.com.ar','$2y$10$CUT1tWOVMzqpg/via/Ki4eUoTwXP.MCS6qalML4qpqvEB2cAOzSD2','cliente','Activo'),(33,'Sebastián','seba@gmail.com','$2y$10$8Q6K4ZiG8qwnyv2qlfuUUe7ljRki8K03XnpXBRSq3v8e.kJNkj2Lm','cliente','Activo'),(34,'Ezequiel','eze@gmail.com','$2y$10$CCbNDSgJxGE0E2.lnz/HxOc3cbMueVU0h8YOqG7Ai8FEORrRZJJQS','cliente','Activo'),(35,'matina','martina@gmail.com','$2y$10$xEhe.gvf4eydv/5S.MYGpObTvIhZntD.9d6U7b7/SqxOsj2lpIW4u','cliente','Activo'),(36,'Raquel','raquelita@gmail.com.ar','$2y$10$D3MhI0cndGXrspR9SwASie7LSNxCnsvxi9KYFdtVSga7/LgAtXPga','cliente','Activo'),(37,'Aron','aron@gmail.com.ar','$2y$10$7/pPxP.HRj9jR4U2DwWgKe46ZheXAV7jpAmw1apEtlKqdKYIEXIue','cliente','Activo');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_venta`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,16,'2026-06-25 22:30:25',126000.00),(2,16,'2026-06-25 22:35:37',126000.00),(3,16,'2026-06-25 22:36:13',132000.00),(4,21,'2026-06-25 22:38:49',126000.00),(5,4,'2026-06-26 10:54:24',126000.00),(6,30,'2026-06-29 10:56:13',94010.00),(7,31,'2026-06-29 14:11:00',179000.00),(8,26,'2026-06-29 16:18:11',47020.00),(9,26,'2026-06-29 16:24:16',10.00),(10,26,'2026-06-29 16:24:34',47000.00),(11,32,'2026-07-01 12:22:48',119000.00),(12,34,'2026-07-02 11:28:35',124000.00),(13,35,'2026-07-02 19:21:05',74000.00),(14,36,'2026-07-14 12:46:09',87000.00),(15,36,'2026-07-14 12:56:35',113000.00),(16,26,'2026-07-14 13:10:50',87000.00),(17,37,'2026-07-14 23:00:52',132000.00);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-14 23:24:02
