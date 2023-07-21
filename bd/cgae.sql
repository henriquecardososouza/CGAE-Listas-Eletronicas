-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21-Jul-2023 às 07:40
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cgae`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `aluno`
--

CREATE TABLE `aluno` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sexo` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `quarto` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `id_refeitorio` int(11) NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `pernoite` tinyint(1) NOT NULL DEFAULT 0,
  `cidade` varchar(255) NOT NULL,
  `responsavel` varchar(255) NOT NULL,
  `telefone_responsavel` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `edit_pernoite`
--

CREATE TABLE `edit_pernoite` (
  `id` int(11) NOT NULL,
  `pernoite` int(11) NOT NULL,
  `endereco` text NOT NULL,
  `nome_responsavel` varchar(255) NOT NULL,
  `telefone` varchar(50) NOT NULL,
  `data_saida` date NOT NULL,
  `data_chegada` date NOT NULL,
  `hora_saida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `edit_saida`
--

CREATE TABLE `edit_saida` (
  `id` int(11) NOT NULL,
  `saida` int(11) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `data_saida` date NOT NULL,
  `data_chegada` date NOT NULL,
  `hora_saida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `edit_vai_volta`
--

CREATE TABLE `edit_vai_volta` (
  `id` int(11) NOT NULL,
  `vai_volta` int(11) NOT NULL,
  `destino` text NOT NULL,
  `data` date NOT NULL,
  `hora_saida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pernoite`
--

CREATE TABLE `pernoite` (
  `id` int(11) NOT NULL,
  `aluno` int(11) NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT 1,
  `endereco` text NOT NULL,
  `nome_responsavel` varchar(255) NOT NULL,
  `telefone` varchar(50) NOT NULL,
  `data_saida` date NOT NULL,
  `data_chegada` date NOT NULL,
  `hora_saida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saida`
--

CREATE TABLE `saida` (
  `id` int(11) NOT NULL,
  `aluno` int(11) NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT 1,
  `destino` text NOT NULL,
  `data_saida` date NOT NULL,
  `data_chegada` date NOT NULL,
  `hora_saida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `solicitacao`
--

CREATE TABLE `solicitacao` (
  `id` int(11) NOT NULL,
  `aluno` int(11) NOT NULL,
  `id_lista` int(11) NOT NULL,
  `id_edit` int(11) NOT NULL DEFAULT -1,
  `lista` varchar(100) NOT NULL,
  `acao` varchar(50) NOT NULL,
  `motivo` text NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT 1,
  `aprovada` tinyint(1) DEFAULT NULL,
  `data_abertura` datetime NOT NULL,
  `data_encerramento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `vai_volta`
--

CREATE TABLE `vai_volta` (
  `id` int(11) NOT NULL,
  `aluno` int(11) NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT 1,
  `destino` text NOT NULL,
  `data` date NOT NULL,
  `hora_saida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `aluno`
--
ALTER TABLE `aluno`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `edit_pernoite`
--
ALTER TABLE `edit_pernoite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pernoite` (`pernoite`);

--
-- Índices para tabela `edit_saida`
--
ALTER TABLE `edit_saida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saida` (`saida`);

--
-- Índices para tabela `edit_vai_volta`
--
ALTER TABLE `edit_vai_volta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vai_volta` (`vai_volta`);

--
-- Índices para tabela `pernoite`
--
ALTER TABLE `pernoite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno` (`aluno`);

--
-- Índices para tabela `saida`
--
ALTER TABLE `saida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno` (`aluno`);

--
-- Índices para tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno` (`aluno`),
  ADD KEY `id_edit` (`id_edit`);

--
-- Índices para tabela `vai_volta`
--
ALTER TABLE `vai_volta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno` (`aluno`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aluno`
--
ALTER TABLE `aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `edit_pernoite`
--
ALTER TABLE `edit_pernoite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `edit_saida`
--
ALTER TABLE `edit_saida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `edit_vai_volta`
--
ALTER TABLE `edit_vai_volta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pernoite`
--
ALTER TABLE `pernoite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `saida`
--
ALTER TABLE `saida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vai_volta`
--
ALTER TABLE `vai_volta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `edit_pernoite`
--
ALTER TABLE `edit_pernoite`
  ADD CONSTRAINT `edit_pernoite_ibfk_1` FOREIGN KEY (`pernoite`) REFERENCES `pernoite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `edit_saida`
--
ALTER TABLE `edit_saida`
  ADD CONSTRAINT `edit_saida_ibfk_1` FOREIGN KEY (`saida`) REFERENCES `saida` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `edit_vai_volta`
--
ALTER TABLE `edit_vai_volta`
  ADD CONSTRAINT `edit_vai_volta_ibfk_2` FOREIGN KEY (`vai_volta`) REFERENCES `vai_volta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pernoite`
--
ALTER TABLE `pernoite`
  ADD CONSTRAINT `pernoite_ibfk_1` FOREIGN KEY (`aluno`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `saida`
--
ALTER TABLE `saida`
  ADD CONSTRAINT `saida_ibfk_1` FOREIGN KEY (`aluno`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  ADD CONSTRAINT `solicitacao_ibfk_1` FOREIGN KEY (`aluno`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `vai_volta`
--
ALTER TABLE `vai_volta`
  ADD CONSTRAINT `vai_volta_ibfk_1` FOREIGN KEY (`aluno`) REFERENCES `aluno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
