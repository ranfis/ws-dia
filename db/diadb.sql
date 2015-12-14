-- phpMyAdmin SQL Dump
-- version 4.0.10.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 14, 2015 at 08:51 AM
-- Server version: 5.6.26
-- PHP Version: 5.5.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `diadb`
--

CREATE DATABASE diadb;

USE diadb;

-- --------------------------------------------------------

--
-- Table structure for table `congreso`
--

CREATE TABLE IF NOT EXISTS `congreso` (
  `id_congreso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `fecha_congreso` date NOT NULL COMMENT 'fecha de cuando se hará o se realizó el congreso',
  `ponencia` varchar(200) NOT NULL,
  `lugar` varchar(140) NOT NULL,
  `patrocinio_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL COMMENT 'fecha que crea un congreso en el sistema\n',
  PRIMARY KEY (`id_congreso`),
  KEY `fk_congreso_patrocinio1_idx` (`patrocinio_id`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `congreso_autor`
--

CREATE TABLE IF NOT EXISTS `congreso_autor` (
  `congreso_id_congreso` int(11) NOT NULL COMMENT 'Tabla relaciona para los autores del congreso',
  `participante_ID` int(11) NOT NULL,
  PRIMARY KEY (`congreso_id_congreso`,`participante_ID`),
  KEY `fk_congreso_has_participante_participante1_idx` (`participante_ID`),
  KEY `fk_congreso_has_participante_congreso1_idx` (`congreso_id_congreso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `estado_actual`
--

CREATE TABLE IF NOT EXISTS `estado_actual` (
  `id_estado_actual` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_estado_actual`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `estado_actual`
--

INSERT INTO `estado_actual` (`id_estado_actual`, `descripcion`, `estatus`) VALUES
(1, 'No finalizado', 1),
(2, 'En Proceso', 1),
(3, 'Finalizado', 1);

-- --------------------------------------------------------

--
-- Table structure for table `estado_aplicacion`
--

CREATE TABLE IF NOT EXISTS `estado_aplicacion` (
  `id_estado_aplicacion` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_estado_aplicacion`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `estado_aplicacion`
--

INSERT INTO `estado_aplicacion` (`id_estado_aplicacion`, `descripcion`, `estatus`) VALUES
(1, 'Rechazada', 1),
(2, 'En Revision', 1),
(3, 'Aceptada', 1);

-- --------------------------------------------------------

--
-- Table structure for table `estatus`
--

CREATE TABLE IF NOT EXISTS `estatus` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME` (`NAME`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `estatus`
--

INSERT INTO `estatus` (`ID`, `NAME`) VALUES
(1, 'Activo'),
(2, 'Desactivado'),
(3, 'Eliminado');

-- --------------------------------------------------------

--
-- Table structure for table `fondo`
--

CREATE TABLE IF NOT EXISTS `fondo` (
  `id_fondo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_fondo`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `institucion`
--

CREATE TABLE IF NOT EXISTS `institucion` (
  `id_institucion` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_institucion`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `institucion`
--

INSERT INTO `institucion` (`id_institucion`, `descripcion`, `estatus`) VALUES
(1, 'institution 1 updated', 3),
(2, 'institution 1', 1),
(3, 'institution 2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `moneda`
--

CREATE TABLE IF NOT EXISTS `moneda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `simbolo` varchar(10) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `moneda`
--

INSERT INTO `moneda` (`id`, `simbolo`, `descripcion`, `estatus`) VALUES
(1, 'RD$', 'Dominican Republic Pesos', 1),
(2, 'USD$', 'United States Dollars', 1),
(3, '&euro;', 'Europe Euro', 1);

-- --------------------------------------------------------

--
-- Table structure for table `participante`
--

CREATE TABLE IF NOT EXISTS `participante` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'tabla que tendrá todos los posibles paritcipantes de las publicaciones, proyectos y congresos del sistema',
  `NOMBRE` varchar(45) NOT NULL,
  `APELLIDO` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patrocinio`
--

CREATE TABLE IF NOT EXISTS `patrocinio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL COMMENT 'nombre del patrocinador',
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `proyecto`
--

CREATE TABLE IF NOT EXISTS `proyecto` (
  `id_proyecto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) NOT NULL COMMENT 'Nombre de Aplicacion',
  `fecha_aplicacion` date DEFAULT NULL COMMENT 'Año Final',
  `fecha_inicio` date DEFAULT NULL,
  `asesor` int(11) DEFAULT NULL,
  `id_estado_actual` int(11) NOT NULL,
  `id_estado_aplicacion` int(11) NOT NULL,
  `contrapartida_unibe` decimal(10,0) DEFAULT NULL,
  `aporte_unibe` varchar(45) DEFAULT NULL,
  `moneda` int(11) DEFAULT NULL,
  `monto_total` decimal(10,0) DEFAULT NULL,
  `overhead_unibe` decimal(10,0) DEFAULT NULL COMMENT 'monto de ganancia que se queda UNIBE de la institucion\n',
  `software` tinyint(1) DEFAULT NULL COMMENT 'determinar si es un software, o se auxilia de un software el proyecto asignado',
  `patente` tinyint(1) DEFAULT NULL,
  `otro_producto` varchar(45) DEFAULT NULL,
  `investigador_id` int(11) NOT NULL,
  `estatus` int(11) NOT NULL,
  `creador` int(11) NOT NULL COMMENT 'Creador del proyecto en el sistema\n',
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id_proyecto`),
  KEY `fk_proyectos_estados_actuales1_idx` (`id_estado_actual`),
  KEY `fk_proyectos_estados_aplicacion1_idx` (`id_estado_aplicacion`),
  KEY `fk_proyecto_participante1_idx` (`investigador_id`),
  KEY `fk_proyecto_user_app1_idx` (`creador`),
  KEY `estatus` (`estatus`),
  KEY `moneda` (`moneda`),
  KEY `asesor` (`asesor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- --------------------------------------------------------

--
-- Table structure for table `proyecto_coinvestigador`
--

CREATE TABLE IF NOT EXISTS `proyecto_coinvestigador` (
  `proyecto_id_proyecto` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  PRIMARY KEY (`proyecto_id_proyecto`,`participante_id`),
  KEY `fk_proyecto_has_participante_participante1_idx` (`participante_id`),
  KEY `fk_proyecto_has_participante_proyecto1_idx` (`proyecto_id_proyecto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proyecto_has_fondo`
--

CREATE TABLE IF NOT EXISTS `proyecto_has_fondo` (
  `id_proyecto` int(11) NOT NULL,
  `id_fondo` int(11) NOT NULL,
  PRIMARY KEY (`id_proyecto`,`id_fondo`),
  KEY `fk_proyectos_has_fondos_fondos1_idx` (`id_fondo`),
  KEY `fk_proyectos_has_fondos_proyectos_idx` (`id_proyecto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proyecto_has_institucion`
--

CREATE TABLE IF NOT EXISTS `proyecto_has_institucion` (
  `proyectos_id_proyecto` int(11) NOT NULL,
  `instituciones_id_institucion` int(11) NOT NULL,
  `principal` tinyint(1) NOT NULL,
  PRIMARY KEY (`proyectos_id_proyecto`,`instituciones_id_institucion`),
  KEY `fk_proyectos_has_instituciones_instituciones1_idx` (`instituciones_id_institucion`),
  KEY `fk_proyectos_has_instituciones_proyectos1_idx` (`proyectos_id_proyecto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `proyecto_has_unidad_ejecutora`
--

CREATE TABLE IF NOT EXISTS `proyecto_has_unidad_ejecutora` (
  `proyecto_id_proyecto` int(11) NOT NULL,
  `unidad_ejecutora_id_unidad_ejecutora` int(11) NOT NULL,
  `unidad_ejecutora` tinyint(1) DEFAULT NULL,
  `unidad_supervisora` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`proyecto_id_proyecto`,`unidad_ejecutora_id_unidad_ejecutora`),
  KEY `fk_proyecto_has_unidad_ejecutora_unidad_ejecutora1_idx` (`unidad_ejecutora_id_unidad_ejecutora`),
  KEY `fk_proyecto_has_unidad_ejecutora_proyecto1_idx` (`proyecto_id_proyecto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `publicacion`
--

CREATE TABLE IF NOT EXISTS `publicacion` (
  `id_publicacion` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) NOT NULL,
  `fecha` date NOT NULL,
  `id_revista_publicacion` int(11) NOT NULL,
  `volumen` varchar(45) DEFAULT NULL,
  `pagina` varchar(45) DEFAULT NULL,
  `propiedad_intelectual` tinyint(1) DEFAULT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_publicacion`),
  KEY `fk_publicacion_revista_publicacion1_idx` (`id_revista_publicacion`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `publicacion_autor`
--

CREATE TABLE IF NOT EXISTS `publicacion_autor` (
  `participante_id` int(11) NOT NULL COMMENT 'Tabla relaciona para asignar los autores',
  `publicacion_id_publicacion` int(11) NOT NULL,
  PRIMARY KEY (`participante_id`,`publicacion_id_publicacion`),
  KEY `fk_participante_has_publicacion_publicacion1_idx` (`publicacion_id_publicacion`),
  KEY `fk_participante_has_publicacion_participante1_idx` (`participante_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `revista_publicacion`
--

CREATE TABLE IF NOT EXISTS `revista_publicacion` (
  `id_revista_publicacion` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_revista_publicacion`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `ID` smallint(6) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`ID`, `NOMBRE`) VALUES
(1, 'ADMIN'),
(2, 'REPORT');

-- --------------------------------------------------------

--
-- Table structure for table `sesion`
--

CREATE TABLE IF NOT EXISTS `sesion` (
  `SESION_ID` varchar(100) NOT NULL,
  `OPCION_ADICIONAL` longtext COMMENT 'Estructura JSON para las opciones adicionales',
  `FECHA_EXPIRACION` datetime NOT NULL COMMENT 'Fecha de Expiración de la sesión',
  `FECHA_CREACION` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `USUARIO_ID` int(11) NOT NULL,
  PRIMARY KEY (`SESION_ID`),
  KEY `fk_sesion_usuario_aplicacion1_idx` (`USUARIO_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `unidad_ejecutora`
--

CREATE TABLE IF NOT EXISTS `unidad_ejecutora` (
  `id_unidad_ejecutora` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_unidad_ejecutora`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `usuario_aplicacion`
--

CREATE TABLE IF NOT EXISTS `usuario_aplicacion` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CORREO` varchar(45) DEFAULT NULL COMMENT 'correo electronico del usuario, es con este dato que se utilizara para hacer login \n',
  `CLAVE` varchar(100) DEFAULT NULL COMMENT 'contrasena encintada del usuario',
  `NOMBRE_COMPLETO` varchar(45) DEFAULT NULL COMMENT 'nombre completo del usuario',
  `ROLE_ID` smallint(6) NOT NULL,
  `VER_CODIGO` varchar(100) NOT NULL COMMENT 'Código de verificación\n',
  `ESTATUS` int(11) NOT NULL,
  `FECHA_LOGIN` datetime DEFAULT NULL COMMENT 'fecha que hizo el ultimo login',
  `FECHA_CREACION` datetime DEFAULT NULL COMMENT 'fecha de creación del usuario',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CORREO_UNIQUE` (`CORREO`),
  KEY `ROLE_ID` (`ROLE_ID`),
  KEY `ESTATUS` (`ESTATUS`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `usuario_aplicacion`
--

INSERT INTO `usuario_aplicacion` (`ID`, `CORREO`, `CLAVE`, `NOMBRE_COMPLETO`, `ROLE_ID`, `VER_CODIGO`, `ESTATUS`, `FECHA_LOGIN`, `FECHA_CREACION`) VALUES
(1, 'diaprincipal1@unibe.edu.do', '$2y$10$I.q34hDyO2M33sV3FetRkeNfYSqtNXUU/6Wvn2FBgMo7.yvzs1KRq', 'Dia Principal #1', 1, '', 1, NULL, NULL),
(2, 'diaprincipal2@unibe.edu.do', '$2y$10$mLJ.j553kmU.t2ve4h2QXu9Yl4GZCJm/Jb5lr2LYiJOhtFOvwyP1G', 'Dia Principal #2', 1, '', 1, NULL, NULL),
(3, 'diaprincipal3@unibe.edu.do', '$2y$10$YikDRHYG.PUNweNyxqV/we.sZqh3.CmSvPhh6BjLf9P0Pwv5UtL.i', 'Dia Principal #3', 1, '', 1, NULL, NULL),
(4, 'diaprincipal4@unibe.edu.do', '$2y$10$4CnKRcnmKLanjG0bGTktk..lP6BcObbRoWyRHf3upoyyfJnWb0epm', 'Dia Principal #4', 1, '', 1, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `congreso`
--
ALTER TABLE `congreso`
  ADD CONSTRAINT `congreso_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`),
  ADD CONSTRAINT `fk_congreso_patrocinio1` FOREIGN KEY (`patrocinio_id`) REFERENCES `patrocinio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `congreso_autor`
--
ALTER TABLE `congreso_autor`
  ADD CONSTRAINT `fk_congreso_has_participante_congreso1` FOREIGN KEY (`congreso_id_congreso`) REFERENCES `congreso` (`id_congreso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_congreso_has_participante_participante1` FOREIGN KEY (`participante_ID`) REFERENCES `participante` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `estado_actual`
--
ALTER TABLE `estado_actual`
  ADD CONSTRAINT `estado_actual_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `estado_aplicacion`
--
ALTER TABLE `estado_aplicacion`
  ADD CONSTRAINT `estado_aplicacion_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `fondo`
--
ALTER TABLE `fondo`
  ADD CONSTRAINT `fondo_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `institucion`
--
ALTER TABLE `institucion`
  ADD CONSTRAINT `institucion_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `participante`
--
ALTER TABLE `participante`
  ADD CONSTRAINT `participante_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `patrocinio`
--
ALTER TABLE `patrocinio`
  ADD CONSTRAINT `patrocinio_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `proyecto`
--
ALTER TABLE `proyecto`
  ADD CONSTRAINT `fk_proyecto_participante1` FOREIGN KEY (`investigador_id`) REFERENCES `participante` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyecto_user_app1` FOREIGN KEY (`creador`) REFERENCES `usuario_aplicacion` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyectos_estados_actuales1` FOREIGN KEY (`id_estado_actual`) REFERENCES `estado_actual` (`id_estado_actual`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyectos_estados_aplicacion1` FOREIGN KEY (`id_estado_aplicacion`) REFERENCES `estado_aplicacion` (`id_estado_aplicacion`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `proyecto_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`),
  ADD CONSTRAINT `proyecto_ibfk_2` FOREIGN KEY (`moneda`) REFERENCES `moneda` (`id`),
  ADD CONSTRAINT `proyecto_ibfk_3` FOREIGN KEY (`asesor`) REFERENCES `participante` (`ID`);

--
-- Constraints for table `proyecto_coinvestigador`
--
ALTER TABLE `proyecto_coinvestigador`
  ADD CONSTRAINT `fk_proyecto_has_participante_participante1` FOREIGN KEY (`participante_id`) REFERENCES `participante` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyecto_has_participante_proyecto1` FOREIGN KEY (`proyecto_id_proyecto`) REFERENCES `proyecto` (`id_proyecto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `proyecto_has_fondo`
--
ALTER TABLE `proyecto_has_fondo`
  ADD CONSTRAINT `fk_proyectos_has_fondos_fondos1` FOREIGN KEY (`id_fondo`) REFERENCES `fondo` (`id_fondo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyectos_has_fondos_proyectos` FOREIGN KEY (`id_proyecto`) REFERENCES `proyecto` (`id_proyecto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `proyecto_has_institucion`
--
ALTER TABLE `proyecto_has_institucion`
  ADD CONSTRAINT `fk_proyectos_has_instituciones_instituciones1` FOREIGN KEY (`instituciones_id_institucion`) REFERENCES `institucion` (`id_institucion`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyectos_has_instituciones_proyectos1` FOREIGN KEY (`proyectos_id_proyecto`) REFERENCES `proyecto` (`id_proyecto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `proyecto_has_unidad_ejecutora`
--
ALTER TABLE `proyecto_has_unidad_ejecutora`
  ADD CONSTRAINT `fk_proyecto_has_unidad_ejecutora_proyecto1` FOREIGN KEY (`proyecto_id_proyecto`) REFERENCES `proyecto` (`id_proyecto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_proyecto_has_unidad_ejecutora_unidad_ejecutora1` FOREIGN KEY (`unidad_ejecutora_id_unidad_ejecutora`) REFERENCES `unidad_ejecutora` (`id_unidad_ejecutora`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_publicacion_revista_publicacion1` FOREIGN KEY (`id_revista_publicacion`) REFERENCES `revista_publicacion` (`id_revista_publicacion`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `publicacion_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `publicacion_autor`
--
ALTER TABLE `publicacion_autor`
  ADD CONSTRAINT `fk_participante_has_publicacion_participante1` FOREIGN KEY (`participante_id`) REFERENCES `participante` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_participante_has_publicacion_publicacion1` FOREIGN KEY (`publicacion_id_publicacion`) REFERENCES `publicacion` (`id_publicacion`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `revista_publicacion`
--
ALTER TABLE `revista_publicacion`
  ADD CONSTRAINT `revista_publicacion_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `sesion`
--
ALTER TABLE `sesion`
  ADD CONSTRAINT `fk_sesion_usuario_aplicacion1` FOREIGN KEY (`USUARIO_ID`) REFERENCES `usuario_aplicacion` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `unidad_ejecutora`
--
ALTER TABLE `unidad_ejecutora`
  ADD CONSTRAINT `unidad_ejecutora_ibfk_1` FOREIGN KEY (`estatus`) REFERENCES `estatus` (`ID`);

--
-- Constraints for table `usuario_aplicacion`
--
ALTER TABLE `usuario_aplicacion`
  ADD CONSTRAINT `usuario_aplicacion_ibfk_1` FOREIGN KEY (`ROLE_ID`) REFERENCES `role` (`ID`),
  ADD CONSTRAINT `usuario_aplicacion_ibfk_2` FOREIGN KEY (`ESTATUS`) REFERENCES `estatus` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
