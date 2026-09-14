-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/09/2026 às 08:41
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
-- Banco de dados: `artco`
--

DELIMITER $$
--
-- Procedimentos
--
DROP PROCEDURE IF EXISTS `sp_produtos_dashboard`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_produtos_dashboard` (IN `p_busca` VARCHAR(150), IN `p_categoria_id` INT, IN `p_limite` INT, IN `p_offset` INT)   BEGIN

    DECLARE v_busca VARCHAR(150);
    DECLARE v_categoria_id INT;
    DECLARE v_limite INT;
    DECLARE v_offset INT;


    -- Normaliza a busca
    SET v_busca = TRIM(
        COALESCE(p_busca, '')
    );


    -- Normaliza categoria
    SET v_categoria_id = p_categoria_id;


    -- Limite padrão: 10
    IF p_limite IS NULL OR p_limite < 1 THEN
        SET v_limite = 10;

    ELSEIF p_limite > 100 THEN
        SET v_limite = 100;

    ELSE
        SET v_limite = p_limite;
    END IF;


    -- Offset nunca pode ser negativo
    IF p_offset IS NULL OR p_offset < 0 THEN
        SET v_offset = 0;

    ELSE
        SET v_offset = p_offset;
    END IF;


    -- =====================================================
    -- CONSULTA PRINCIPAL
    -- =====================================================

    SELECT

        id,

        produto,

        categoria,

        preco,

        estoque,

        quantidade_vendida,

        faturamento

    FROM vw_produtos_analitico

    WHERE

        (
            v_busca = ''

            OR produto LIKE CONCAT('%', v_busca, '%')

            OR categoria LIKE CONCAT('%', v_busca, '%')
        )

        AND

        (
            v_categoria_id IS NULL

            OR categoria_id = v_categoria_id
        )

    ORDER BY produto ASC

    LIMIT v_offset, v_limite;

END$$

--
-- Funções
--
DROP FUNCTION IF EXISTS `fn_calcular_faturamento`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_calcular_faturamento` (`p_quantidade` INT, `p_valor_unitario` DECIMAL(10,2)) RETURNS DECIMAL(12,2) DETERMINISTIC BEGIN
    RETURN p_quantidade * p_valor_unitario;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`) VALUES
(1, 'Tintas', 'Tintas para diferentes técnicas artísticas'),
(2, 'Pincéis', 'Pincéis para pintura e acabamento'),
(3, 'Desenho', 'Materiais para desenho e ilustração'),
(4, 'Papéis', 'Papéis para desenho, pintura e aquarela'),
(5, 'Telas', 'Telas e superfícies para pintura'),
(6, 'Aquarela', 'Materiais específicos para aquarela'),
(7, 'Materiais para Modelagem', 'Materiais para modelagem, escultura e trabalhos tridimensionais'),
(8, 'Colagem e Artesanato', 'Materiais para colagem, artesanato e trabalhos manuais'),
(9, 'Ferramentas Artísticas', 'Ferramentas utilizadas na produção e acabamento de trabalhos artísticos'),
(10, 'Acessórios', 'Acessórios e complementos para atividades artísticas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

DROP TABLE IF EXISTS `itens_venda`;
CREATE TABLE `itens_venda` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_venda`
--

