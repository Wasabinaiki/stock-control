-- Backup generado el 2025-04-08 21:40:55
-- Base de datos: control-stock

-- Estructura de la tabla `auditoria`
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `tabla` varchar(100) DEFAULT NULL,
  `id_registro` int(11) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `ip_usuario` varchar(45) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `auditoria`
INSERT INTO `auditoria` VALUES
('1','1','Inicio de sesión exitoso','usuarios','1',NULL,'::1','2025-04-07 23:14:42'),
('2','1','Cierre de sesión','usuarios','1',NULL,'::1','2025-04-07 23:14:52'),
('3','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-07 23:59:02'),
('4','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-07 23:59:34'),
('5','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-07 23:59:35'),
('6','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 00:13:23'),
('7','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 00:14:16'),
('8','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 00:18:22'),
('9','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:20:36'),
('10','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 00:20:49'),
('11','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:20:53'),
('12','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:20:59'),
('13','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:01'),
('14','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:03'),
('15','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:05'),
('16','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:22'),
('17','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:24'),
('18','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:26'),
('19','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:29'),
('20','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:33'),
('21','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:42'),
('22','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:50'),
('23','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:50'),
('24','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:51'),
('25','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:51'),
('26','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:51'),
('27','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:21:52'),
('28','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: csv','::1','2025-04-08 00:21:55'),
('29','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:23:49'),
('30','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:23:59'),
('31','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:24:07'),
('32','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:24:12'),
('33','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:24:16'),
('34','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:02'),
('35','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:04'),
('36','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:08'),
('37','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:40'),
('38','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:42'),
('39','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:43'),
('40','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:28:45'),
('41','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 00:46:58'),
('42','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 00:47:34'),
('43','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:02:50'),
('44','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:36'),
('45','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:05:39'),
('46','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:49'),
('47','1','Acceso a módulo: Acceso a página: bodega',NULL,NULL,NULL,'::1','2025-04-08 01:05:50'),
('48','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:51'),
('49','1','Acceso a módulo: Acceso a página: admin_roles',NULL,NULL,NULL,'::1','2025-04-08 01:05:51'),
('50','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:52'),
('51','1','Acceso a módulo: Acceso a página: admin_pqrs',NULL,NULL,NULL,'::1','2025-04-08 01:05:52'),
('52','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:52'),
('53','1','Acceso a módulo: Acceso a página: admin_mantenimientos',NULL,NULL,NULL,'::1','2025-04-08 01:05:53'),
('54','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:53'),
('55','1','Acceso a módulo: Acceso a página: admin_envios',NULL,NULL,NULL,'::1','2025-04-08 01:05:53'),
('56','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:53'),
('57','1','Acceso a módulo: Acceso a página: admin_informes',NULL,NULL,NULL,'::1','2025-04-08 01:05:54'),
('58','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:54'),
('59','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 01:05:55'),
('60','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:05:55'),
('61','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:05:56'),
('62','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:06:07'),
('63','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:06:10'),
('64','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:06:11'),
('65','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:07:53'),
('66','1','Acceso a módulo: Acceso a página: dispositivos',NULL,NULL,NULL,'::1','2025-04-08 01:08:08'),
('67','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 01:08:09'),
('68','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:08:10'),
('69','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:08:18'),
('70','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:08:19'),
('71','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:08:25'),
('72','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:08:27'),
('73','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 01:08:30'),
('74','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: csv','::1','2025-04-08 01:08:30'),
('75','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 01:08:31'),
('76','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 01:08:31'),
('77','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 01:08:33'),
('78','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 01:08:35'),
('79','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 01:08:35'),
('80','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 02:09:37'),
('81','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 02:17:10'),
('82','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 02:17:10'),
('83','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 02:23:06'),
('84','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 02:23:28'),
('85','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 02:23:29'),
('86','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 02:23:29'),
('87','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 02:24:35'),
('88','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 02:24:37'),
('89','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 02:24:46'),
('90','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 02:24:46'),
('91','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 02:25:44'),
('92','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:25:47'),
('93','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:25:48'),
('94','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:33:49'),
('95','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:33:50'),
('96','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 02:33:55'),
('97','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 02:45:50'),
('98','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:48:18'),
('99','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:53:06'),
('100','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 02:53:11'),
('101','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:54:10'),
('102','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:54:12'),
('103','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 02:54:16'),
('104','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:57:12'),
('105','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:58:09'),
('106','1','Acceso a módulo: Acceso a página: dispositivos',NULL,NULL,NULL,'::1','2025-04-08 02:58:10'),
('107','1','Acceso a módulo: Acceso a página: eliminar_dispositivo',NULL,NULL,NULL,'::1','2025-04-08 02:58:15'),
('108','1','Acceso a módulo: Acceso a página: eliminar_dispositivo',NULL,NULL,NULL,'::1','2025-04-08 02:58:18'),
('109','1','Acceso a módulo: Acceso a página: dispositivos',NULL,NULL,NULL,'::1','2025-04-08 02:58:18'),
('110','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:58:28'),
('111','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:59:03'),
('112','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:59:08'),
('113','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:59:10'),
('114','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:59:50'),
('115','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 02:59:51'),
('116','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:09'),
('117','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:10'),
('118','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:11'),
('119','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:33'),
('120','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:35'),
('121','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:40'),
('122','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:43'),
('123','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:45'),
('124','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:46'),
('125','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:47'),
('126','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:00:47'),
('127','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:48'),
('128','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:50'),
('129','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:52'),
('130','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:55'),
('131','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:00:55'),
('132','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:01:07'),
('133','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:01:15'),
('134','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:02:09'),
('135','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:02:11'),
('136','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:02:12'),
('137','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:02:20'),
('138','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:02:21'),
('139','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:02:22'),
('140','1','Acceso a módulo: Acceso a página: exportar_auditoria',NULL,NULL,NULL,'::1','2025-04-08 03:02:35'),
('141','1','Generación de reporte: Auditoría',NULL,NULL,'Formato: pdf','::1','2025-04-08 03:02:35'),
('142','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:02:48'),
('143','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:03:00'),
('144','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:03:13'),
('145','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:03:14'),
('146','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:03:18'),
('147','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 03:04:36'),
('148','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:04:41'),
('149','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:04:42'),
('150','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:04:44'),
('151','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:04:53'),
('152','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:04:54'),
('153','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:15:55'),
('154','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:15:56'),
('155','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:15:57'),
('156','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:16:26'),
('157','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:20:18'),
('158','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:20:24'),
('159','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 03:20:36'),
('160','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:23'),
('161','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:30'),
('162','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:36'),
('163','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:38'),
('164','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 03:22:41'),
('165','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 03:22:41'),
('166','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:42'),
('167','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:22:43'),
('168','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:44'),
('169','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:45'),
('170','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 03:22:47'),
('171','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 03:22:47'),
('172','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:47'),
('173','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:49'),
('174','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:50'),
('175','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:22:51'),
('176','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:51'),
('177','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:22:53'),
('178','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:23:08'),
('179','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:23:18'),
('180','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:23:36'),
('181','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:23:39'),
('182','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:24:37'),
('183','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:24:38'),
('184','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:24:40'),
('185','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:24:43'),
('186','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:24:44'),
('187','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:25:32'),
('188','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:25:35'),
('189','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:25:35'),
('190','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 03:25:35'),
('191','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 03:25:35'),
('192','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:25:36'),
('193','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:25:37'),
('194','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:25:38'),
('195','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:25:43'),
('196','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 03:25:46'),
('197','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:25:53'),
('198','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:26:16'),
('199','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:26:17'),
('200','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:27:06'),
('201','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:27:06'),
('202','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:28:24'),
('203','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:28:52'),
('204','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:32:42'),
('205','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 03:32:43'),
('206','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:34:30'),
('207','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:34:33'),
('208','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:35'),
('209','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:36'),
('210','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:36'),
('211','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:41'),
('212','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:45'),
('213','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:45'),
('214','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:49'),
('215','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:34:49'),
('216','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:35:00'),
('217','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:35:02'),
('218','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 03:35:11'),
('219','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:35:35'),
('220','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:35:35'),
('221','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 03:36:19'),
('222','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:44:40'),
('223','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:44:41'),
('224','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 03:44:42'),
('225','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:44:54'),
('226','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:45:10'),
('227','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:45:13'),
('228','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 03:45:27'),
('229','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:45:28'),
('230','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:45:48'),
('231','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:46:35'),
('232','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:46:36'),
('233','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:46:38'),
('234','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:46:39'),
('235','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 03:46:42'),
('236','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 03:47:10'),
('237','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:10:30'),
('238','1','Acceso a módulo: Acceso a página: chatbot',NULL,NULL,NULL,'::1','2025-04-08 14:10:34'),
('239','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:13:14'),
('240','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:13:17'),
('241','1','Acceso a módulo: Auditoría',NULL,NULL,NULL,'::1','2025-04-08 14:13:19'),
('242','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:13:19'),
('243','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:14:30'),
('244','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 14:14:32'),
('245','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 14:14:39'),
('246','1','Exportación de reporte: todos en formato pdf',NULL,NULL,'Usuario: wng_dngstruque, Tipo: todos, Formato: pdf','::1','2025-04-08 14:14:39'),
('247','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 14:17:34'),
('248','1','Exportación de reporte: todos en formato excel',NULL,NULL,'Usuario: wng_dngstruque, Tipo: todos, Formato: excel','::1','2025-04-08 14:17:34'),
('249','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 14:24:33'),
('250','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:24:34'),
('251','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:24:35'),
('252','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:24:35'),
('253','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:25:06'),
('254','1','Acceso a módulo: Acceso a página: reportes',NULL,NULL,NULL,'::1','2025-04-08 14:25:07'),
('255','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 14:25:08'),
('256','1','Generación de reporte: Reporte: todos',NULL,NULL,'Formato: pdf','::1','2025-04-08 14:25:08'),
('257','1','Acceso a módulo: Acceso a página: dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:27:09'),
('258','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:27:11'),
('259','1','Acceso a módulo: Acceso a página: admin_reportes',NULL,NULL,NULL,'::1','2025-04-08 14:27:12'),
('260','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 14:27:31'),
('261','1','Generación de reporte: Reporte: todos',NULL,NULL,'Formato: pdf','::1','2025-04-08 14:27:31'),
('262','1','Acceso a módulo: Acceso a página: exportar_reportes',NULL,NULL,NULL,'::1','2025-04-08 14:27:37'),
('263','1','Generación de reporte: Reporte: todos',NULL,NULL,'Formato: excel','::1','2025-04-08 14:27:37'),
('264','1','Acceso a módulo: Acceso a página: admin_dashboard',NULL,NULL,NULL,'::1','2025-04-08 14:29:32'),
('265','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 14:29:33'),
('266','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 14:29:33'),
('267','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 14:29:33'),
('268','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 14:29:33'),
('269','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 14:29:34'),
('270','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 14:29:34'),
('271','1','Acceso a módulo: Acceso a página: admin_backup',NULL,NULL,NULL,'::1','2025-04-08 14:40:55'),
('272','1','Acceso a módulo: Backup',NULL,NULL,NULL,'::1','2025-04-08 14:40:55');


