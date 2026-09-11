-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: crm_db
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
  `id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `profile_id` char(36) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_agente_profile` (`profile_id`),
  CONSTRAINT `fk_agente_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentes`
--

LOCK TABLES `agentes` WRITE;
/*!40000 ALTER TABLE `agentes` DISABLE KEYS */;
INSERT INTO `agentes` VALUES ('34a0c863-335a-41ce-b174-f75de07397b6','Ruben Lopez',NULL,1,'2026-08-27 16:58:57'),('aada5d49-1a85-4edb-9b23-0307ec80e939','Lucia Rojas',NULL,1,'2026-08-27 16:59:14'),('d0af6808-98c9-4660-a48a-5e05569a7c8a','Javier Diaz',NULL,1,'2026-08-26 16:07:31');
/*!40000 ALTER TABLE `agentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campanas`
--

DROP TABLE IF EXISTS `campanas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campanas` (
  `id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `empresa_id` char(36) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_camp_empresa` (`empresa_id`),
  CONSTRAINT `fk_camp_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campanas`
--

LOCK TABLES `campanas` WRITE;
/*!40000 ALTER TABLE `campanas` DISABLE KEYS */;
INSERT INTO `campanas` VALUES ('10406bf4-ddeb-48e1-8a5e-5934419002cb','Routers','e1aad448-43eb-4871-b5c3-45b074b8a392','2026-09-08 15:46:30'),('3c90120a-671a-49a9-90cc-be9e3094ef38','Internet por Fibra Optica','e1aad448-43eb-4871-b5c3-45b074b8a392','2026-08-27 17:04:10'),('4ec82ce8-9cf0-4de2-8150-c5ff4ba17e28','telefonos gratis','4ac74b9a-388a-4415-a978-5c98d8cad44f','2026-09-07 17:45:04'),('5ad4174a-dd96-4887-8b98-d5d1a8bbe450','Canales por Internet','4ac74b9a-388a-4415-a978-5c98d8cad44f','2026-08-26 16:06:30'),('ba9707f1-c1b6-43f8-9b37-f69f5baa3f73','antenas gratis','4ac74b9a-388a-4415-a978-5c98d8cad44f','2026-09-07 17:45:21');
/*!40000 ALTER TABLE `campanas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `canales`
--

DROP TABLE IF EXISTS `canales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `canales` (
  `id` char(36) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `canales`
--

LOCK TABLES `canales` WRITE;
/*!40000 ALTER TABLE `canales` DISABLE KEYS */;
INSERT INTO `canales` VALUES ('5b1e6352-a15f-11f1-98d7-080027fab11a','Facebook','2026-08-26 15:03:54'),('5b1e6a63-a15f-11f1-98d7-080027fab11a','Instagram','2026-08-26 15:03:54'),('5b1e7fc8-a15f-11f1-98d7-080027fab11a','WhatsApp','2026-08-26 15:03:54'),('5b1e9e5a-a15f-11f1-98d7-080027fab11a','TikTok','2026-08-26 15:03:54'),('5b1ea242-a15f-11f1-98d7-080027fab11a','Referido','2026-08-26 15:03:54'),('5b1ea33e-a15f-11f1-98d7-080027fab11a','Web','2026-08-26 15:03:54'),('5b1f223b-a15f-11f1-98d7-080027fab11a','Google Ads','2026-08-26 15:03:54');
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
  `nombre` varchar(100) NOT NULL,
  `pais_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ciudad_pais` (`pais_id`),
  CONSTRAINT `fk_ciudad_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES ('4ac74b9a-388a-4415-a978-5c98d8cad44f','Entel','2026-08-26 16:04:40'),('e1aad448-43eb-4871-b5c3-45b074b8a392','Tigo','2026-08-27 17:00:21');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `pais` varchar(150) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `pais_id` int DEFAULT NULL,
  `empresa_id` char(36) DEFAULT NULL,
  `proyecto_id` char(36) DEFAULT NULL,
  `campana_id` char(36) DEFAULT NULL,
  `canal_id` char(36) DEFAULT NULL,
  `mql_id` char(36) DEFAULT NULL,
  `temperatura` enum('frio','tibio','caliente') NOT NULL DEFAULT 'frio',
  `tipo_pago` varchar(50) DEFAULT NULL,
  `comentario` text,
  `operador_id` char(36) DEFAULT NULL,
  `vendedor_id` char(36) DEFAULT NULL,
  `vendedor_tabla_id` int DEFAULT NULL,
  `agente_id` char(36) DEFAULT NULL,
  `derivado_ventas` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_derivado_ventas` datetime DEFAULT NULL,
  `ejecutivo_ventas` varchar(190) DEFAULT NULL,
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
  CONSTRAINT `fk_lead_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES ('0098a3ed-7da3-485d-8a98-2742bd3da250','joaquin risas','545467878','joaquin@gmail.com',NULL,'Santiago',3,NULL,NULL,NULL,'5b1e6a63-a15f-11f1-98d7-080027fab11a','5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'816461bb-5e2e-409d-bc99-2eca98350e4f',NULL,816461,NULL,1,'2026-09-09 19:31:49',NULL,'2026-09-09','2026-09-09 18:44:37','2026-09-09 19:31:49'),('19941341-873e-4bb4-a013-30891a79fb7e','Fabian Rojas','645548211','fabian@gmail.com',NULL,NULL,2,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e6a63-a15f-11f1-98d7-080027fab11a','5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'d0af6808-98c9-4660-a48a-5e05569a7c8a',0,NULL,NULL,'2026-08-26','2026-08-26 17:03:05','2026-08-26 17:03:05'),('363cdc55-18ff-4fc6-ba8e-a4bf4386df49','gregorio ogro','4545648','gregorio@gmail.com',NULL,'Madrid',10,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1ea33e-a15f-11f1-98d7-080027fab11a','5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'816461bb-5e2e-409d-bc99-2eca98350e4f',NULL,816461,NULL,1,'2026-09-09 18:48:50',NULL,'2026-09-09','2026-09-09 18:48:50','2026-09-09 18:48:50'),('39e5ff64-cca6-4150-bd52-a222b4ff0b62','casemiro franco','71521515','casemiro@gmail.com',NULL,'Santa Cruz',1,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','ba9707f1-c1b6-43f8-9b37-f69f5baa3f73','5b1e6352-a15f-11f1-98d7-080027fab11a','5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,816461,'d0af6808-98c9-4660-a48a-5e05569a7c8a',1,'2026-09-09 17:45:17',NULL,'2026-09-09','2026-09-09 17:45:17','2026-09-09 17:45:17'),('4ce68a2c-29ed-4c99-a89e-f92fd609fbfd','Isaias Moron','8655122211','isaias@gmail.com',NULL,'La Paz',1,'e1aad448-43eb-4871-b5c3-45b074b8a392','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','3c90120a-671a-49a9-90cc-be9e3094ef38','5b1e6a63-a15f-11f1-98d7-080027fab11a','5b21c575-a15f-11f1-98d7-080027fab11a','frio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,816461,'34a0c863-335a-41ce-b174-f75de07397b6',1,'2026-08-28 14:51:43',NULL,'2026-08-28','2026-08-28 13:00:18','2026-08-28 14:51:43'),('5674a881-5d5c-4c48-a6f1-d8dde36961a6','lionel mesi','464416874','mesi@futbol.com',NULL,'Buenos Aires',2,NULL,NULL,NULL,NULL,NULL,'frio',NULL,NULL,'00000000-0000-0000-0000-000000000A01',NULL,563,NULL,1,'2026-09-09 19:03:17',NULL,'2026-09-09','2026-09-09 19:03:17','2026-09-09 19:03:17'),('57bc2612-d0e1-4f71-bb93-b66e10c49819','teresa lucxas','45645644','teresa@gmail.com',NULL,'Buenos Aires',2,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e6352-a15f-11f1-98d7-080027fab11a','5b21b5c1-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'816461bb-5e2e-409d-bc99-2eca98350e4f',NULL,816461,NULL,1,'2026-09-09 18:42:11',NULL,'2026-09-09','2026-09-09 18:42:11','2026-09-09 18:42:11'),('6a5bfe82-464b-4c5f-b621-d69f3bc813dd','Marcos Perez','64551122','marcos@gmail.com',NULL,'Tarija',2,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e9e5a-a15f-11f1-98d7-080027fab11a','5b21c575-a15f-11f1-98d7-080027fab11a','frio','Contado',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'d0af6808-98c9-4660-a48a-5e05569a7c8a',0,NULL,NULL,'2026-08-26','2026-08-26 18:58:44','2026-08-26 18:58:44'),('76b74c35-5104-4025-8abd-e6d23e6e1f45','karla Ortiz','78547845','karla@gmail.com',NULL,'Oruro',1,'e1aad448-43eb-4871-b5c3-45b074b8a392','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e6a63-a15f-11f1-98d7-080027fab11a','5b21b5c1-a15f-11f1-98d7-080027fab11a','caliente','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,816461,'aada5d49-1a85-4edb-9b23-0307ec80e939',1,'2026-08-28 14:26:54',NULL,'2026-08-28','2026-08-28 13:02:21','2026-08-28 14:26:54'),('842b9206-c76c-4df3-a25f-6ab68eae92b1','Rolando Rojas','72154545','rolando@gmail.com',NULL,'Santa Cruz',1,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e6352-a15f-11f1-98d7-080027fab11a','5b21c575-a15f-11f1-98d7-080027fab11a','caliente','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'d0af6808-98c9-4660-a48a-5e05569a7c8a',0,NULL,NULL,'2026-08-26','2026-08-26 16:10:54','2026-08-26 16:10:54'),('993efc00-a258-4f1e-8d5c-594f93f65af9','Carmen Rios','75645452','carmen@gmail.com',NULL,'Quito',7,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e9e5a-a15f-11f1-98d7-080027fab11a','5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'d0af6808-98c9-4660-a48a-5e05569a7c8a',0,NULL,NULL,'2026-08-27','2026-08-27 16:06:24','2026-08-27 16:06:24'),('a615f819-b648-46e7-9815-c31a431af781','cr7','87542121','cr7@gmail.com',NULL,'Buenos Aires',2,NULL,NULL,NULL,NULL,'5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,816461,NULL,1,'2026-09-09 18:59:41',NULL,'2026-09-09','2026-09-09 18:59:41','2026-09-09 18:59:41'),('c962550c-ab01-4a14-8c51-4115c533f7bc','Filomeno Rios','66461321','filomeno@gmail.com',NULL,'Potosí',1,'4ac74b9a-388a-4415-a978-5c98d8cad44f','20b5d382-cd66-46bc-aaa8-3d09156846d9','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e6352-a15f-11f1-98d7-080027fab11a','5b21b5c1-a15f-11f1-98d7-080027fab11a','caliente','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'aada5d49-1a85-4edb-9b23-0307ec80e939',0,NULL,NULL,'2026-08-28','2026-08-28 18:20:41','2026-08-28 20:39:46'),('dd7b5950-d520-4d15-b6f5-908f210eead6','Gabriel Rios','64542121','gabriel@gmail.co',NULL,'Madrid',10,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e7fc8-a15f-11f1-98d7-080027fab11a','5b21c575-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'d0af6808-98c9-4660-a48a-5e05569a7c8a',0,NULL,NULL,'2026-08-27','2026-08-27 16:20:52','2026-08-27 18:43:43'),('e32da6c5-26fb-4cd7-bdf3-e6eff10bddcc','Hernan','68787737','hernan.redes63@gmail.com',NULL,'Santa Cruz',1,'4ac74b9a-388a-4415-a978-5c98d8cad44f','099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','5ad4174a-dd96-4887-8b98-d5d1a8bbe450','5b1e6352-a15f-11f1-98d7-080027fab11a','5b21be27-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01',NULL,NULL,'d0af6808-98c9-4660-a48a-5e05569a7c8a',0,NULL,NULL,'2026-08-26','2026-08-26 16:09:27','2026-08-26 16:09:27');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_leads_prevent_dup_derivation` BEFORE UPDATE ON `leads` FOR EACH ROW BEGIN
    IF NEW.derivado_ventas = 1
       AND OLD.derivado_ventas = 1
       AND OLD.vendedor_tabla_id IS NOT NULL
       AND NEW.vendedor_tabla_id <> OLD.vendedor_tabla_id THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Este lead ya fue derivado a ventas. No se puede derivar nuevamente.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `leads_redes`
