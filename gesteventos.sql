-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/09/2024 às 03:04
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gesteventos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Eventos`
--

CREATE TABLE `Eventos` (
  `EventoID` int(11) NOT NULL,
  `nome_eventos` varchar(255) NOT NULL,
  `Descricao` text DEFAULT NULL,
  `DataInicio` datetime NOT NULL,
  `DataFim` datetime DEFAULT NULL,
  `CriadorID` int(11) DEFAULT NULL,
  `TipoID` int(11) DEFAULT NULL,
  `foto_evento` varchar(254) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `local_evento` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Eventos`
--

INSERT INTO `Eventos` (`EventoID`, `nome_eventos`, `Descricao`, `DataInicio`, `DataFim`, `CriadorID`, `TipoID`, `foto_evento`, `id_user`, `local_evento`) VALUES
(3, 'gera 20', 'evento politico', '2024-02-09 11:20:00', '2026-02-10 11:20:00', NULL, NULL, '66df67555b594.jpeg', 3, 'beira'),
(4, 'gera 22', 'evento', '2024-02-09 11:20:00', '2025-11-10 11:20:00', NULL, NULL, '66e2102cc241f.jpeg', 3, 'beira'),
(5, 'gera', 'evento politico', '2024-02-09 11:20:00', '2026-02-10 11:20:00', NULL, NULL, '66e20fe43760c.jpeg', 4, 'rodoviaria');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Inscricoes`
--

CREATE TABLE `Inscricoes` (
  `InscricaoID` int(11) NOT NULL,
  `EventoID` int(11) DEFAULT NULL,
  `ParticipanteID` int(11) DEFAULT NULL,
  `DataInscricao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Locais`
--

CREATE TABLE `Locais` (
  `LocalID` int(11) NOT NULL,
  `local_evento` varchar(255) NOT NULL,
  `Endereco` varchar(255) NOT NULL,
  `Estado` varchar(255) NOT NULL,
  `Cidade` varchar(255) NOT NULL,
  `CEP` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Participantes`
--

CREATE TABLE `Participantes` (
  `ParticipanteID` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  `EventoID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `nome_user` varchar(255) NOT NULL,
  `email_user` varchar(255) NOT NULL,
  `senha_user` varchar(255) NOT NULL,
  `foto_user` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nome_user`, `email_user`, `senha_user`, `foto_user`) VALUES
(3, 'vini', 'viniciusmartinsgsi@gmail.com', '$2y$10$aQimBfpf0P5kV8nPQUjDkeev3uhvvsje/qPmaY/fFsjTZH9QZZj1K', 'avatar-padrao.png'),
(4, 'vinicius', 'freefire2000@gmail.com', '$2y$10$eUgCSEXPpp/qhf5F49i7tOlG3C6RUMbkFc6hW9nDx5btjHcqPEvxO', 'avatar-padrao.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `TiposEvento`
--

CREATE TABLE `TiposEvento` (
  `TipoID` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Eventos`
--
ALTER TABLE `Eventos`
  ADD PRIMARY KEY (`EventoID`),
  ADD KEY `CriadorID` (`CriadorID`),
  ADD KEY `TipoID` (`TipoID`),
  ADD KEY `id_user` (`id_user`);

--
-- Índices de tabela `Inscricoes`
--
ALTER TABLE `Inscricoes`
  ADD PRIMARY KEY (`InscricaoID`),
  ADD KEY `EventoID` (`EventoID`),
  ADD KEY `ParticipanteID` (`ParticipanteID`);

--
-- Índices de tabela `Locais`
--
ALTER TABLE `Locais`
  ADD PRIMARY KEY (`LocalID`);

--
-- Índices de tabela `Participantes`
--
ALTER TABLE `Participantes`
  ADD PRIMARY KEY (`ParticipanteID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `EventoID` (`EventoID`);

--
-- Índices de tabela `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `Email` (`email_user`);

--
-- Índices de tabela `TiposEvento`
--
ALTER TABLE `TiposEvento`
  ADD PRIMARY KEY (`TipoID`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Eventos`
--
ALTER TABLE `Eventos`
  MODIFY `EventoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `Inscricoes`
--
ALTER TABLE `Inscricoes`
  MODIFY `InscricaoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `Locais`
--
ALTER TABLE `Locais`
  MODIFY `LocalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Participantes`
--
ALTER TABLE `Participantes`
  MODIFY `ParticipanteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `TiposEvento`
--
ALTER TABLE `TiposEvento`
  MODIFY `TipoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Eventos`
--
ALTER TABLE `Eventos`
  ADD CONSTRAINT `Eventos_ibfk_2` FOREIGN KEY (`CriadorID`) REFERENCES `tb_user` (`id_user`),
  ADD CONSTRAINT `Eventos_ibfk_3` FOREIGN KEY (`TipoID`) REFERENCES `TiposEvento` (`TipoID`),
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `Inscricoes`
--
ALTER TABLE `Inscricoes`
  ADD CONSTRAINT `Inscricoes_ibfk_1` FOREIGN KEY (`EventoID`) REFERENCES `Eventos` (`EventoID`),
  ADD CONSTRAINT `Inscricoes_ibfk_2` FOREIGN KEY (`ParticipanteID`) REFERENCES `Participantes` (`ParticipanteID`);

--
-- Restrições para tabelas `Participantes`
--
ALTER TABLE `Participantes`
  ADD CONSTRAINT `EventoID` FOREIGN KEY (`EventoID`) REFERENCES `Eventos` (`EventoID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
