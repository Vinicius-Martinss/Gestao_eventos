-- MySQL dump 10.13  Distrib 8.0.39, for Linux (x86_64)
--
-- Host: localhost    Database: gestao_eventos
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Eventos`
--

DROP TABLE IF EXISTS `Eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Eventos` (
  `EventoID` int NOT NULL AUTO_INCREMENT,
  `nome_eventos` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Descricao` text COLLATE utf8mb4_general_ci,
  `DataInicio` datetime NOT NULL,
  `DataFim` datetime DEFAULT NULL,
  `CriadorID` int DEFAULT NULL,
  `TipoID` int DEFAULT NULL,
  `foto_evento` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `local_evento` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `criador_id` int DEFAULT NULL COMMENT '	',
  `idade_minima` int NOT NULL,
  `valor_ingresso` decimal(10,2) NOT NULL,
  PRIMARY KEY (`EventoID`),
  KEY `CriadorID` (`CriadorID`),
  KEY `TipoID` (`TipoID`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `Eventos_ibfk_2` FOREIGN KEY (`CriadorID`) REFERENCES `tb_user` (`id_user`),
  CONSTRAINT `Eventos_ibfk_3` FOREIGN KEY (`TipoID`) REFERENCES `TiposEvento` (`TipoID`),
  CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Eventos`
--

LOCK TABLES `Eventos` WRITE;
/*!40000 ALTER TABLE `Eventos` DISABLE KEYS */;
INSERT INTO `Eventos` VALUES (30,'Conferência Internacional de Tecnologia 2024','Evento anual que reúne líderes globais em tecnologia','2024-05-11 00:00:00','2024-05-16 00:00:00',NULL,NULL,'66eaba5df1765.jpg',8,' Centro de Convenções Internacional, São Paulo ',NULL,21,1200.00),(31,'gera','evento politico','2024-02-09 00:00:00','2026-02-10 00:00:00',NULL,NULL,'66eb5d654dc22.jpeg',8,'beiras',NULL,11,30.00);
/*!40000 ALTER TABLE `Eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Inscricoes`
--

DROP TABLE IF EXISTS `Inscricoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Inscricoes` (
  `InscricaoID` int NOT NULL AUTO_INCREMENT,
  `EventoID` int DEFAULT NULL,
  `ParticipanteID` int DEFAULT NULL,
  `DataInscricao` datetime NOT NULL,
  PRIMARY KEY (`InscricaoID`),
  KEY `EventoID` (`EventoID`),
  KEY `ParticipanteID` (`ParticipanteID`),
  CONSTRAINT `Inscricoes_ibfk_1` FOREIGN KEY (`EventoID`) REFERENCES `Eventos` (`EventoID`),
  CONSTRAINT `Inscricoes_ibfk_2` FOREIGN KEY (`ParticipanteID`) REFERENCES `Participantes` (`ParticipanteID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Inscricoes`
--

LOCK TABLES `Inscricoes` WRITE;
/*!40000 ALTER TABLE `Inscricoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Inscricoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Locais`
--

DROP TABLE IF EXISTS `Locais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Locais` (
  `LocalID` int NOT NULL AUTO_INCREMENT,
  `local_evento` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Endereco` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Estado` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Cidade` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `CEP` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`LocalID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Locais`
--

LOCK TABLES `Locais` WRITE;
/*!40000 ALTER TABLE `Locais` DISABLE KEYS */;
/*!40000 ALTER TABLE `Locais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Participantes`
--

DROP TABLE IF EXISTS `Participantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Participantes` (
  `ParticipanteID` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `EventoID` int DEFAULT NULL,
  `sexo` char(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idade` int DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  PRIMARY KEY (`ParticipanteID`),
  UNIQUE KEY `Email` (`Email`),
  KEY `EventoID` (`EventoID`),
  KEY `fk_user` (`id_user`),
  CONSTRAINT `EventoID` FOREIGN KEY (`EventoID`) REFERENCES `Eventos` (`EventoID`),
  CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Participantes`
--

LOCK TABLES `Participantes` WRITE;
/*!40000 ALTER TABLE `Participantes` DISABLE KEYS */;
INSERT INTO `Participantes` VALUES (27,'vini','',NULL,31,'M',17,NULL);
/*!40000 ALTER TABLE `Participantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TiposEvento`
--

DROP TABLE IF EXISTS `TiposEvento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TiposEvento` (
  `TipoID` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`TipoID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TiposEvento`
--

LOCK TABLES `TiposEvento` WRITE;
/*!40000 ALTER TABLE `TiposEvento` DISABLE KEYS */;
/*!40000 ALTER TABLE `TiposEvento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_user`
--

DROP TABLE IF EXISTS `tb_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tb_user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nome_user` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_user` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `senha_user` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `foto_user` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `idade_user` int NOT NULL DEFAULT '0',
  `data_nascimento` date DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `Email` (`email_user`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_user`
--

LOCK TABLES `tb_user` WRITE;
/*!40000 ALTER TABLE `tb_user` DISABLE KEYS */;
INSERT INTO `tb_user` VALUES (8,'teste2','freefire@gmail.com','$2y$10$CLwUGh5JoylEdjzB7/wTMutVv3Lu2eiZGckTJNMOO/DgiS75YqIZu','66eaac2e84b8e.jpg',0,'2007-03-10'),(9,'vinicius','viniciusmartinsgsi@gmail.com','$2y$10$Hz3Gru6QSs7WYstNZRVs2uz/P.Z483in.B9NEa6yfYSJILSdXMMae','66eb5610dbd58.jpeg',0,'2007-10-17'),(10,'teste','freefire2000@gmail.com','$2y$10$NPh0zdjhM3quHAzSxWWtSuPJwWa/5Q96lYyUHyDLXOJulujB8ON/K','66eaaad9df34e.jpg',0,'1900-02-20'),(11,'teste3','freefire2000t@gmail.com','$2y$10$Z/cSBR7pjhatlpgo2WwrxOdd2pdJDqAFGoEyj5nd4Ni/jWD.AqNpu','66eaabd06959f.jpg',0,'2000-05-19');
/*!40000 ALTER TABLE `tb_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-20 16:43:47
