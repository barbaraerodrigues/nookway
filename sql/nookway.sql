-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/12/2025 às 19:12
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
-- Banco de dados: `nookway`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `id_tour` int(11) NOT NULL,
  `id_turista` int(11) NOT NULL,
  `nota` tinyint(4) NOT NULL,
  `comentario` text NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `id_tour`, `id_turista`, `nota`, `comentario`, `criado_em`) VALUES
(15, 32, 16, 5, 'Muito bom!', '2025-12-11 17:28:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `id_turista` int(11) NOT NULL,
  `id_tour` int(11) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `favoritos`
--

INSERT INTO `favoritos` (`id`, `id_turista`, `id_tour`, `criado_em`) VALUES
(43, 16, 33, '2025-12-11 17:13:11'),
(44, 16, 32, '2025-12-11 17:27:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `id_tour` int(11) NOT NULL,
  `data_reservada` datetime NOT NULL,
  `codigo_confirmacao` varchar(10) NOT NULL,
  `id_turista` int(11) NOT NULL,
  `data_reserva` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','confirmada','cancelada') DEFAULT 'pendente',
  `quantidade_pessoas` int(11) DEFAULT 1,
  `visivel_turista` tinyint(1) DEFAULT 1,
  `visivel_guia` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `id_tour`, `data_reservada`, `codigo_confirmacao`, `id_turista`, `data_reserva`, `status`, `quantidade_pessoas`, `visivel_turista`, `visivel_guia`) VALUES
(71, 32, '2025-12-18 12:00:00', '5E62E9', 9, '2025-12-12 11:59:09', 'pendente', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `cidade` varchar(120) DEFAULT NULL,
  `categoria` varchar(80) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `preco` decimal(8,2) DEFAULT 0.00,
  `moeda` varchar(10) DEFAULT NULL,
  `moeda_customizada` varchar(10) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `id_guia` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `dias_semana` varchar(20) NOT NULL,
  `horarios` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tours`
--

INSERT INTO `tours` (`id`, `titulo`, `descricao`, `cidade`, `categoria`, `latitude`, `longitude`, `preco`, `moeda`, `moeda_customizada`, `imagem`, `id_guia`, `criado_em`, `data_inicio`, `data_fim`, `dias_semana`, `horarios`) VALUES
(32, 'City Tour Histórico', 'Tour guiado pelos principais pontos históricos da cidade, incluindo museus, praças e edifícios históricos.', 'Sé do Porto', 'Cultural', 41.1422147, -8.6115164, 30.00, NULL, NULL, 'tour_693af947b0ea0.jpg', 15, '2025-12-11 17:03:03', '2025-12-11', NULL, '1,3,4', '09:00,12:00,16:00'),
(33, 'Tuktuk', 'Ande de Tuktuk pelos melhores pontos turísticos da cidade.', 'Rua da Alegria 77', 'Tuktuk', 41.1476044, -8.6039439, 50.00, NULL, NULL, 'tour_693afab8c56e6.png', 15, '2025-12-11 17:09:12', '2025-12-11', NULL, '0,5,6', '10:00,12:00,14:00,16:00,18:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_conta` enum('turista','guia') NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `telefone`, `localizacao`, `bio`, `data_nascimento`, `senha`, `tipo_conta`, `foto_perfil`, `criado_em`) VALUES
(1, 'Barbara Rodrigues', 'barbaraelrodrigues@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$GMdQoF.h9fZKMTfOg0tZGuRf6NiPSLFX/ZcetvL2pmasWM72/aOJi', 'guia', NULL, '2025-11-23 00:46:10'),
(2, 'Barbara Rodrigues', 'barbaraellenrodrigues2013@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$gpPojzlfJDlQGCKRG4KyMO.uKB0vwpIG0r0HNMbeNT5DpG05sfrPO', 'turista', NULL, '2025-11-23 00:46:28'),
(3, 'Barbara Ellen Rodrigues', 'contatobarbaraerodrigues@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$zqXo.nsXabzSbhcLc7eTNeBykLA.SbtHpjaEL4jGMjHNg7Us7jLq.', 'turista', NULL, '2025-11-23 01:18:04'),
(4, 'Barbara Ellen Rodrigues', 'barbaraellenrodrigues@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$cmihlNf3115KQ4riYrTpEetuH8RZEOwKHo3C9s2LAtlxf2V/psVue', 'guia', NULL, '2025-11-23 14:45:04'),
(5, '', '', '', '', '', NULL, '$2y$10$w6ldNnoLuXm2RoJcVr6T6Obon7DwXu0mt69iwEB1vHZ0sYoI0403O', 'turista', 'uploads/usuarios/user_5.png', '2025-11-23 15:04:02'),
(6, 'guia', 'guia@gmail.com', '123456789', '', 'teste', '0000-00-00', '$2y$10$klguoqbIaZiGRsmQuCOHv.P6TTvXITJ9bJxsZHIxa.aRpJxIhRc.m', 'guia', 'user_6_1765339564.png', '2025-11-23 15:13:44'),
(7, 'Barbara Ellen Rodrigues', 'bagues@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$9k9bXtCW9vrBwkUAaOHmGOn8g76ogShoA8Slid9afuxMC5f7Glt4m', 'turista', NULL, '2025-11-23 15:26:01'),
(8, 'Diogo Coelho', 'diogoluiscoelho@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$vje8btCV2GsRBIwZG4L6t./9mGrOgNB8PPTc8k5ufi9P5VmsCUNsy', 'guia', NULL, '2025-11-24 09:27:37'),
(9, 'turista', 'turista@gmail.com', '123456', NULL, 'teste', '1997-07-07', '$2y$10$cKwDW7JGwq7aqKp6LTuSM..MR0lneonAjNNNLoCpq9Ul6f7m9jVT.', 'turista', 'user_9_1765372914.jpg', '2025-11-25 13:26:43'),
(10, 'Ana Silva', 'ana@exemplo.com', '912345678', 'Porto', 'Guia especializada em tours históricos', '1985-06-12', 'senha123', 'guia', 'ana.jpg', '2025-12-06 18:10:25'),
(11, 'João Pereira', 'joao@exemplo.com', '913456789', 'Lisboa', 'Guia de gastronomia e vinhos', '1990-03-22', 'senha123', 'guia', 'joao.jpg', '2025-12-06 18:10:25'),
(12, 'Maria Costa', 'maria@exemplo.com', '914567890', 'Porto', 'Ama viagens e experiências locais', '1995-09-10', 'senha123', 'turista', 'maria.jpg', '2025-12-06 18:10:26'),
(13, 'Pedro Santos', 'pedro@exemplo.com', '915678901', 'Lisboa', 'Viajante foodie', '1992-12-05', 'senha123', 'turista', 'pedro.jpg', '2025-12-06 18:10:26'),
(15, 'Diogo Coelho', 'diogocoelho@gmail.com', '+351 912 345 678', NULL, 'Nascido e criado em Guimarães, o berço de Portugal. Guia Oficial há 7 anos.', '2002-11-05', '$2y$10$OzySd4AEv0e21CK2WfzUyOTGInxHv9DfcBH8eLd8kDL8KNC1/qHKe', 'guia', 'user_15_1765472445.jpg', '2025-12-11 16:58:49'),
(16, 'Julia Fernandez', 'juliafernandez@gmail.com', '+351 987 654 321', NULL, 'Adoro viajar!', '2005-12-15', '$2y$10$p2Z4mLPBevqPTiUQ/IvIUOD8oOPoYG9HbL18NJmwd2Qf3njgIpmxO', 'turista', 'user_16_1765473116.jpg', '2025-12-11 17:11:09');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tour` (`id_tour`),
  ADD KEY `id_turista` (`id_turista`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico` (`id_turista`,`id_tour`),
  ADD KEY `id_tour` (`id_tour`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_confirmacao` (`codigo_confirmacao`),
  ADD KEY `id_tour` (`id_tour`),
  ADD KEY `id_turista` (`id_turista`);

--
-- Índices de tabela `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de tabela `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_tour`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`id_turista`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_turista`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_tour`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_tour`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_turista`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