INSERT INTO `itens_venda` (`id`, `venda_id`, `produto_id`, `quantidade`, `valor_unitario`) VALUES
(1, 1, 9, 1, 22.50),
(2, 1, 8, 1, 18.90),
(3, 2, 8, 1, 18.90),
(4, 3, 7, 1, 45.00),
(5, 4, 1, 2, 25.90),
(6, 4, 7, 1, 45.00),
(7, 4, 5, 1, 28.90),
(8, 5, 1, 1, 25.90),
(9, 5, 6, 1, 39.90),
(10, 6, 10, 1, 15.90),
(11, 7, 10, 1, 15.90),
(12, 7, 6, 1, 39.90),
(13, 8, 5, 1, 28.90),
(14, 9, 5, 1, 28.90),
(15, 9, 3, 1, 12.90),
(16, 10, 7, 1, 45.00),
(17, 11, 4, 1, 8.50),
(18, 12, 6, 1, 39.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `categoria_id` int(11) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `estoque`, `categoria_id`, `ativo`) VALUES
(1, 'Tinta Acrílica 100ml', 'Tinta acrílica para pintura artística.', 25.90, 27, 1, 1),
(2, 'Tinta Óleo 60ml', 'Tinta a óleo para pintura sobre tela.', 32.50, 20, 1, 1),
(3, 'Pincel Redondo nº 8', 'Pincel para detalhes e acabamento.', 12.90, 44, 2, 1),
(4, 'Lápis 6B', 'Lápis de grafite macio para desenho.', 8.50, 49, 3, 1),
(5, 'Papel para Aquarela A4', 'Papel de alta gramatura para aquarela.', 28.90, 22, 4, 1),
(6, 'Tela 30x40', 'Tela de pintura com acabamento em algodão.', 39.90, 15, 5, 1),
(7, 'Kit Aquarela 12 Cores', 'Kit com 12 cores para pintura em aquarela.', 45.00, 12, 6, 1),
(8, 'Caneta Nanquim Preta', 'Caneta para desenho técnico, ilustração e contornos.', 18.90, 33, 3, 1),
(9, 'Bloco de Papel para Desenho A4', 'Bloco com folhas próprias para desenhos e ilustrações.', 22.50, 39, 4, 1),
(10, 'Pincel Chanfrado nº 10', 'Pincel chanfrado para detalhes e diferentes técnicas de pintura.', 15.90, 29, 2, 1),
(11, 'Tinta Acrílica Azul Cobalto 100ml', 'Tinta acrílica de alta qualidade para pintura artística e trabalhos sobre tela.', 27.90, 18, 1, 1),
(12, 'Tinta Acrílica Vermelho Carmim 100ml', 'Tinta acrílica com pigmentação intensa para diferentes técnicas de pintura.', 27.90, 25, 1, 1),
(13, 'Tinta Acrílica Amarelo Limão 100ml', 'Tinta acrílica indicada para pintura artística, ilustração e trabalhos decorativos.', 26.90, 14, 1, 1),
(14, 'Tinta Guache Escolar 250ml', 'Tinta guache de fácil aplicação para trabalhos artísticos e escolares.', 19.90, 32, 1, 1),
(15, 'Pincel Chato nº 4', 'Pincel de cerdas macias indicado para preenchimentos e detalhes.', 9.90, 35, 2, 1),
(16, 'Pincel Chato nº 12', 'Pincel para áreas maiores, fundos e aplicações de tinta.', 14.90, 27, 2, 1),
(17, 'Pincel Língua de Gato nº 8', 'Pincel versátil para pintura, detalhes e criação de diferentes traços.', 16.90, 11, 2, 1),
(18, 'Pincel Filete nº 2', 'Pincel fino para contornos, linhas e pequenos detalhes.', 8.90, 7, 2, 1),
(19, 'Lápis 2B', 'Lápis de grafite macio indicado para desenhos, esboços e sombreamento.', 7.50, 40, 3, 1),
(20, 'Lápis 4B', 'Lápis de grafite macio para sombreados e desenhos artísticos.', 7.90, 36, 3, 1),
(21, 'Lápis 8B', 'Lápis de grafite extra macio para sombras intensas e desenhos detalhados.', 8.90, 22, 3, 1),
(22, 'Borracha Artística Branca', 'Borracha macia indicada para correções e detalhes em desenhos.', 5.90, 48, 3, 1),
(23, 'Papel Canson A4 180g', 'Papel de alta gramatura para desenhos, ilustrações e técnicas secas.', 24.90, 30, 4, 1),
(24, 'Papel Canson A3 180g', 'Papel artístico de alta gramatura para desenhos e trabalhos de maior formato.', 39.90, 16, 4, 1),
(25, 'Papel Kraft A3', 'Papel kraft para desenhos, projetos artesanais e trabalhos criativos.', 18.90, 26, 4, 1),
(26, 'Papel Vegetal A4', 'Papel translúcido indicado para desenhos técnicos, decalques e projetos artísticos.', 15.90, 9, 4, 1),
(27, 'Tela 20x30cm', 'Tela de algodão preparada para pinturas com diferentes técnicas.', 24.90, 20, 5, 1),
(28, 'Tela 40x50cm', 'Tela de algodão para pinturas artísticas em formato médio.', 49.90, 13, 5, 1),
(29, 'Tela 50x70cm', 'Tela de pintura em tamanho amplo para trabalhos artísticos detalhados.', 69.90, 8, 5, 1),
(30, 'Tela Redonda 30cm', 'Tela circular preparada para pintura decorativa e artística.', 44.90, 5, 5, 1),
(31, 'Estojo de Aquarela 24 Cores', 'Estojo com 24 cores de aquarela para ilustrações e pinturas artísticas.', 59.90, 17, 6, 1),
(32, 'Pincel para Aquarela nº 6', 'Pincel de ponta macia desenvolvido para técnicas de aquarela.', 13.90, 24, 6, 1),
(33, 'Pincel para Aquarela nº 10', 'Pincel de ponta macia para preenchimentos e áreas maiores.', 17.90, 12, 6, 1),
(34, 'Godê Plástico para Aquarela', 'Godê com divisórias para mistura e organização de tintas.', 11.90, 6, 6, 1),
(35, 'Massa para Modelagem Branca 500g', 'Massa para modelagem indicada para esculturas e trabalhos tridimensionais.', 22.90, 21, 7, 1),
(36, 'Massa para Modelagem Terracota 500g', 'Massa de modelagem em tom terracota para esculturas e peças artesanais.', 24.90, 15, 7, 1),
(37, 'Argila para Modelagem 1kg', 'Argila para trabalhos de modelagem, escultura e criação de peças.', 18.90, 28, 7, 1),
(38, 'Estecas para Modelagem com 6 Peças', 'Conjunto de ferramentas para cortes, texturas e acabamentos em modelagem.', 29.90, 10, 7, 1),
(39, 'Cola Branca Artesanal 500g', 'Cola branca para colagens, artesanato e trabalhos artísticos.', 16.90, 33, 8, 1),
(40, 'Cola em Bastão 20g', 'Cola em bastão para papéis, cartolinas e trabalhos artesanais.', 6.90, 45, 8, 1),
(41, 'Fita Adesiva Decorativa 10m', 'Fita adesiva decorativa para personalização e trabalhos artesanais.', 12.90, 19, 8, 1),
(42, 'Kit de Papéis Coloridos', 'Conjunto de papéis coloridos para colagens, artesanato e projetos criativos.', 21.90, 23, 8, 1),
(43, 'Estilete Artístico de Precisão', 'Estilete de precisão para cortes detalhados em papéis e materiais artísticos.', 18.90, 14, 9, 1),
(44, 'Régua de Acrílico 30cm', 'Régua transparente para medições e criação de linhas precisas.', 9.90, 31, 9, 1),
(45, 'Esquadro 45 Graus', 'Esquadro para desenho técnico, composição e criação de linhas geométricas.', 12.90, 18, 9, 1),
(46, 'Base de Corte A3', 'Base de corte para proteção da superfície durante trabalhos manuais.', 54.90, 4, 9, 1),
(47, 'Paleta Plástica para Pintura', 'Paleta com divisórias para mistura e organização de tintas.', 13.90, 26, 10, 1),
(48, 'Porta-Pincéis de Mesa', 'Organizador para armazenar pincéis e manter os materiais de pintura organizados.', 27.90, 12, 10, 1),
(49, 'Avental para Pintura', 'Avental para proteção durante atividades de pintura e trabalhos artísticos.', 34.90, 9, 10, 1),
(50, 'Caixa Organizadora para Materiais', 'Caixa organizadora para armazenamento de materiais e acessórios artísticos.', 42.90, 3, 10, 1);