-- Estructura de la tabla `backups`
DROP TABLE IF EXISTS `backups`;
CREATE TABLE `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL,
  `tamanio` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `backups_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `backups`


-- Estructura de la tabla `configuracion`
DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `configuracion`


-- Estructura de la tabla `contactos`
DROP TABLE IF EXISTS `contactos`;
CREATE TABLE `contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `asunto` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Pendiente','En proceso','Resuelto') DEFAULT 'Pendiente',
  `notas` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `contactos`
INSERT INTO `contactos` VALUES
('1','santiago','danielitoteresuelve1212@gmail.com','','soporte','Necesito ayuda','','2024-11-22 20:08:00','Resuelto','LISTO!'),
('2','Daniel Santiago Truque Martinez','truquemdaniels.cla@gmail.com','','soporte','Necesito ayuda con mi perfil','','2025-04-01 17:39:14','En proceso',''),
('3','Daniel Santiago Truque Martinez','danielitoteresuelve1212@gmail.com','','otros','PRUEBA 20','','2025-04-01 22:37:34','Pendiente',NULL),
('4','PRUEBA','PRUEBA@PRUEBA.COM','PRUEBA','quejas','PRUEBA','','2025-04-01 22:57:35','Resuelto',''),
('5','PRUEBA','PRUEBA@PRUEBA.COM','','sugerencias','PRUEBA','','2025-04-03 22:00:47','Pendiente',NULL);


-- Estructura de la tabla `dispositivos`
DROP TABLE IF EXISTS `dispositivos`;
CREATE TABLE `dispositivos` (
  `id_dispositivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `tipo_dispositivo` enum('Computador','Tablet','Celular') NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `estado` enum('Activo','En Reparación','Inactivo','Completado') NOT NULL,
  `licencias` varchar(255) DEFAULT NULL,
  `procesador` varchar(100) DEFAULT NULL,
  `almacenamiento` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_dispositivo`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `dispositivos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `dispositivos`
