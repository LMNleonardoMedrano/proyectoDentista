-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-11-2024 a las 19:58:44
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
-- Base de datos: `dentali`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `idCitas` int(50) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora` time NOT NULL,
  `MotivoConsulta` varchar(100) NOT NULL,
  `idPaciente` int(50) NOT NULL,
  `idOdontologo` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `odontograma`
--

CREATE TABLE `odontograma` (
  `idOdontograma` int(50) NOT NULL,
  `Posicion` int(100) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Categoria` varchar(100) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `idTratamiento` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `odontologo`
--

CREATE TABLE `odontologo` (
  `idOdontologo` int(50) NOT NULL,
  `Ci` varchar(20) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `TelCel` int(20) NOT NULL,
  `Domicilio` text NOT NULL,
  `FechaNac` date NOT NULL,
  `Turno` varchar(50) NOT NULL,
  `Correo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `odontologo`
--

INSERT INTO `odontologo` (`idOdontologo`, `Ci`, `Nombre`, `Apellido`, `TelCel`, `Domicilio`, `FechaNac`, `Turno`, `Correo`) VALUES
(7, '12', 'leonardo', 'domingo', 2221, 'av', '2024-11-20', 'Tarde', 'loky@gmail.com'),
(8, '12', '22', 'domingo', 323, '33', '2024-12-06', 'Mañana', 'loky@gmail.com'),
(9, '1243', '24', '22', 12, '12', '2024-11-14', 'Administrador', 'lmnleo4318@gmail.com'),
(10, '12322', 'rebe', '2', 22222, 'bes', '2024-11-29', 'paciente', 'lmnleo4318@gmail.com'),
(11, '242', 'pedro', 'mrc', 12343, 'bes', '2024-11-16', 'Tarde', 'lmnleo4318@gmail.com'),
(12, '123222', 'leonardo', 'domingo', 22222, '23', '2024-11-19', 'Tarde', 'loky@gmail.com'),
(13, '1', '2', '3', 5, '4', '2024-12-02', 'Mañana', 'lmnleo4318@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

CREATE TABLE `paciente` (
  `idPaciente` int(50) NOT NULL,
  `Ci` varchar(20) NOT NULL,
  `Domicilio` varchar(100) NOT NULL,
  `FechaNac` date NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Genero` varchar(50) NOT NULL,
  `Ocupacion` varchar(100) NOT NULL,
  `TelCel` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`idPaciente`, `Ci`, `Domicilio`, `FechaNac`, `Nombre`, `Genero`, `Ocupacion`, `TelCel`) VALUES
(1, '12322', 'bes', '2024-11-23', 'pedro', 'Masculino', 'aaaa', 123123),
(3, '1112222', 'fff', '2024-11-08', 'gg', 'Femenino', 'www', 4545),
(4, '12322', '23', '2024-10-30', 'leonardo', 'Masculino', 'qqqq', 123123),
(5, '2134', '22', '2024-10-31', 'luis', 'Femenino', 'aaaa', 11),
(6, '12345678', 'avetr', '2024-11-29', 'joel', 'Masculino', 'asd', 1234567);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagotratamiento`
--

CREATE TABLE `pagotratamiento` (
  `idPagoTratamiento` int(50) NOT NULL,
  `Fecha` date NOT NULL,
  `Monto` int(100) NOT NULL,
  `idPaciente` int(50) NOT NULL,
  `idTratamiento` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realiza`
--