--
-- Acionadores `produtos`
--
DROP TRIGGER IF EXISTS `antes_atualizar_produto`;
DELIMITER $$
CREATE TRIGGER `antes_atualizar_produto` BEFORE UPDATE ON `produtos` FOR EACH ROW BEGIN

    IF NEW.preco < 0 THEN
        SET NEW.preco = ABS(NEW.preco);
    END IF;

    IF NEW.estoque < 0 THEN
        SET NEW.estoque = ABS(NEW.estoque);
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'vendedor',
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `ativo`) VALUES
(1, 'Ruby', 'ruby@gmail.com', '$2y$10$7VmnM.90cH/WzGELzhbkcu.woi95dwuc3lnq2.zz514oVGc0I1TC2', 'vendedor', 1),
(2, 'Felicia', 'felicia@gmail.com', '$2y$10$qDcJGHacEBl2ACIzzr00NOJOaz6JDg7mnlsT8w8lwIczj0Fb8Jvua', 'usuario', 1),
(3, 'Admin', 'admin@gmail.com', '$2y$10$hL67xPJfasPHglbXjaecAO2H47wFlOlJeLq8GwgLhhpJedJgmCAEa', 'admin', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_venda` datetime NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `usuario_id`, `data_venda`, `total`) VALUES
(1, 1, '2026-08-23 18:51:12', 41.40),
(2, 1, '2026-08-23 19:08:50', 18.90),
(3, 1, '2026-08-23 19:10:40', 45.00),
(4, 1, '2026-08-23 19:26:40', 125.70),
(5, 1, '2026-08-23 19:27:51', 65.80),
(6, 2, '2026-08-23 20:47:33', 15.90),
(7, 3, '2026-08-23 22:40:15', 55.80),
(8, 1, '2026-08-23 22:41:14', 28.90),
(9, 1, '2026-09-06 02:05:48', 41.80),
(10, 3, '2026-09-13 03:48:32', 45.00),
(11, 3, '2026-09-13 04:49:54', 8.50),
(12, 3, '2026-09-13 04:53:24', 39.90);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_produtos_analitico`
-- (Veja abaixo para a visão atual)
--
DROP VIEW IF EXISTS `vw_produtos_analitico`;
CREATE TABLE `vw_produtos_analitico` (
`id` int(11)
,`produto` varchar(150)
,`categoria_id` int(11)
,`categoria` varchar(100)
,`preco` decimal(10,2)
,`estoque` int(11)
,`quantidade_vendida` decimal(32,0)
,`faturamento` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_produtos_analitico`
--
DROP TABLE IF EXISTS `vw_produtos_analitico`;

DROP VIEW IF EXISTS `vw_produtos_analitico`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_produtos_analitico`  AS WITH vendas_produtos AS (SELECT `iv`.`produto_id` AS `produto_id`, sum(`iv`.`quantidade`) AS `quantidade_vendida`, sum(`fn_calcular_faturamento`(`iv`.`quantidade`,`iv`.`valor_unitario`)) AS `faturamento` FROM `itens_venda` AS `iv` GROUP BY `iv`.`produto_id`) SELECT `p`.`id` AS `id`, `p`.`nome` AS `produto`, `p`.`categoria_id` AS `categoria_id`, `c`.`nome` AS `categoria`, `p`.`preco` AS `preco`, `p`.`estoque` AS `estoque`, coalesce(`vp`.`quantidade_vendida`,0) AS `quantidade_vendida`, coalesce(`vp`.`faturamento`,0.00) AS `faturamento` FROM ((`produtos` `p` join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) left join `vendas_produtos` `vp` on(`p`.`id` = `vp`.`produto_id`)) WHERE `p`.`ativo` = 11  ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_venda` (`venda_id`),
  ADD KEY `fk_item_produto` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_categoria` (`categoria_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_venda_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `fk_item_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  ADD CONSTRAINT `fk_item_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `fk_venda_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