INSERT INTO `dispositivos` VALUES
('22','1','computadora','Computador','Gigabyte','4060','2025-04-01','Activo','Office 365','Ryzen 5','1 TB','16 GB','293087463'),
('23','1','computadora','Computador','Asus','Zenbook','2025-04-01','En Reparación','N/A','Ryzen 5','512 GB','12 GB','19273645134'),
('24','1','Tablet','Computador','Tablet','N/A','2025-04-01','Inactivo','N/A','Snapdragon 111','256 GB','6 GB','2873461645'),
('25','1','celular','Computador','Xiaomi','Redmi Note 11 PRO 5G','2025-04-01','Activo','N/A','Snapdragon 666','512GB','8 GB','187364521346'),
('26','1','celular','Computador','Samsung','Galaxy A22 4G','2025-04-01','Activo','N/A','Snapdragon 777','512 GB','8 GB','23879465725'),
('29','7','Computadora','Computador','PRUREBA','PRUREBA','2025-04-05','Activo','PRUREBA','PRUREBA','PRUREBA','PRUREBA','12365234623462345');


-- Estructura de la tabla `envios`
DROP TABLE IF EXISTS `envios`;
CREATE TABLE `envios` (
  `id_envio` int(11) NOT NULL AUTO_INCREMENT,
  `direccion_destino` text DEFAULT NULL,
  `fecha_envio` date DEFAULT NULL,
  `estado_envio` enum('En Proceso','Completado') NOT NULL DEFAULT 'En Proceso',
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `fecha_llegada` date DEFAULT NULL,
  PRIMARY KEY (`id_envio`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `envios`
INSERT INTO `envios` VALUES
('2','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-03'),
('3','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-01'),
('4','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-01'),
('5','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-01'),
('6','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-01'),
('7','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-01'),
('8','Bodega Central','2025-04-01','Completado','1','2025-04-01','2025-04-01'),
('9','Bodega Central','2025-04-02','Completado','1','2025-04-02','2025-04-03'),
('10','Bodega Central','2025-04-04','En Proceso','1','2025-04-04',NULL),
('11','Bodega Central','2025-04-04','En Proceso','1','2025-04-04',NULL),
('12','Bodega Central','2025-04-05','En Proceso','7','2025-04-05',NULL);


-- Estructura de la tabla `facturas`
DROP TABLE IF EXISTS `facturas`;
CREATE TABLE `facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `numero_factura` varchar(50) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `facturas`


-- Estructura de la tabla `mantenimientos`
DROP TABLE IF EXISTS `mantenimientos`;
CREATE TABLE `mantenimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dispositivo` int(11) DEFAULT NULL,
  `fecha_programada` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('programado','en_proceso','completado') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_dispositivo` (`id_dispositivo`),
  CONSTRAINT `mantenimientos_ibfk_1` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id_dispositivo`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `mantenimientos`
INSERT INTO `mantenimientos` VALUES
('16','26','2025-04-01','Necesito cambiarle la tapa a mi celular por una nueva, está agrietada y dañada','completado'),
('17','22','2025-04-01','Una limpieza para mi torre','en_proceso'),
('18','25','2025-04-01','Limpieza para mi cel','en_proceso'),
('19','23','2025-04-01','Necesito arreglarle el altavoz izquierdo de mi portátil','programado'),
('20','22','2025-04-01','PRUEBA 1','programado'),
('21','22','2025-04-02','prueba','completado'),
('23','22','2025-04-04','PRUEBA','programado'),
('24','29','2025-04-05','PRUREBA','programado');


-- Estructura de la tabla `pqrs`
DROP TABLE IF EXISTS `pqrs`;
CREATE TABLE `pqrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo` enum('peticion','queja','reclamo','sugerencia') DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','resuelto') DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `respuesta` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pqrs_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `pqrs`
INSERT INTO `pqrs` VALUES
('1','4','sugerencia','Sugiero que mejoren la app','pendiente','2024-11-09 08:52:06',NULL),
('2','5','peticion','NECESITO LA REVISION DE UN DISPOSITIVO','resuelto','2024-11-09 09:23:34',''),
('3','5','queja','MUCHA DEMORA','resuelto','2024-11-09 09:28:06','¡SU QUEJA HA SIDO RESUELTA EXITOSAMENTE!'),
('4','1','queja','uhfyrfy','pendiente','2025-03-31 16:11:03',''),
('5','1','peticion','PRUEBA','','2025-04-01 17:33:44',''),
('6','1','peticion','PRUEBA','en_proceso','2025-04-01 22:57:02',''),
('7','1','queja','PRUEBA DE QUEJA','resuelto','2025-04-02 00:33:37','');


-- Estructura de la tabla `usuarios`
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `rol` enum('usuario','administrador') DEFAULT 'usuario',
  `tamanio_texto` enum('pequeno','normal','grande') DEFAULT 'normal',
  `tema` enum('claro','oscuro') DEFAULT 'claro',
  `idioma` char(2) DEFAULT 'es',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de la tabla `usuarios`
INSERT INTO `usuarios` VALUES
('1','wng_dngstruque','$2y$10$zHha3k0pDeSRtR/JxW551u5SoUtWJ5o67YHlsyaBDTxwvz.4hnfQ2','danielitoteresuelve1212@gmail.com','2024-11-06 17:19:48','Daniel Santiago','Truque Martinez','3246044420','N/A','administrador','normal','claro','es'),
('3','asdfg','$2y$10$WSuiJbmloireu8WT3NL8ledOXSc1UsfRFMM6dQudynyKjhT4X6WTC','juanpablorubiano1977@gmail.com','2024-11-09 08:31:17',NULL,NULL,NULL,NULL,'usuario','normal','claro','es'),
('4','zxcv','$2y$10$1JIzTbInQUuzSmhAGgqa1elg3UXXfCGRFZG3kpwYHa1rE/g6ATcvO','sebas@gmail.com','2024-11-09 08:33:37',NULL,NULL,NULL,NULL,'usuario','normal','claro','es'),
('5','ruben','$2y$10$5CkbYQ7hfdjQwfcOi5U0fem1568ES7/51RNO/0wsBsRVTTLH69snu','ruben.diaz@gmail.com','2024-11-09 09:19:17',NULL,NULL,NULL,NULL,'usuario','normal','claro','es'),
('6','dngtruque','$2y$10$l0PU1guegnCNAbGypPeIG.fuaW9giwB1aJbh0I389ugWBpK/LdM8K','truquemdaniels.cla@gmail.com','2024-11-22 16:52:07',NULL,NULL,NULL,NULL,'usuario','normal','claro','es'),
('7','usuariodeprueba','$2y$10$nUtqZJX7cov1PiAMrySGaeIE5GPFaPKvTPjSx6Txxzo44kvLFmzhe','wasabinaiki@gmail.com','2025-04-05 00:51:08',NULL,NULL,NULL,NULL,'usuario','normal','claro','es'),
('8','mesideprueba','$2y$10$E45Ro3eh6mQaBhgNUi3dROWatJQfD3scTElH/nTVHJgXZDT6wAkdC','mesideprueba@gmail.com','2025-04-07 16:29:47',NULL,NULL,NULL,NULL,'usuario','normal','claro','es');


