

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




CREATE TABLE `cidades` (
  `cidade_id` int(11) NOT NULL,
  `cidade_nome` varchar(125) NOT NULL,
  `uf_id` int(11) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `cidades` (`cidade_id`, `cidade_nome`, `uf_id`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'São Jose dos Campos', 1, '2025-09-01 23:09:41', NULL),
(2, 'São Paulo', 1, '2025-09-09 10:54:56', NULL),
(3, 'Taubate', 1, '2025-09-10 23:08:55', NULL),
(4, 'Caraguatatuba', 1, '2025-09-11 14:57:02', NULL);



CREATE TABLE `estados` (
  `uf_id` int(11) NOT NULL,
  `uf_sigla` char(2) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
-- Estrutura para tabela `pontos_ajuda`
--

CREATE TABLE `pontos_ajuda` (
  `ponto_id` int(11) NOT NULL,
  `titulo` varchar(125) NOT NULL,
  `descricao` text NOT NULL,
  `endereco` varchar(125) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(10,8) NOT NULL,
  `tipo_ajuda` enum('alimento','roupa','medicamento','abrigo','outros') NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pontos_ajuda`
--

INSERT INTO `pontos_ajuda` (`ponto_id`, `titulo`, `descricao`, `endereco`, `cidade_id`, `latitude`, `longitude`, `tipo_ajuda`, `usuario_id`, `ativo`, `dt_criacao`, `dt_atualizacao`) VALUES
(1, 'Doação de Alimentos', 'Local para doação de alimentos não perecíveis', 'Rua das Flores, 123', 1, -23.18015230, -45.85841420, 'alimento', 13, 1, '2025-09-01 23:09:01', '2025-09-02 23:09:29'),
(2, 'Abrigo Emergencial', 'Abrigo para pessoas em situação de rua', 'Avenida Central, 456', 1, -23.21620870, -45.77155400, 'abrigo', 14, 1, '2025-09-01 23:09:01', '2025-09-02 23:09:31'),
(3, 'Posto de Saúde', 'Distribuição de medicamentos básicos', 'Praça da Saúde, 789', 1, -23.19293220, -45.79064400, 'medicamento', 15, 1, '2025-09-01 23:09:01', '2025-09-02 23:09:33'),
(4, 'Wicca Doações', 'Organização que ajuda pessoas de ruas com banhos e doações de novas roupas', 'Rua córrego do cambuí, 35', 1, -23.14240110, -45.91681690, 'roupa', 22, 1, '2025-09-01 23:09:01', '2025-09-02 23:09:34'),
(5, 'Senac Doaçoes', 'Ajudamos pobres', 'Rua Avião Muniz, 459', 1, -23.20085250, -45.85963920, 'outros', 22, 1, '2025-09-01 23:09:01', '2025-09-02 23:09:36'),
(6, 'Novo ponto', 'Bar localizado na Vila Industrial com intuito de ajudar pessoas em estado de vulnerabilidade <3', 'SENAC, Rua Saigiro Nakamura, Jardim Ismênia, Jardim Ismenia, São José dos Campos, Região Imediata de São José dos Campos, Reg', 1, -23.18051320, -45.85893670, 'alimento', 24, 1, '2025-09-08 23:02:26', '2025-09-11 14:49:54'),
(7, 'Bar', 'Ajudamos os bebados anonimos', 'R. Córrego do Cambuí - Altos da Vila Paiva', 1, -23.14283560, -45.91661880, 'alimento', 25, 1, '2025-09-09 02:53:04', NULL),
(8, 'E.E Maria Luiza Guimarães Medeiros', 'Ponto de arrecadação de roupa\r\n7:00 as 16:00', 'Escola Estadual Maria Luiza de Guimarães  Medeiros, 89, Rua São Luiz Gonzaga, Vila Sinha, São José dos Campos, Região Imediat', 1, -23.16289100, -45.89941250, 'roupa', 26, 1, '2025-09-09 10:48:02', '2025-09-11 14:56:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nivel_acesso_id` int(11) DEFAULT 3,
  `nome_completo` varchar(125) NOT NULL,
  `razao_social` varchar(125) NOT NULL,
  `nome_fantasia` varchar(125) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `dt_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `dt_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `usuario`, `email`, `senha_hash`, `nivel_acesso_id`, `nome_completo`, `razao_social`, `nome_fantasia`, `endereco`, `cidade_id`, `foto_perfil`, `status`, `dt_criacao`, `dt_atualizacao`, `reset_token`, `reset_expira`) VALUES
(13, 'Wendell', 'Wendell@exemplo.com', '$2y$10$QTfJy9P8z5KYVz1Y0.lg8OXR8yCWpSwp1TkKtKeM.NY7XFdJ0tIse', 3, 'Wendell Araujo', 'Wendell Araujo', NULL, NULL, NULL, NULL, 1, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(14, 'Matheus1999', 'Matheus1999@exemplo.com', '$2y$10$4Lvtd6gx5gozc0gOajgwGerpaMDlebDeeo.lVu5puhrSZxA7Wf38u', 3, 'Matheus Jo', 'Imaginario', NULL, NULL, NULL, NULL, 1, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(15, 'Henrique', 'Henrique@exemplo.com', '$2y$10$0lq43HBnukSXvwuBlfnMEuzlHp6aNSZ65wfYaOJQiBvuJOa.2v1X6', 3, 'henrique l', 'Henrique Luan', NULL, NULL, NULL, NULL, 1, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(16, 'Nunes_2007', 'Nunes_2007@exemplo.com', '$2y$10$U.J96o/4thHLh8xdUK965u3lMCONXLnGgwqK9xCoPtq16/bhgu20K', 3, 'Matheus He', 'Matheus', NULL, NULL, NULL, NULL, 0, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(17, 'F3l.ipé;;', 'F3l.ipé;;@exemplo.com', '$2y$10$kr88dnhQyVEEc8pEq6KP3upXilywBw.Hle.bY7NnoWhbYNNGuT43i', 3, 'Felipe Aug', 'Sla mano', NULL, NULL, NULL, NULL, 0, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(18, 'marcelo', 'marcelo@exemplo.com', '$2y$10$QhPaqX32d7e8nPdEJ3XR.Or/3/AtZtohUlA6qAL56ojlXQC3ERnhS', 3, 'marcelo os', 'marcelo', NULL, NULL, NULL, NULL, 0, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(19, 'BananaoViril', 'BananaoViril@exemplo.com', '$2y$10$pxf9n0pzmgpPVTWAFpZkYe2L/73OuxfXjq.2g8pdv2XYlqj5IYCS6', 1, 'Petter da Costa Pinto', 'ASDFGHJYX', NULL, NULL, NULL, NULL, 1, '2025-09-01 23:05:37', '2025-09-18 22:10:06', NULL, NULL),
(20, 'eoBigode', 'eoBigode@exemplo.com', '$2y$10$JwSHli.0KSpQp/0CqEhSIePNybF/tXO7ioncz2aH8MxX9bYoOf2Ea', 3, 'Samuel Sil', 'Senac', NULL, NULL, NULL, NULL, 0, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(21, 'Wicca2140', 'Wicca2140@exemplo.com', '$2y$10$bu3flXYu0dHy6SMvc1R6c.nyFubnZlGpR8MuujaMxERh6xwSZ6wmq', 2, 'Rodrigo Av', 'Cafetão', NULL, NULL, NULL, NULL, 0, '2025-09-01 23:05:37', '2025-09-11 15:00:30', NULL, NULL),
(22, 'rodrigordg0429', 'rodrigordg0429@exemplo.com', '$2y$10$vK5UMHP5Cbrky0cLBOYy4OI53Khj6SPcmfyBSz7rU.trMYtznIla2', 3, 'RODRIGO AVILA RODRIGUES', 'Rodrigo Avila Rodrigues', NULL, NULL, NULL, 'uploads/68ae4db99a345_Captura de tela 2025-03-26 212305.png', 1, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(23, 'sugirokimimami', 'sugirokimimami@exemplo.com', '$2y$10$wtH6tU0Ef4SRhRAfPq3eROdITPZhJY.bFgEqwKRwTfR3CRE01JtJG', 3, 'Samuel Dan', 'Samuel Dantas', NULL, NULL, NULL, NULL, 0, '2025-09-01 23:05:37', '2025-09-08 23:22:44', NULL, NULL),
(24, 'AdminTeste', 'rodrigordg0429@gmail.com', '$2y$10$a2M5c6YN3/atyiRH61ZnyeoA3meEU4kQUe4mHBU.nxblTTXvHBJMO', 1, 'Novo Nome', 'Projeto Esperança', 'Projeto Esperança', 'Rua Saigiro Nakamura, 400, Vila Industrial', 1, 'uploads/68c2e17c3044d_1757602172.png', 1, '2025-09-08 22:57:14', '2025-09-11 14:49:32', NULL, NULL),
(25, 'duzin89', 'dudurdg@gmail.com', '$2y$10$TDk9Dq8lYSczV3se/bZVT.finC5.dlranpy4XIvC.DPvMBa7TTfvO', 1, 'Eduardo Rodolfo Rodrigues Barreiros Avila', 'Mercado Pago', 'Mercado Pago', 'R. Córrego do Cambuí - Altos da Vila Paiva', 1, 'uploads/68bf962423460_1757386276.png', 1, '2025-09-09 02:45:54', '2025-09-09 03:50:55', '3244bf167da83ad8eadc9fa3f34735f010fc8275f511786b7792e010c3485825', '2025-09-09 06:11:40'),
(26, 'Gustavo Admin', 'gustavoliveira097@gmail.com', '$2y$10$LrN5l/5Qe69nNmXON7B6VOGkExwppdTDFg3jFHsTmEmD13lQrHIVm', 1, 'Gustavo de Lima Oliveira', 'Gustavo de Lima Oliveira', 'Gustavo', 'Rua São Sebastião 42, Jardim Santarém', 1, 'uploads/68c005489e3de_1757414728.png', 1, '2025-09-09 10:41:16', '2025-09-16 11:06:31', '54b88cf110ee23528b4b053e745a868e9c32611cb0c07de844fecac8b79a878a', '2025-09-09 13:49:05'),
(27, 'teste1', 'seu@gmail.com', '$2y$10$65olLBTJ5wHBlNuaRNmUTO0QlT1NZr55bSdrpzrZMoh7ng8RGrCRi', 3, 'Teste1', 'teste1', 'teste1', 'SENAC, Rua Saigiro Nakamura, Jardim Ismênia, Jardim Ismenia, São José dos Campos, Região Imediata de São José dos Campos, Reg', 1, NULL, 1, '2025-09-16 11:05:45', NULL, NULL, NULL);

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
-- Índices de tabela `pontos_ajuda`
--
ALTER TABLE `pontos_ajuda`
  ADD PRIMARY KEY (`ponto_id`),
  ADD KEY `fk_ponto_usuario` (`usuario_id`),
  ADD KEY `fk_ponto_cidade` (`cidade_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `razao_social` (`razao_social`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `fk_usuario_acesso` (`nivel_acesso_id`),
  ADD KEY `fk_usuario_cidade` (`cidade_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `cidade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT de tabela `pontos_ajuda`
--
ALTER TABLE `pontos_ajuda`
  MODIFY `ponto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cidades`
--
ALTER TABLE `cidades`
  ADD CONSTRAINT `fk_cidade_estado` FOREIGN KEY (`uf_id`) REFERENCES `estados` (`uf_id`);

--
-- Restrições para tabelas `pontos_ajuda`
--
ALTER TABLE `pontos_ajuda`
  ADD CONSTRAINT `fk_ponto_cidade` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`cidade_id`),
  ADD CONSTRAINT `fk_ponto_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_acesso` FOREIGN KEY (`nivel_acesso_id`) REFERENCES `permissoes` (`permissao_id`),
  ADD CONSTRAINT `fk_usuario_cidade` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`cidade_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