--

DROP TABLE IF EXISTS `leads_redes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads_redes` (
  `id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `pais_id` int DEFAULT NULL,
  `ciudad` varchar(150) DEFAULT NULL,
  `empresa_id` char(36) DEFAULT NULL,
  `proyecto_id` char(36) DEFAULT NULL,
  `campana_id` char(36) DEFAULT NULL,
  `canal_id` char(36) DEFAULT NULL,
  `mql_id` char(36) DEFAULT NULL,
  `temperatura` enum('frio','tibio','caliente') NOT NULL DEFAULT 'frio',
  `tipo_pago` varchar(50) DEFAULT NULL,
  `comentario` text,
  `operador_id` char(36) DEFAULT NULL,
  `agente_id` char(36) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads_redes`
--

LOCK TABLES `leads_redes` WRITE;
/*!40000 ALTER TABLE `leads_redes` DISABLE KEYS */;
INSERT INTO `leads_redes` VALUES ('4c0d1784-1229-46cc-9508-4d854be38641','Marcelo Cabrera','68465421','marcelo@gmail.com',9,'Asunción','e1aad448-43eb-4871-b5c3-45b074b8a392','20b5d382-cd66-46bc-aaa8-3d09156846d9','3c90120a-671a-49a9-90cc-be9e3094ef38','5b1e6352-a15f-11f1-98d7-080027fab11a','5b21c575-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01','34a0c863-335a-41ce-b174-f75de07397b6','2026-08-27','2026-08-27 17:25:01','2026-08-27 17:26:47'),('e4cccbd6-02ed-489c-a38b-458951dc999d','Joel Rojas','66645613','joel@gmail.com',10,'Madrid','e1aad448-43eb-4871-b5c3-45b074b8a392','20b5d382-cd66-46bc-aaa8-3d09156846d9','3c90120a-671a-49a9-90cc-be9e3094ef38','5b1e6a63-a15f-11f1-98d7-080027fab11a','5b21c575-a15f-11f1-98d7-080027fab11a','tibio','Plazo',NULL,'00000000-0000-0000-0000-000000000A01','34a0c863-335a-41ce-b174-f75de07397b6','2026-08-27','2026-08-27 17:23:57','2026-08-27 17:23:57');
/*!40000 ALTER TABLE `leads_redes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mql_status`
--

DROP TABLE IF EXISTS `mql_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mql_status` (
  `id` char(36) NOT NULL,
  `nivel` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nivel` (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mql_status`
--

LOCK TABLES `mql_status` WRITE;
/*!40000 ALTER TABLE `mql_status` DISABLE KEYS */;
INSERT INTO `mql_status` VALUES ('5b21b5c1-a15f-11f1-98d7-080027fab11a','Alto','2026-08-26 15:03:54'),('5b21be27-a15f-11f1-98d7-080027fab11a','Medio','2026-08-26 15:03:54'),('5b21c575-a15f-11f1-98d7-080027fab11a','Malo','2026-08-26 15:03:54');
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
  `nombre` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Bolivia','2026-08-26 15:03:54'),(2,'Argentina','2026-08-26 15:03:54'),(3,'Chile','2026-08-26 15:03:54'),(4,'Perú','2026-08-26 15:03:54'),(5,'Colombia','2026-08-26 15:03:54'),(6,'México','2026-08-26 15:03:54'),(7,'Ecuador','2026-08-26 15:03:54'),(8,'Uruguay','2026-08-26 15:03:54'),(9,'Paraguay','2026-08-26 15:03:54'),(10,'España','2026-08-26 15:03:54'),(11,'Estados Unidos','2026-08-27 15:56:05');
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` VALUES ('00000000-0000-0000-0000-000000000A01','00000000-0000-0000-0000-000000000001','Administrador','admin@crm.local',1,'2026-08-26 15:03:54','2026-08-26 15:03:54'),('04e0946c-8078-4129-90e0-77a1b7a65db8','48ff8255-4286-498d-bb52-06f9fdbd4b64','net flanders','net@gmail.com',1,'2026-09-09 17:50:53','2026-09-09 17:50:53'),('186e3093-f510-499f-9d55-ef3a4e318002','3929cb19-c418-46ef-94c4-2e4828ad1fb1','hernan','hernan.redes63@gmail.com',1,'2026-08-28 13:14:53','2026-08-28 13:14:53'),('563f8ed7-b124-4bce-81e0-ca752a330835','e8569217-c195-49ed-8d06-5f077cb157b1','homero simpson','homero@gmail.com',1,'2026-09-09 17:50:07','2026-09-09 17:50:07'),('816461bb-5e2e-409d-bc99-2eca98350e4f','ce0a3353-b85f-4679-83d0-a9b50163f49c','Carlos Camacho','carlos@gmail.com',1,'2026-08-28 14:03:05','2026-08-28 14:03:05'),('98a8f91d-d920-4336-85b6-406730ce73db','4980ddcf-f503-4cd9-ad74-69d20da0b57e','manuel','manuel@gmail.com',1,'2026-09-07 16:00:42','2026-09-07 16:00:42'),('e82f2f4f-f84b-4f96-aa46-85c2c86e5d03','18b9e86e-6a95-462e-b703-50aafd16ea37','roger','roger@gmail.com',1,'2026-09-07 15:24:33','2026-09-07 15:24:33');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proyectos` (
  `id` char(36) NOT NULL,
  `nombre` varchar(190) NOT NULL,
  `empresa_id` char(36) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_proy_empresa` (`empresa_id`),
  CONSTRAINT `fk_proy_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyectos`
--

LOCK TABLES `proyectos` WRITE;
/*!40000 ALTER TABLE `proyectos` DISABLE KEYS */;
INSERT INTO `proyectos` VALUES ('099a7b4f-ce0a-45b0-b1e7-60ef780b4f0d','TV Internet','4ac74b9a-388a-4415-a978-5c98d8cad44f','2026-08-26 16:05:21'),('20b5d382-cd66-46bc-aaa8-3d09156846d9','Fibra Optica','e1aad448-43eb-4871-b5c3-45b074b8a392','2026-08-27 17:03:35');
/*!40000 ALTER TABLE `proyectos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento_ventas`
--

DROP TABLE IF EXISTS `seguimiento_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_ventas` (
  `id` char(36) NOT NULL,
  `lead_id` char(36) NOT NULL,
  `vendedor_tabla_id` int NOT NULL,
  `estado` enum('pendiente','visita','venta','perdido') NOT NULL DEFAULT 'pendiente',
  `comentario` text,
  `fecha_visita` date DEFAULT NULL,
  `fecha_venta` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sv_lead` (`lead_id`),
  KEY `fk_sv_vend` (`vendedor_tabla_id`),
  CONSTRAINT `fk_sv_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sv_vend` FOREIGN KEY (`vendedor_tabla_id`) REFERENCES `vendedor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `id` char(36) NOT NULL,
  `lead_id` char(36) NOT NULL,
  `iteracion` tinyint NOT NULL,
  `estado` enum('contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje') NOT NULL,
  `comentario` text,
  `fecha_proximo_contacto` datetime DEFAULT NULL,
  `google_event_id` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_iter` (`lead_id`,`iteracion`),
  CONSTRAINT `fk_seg_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_iter` CHECK ((`iteracion` between 1 and 7))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimientos`
--

LOCK TABLES `seguimientos` WRITE;
/*!40000 ALTER TABLE `seguimientos` DISABLE KEYS */;
INSERT INTO `seguimientos` VALUES ('01123e72-e998-4eec-9a68-3f0881f4bab7','6a5bfe82-464b-4c5f-b621-d69f3bc813dd',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-08-26 18:58:45'),('01acbf62-1d71-4387-a786-45c7b029f825','993efc00-a258-4f1e-8d5c-594f93f65af9',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-08-27 16:06:24'),('30f32f24-c360-4c9d-88ff-e0216f46b6a8','dd7b5950-d520-4d15-b6f5-908f210eead6',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-08-27 16:20:53'),('44e67b88-7e03-47ec-8eac-2dc464c93c16','c962550c-ab01-4a14-8c51-4115c533f7bc',3,'contesto','test22',NULL,NULL,'2026-08-28 20:51:46'),('9e131577-dd4c-498c-a1c4-12a481d60f75','76b74c35-5104-4025-8abd-e6d23e6e1f45',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-08-28 13:02:21'),('a82e8b33-0b9d-4c66-9e46-c11058bc4fc4','76b74c35-5104-4025-8abd-e6d23e6e1f45',2,'se_envio_mensaje','Mensaje enviado registrado desde la edición del lead.',NULL,NULL,'2026-08-28 13:18:23'),('cb590600-49d2-4651-a512-2509886f5246','4ce68a2c-29ed-4c99-a89e-f92fd609fbfd',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-08-28 13:00:18'),('d7e00509-4dd2-41bc-80a8-8eb743bdb3c7','c962550c-ab01-4a14-8c51-4115c533f7bc',2,'contesto','dsfdsfs',NULL,NULL,'2026-08-28 20:51:30'),('eea77faf-f20f-45d4-8668-07b9deae9363','c962550c-ab01-4a14-8c51-4115c533f7bc',1,'se_envio_mensaje','Mensaje enviado registrado desde el formulario principal.',NULL,NULL,'2026-08-28 18:20:41');
/*!40000 ALTER TABLE `seguimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimientos_redes`
--

DROP TABLE IF EXISTS `seguimientos_redes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimientos_redes` (
  `id` char(36) NOT NULL,
  `lead_id` char(36) NOT NULL,
  `iteracion` tinyint NOT NULL,
  `estado` enum('contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje') NOT NULL,
  `comentario` text,
  `fecha_proximo_contacto` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lr_iter` (`lead_id`,`iteracion`),
  CONSTRAINT `fk_segr_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads_redes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_iter_r` CHECK ((`iteracion` between 1 and 7))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `role` enum('admin','supervisor','operador','vendedor','marketing') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_role` (`user_id`,`role`),
  CONSTRAINT `fk_role_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES ('00000000-0000-0000-0000-000000000B01','00000000-0000-0000-0000-000000000001','admin','2026-08-26 15:03:54'),('1fda8486-9209-43b9-bc81-9664b7f40573','18b9e86e-6a95-462e-b703-50aafd16ea37','operador','2026-09-07 15:24:33'),('60a4a016-862b-4985-8b6f-9d612131cbd5','ce0a3353-b85f-4679-83d0-a9b50163f49c','vendedor','2026-08-28 14:03:05'),('78729562-d182-4fa3-8156-b0f9f20fe13c','e8569217-c195-49ed-8d06-5f077cb157b1','vendedor','2026-09-09 17:50:07'),('b064482d-aaff-4f84-a807-9311560ef318','48ff8255-4286-498d-bb52-06f9fdbd4b64','vendedor','2026-09-09 17:50:53'),('b2ea641d-d524-422c-86e6-fd3539c1e7ba','3929cb19-c418-46ef-94c4-2e4828ad1fb1','admin','2026-08-28 13:14:53'),('f2a43ceb-c8f2-4f6f-bcca-4c651ab31716','4980ddcf-f503-4cd9-ad74-69d20da0b57e','supervisor','2026-09-07 16:00:42');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('00000000-0000-0000-0000-000000000001','admin@crm.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-08-26 15:03:54','2026-08-26 15:45:00'),('18b9e86e-6a95-462e-b703-50aafd16ea37','roger@gmail.com','$2y$10$51cuWLWY3Sitcb768.BrmO.TRO5KO0PAr2utc3YsNRG.8qhS56jb2','2026-09-07 15:24:33','2026-09-07 15:24:33'),('3929cb19-c418-46ef-94c4-2e4828ad1fb1','hernan.redes63@gmail.com','$2y$12$al2IAmlNRvqPtU0MwhQYw.BvvkQeCJFF4LJ6Q0YNaYEFTkD4f0vrS','2026-08-28 13:14:53','2026-08-28 13:14:53'),('48ff8255-4286-498d-bb52-06f9fdbd4b64','net@gmail.com','$2y$10$lnFXxoPjpJaSzaaNTTFxte/WIpqjJJ1LKmgJzwFw32gHXZqpdfs9S','2026-09-09 17:50:53','2026-09-09 17:50:53'),('4980ddcf-f503-4cd9-ad74-69d20da0b57e','manuel@gmail.com','$2y$10$6grTXTsuC1m0waiONuu5MOcC4EdF2Ft0NeKk/ezoyGwB5sXI6w3Bu','2026-09-07 16:00:42','2026-09-07 16:00:42'),('ce0a3353-b85f-4679-83d0-a9b50163f49c','carlos@gmail.com','$2y$12$BgaQUsJRxIfltUnBm4P29utRJXT7tKPpYY2TGWQZuLjcn5urPkrxy','2026-08-28 14:03:05','2026-08-28 14:03:05'),('e8569217-c195-49ed-8d06-5f077cb157b1','homero@gmail.com','$2y$10$7cJQKHwTPwTF2q409ElMxeGYujG5bNlT637XlzTTWYDlEfYhd3qMK','2026-09-09 17:50:07','2026-09-09 17:50:07');
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
  `nombre` varchar(150) NOT NULL,
  `profile_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vend_profile` (`profile_id`),
  CONSTRAINT `fk_vend_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendedor`
--

LOCK TABLES `vendedor` WRITE;
/*!40000 ALTER TABLE `vendedor` DISABLE KEYS */;
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

-- Dump completed on 2026-09-09 19:43:01
