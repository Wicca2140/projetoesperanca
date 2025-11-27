-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03/09/2025 às 01:10
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projetoesperanca`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades`
--

CREATE TABLE `cidades` (
  `cidade_id` int(11) NOT NULL,
  `cidade_nome` varchar(125) NOT NULL,
  `uf_id` int(11) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cidades`
--

INSERT INTO `cidades` (`cidade_id`, `cidade_nome`, `uf_id`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'São Jose dos Campos', 1, '2025-09-01 23:09:41', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estados`
--

CREATE TABLE `estados` (
  `uf_id` int(11) NOT NULL,
  `uf_sigla` char(2) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estados`
--

INSERT INTO `estados` (`uf_id`, `uf_sigla`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'SP', '2025-09-01 23:09:25', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `permissao_id` int(11) NOT NULL,
  `cargo` varchar(35) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `permissoes`
--

INSERT INTO `permissoes` (`permissao_id`, `cargo`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'Administrador', '2025-09-01 23:02:42', '2025-09-02 00:16:34'),
(2, 'Gerente', '2025-09-01 23:02:42', '2025-09-01 23:02:42'),
(3, 'Voluntario', '2025-09-01 23:02:42', '2025-09-01 23:02:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pontos`
--

CREATE TABLE `pontos` (
  `ponto_id` int(11) NOT NULL,
  `titulo` varchar(125) NOT NULL,
  `nome_fantasia` varchar(125) NOT NULL,
  `descricao` text NOT NULL,
  `endereco` varchar(125) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(10,8) NOT NULL,
  `tipo_ajuda` enum('alimento','roupa','medicamento','abrigo','outros') NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ativo` enum('ativo','inativo') DEFAULT 'inativo',
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pontos`
--

INSERT INTO `pontos` (`ponto_id`, `titulo`, `nome_fantasia`, `descricao`, `endereco`, `cidade_id`, `latitude`, `longitude`, `tipo_ajuda`, `usuario_id`, `ativo`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'Doação de Alimentos', '', 'Local para doação de alimentos não perecíveis', 'Rua das Flores, 123', 1, -23.18015230, -45.85841420, 'alimento', 13, 'ativo', '2025-09-01 23:09:01', '2025-09-02 23:09:29'),
(2, 'Abrigo Emergencial', '', 'Abrigo para pessoas em situação de rua', 'Avenida Central, 456', 1, -23.21620870, -45.77155400, 'abrigo', 14, 'ativo', '2025-09-01 23:09:01', '2025-09-02 23:09:31'),
(3, 'Posto de Saúde', '', 'Distribuição de medicamentos básicos', 'Praça da Saúde, 789', 1, -23.19293220, -45.79064400, 'medicamento', 15, 'ativo', '2025-09-01 23:09:01', '2025-09-02 23:09:33'),
(4, 'Wicca Doações', '', 'Organização que ajuda pessoas de ruas com banhos e doações de novas roupas', 'Rua córrego do cambuí, 35', 1, -23.14240110, -45.91681690, 'roupa', 22, 'ativo', '2025-09-01 23:09:01', '2025-09-02 23:09:34'),
(5, 'Senac Doaçoes', '', 'Ajudamos pobres', 'Rua Avião Muniz, 459', 1, -23.20085250, -45.85963920, 'outros', 22, 'ativo', '2025-09-01 23:09:01', '2025-09-02 23:09:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `permissao_id` int(11) DEFAULT NULL,
  `primeiro_nome` varchar(10) NOT NULL,
  `segundo_nome` varchar(115) NOT NULL,
  `razao_social` varchar(125) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `dt_nascimento` date NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'inativo',
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `email`, `senha_hash`, `permissao_id`, `primeiro_nome`, `segundo_nome`, `razao_social`, `foto_perfil`, `dt_nascimento`, `status`, `dt_criacao`, `dt_atualizacao`) VALUES
(13, 'Wendell', '$2y$10$QTfJy9P8z5KYVz1Y0.lg8OXR8yCWpSwp1TkKtKeM.NY7XFdJ0tIse', 3, 'Wendell Ar', '', 'Wendell Araujo', NULL, '2025-08-26', 'ativo', '2025-09-01 23:05:37', NULL),
(14, 'Matheus1999', '$2y$10$4Lvtd6gx5gozc0gOajgwGerpaMDlebDeeo.lVu5puhrSZxA7Wf38u', 3, 'Matheus Jo', '', 'Imaginario', NULL, '2025-08-26', 'ativo', '2025-09-01 23:05:37', NULL),
(15, 'Henrique', '$2y$10$0lq43HBnukSXvwuBlfnMEuzlHp6aNSZ65wfYaOJQiBvuJOa.2v1X6', 3, 'henrique l', '', ' Henrique Luan', NULL, '2025-08-26', 'ativo', '2025-09-01 23:05:37', NULL),
(16, 'Nunes_2007', '$2y$10$U.J96o/4thHLh8xdUK965u3lMCONXLnGgwqK9xCoPtq16/bhgu20K', 3, 'Matheus He', '', 'Matheus', NULL, '2025-08-26', 'inativo', '2025-09-01 23:05:37', NULL),
(17, 'F3l.ipé;;', '$2y$10$kr88dnhQyVEEc8pEq6KP3upXilywBw.Hle.bY7NnoWhbYNNGuT43i', 3, 'Felipe Aug', '', 'Sla mano', NULL, '2025-08-26', 'inativo', '2025-09-01 23:05:37', NULL),
(18, 'marcelo', '$2y$10$QhPaqX32d7e8nPdEJ3XR.Or/3/AtZtohUlA6qAL56ojlXQC3ERnhS', 3, 'marcelo os', '', 'marcelo', NULL, '2025-08-26', 'inativo', '2025-09-01 23:05:37', NULL),
(19, 'BananaoViril@hotmail.com', '$2y$10$pxf9n0pzmgpPVTWAFpZkYe2L/73OuxfXjq.2g8pdv2XYlqj5IYCS6', 1, 'Petter', ' da Costa Pinto', 'ASDFGHJYX', NULL, '2002-01-17', 'inativo', '2025-09-01 23:05:37', '2025-09-02 00:42:20'),
(20, 'eoBigode', '$2y$10$JwSHli.0KSpQp/0CqEhSIePNybF/tXO7ioncz2aH8MxX9bYoOf2Ea', 3, 'Samuel Sil', '', 'Senac', NULL, '2025-08-26', 'inativo', '2025-09-01 23:05:37', NULL),
(21, 'Wicca2140', '$2y$10$bu3flXYu0dHy6SMvc1R6c.nyFubnZlGpR8MuujaMxERh6xwSZ6wmq', 3, 'Rodrigo Av', '', 'Cafetão', NULL, '2025-08-26', 'inativo', '2025-09-01 23:05:37', NULL),
(22, 'rodrigordg0429@gmail.com', '$2y$10$vK5UMHP5Cbrky0cLBOYy4OI53Khj6SPcmfyBSz7rU.trMYtznIla2', 3, 'RODRIGO AV', '', 'Rodrigo Avila Rodrigues', 'uploads/68ae4db99a345_Captura de tela 2025-03-26 212305.png', '2025-08-26', 'ativo', '2025-09-01 23:05:37', NULL),
(23, 'sugirokimimami', '$2y$10$wtH6tU0Ef4SRhRAfPq3eROdITPZhJY.bFgEqwKRwTfR3CRE01JtJG', 3, 'Samuel Dan', '', 'Samuel Dantas', NULL, '2025-08-26', 'inativo', '2025-09-01 23:05:37', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`cidade_id`),
  ADD UNIQUE KEY `cidade_nome` (`cidade_nome`),
  ADD KEY `fk_cidade_estado` (`uf_id`);

--
-- Índices de tabela `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`uf_id`),
  ADD UNIQUE KEY `uf_sigla` (`uf_sigla`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`permissao_id`) USING BTREE,
  ADD UNIQUE KEY `cargo` (`cargo`);

--
-- Índices de tabela `pontos`
--
ALTER TABLE `pontos`
  ADD PRIMARY KEY (`ponto_id`),
  ADD KEY `fk_ponto_usuario` (`usuario_id`),
  ADD KEY `fk_ponto_cidade` (`cidade_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `razao_social` (`razao_social`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_acesso` (`permissao_id`) USING BTREE;

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `cidade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `estados`
--
ALTER TABLE `estados`
  MODIFY `uf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `permissao_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pontos`
--
ALTER TABLE `pontos`
  MODIFY `ponto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cidades`
--
ALTER TABLE `cidades`
  ADD CONSTRAINT `fk_cidade_estado` FOREIGN KEY (`uf_id`) REFERENCES `estados` (`uf_id`);

--
-- Restrições para tabelas `pontos`
--
ALTER TABLE `pontos`
  ADD CONSTRAINT `fk_ponto_cidade` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`cidade_id`),
  ADD CONSTRAINT `fk_ponto_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_permissoes` FOREIGN KEY (`permissao_id`) REFERENCES `permissoes` (`permissao_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
