-- Desativando as verificações de chaves estrangeiras temporariamente
SET FOREIGN_KEY_CHECKS = 0;

-- Tabela `Locais`
DROP TABLE IF EXISTS `Locais`;
CREATE TABLE `Locais` (
  `LocalID` int NOT NULL,
  `local_evento` varchar(255) NOT NULL,
  `Endereco` varchar(255) NOT NULL,
  `Estado` varchar(255) NOT NULL,
  `Cidade` varchar(255) NOT NULL,
  `CEP` varchar(10) NOT NULL,
  PRIMARY KEY (`local_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela `tb_user`
DROP TABLE IF EXISTS `tb_user`;
CREATE TABLE `tb_user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nome_user` varchar(255) NOT NULL,
  `email_user` varchar(255) NOT NULL,
  `senha_user` varchar(255) NOT NULL,
  `foto_user` varchar(150) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `Email` (`email_user`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela `TiposEvento`
DROP TABLE IF EXISTS `TiposEvento`;
CREATE TABLE `TiposEvento` (
  `TipoID` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(255) NOT NULL,
  PRIMARY KEY (`TipoID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela `Eventos`
DROP TABLE IF EXISTS `Eventos`;
CREATE TABLE `Eventos` (
  `EventoID` int NOT NULL AUTO_INCREMENT,
  `nome_eventos` varchar(255) NOT NULL,
  `Descricao` text,
  `DataInicio` datetime NOT NULL,
  `DataFim` datetime DEFAULT NULL,
  `CriadorID` int DEFAULT NULL,
  `TipoID` int DEFAULT NULL,
  `foto_evento` varchar(254) DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `local_evento` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`EventoID`),
  KEY `CriadorID` (`CriadorID`),
  KEY `TipoID` (`TipoID`),
  KEY `id_user` (`id_user`),
  KEY `fk_user` (`local_evento`),
  CONSTRAINT `Eventos_ibfk_2` FOREIGN KEY (`CriadorID`) REFERENCES `tb_user` (`id_user`),
  CONSTRAINT `Eventos_ibfk_3` FOREIGN KEY (`TipoID`) REFERENCES `TiposEvento` (`TipoID`),
  CONSTRAINT `fk_user` FOREIGN KEY (`local_evento`) REFERENCES `Locais` (`local_evento`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela `Participantes`
DROP TABLE IF EXISTS `Participantes`;
CREATE TABLE `Participantes` (
  `ParticipanteID` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ParticipanteID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela `Inscricoes`
DROP TABLE IF EXISTS `Inscricoes`;
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

-- Reativando as verificações de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 1;

-- Inserção de dados nas tabelas

-- Dados na tabela `Locais` 
LOCK TABLES `Locais` WRITE;
/*!40000 ALTER TABLE `Locais` DISABLE KEYS */;
/*!40000 ALTER TABLE `Locais` ENABLE KEYS */;
UNLOCK TABLES;

-- Dados na tabela `tb_user`
LOCK TABLES `tb_user` WRITE;
/*!40000 ALTER TABLE `tb_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_user` ENABLE KEYS */;
UNLOCK TABLES;

-- Dados na tabela `TiposEvento`
LOCK TABLES `TiposEvento` WRITE;
/*!40000 ALTER TABLE `TiposEvento` DISABLE KEYS */;
/*!40000 ALTER TABLE `TiposEvento` ENABLE KEYS */;
UNLOCK TABLES;

-- Dados na tabela `Eventos`
LOCK TABLES `Eventos` WRITE;
/*!40000 ALTER TABLE `Eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `Eventos` ENABLE KEYS */;
UNLOCK TABLES;

-- Dados na tabela `Participantes`
LOCK TABLES `Participantes` WRITE;
/*!40000 ALTER TABLE `Participantes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Participantes` ENABLE KEYS */;
UNLOCK TABLES;

-- Dados na tabela `Inscricoes`
LOCK TABLES `Inscricoes` WRITE;
/*!40000 ALTER TABLE `Inscricoes`;
/*!40000 ALTER TABLE `Inscricoes` ENABLE KEYS */;
UNLOCK TABLES;
