-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: crm_callcenter
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agentes`
--

DROP TABLE IF EXISTS `agentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agentes` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_agente_profile` (`profile_id`),
  CONSTRAINT `fk_agente_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentes`
--

LOCK TABLES `agentes` WRITE;
/*!40000 ALTER TABLE `agentes` DISABLE KEYS */;
INSERT INTO `agentes` VALUES ('33eec760-adf5-11f1-83c8-080027fab11a','Eddy Aguilar','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',1,'2026-09-11 15:26:47'),('38e848a3-0523-489b-9268-56fd5aaf6503','Sdenka Roca',NULL,1,'2026-09-03 15:09:07'),('6e4036da-d076-4ec7-af70-526a438afb76','Lizeth Micail Guzmán',NULL,1,'2026-09-03 15:11:37'),('830e6dd9-c887-4254-abf2-3ad10e282752','Pedro Tarupiz',NULL,1,'2026-09-03 15:09:28'),('ceb8a5db-1226-4429-80a5-b719d6df5161','Eddy Aguilar',NULL,1,'2026-09-03 15:07:37');
/*!40000 ALTER TABLE `agentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campanas`
--

DROP TABLE IF EXISTS `campanas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campanas` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_camp_empresa` (`empresa_id`),
  CONSTRAINT `fk_camp_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campanas`
--

LOCK TABLES `campanas` WRITE;
/*!40000 ALTER TABLE `campanas` DISABLE KEYS */;
INSERT INTO `campanas` VALUES ('0ad1e9ae-1d2d-4e3a-b0c1-1fae1222753d','VILLAREAL-TOFU-MAY26 )','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-06 18:20:43'),('115b063e-4649-4b52-8d33-0686b5969135','VILLAREAL-MOFU-FEB26 :)','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-20 13:23:25'),('151b1057-3ffb-4133-9360-72311c55730e','CLARASERRANO-EXTRANEJRO-MAY26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-11 19:14:39'),('159aacf1-66a5-4824-af0d-433985a5d10f','VILLAREAL-EXTRANJERO-CHILE-ABR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-04-13 13:35:51'),('1641f79e-169e-4d24-852b-5c90e1843005','Formulario Regional LPZ','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-25 13:11:04'),('1cb42f32-8186-4ddc-bdd0-f27e73e97084','TI TOK CLARASERRANO-GCP-MAY10','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-05 15:45:29'),('30e3a9d1-5fcc-4781-9689-eb9997132a86','CLARASERRANO-GOFU-MAY26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-19 20:33:18'),('3372182f-7d84-4526-b137-1dbbc5fdb186','CLARASERRANO-GOFU-MAR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-23 14:11:05'),('389dc612-e4c2-41e7-8ea7-9a3e4a441a6f','CLARASERRANO-GOFU-ABR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-04-21 17:34:41'),('397c1650-ae6a-4ea2-8702-fc21ce5e175a','VILLA REAL-GOFU-MAR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-23 14:10:47'),('45647a73-2cf0-43cd-99e6-4b11b7ee3e9f','Formulario Regional Cochabamba','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-04 15:40:21'),('46c2574b-3d54-49f2-bca2-2c5db2ce49e2','CLARASERRANO-MOFU-MAR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-14 13:50:59'),('4ef858fe-a615-43d9-8099-7aab4ddee958','SIN IDENTIFICAR','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-10 16:13:07'),('4fb301cc-e55a-4244-80b9-dc18e81ea718','VILLAREAL-VENTANOCTURNA-MAR26','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-23 14:12:23'),('555f8ae3-3083-4904-9cba-bc5e408d56ab','CLARASERRANO-COMBOS-MAR26 TIK TOK','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-05 13:28:49'),('583ed5a6-5d67-48f3-933e-862b26e47638','Formulario Regional Potosi','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-04 15:40:37'),('5953f53b-cb5a-45cb-91f4-06a01dd79efe','VILLA REAL-GCP-GOFU-ENE26 (C3VE)','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-02 15:24:32'),('5c3ec31b-baaa-41fd-8ac8-ee29c011f69e','VILLAREAL-GCP-ENE26','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-20 17:59:30'),('5e09ed33-7082-4c91-b744-3ce48c14ecc0','VILLA-PROMO-MAR26 TIK TOK','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-05 14:56:13'),('66a72a49-10ae-4e76-a8c1-29cb190cf98e','VILLAREAL-TOFU-MAR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-04 00:05:12'),('6c43b1ce-c46a-48c0-89fe-d8bd333557b3','CLARASERRANO-COMBOSMUNDIAL-MAY26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-11 19:16:04'),('6ca2373f-5aa0-4196-a5a2-96a3841ef1b8','CLARASERRANO-COMBOSPADRE-MAR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-04 21:12:21'),('76b6ee8c-96eb-4cdf-95de-fe9ffd97c1cd','Formulario Regional Cochabamba','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-16 17:11:21'),('789e60d9-90ee-4ebb-86da-c21e34a4cd18','CLARASERRANO-GCP-TOFU-ENE26 C1','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-23 15:31:32'),('7b932d2f-720b-409a-96b3-9bd40ddb565f','CLARASERRANO-TOFU-MAR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-04 00:04:11'),('7cbdcab7-6874-4975-b5eb-127d7db77b90','CLARASERRANO-MOFU-MAY26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-13 20:38:07'),('7ebe7d0c-95fc-4aea-a68f-76fd9368c256','VILLA REAL-MOFU-MAR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-16 14:19:19'),('8481ad93-d19c-4bdc-9864-fa58be4e5da3','prueba😅','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-07-20 16:57:55'),('87410707-649e-4bd4-b0ef-b65ec091669b','VILLAREAL-MOFU-ABR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-04-11 15:43:59'),('8880b7c6-0e61-426e-86d6-ee497871a5bb','TIK TOK VILLAREAL-GCP-ABR26','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-06 18:19:59'),('8bc1e1ba-66a3-45ff-a07a-e08a4f8e7510','CLARASERRANO-DIADELNIÃ‘O-ABR()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-04-04 13:16:41'),('9442a1f1-c1af-466b-ac9c-d5714a65d956','VILLAREAL-TOFU-ABR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-04-04 16:07:42'),('94d2f315-da31-4056-b48a-37b079a48740','CLARA SERRANO-GCP-GOFU-ENE26 (C3)','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-02 15:24:06'),('9aff4470-d0be-4c19-9571-11bcc39eebda','CLARASERRANO-ALCANCE-PAGINAWEB-ABR26','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-04-04 13:26:15'),('9b899fe8-2641-4b22-b594-876ff31b0b07','CLARASERRANO-TOFU-MAY26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-06 18:23:23'),('9ffbcaac-b6b7-4a24-adde-99d655e56bfb','VILLA REAL-GOFU-ABR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-04-21 17:33:24'),('a0c5fdbc-585c-4311-84bc-3a299b4aa22e','Formulario Regional Oruro','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-16 17:10:37'),('a351a0a8-47b9-426b-a8b9-7965b5889abd','Formulario Regional Oruro','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-04 15:40:49'),('a76cc22b-36eb-495d-aa1c-8b0ee7795394','Formulario Regional Potosi','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-16 17:10:50'),('a784fb10-6e2b-48e8-b537-d92d7c6689c5','VILLA REAL-COMBOSMUNDIAL-MAY26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-11 19:09:49'),('a8d93e6c-3d4e-44e2-9f81-417bf99d42e1','VILLA REAL-EXTRANJERO-MAR26 (InformaciÃ³n de los terrenos SC)','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-14 13:53:31'),('aaf134ad-e7e8-4b29-a457-40bc36300ee4','CLARASERRANO-VENTANOCTURNA-MAR26','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-23 14:14:29'),('b06e8a30-bcc2-48f7-8212-d6c0da06e2d7','CLARASERRANO-EXTRANJERO-CHILE-ABR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-04-13 13:35:06'),('b452b27c-0361-452a-bec9-eb5c2c50b4d8','CLARASERRANO-EXTRANJERO-MAR26 (informaciÃ³n Terrenos SC)','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-14 13:55:12'),('b4d2af65-f645-4313-978a-b6c4387425af','VILLA REAL-PROMOTAZA-MAR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-14 13:48:18'),('b6001e92-335f-4e90-9a3f-502ad0d8e3c3','VILLAREAL-ALCANCE-PAGINAWEB-ABR26','24a8bc01-8c20-4432-a1de-3230b745e084','2026-04-04 13:18:01'),('c179041e-1834-4f26-8555-bb67312519a8','CLARASERRANO-MOFU-ABR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-04-11 15:43:50'),('c26c9c9d-c5c8-4772-8374-4aa783e4911a','VILLAREAL-MOFU-MAY26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-13 20:40:06'),('c63298be-31b4-4766-a25a-dfe18eefb0d7','VILLAREAL-DIADELNIÃ‘O-ABR ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-04-04 13:17:26'),('cf502ff7-fc18-4406-9cf2-7974bb565379','VILLAREAL-REPECHAJE1-MAR26','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-24 19:59:17'),('d94eefa8-b931-4643-aa0e-a0d85f89e1cc','Formulario Regional LPZ','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-16 17:10:27'),('d98711b9-eb1f-45cc-9a25-e1ea684a7379','SIN IDENTIFICAR','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:13:00'),('da4a9ed2-8b6e-4559-af81-028b44cf6348','VILLAREAL-GOFU-MAY26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-19 21:12:10'),('dba107c4-1b4d-48d7-9cdd-00208a70de05','VILLA REAL-EXTRANJERO-MAY26 (Estamos en SC)','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-11 19:10:54'),('dc5f79b8-6e9a-41c1-83d1-cd01a0f768f6','TIK TOK CLARASERRANO-GCP-ABR26','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-05-06 18:19:33'),('e4f0e41c-e62e-4e8c-a639-ca729555c5c3','TIK TOK VILLAREAL-GCP-MAY26','24a8bc01-8c20-4432-a1de-3230b745e084','2026-05-05 15:44:47'),('ef497e7e-1005-40de-9bf9-56f41bc2551a','CLARASERRANO-TOFU-ABR26 ()','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-04-04 16:07:56'),('f43ed32e-b8fc-431d-9f58-df4db529971c','CLARASERRANO-MOFU-FEB26 :P','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-20 13:23:06'),('f65f7f7b-9cb9-489b-8d3d-8c57a78ab1a4','CLARASERRANO-REPECHAJE1-MAR26','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-03-24 19:58:52'),('f995f10f-1b47-4c3c-ad67-d1918098de16','CLARASERRANO-GCP-CHILEUSA-FEB26 (Terrenos en Santa Cruz Bolivia)','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-24 13:28:32'),('fad57377-87b0-4e77-b169-b2d476b9e530','VILLAREAL-COMBOSPADRE-MAR26 ()','24a8bc01-8c20-4432-a1de-3230b745e084','2026-03-04 21:12:45');
/*!40000 ALTER TABLE `campanas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `canales`
--

DROP TABLE IF EXISTS `canales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `canales` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `canales`
--

LOCK TABLES `canales` WRITE;
/*!40000 ALTER TABLE `canales` DISABLE KEYS */;
INSERT INTO `canales` VALUES ('261c72ec-81e8-11f1-bb83-c025a568360e','Facebook','2026-07-17 10:00:19'),('261c7687-81e8-11f1-bb83-c025a568360e','Instagram','2026-07-17 10:00:19'),('261c7a2a-81e8-11f1-bb83-c025a568360e','WhatsApp','2026-07-17 10:00:19'),('261c7b01-81e8-11f1-bb83-c025a568360e','TikTok','2026-07-17 10:00:19'),('261c7ba2-81e8-11f1-bb83-c025a568360e','Referido','2026-07-17 10:00:19'),('261c7c43-81e8-11f1-bb83-c025a568360e','Web','2026-07-17 10:00:19'),('261c7ce6-81e8-11f1-bb83-c025a568360e','Google Ads','2026-07-17 10:00:19'),('36c291a1-2426-4444-8bfc-f3c6715c639e','Oficina central','2026-07-25 17:33:45'),('6937fce7-ee33-439e-827e-4e7dc831683f','Línea 800','2026-07-25 17:34:57');
/*!40000 ALTER TABLE `canales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciudad`
--

DROP TABLE IF EXISTS `ciudad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciudad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ciudad_pais` (`pais_id`),
  CONSTRAINT `fk_ciudad_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudad`
--

LOCK TABLES `ciudad` WRITE;
/*!40000 ALTER TABLE `ciudad` DISABLE KEYS */;
INSERT INTO `ciudad` VALUES (1,'La Paz',1),(2,'Cochabamba',1),(3,'Santa Cruz',1),(4,'Sucre',1),(5,'Oruro',1),(6,'Potosí',1),(7,'Tarija',1),(8,'Beni',1),(9,'Pando',1);
/*!40000 ALTER TABLE `ciudad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES ('24a8bc01-8c20-4432-a1de-3230b745e084','NM','2026-07-20 15:06:10'),('d254a6a7-cbe5-4c0b-85a7-248c81314c5f','Mi lote','2026-07-20 15:06:01');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais_id` int DEFAULT NULL,
  `empresa_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proyecto_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campana_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canal_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mql_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperatura` enum('frio','tibio','caliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'frio',
  `tipo_pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `operador_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendedor_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendedor_tabla_id` int DEFAULT NULL,
  `agente_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `derivado_ventas` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_derivado_ventas` datetime DEFAULT NULL,
  `ejecutivo_ventas` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_lead` date NOT NULL DEFAULT (curdate()),
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_leads_telefono` (`telefono`),
  KEY `idx_leads_operador` (`operador_id`),
  KEY `idx_leads_fecha_lead` (`fecha_lead`),
  KEY `idx_leads_derivado` (`derivado_ventas`,`fecha_derivado_ventas`),
  KEY `fk_lead_empresa` (`empresa_id`),
  KEY `fk_lead_proyecto` (`proyecto_id`),
  KEY `fk_lead_campana` (`campana_id`),
  KEY `fk_lead_canal` (`canal_id`),
  KEY `fk_lead_mql` (`mql_id`),
  KEY `fk_lead_vendedor` (`vendedor_id`),
  KEY `fk_lead_vendtabla` (`vendedor_tabla_id`),
  KEY `fk_lead_agente` (`agente_id`),
  KEY `fk_lead_pais` (`pais_id`),
  CONSTRAINT `fk_lead_agente` FOREIGN KEY (`agente_id`) REFERENCES `agentes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_campana` FOREIGN KEY (`campana_id`) REFERENCES `campanas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_canal` FOREIGN KEY (`canal_id`) REFERENCES `canales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_mql` FOREIGN KEY (`mql_id`) REFERENCES `mql_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_operador` FOREIGN KEY (`operador_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_vendtabla` FOREIGN KEY (`vendedor_tabla_id`) REFERENCES `vendedor` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES ('0a03005c-1cd0-4971-a716-eceac46a1cf9','VIVIAN CABALLERO','76402610',NULL,NULL,NULL,NULL,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f',NULL,'4ef858fe-a615-43d9-8099-7aab4ddee958','261c72ec-81e8-11f1-bb83-c025a568360e',NULL,'frio',NULL,'CAMPAÑA 30% LUJAN SEPTIEMBRE - MI LOTE','8c4f1c6b-29ee-41b4-aede-ca976328d6da',NULL,NULL,NULL,0,NULL,NULL,'2026-09-03','2026-09-03 15:06:53','2026-09-03 15:17:41'),('1f4d747c-0f85-41f6-a61e-2cf6d1a9c4d7','JIMENA','75534672',NULL,NULL,NULL,NULL,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f',NULL,'d98711b9-eb1f-45cc-9a25-e1ea684a7379','261c72ec-81e8-11f1-bb83-c025a568360e','261d0d41-81e8-11f1-bb83-c025a568360e','frio',NULL,'CAMPAÑA 30% LUJAN SEPTIEMBRE - MI LOTE/PREGUNTA POR SU MINUTA','8c4f1c6b-29ee-41b4-aede-ca976328d6da',NULL,NULL,NULL,0,NULL,NULL,'2026-09-03','2026-09-03 14:48:59','2026-09-03 14:55:15'),('5983a837-aff5-432a-946f-ea96cdff3e38','Sin nombre','60890260',NULL,NULL,NULL,1,'24a8bc01-8c20-4432-a1de-3230b745e084',NULL,'d98711b9-eb1f-45cc-9a25-e1ea684a7379','261c7a2a-81e8-11f1-bb83-c025a568360e',NULL,'frio',NULL,'Base de datos/ Cliente solo saluda.','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,NULL,0,NULL,NULL,'2026-07-25','2026-07-25 15:02:19','2026-07-25 15:02:19'),('70994fa1-32f6-4578-b241-ef08bf69ccaa','Tuly','75678531',NULL,NULL,NULL,1,'24a8bc01-8c20-4432-a1de-3230b745e084',NULL,'4ef858fe-a615-43d9-8099-7aab4ddee958',NULL,'261d0d41-81e8-11f1-bb83-c025a568360e','frio',NULL,'Cliente envía mensajes de reflexion','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,NULL,0,NULL,NULL,'2026-09-02','2026-09-02 15:45:47','2026-09-02 15:45:47'),('a0ebcbbf-a493-40bb-a810-879412072ef8','Prueba alex','77777777',NULL,NULL,NULL,NULL,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f',NULL,'4ef858fe-a615-43d9-8099-7aab4ddee958','261c72ec-81e8-11f1-bb83-c025a568360e',NULL,'frio',NULL,'prueba','eef1ceaa-95e8-4d84-bf74-25bce8b53fe4',NULL,NULL,NULL,0,NULL,NULL,'2026-09-03','2026-09-03 15:02:46','2026-09-03 15:02:46'),('a86ff737-a830-489f-9183-3b1a170e9499','Sin nombre','70005426',NULL,NULL,NULL,1,'24a8bc01-8c20-4432-a1de-3230b745e084',NULL,'d98711b9-eb1f-45cc-9a25-e1ea684a7379','261c72ec-81e8-11f1-bb83-c025a568360e',NULL,'frio',NULL,'Se envío información por mensaje de whatsapp,','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,NULL,0,NULL,NULL,'2026-09-02','2026-09-02 15:38:32','2026-09-02 15:38:32'),('aabd14fb-cfe8-42b4-91a4-e641a3c125de','mateo calizaya','72507580',NULL,NULL,NULL,1,'24a8bc01-8c20-4432-a1de-3230b745e084',NULL,'d98711b9-eb1f-45cc-9a25-e1ea684a7379','261c72ec-81e8-11f1-bb83-c025a568360e',NULL,'frio',NULL,'Se envío información por mensaje de whatsapp,','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,NULL,0,NULL,NULL,'2026-09-02','2026-09-02 16:46:41','2026-09-02 16:46:41'),('b943df9f-46d8-41ba-abe8-0f8a291bcc62','Prueba TYU','+591 7777777','prueba@gmail.com',NULL,'Santa Cruz',1,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f','51b69128-9462-45a1-bb95-b8e6352af5e8','30e3a9d1-5fcc-4781-9689-eb9997132a86','261c7b01-81e8-11f1-bb83-c025a568360e','261d0e50-81e8-11f1-bb83-c025a568360e','tibio','Plazo','Prueba','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,'33eec760-adf5-11f1-83c8-080027fab11a',0,NULL,NULL,'2026-09-03','2026-09-03 15:14:43','2026-09-11 15:26:47'),('d0b32959-91d9-4f98-9dd8-a86bcc23943b','Jenny lu','+591 60818476','jeny@gmail.com',NULL,'Cochabamba',1,'24a8bc01-8c20-4432-a1de-3230b745e084','ced707ad-9fbc-4ef6-ac9a-aefb0b98fb8b','5953f53b-cb5a-45cb-91f4-06a01dd79efe','261c72ec-81e8-11f1-bb83-c025a568360e','261d0d41-81e8-11f1-bb83-c025a568360e','tibio','Plazo','Se envío información por mensaje de whatsapp,','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,9,'33eec760-adf5-11f1-83c8-080027fab11a',1,'2026-09-11 17:15:58',NULL,'2026-09-02','2026-09-02 16:50:05','2026-09-11 17:15:58'),('d60165de-1f21-4c98-83bb-713c8109a8c5','MARIA ESTHER DELGADILLO AVILA','72612744','ed@gmail.com',NULL,'SCZ',1,NULL,NULL,NULL,NULL,NULL,'frio',NULL,'TEST11','00000000-0000-0000-0000-000000000A01',NULL,NULL,NULL,0,NULL,NULL,'2026-07-17','2026-07-17 10:39:34','2026-07-17 10:39:46'),('dc7526df-246f-4398-8847-effa5436ad17','javier perez','+54 456456345','javier@gmail.com',NULL,'Buenos Aires',2,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f','76718473-bd03-4f03-8c43-2970a238f744','789e60d9-90ee-4ebb-86da-c21e34a4cd18','261c7687-81e8-11f1-bb83-c025a568360e','261d0e50-81e8-11f1-bb83-c025a568360e','frio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,9,'ceb8a5db-1226-4429-80a5-b719d6df5161',1,'2026-09-11 17:00:31',NULL,'2026-09-11','2026-09-11 15:39:50','2026-09-11 17:00:31'),('ddb1c41f-575a-443f-bd56-5357d543e82a','Mari','68805239',NULL,NULL,NULL,1,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f',NULL,'d98711b9-eb1f-45cc-9a25-e1ea684a7379','261c7a2a-81e8-11f1-bb83-c025a568360e','261d0d41-81e8-11f1-bb83-c025a568360e','frio',NULL,'Cliente se vuelve a contactar para el pago de cuota/ En gestión anterior pregunto por su recibo.','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,NULL,0,NULL,NULL,'2026-07-25','2026-07-25 15:09:20','2026-07-25 15:37:26'),('e73df9e2-9b7c-432a-adce-5c63200e1140','Eddy-Prueba','73667424',NULL,NULL,NULL,1,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f',NULL,'4ef858fe-a615-43d9-8099-7aab4ddee958','36c291a1-2426-4444-8bfc-f3c6715c639e','261d0d41-81e8-11f1-bb83-c025a568360e','frio',NULL,'Pruebas...','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,NULL,NULL,0,NULL,NULL,'2026-07-25','2026-07-25 18:40:27','2026-07-25 18:40:27');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads_redes`
--

DROP TABLE IF EXISTS `leads_redes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads_redes` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais_id` int DEFAULT NULL,
  `ciudad` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proyecto_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campana_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canal_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mql_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperatura` enum('frio','tibio','caliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'frio',
  `tipo_pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `operador_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agente_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_lead` date NOT NULL DEFAULT (curdate()),
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lr_fecha` (`fecha_lead`),
  KEY `idx_lr_operador` (`operador_id`),
  KEY `fk_lr_empresa` (`empresa_id`),
  KEY `fk_lr_proyecto` (`proyecto_id`),
  KEY `fk_lr_campana` (`campana_id`),
  KEY `fk_lr_canal` (`canal_id`),
  KEY `fk_lr_mql` (`mql_id`),
  KEY `fk_lr_agente` (`agente_id`),
  KEY `fk_lr_pais` (`pais_id`),
  CONSTRAINT `fk_lr_agente` FOREIGN KEY (`agente_id`) REFERENCES `agentes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_campana` FOREIGN KEY (`campana_id`) REFERENCES `campanas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_canal` FOREIGN KEY (`canal_id`) REFERENCES `canales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_mql` FOREIGN KEY (`mql_id`) REFERENCES `mql_status` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_operador` FOREIGN KEY (`operador_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lr_proyecto` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads_redes`
--

LOCK TABLES `leads_redes` WRITE;
/*!40000 ALTER TABLE `leads_redes` DISABLE KEYS */;
INSERT INTO `leads_redes` VALUES ('c55f7707-2121-43d1-abb5-16db584ff1dd','Rosa Alemán',NULL,NULL,NULL,NULL,'d254a6a7-cbe5-4c0b-85a7-248c81314c5f',NULL,'d98711b9-eb1f-45cc-9a25-e1ea684a7379','261c72ec-81e8-11f1-bb83-c025a568360e',NULL,'frio',NULL,'Solicita más información.','f7cd943e-dce5-4be3-8ce0-2bfe8584528f',NULL,'2026-07-25','2026-07-25 17:37:59','2026-07-25 17:37:59');
/*!40000 ALTER TABLE `leads_redes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mql_status`
--

DROP TABLE IF EXISTS `mql_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mql_status` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nivel` (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mql_status`
--

LOCK TABLES `mql_status` WRITE;
/*!40000 ALTER TABLE `mql_status` DISABLE KEYS */;
INSERT INTO `mql_status` VALUES ('261d0a88-81e8-11f1-bb83-c025a568360e','MQL1 - ALTO','2026-07-17 10:00:19'),('261d0d41-81e8-11f1-bb83-c025a568360e','MQL2 - MALO','2026-07-17 10:00:19'),('261d0e50-81e8-11f1-bb83-c025a568360e','MQL3 - MEDIO','2026-07-17 10:00:19'),('261d0ece-81e8-11f1-bb83-c025a568360e','MQL4','2026-07-17 10:00:19');
/*!40000 ALTER TABLE `mql_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paises` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Bolivia','2026-07-17 10:00:19'),(2,'Argentina','2026-07-17 10:00:19'),(3,'Chile','2026-07-17 10:00:19'),(4,'Perú','2026-07-17 10:00:19'),(5,'Colombia','2026-07-17 10:00:19'),(6,'México','2026-07-17 10:00:19'),(7,'Ecuador','2026-07-17 10:00:19'),(8,'Uruguay','2026-07-17 10:00:19'),(9,'Paraguay','2026-07-17 10:00:19'),(10,'España','2026-07-17 10:00:19');
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` VALUES ('00000000-0000-0000-0000-000000000A01','00000000-0000-0000-0000-000000000001','Administrador','admin@crm.local',1,'2026-07-17 10:00:19','2026-07-17 10:00:19'),('0cee599c-fe66-4e52-ba79-b8c1d6b3e286','d1abdc29-b2f2-4706-a24e-5e3054a87fbf','Maria Jose Justiniano','jjustiniano@gestiona.bo',1,'2026-09-03 14:54:19','2026-09-03 14:54:19'),('8c4f1c6b-29ee-41b4-aede-ca976328d6da','3605ea69-3ada-4049-977e-703a693dc569','Sdenka Roca','sroca@gestiona.bo',1,'2026-08-31 14:49:31','2026-08-31 14:49:31'),('9dbbd728-8dd1-4089-80b3-7bef17c142b0','d4917d54-ea95-46b5-a29d-d3032b93b1e7','Juan De Dios Rodriguez','jrodriguez@gestiona.bo',1,'2026-09-03 14:58:01','2026-09-03 14:58:01'),('eef1ceaa-95e8-4d84-bf74-25bce8b53fe4','5d289ca7-e0f7-4349-a5c3-a2d30ce5b955','Alexander Ledezma','aledezma@gestiona.bo',1,'2026-07-20 09:27:45','2026-07-20 09:27:45'),('f7cd943e-dce5-4be3-8ce0-2bfe8584528f','81a8b1c6-af3c-4d5b-aacf-0676dad0a08c','Eddy Aguilar','eaguilar@gestiona.bo',1,'2026-07-20 09:28:51','2026-07-20 09:28:51');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proyectos` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_proy_empresa` (`empresa_id`),
  CONSTRAINT `fk_proy_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyectos`
--

LOCK TABLES `proyectos` WRITE;
/*!40000 ALTER TABLE `proyectos` DISABLE KEYS */;
INSERT INTO `proyectos` VALUES ('1ebe6837-b5c1-4230-9048-e7d697d18af6','CS Los Mangales','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:17:32'),('319cd108-427d-46a4-abfe-adafefa8c2c9','Villa Real V','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-10 16:16:02'),('51b69128-9462-45a1-bb95-b8e6352af5e8','CS Villa Real III','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:18:52'),('60c23a7d-76b2-4696-9e28-0969c12abab8','Villa Real IV','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-10 16:14:52'),('71353d0c-708d-493b-a1a7-73ea4bc91e9d','CS Villa Real IV','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:19:27'),('728c2eb9-08fa-4ba1-8c1a-59639b128e88','CS Cosorio','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:16:53'),('76718473-bd03-4f03-8c43-2970a238f744','CS Los Tajibos','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:17:08'),('7cb4be15-2d5f-43aa-aec7-9cae1a19f777','CS Las Lomas','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:18:20'),('bcbe7c75-d370-497e-9676-5b79a90ca44e','CS Villa Real V','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:19:16'),('be83ad2e-4b35-40b8-959e-47a2ace69cef','Villa Real III','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-10 16:14:41'),('ced707ad-9fbc-4ef6-ac9a-aefb0b98fb8b','Villa Real II','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-10 16:14:33'),('d6abcf7f-ecda-4b2a-8ef5-b22e1d893dfb','CS San Silvestre','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-07 02:50:03'),('de0be0a7-855f-4b02-9cbd-b5baeca795e0','Villa Real 1','24a8bc01-8c20-4432-a1de-3230b745e084','2026-02-09 19:01:45'),('e2e1f5c4-2bdf-4c4b-bca6-336cabfc2453','CS Virgen de Lujan','d254a6a7-cbe5-4c0b-85a7-248c81314c5f','2026-02-10 16:17:22');
/*!40000 ALTER TABLE `proyectos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento_ventas`
--

DROP TABLE IF EXISTS `seguimiento_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_ventas` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendedor_tabla_id` int NOT NULL,
  `estado` enum('pendiente','visita','venta','perdido') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_visita` date DEFAULT NULL,
  `fecha_venta` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sv_lead` (`lead_id`),
  KEY `fk_sv_vend` (`vendedor_tabla_id`),
  CONSTRAINT `fk_sv_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sv_vend` FOREIGN KEY (`vendedor_tabla_id`) REFERENCES `vendedor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento_ventas`
--

LOCK TABLES `seguimiento_ventas` WRITE;
/*!40000 ALTER TABLE `seguimiento_ventas` DISABLE KEYS */;
/*!40000 ALTER TABLE `seguimiento_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimientos`
--

DROP TABLE IF EXISTS `seguimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimientos` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iteracion` tinyint NOT NULL,
  `estado` enum('contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_proximo_contacto` datetime DEFAULT NULL,
  `google_event_id` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_iter` (`lead_id`,`iteracion`),
  CONSTRAINT `fk_seg_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_iter` CHECK ((`iteracion` between 1 and 7))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimientos`
--

LOCK TABLES `seguimientos` WRITE;
/*!40000 ALTER TABLE `seguimientos` DISABLE KEYS */;
INSERT INTO `seguimientos` VALUES ('30894a1a-3fb9-4a36-8d2e-8e52c0803a17','d0b32959-91d9-4f98-9dd8-a86bcc23943b',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-02 16:50:05'),('39a078d5-debe-4d15-aad4-c22248014219','e73df9e2-9b7c-432a-adce-5c63200e1140',4,'se_envio_mensaje','Mensaje enviado registrado desde la edición del lead.',NULL,NULL,'2026-08-31 16:10:30'),('548adeb9-25bc-4048-9003-c55c6d9cdf56','b943df9f-46d8-41ba-abe8-0f8a291bcc62',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-03 15:14:43'),('5e3c3845-86f1-4e97-af38-f402be77df12','e73df9e2-9b7c-432a-adce-5c63200e1140',1,'se_envio_mensaje','Prueba 1','2026-07-28 18:40:00',NULL,'2026-07-25 18:40:45'),('63ea857f-ca74-4f58-be9d-f5c9de460371','1f4d747c-0f85-41f6-a61e-2cf6d1a9c4d7',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-03 14:48:59'),('8930df7a-7785-4efa-b5b9-d064d0f105e4','ddb1c41f-575a-443f-bd56-5357d543e82a',1,'se_envio_mensaje','Cliente se vuelve a contactar/ En gestión anterior preguntó por su recibo.','2026-07-28 15:09:00',NULL,'2026-07-25 15:09:50'),('8d05f8ca-c6c9-4239-bb02-e7188ca57b4d','70994fa1-32f6-4578-b241-ef08bf69ccaa',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-02 15:45:47'),('9e335834-121d-4370-8c7f-f0226401213e','a0ebcbbf-a493-40bb-a810-879412072ef8',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-03 15:02:46'),('a184611a-b98e-4234-957b-66145472907a','a86ff737-a830-489f-9183-3b1a170e9499',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-02 15:38:33'),('bff4b1b9-4398-4dc8-af21-9585d1c533d0','e73df9e2-9b7c-432a-adce-5c63200e1140',2,'se_envio_mensaje','Prueba 2','2026-07-28 18:40:00',NULL,'2026-07-25 18:40:58'),('c2386922-55b8-49ce-8bea-422b4c7f9e5c','aabd14fb-cfe8-42b4-91a4-e641a3c125de',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-09-02 16:46:41'),('c3f04c29-3799-4041-b32c-8e609f958297','e73df9e2-9b7c-432a-adce-5c63200e1140',5,'se_envio_mensaje','Prueba 31/08/2026','2026-09-04 16:11:00',NULL,'2026-08-31 16:11:27'),('c6e903b2-d0c0-4339-aab8-ca3302c29d75','e73df9e2-9b7c-432a-adce-5c63200e1140',3,'se_envio_mensaje','Prueba 3','2026-07-28 18:42:00',NULL,'2026-07-25 18:42:19');
/*!40000 ALTER TABLE `seguimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimientos_redes`
--

DROP TABLE IF EXISTS `seguimientos_redes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimientos_redes` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iteracion` tinyint NOT NULL,
  `estado` enum('contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_proximo_contacto` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lr_iter` (`lead_id`,`iteracion`),
  CONSTRAINT `fk_segr_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads_redes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_iter_r` CHECK ((`iteracion` between 1 and 7))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimientos_redes`
--

LOCK TABLES `seguimientos_redes` WRITE;
/*!40000 ALTER TABLE `seguimientos_redes` DISABLE KEYS */;
/*!40000 ALTER TABLE `seguimientos_redes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','supervisor','operador','vendedor','marketing') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_role` (`user_id`,`role`),
  CONSTRAINT `fk_role_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES ('00000000-0000-0000-0000-000000000B01','00000000-0000-0000-0000-000000000001','admin','2026-07-17 10:00:19'),('07c801ab-1f4b-4a95-b7b2-5f84a7ef9a5f','5d289ca7-e0f7-4349-a5c3-a2d30ce5b955','admin','2026-07-20 09:27:45'),('39811fb5-0469-4ccf-9791-160880ea1711','d4917d54-ea95-46b5-a29d-d3032b93b1e7','vendedor','2026-09-03 14:58:01'),('8a67dfe1-ea29-450f-a6cd-25d725a1200b','81a8b1c6-af3c-4d5b-aacf-0676dad0a08c','operador','2026-07-20 09:28:51'),('d1898007-3db4-45ce-9b94-f6d6d749bf39','d1abdc29-b2f2-4706-a24e-5e3054a87fbf','vendedor','2026-09-03 14:54:19'),('e5ced0fc-af69-4577-accd-b5d7656f2441','3605ea69-3ada-4049-977e-703a693dc569','operador','2026-08-31 14:49:31');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('00000000-0000-0000-0000-000000000001','admin@crm.local','$2y$10$LCgyCqwgr3pawgHoij6N6uP6TI0KUgIzu8bgtfXc8EmJ5HJxeOf1y','2026-07-17 10:00:19','2026-09-11 15:00:49'),('3605ea69-3ada-4049-977e-703a693dc569','sroca@gestiona.bo','$2y$10$nM8z58fn4tPn1EOWGbzz8uJ0UABuZEHd3wVJFjTQ2M2o6X39QY/1W','2026-08-31 14:49:31','2026-08-31 14:49:31'),('5d289ca7-e0f7-4349-a5c3-a2d30ce5b955','aledezma@gestiona.bo','$2y$10$pedjIORm1PWFIihY0hBne.smsN2eNUBiv.idJ78T9fF4AbiJGwq2i','2026-07-20 09:27:45','2026-07-20 09:27:45'),('81a8b1c6-af3c-4d5b-aacf-0676dad0a08c','eaguilar@gestiona.bo','$2y$10$LCgyCqwgr3pawgHoij6N6uP6TI0KUgIzu8bgtfXc8EmJ5HJxeOf1y','2026-07-20 09:28:51','2026-09-11 14:55:00'),('d1abdc29-b2f2-4706-a24e-5e3054a87fbf','jjustiniano@gestiona.bo','$2y$10$f0FV6qFO4fPFJ/LwuYoIiulr2aeuGNV2D17LgWtI5RRx/va9KAUWK','2026-09-03 14:54:19','2026-09-03 14:54:19'),('d4917d54-ea95-46b5-a29d-d3032b93b1e7','jrodriguez@gestiona.bo','$2y$10$LMLF3G3/tyvajEHDegPONOzXQ/7ObXTZ7ezu9uRUTI18fiuCYqZQm','2026-09-03 14:58:01','2026-09-03 14:58:01');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendedor`
--

DROP TABLE IF EXISTS `vendedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendedor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vend_profile` (`profile_id`),
  CONSTRAINT `fk_vend_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendedor`
--

LOCK TABLES `vendedor` WRITE;
/*!40000 ALTER TABLE `vendedor` DISABLE KEYS */;
INSERT INTO `vendedor` VALUES (9,'Vendedor Sincronizado #9',NULL);
/*!40000 ALTER TABLE `vendedor` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-11 17:23:24