CREATE TABLE `realiza` (
  `idPaciente` int(50) NOT NULL,
  `idTratamiento` int(50) NOT NULL,
  `fecha` date NOT NULL,
  `Procedimiento` varchar(50) NOT NULL,
  `idRealiza` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetamedica`
--

CREATE TABLE `recetamedica` (
  `idRecetaMedica` int(50) NOT NULL,
  `dosis` text NOT NULL,
  `Medicamento` text NOT NULL,
  `Tiempo` text NOT NULL,
  `idTratamiento` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamiento`
--

CREATE TABLE `tratamiento` (
  `idTratamiento` int(50) NOT NULL,
  `Diagnostico` text NOT NULL,
  `Procedimiento` text NOT NULL,
  `Tipo` varchar(100) NOT NULL,
  `Monto` int(100) NOT NULL,
  `idOdontologo` int(50) NOT NULL,
  `saldo` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorpadre`
--

CREATE TABLE `tutorpadre` (
  `IdTutorPadre` int(50) NOT NULL,
  `idPaciente` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Domicilio` varchar(100) NOT NULL,
  `FechaNac` date NOT NULL,
  `Genero` char(50) NOT NULL,
  `Ci` int(20) NOT NULL,
  `Ocupacion` varchar(100) NOT NULL,
  `Relacion` varchar(100) NOT NULL,
  `TelCel` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutorpadre`
--

INSERT INTO `tutorpadre` (`IdTutorPadre`, `idPaciente`, `Nombre`, `Domicilio`, `FechaNac`, `Genero`, `Ci`, `Ocupacion`, `Relacion`, `TelCel`) VALUES
(1, 3, 'leo', 'av', '2024-10-02', 'Masculino', 12345, 'estud', 'Tutor', 123),
(5, 4, 'rebe', '2', '2024-11-29', 'Femenino', 222, '2', 'Tutor', 2221);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuarios` int(50) NOT NULL,
  `Nombre` text NOT NULL,
  `Usuario` text NOT NULL,
  `correo` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Perfil` text NOT NULL,
  `Foto` text NOT NULL,
  `Estado` int(50) NOT NULL,
  `UltimoLogin` datetime NOT NULL,
  `Fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuarios`, `Nombre`, `Usuario`, `correo`, `Password`, `Perfil`, `Foto`, `Estado`, `UltimoLogin`, `Fecha`) VALUES
(42, 'UsuarioAdministrador', 'admin', '', '$2a$07$asxx54ahjppf45sd87a5auXBm1Vr2M1NV5t/zNQtGHGpS5fFirrbG', 'Administrador', 'vistas/img/usuarios/admin/420.jpg', 1, '2024-11-17 13:28:44', '2024-11-17 18:28:44'),
(49, 'pedro', 'pablo', '', '$2a$07$asxx54ahjppf45sd87a5auI89owGrS7usVcyl/sik60F2C6XWteDO', 'paciente', '', 1, '2024-11-09 15:20:37', '2024-11-10 02:51:54'),
(50, 'luis', 'lucas', '', '$2a$07$asxx54ahjppf45sd87a5au/TkyqEuoSC9YSdibgB.uiPSLz47WKXG', 'paciente', 'vistas/img/usuarios/lucas/146.jpg', 1, '2024-11-10 18:43:53', '2024-11-10 23:43:53'),
(63, 'leonardo', 'leo', 'lmnleo4318@gmail.com', '$2a$07$asxx54ahjppf45sd87a5auGZEtGHuyZwm.Ur.FJvWLCql3nmsMbXy', 'doctor', 'vistas/img/usuarios/leo/998.jpg', 1, '2024-11-10 18:44:20', '2024-11-10 23:44:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCitas`),
  ADD UNIQUE KEY `idPaciente_2` (`idPaciente`),
  ADD UNIQUE KEY `idOdontologo_2` (`idOdontologo`),
  ADD KEY `idPaciente` (`idPaciente`),
  ADD KEY `idOdontologo` (`idOdontologo`);

--
-- Indices de la tabla `odontograma`
--
ALTER TABLE `odontograma`
  ADD PRIMARY KEY (`idOdontograma`),
  ADD UNIQUE KEY `idTratamiento_2` (`idTratamiento`),
  ADD KEY `idTratamiento` (`idTratamiento`);

--
-- Indices de la tabla `odontologo`
--
ALTER TABLE `odontologo`
  ADD PRIMARY KEY (`idOdontologo`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`idPaciente`);

--
-- Indices de la tabla `pagotratamiento`
--
ALTER TABLE `pagotratamiento`
  ADD PRIMARY KEY (`idPagoTratamiento`),
  ADD UNIQUE KEY `idPaciente_2` (`idPaciente`),
  ADD UNIQUE KEY `idTratamiento_2` (`idTratamiento`),
  ADD KEY `idPaciente` (`idPaciente`),
  ADD KEY `idTratamiento` (`idTratamiento`);

--
-- Indices de la tabla `realiza`
--
ALTER TABLE `realiza`
  ADD PRIMARY KEY (`idRealiza`),
  ADD UNIQUE KEY `idPaciente_2` (`idPaciente`),
  ADD UNIQUE KEY `idTratamiento_2` (`idTratamiento`),
  ADD KEY `idPaciente` (`idPaciente`),
  ADD KEY `idTratamiento` (`idTratamiento`);

--
-- Indices de la tabla `recetamedica`
--
ALTER TABLE `recetamedica`
  ADD PRIMARY KEY (`idRecetaMedica`),
  ADD UNIQUE KEY `idTratamiento_2` (`idTratamiento`),
  ADD KEY `idTratamiento` (`idTratamiento`);

--
-- Indices de la tabla `tratamiento`
--
ALTER TABLE `tratamiento`
  ADD PRIMARY KEY (`idTratamiento`),
  ADD UNIQUE KEY `idOdontologo_2` (`idOdontologo`),
  ADD KEY `idOdontologo` (`idOdontologo`);

--
-- Indices de la tabla `tutorpadre`
--
ALTER TABLE `tutorpadre`
  ADD PRIMARY KEY (`IdTutorPadre`),
  ADD KEY `idPaciente` (`idPaciente`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuarios`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCitas` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `odontograma`
--
ALTER TABLE `odontograma`
  MODIFY `idOdontograma` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `odontologo`
--
ALTER TABLE `odontologo`
  MODIFY `idOdontologo` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `idPaciente` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pagotratamiento`
--
ALTER TABLE `pagotratamiento`
  MODIFY `idPagoTratamiento` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `realiza`
--
ALTER TABLE `realiza`
  MODIFY `idRealiza` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recetamedica`
--
ALTER TABLE `recetamedica`
  MODIFY `idRecetaMedica` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tratamiento`
--
ALTER TABLE `tratamiento`
  MODIFY `idTratamiento` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutorpadre`
--
ALTER TABLE `tutorpadre`
  MODIFY `IdTutorPadre` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuarios` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `Odontologo` FOREIGN KEY (`idOdontologo`) REFERENCES `odontologo` (`idOdontologo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Paciente` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagotratamiento`
--
ALTER TABLE `pagotratamiento`
  ADD CONSTRAINT `pagotratamiento_ibfk_1` FOREIGN KEY (`idTratamiento`) REFERENCES `tratamiento` (`idTratamiento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pagotratamiento_ibfk_2` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`);

--
-- Filtros para la tabla `realiza`
--
ALTER TABLE `realiza`
  ADD CONSTRAINT `realiza_ibfk_1` FOREIGN KEY (`idTratamiento`) REFERENCES `tratamiento` (`idTratamiento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `realiza_ibfk_2` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recetamedica`
--
ALTER TABLE `recetamedica`
  ADD CONSTRAINT `recetamedica_ibfk_1` FOREIGN KEY (`idTratamiento`) REFERENCES `tratamiento` (`idTratamiento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tratamiento`
--
ALTER TABLE `tratamiento`
  ADD CONSTRAINT `tratamiento_ibfk_1` FOREIGN KEY (`idOdontologo`) REFERENCES `odontologo` (`idOdontologo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tratamiento_ibfk_2` FOREIGN KEY (`idTratamiento`) REFERENCES `odontograma` (`idTratamiento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tutorpadre`
--
ALTER TABLE `tutorpadre`
  ADD CONSTRAINT `tutorpadre_ibfk_1` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
