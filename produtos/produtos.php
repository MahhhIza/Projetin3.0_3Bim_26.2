<?php

session_start();

require_once "../config.php";

/*
 * Quantidade de produtos por página.
 */
$produtosPorPagina = 8;

/*
 * Página atual.
 */
$paginaAtual = filter_input(
    INPUT_GET,
    "pagina",
    FILTER_VALIDATE_INT
);

if (
    $paginaAtual === false ||
    $paginaAtual === null ||
    $paginaAtual < 1
) {
    $paginaAtual = 1;
}

/*
 * Categoria selecionada.
 */
$categoriaSelecionada = filter_input(
    INPUT_GET,
    "categoria",
    FILTER_VALIDATE_INT
);

if (
    $categoriaSelecionada === false ||
    $categoriaSelecionada === null ||
    $categoriaSelecionada < 1
) {
    $categoriaSelecionada = null;
}

/*
 * Busca realizada pelo usuário.
 */
$busca = trim($_GET["busca"] ?? "");

/*
 * Parâmetros utilizados nos filtros.
 */
$parametros = [];

/*
 * Monta os filtros.
 */
$filtros = "WHERE p.ativo = TRUE";

/*
 * Filtro por categoria.
 */
if ($categoriaSelecionada !== null) {

    $filtros .= " AND c.id = ?";

    $parametros[] = $categoriaSelecionada;
}

/*
 * Filtro por pesquisa.
 */
if ($busca !== "") {

    $filtros .= " AND (
        p.nome LIKE ?
        OR p.descricao LIKE ?
    )";

    $parametros[] = "%" . $busca . "%";
    $parametros[] = "%" . $busca . "%";
}

try {

    /*
     * =========================================
     * TOTAL DE PRODUTOS
     * =========================================
     */

    $sqlTotal = "
        SELECT COUNT(*)
        FROM produtos p
        INNER JOIN categorias c
            ON p.categoria_id = c.id
        $filtros
    ";

    $stmtTotal = $pdo->prepare($sqlTotal);

    $stmtTotal->execute($parametros);

    $totalProdutos = (int) $stmtTotal->fetchColumn();

    /*
     * Calcula o total de páginas.
     */
    $totalPaginas = max(
        1,
        (int) ceil(
            $totalProdutos / $produtosPorPagina
        )
    );

    /*
     * Se a página informada não existir,
     * volta para a última página válida.
     */
    if ($paginaAtual > $totalPaginas) {

        $paginaAtual = $totalPaginas;
    }

    /*
     * Calcula o OFFSET.
     */
    $offset =
        ($paginaAtual - 1)
        * $produtosPorPagina;

    /*
     * =========================================
     * BUSCA OS PRODUTOS DA PÁGINA
     * =========================================
     */

    $sql = "
        SELECT

            p.id,
            p.nome,
            p.descricao,
            p.preco,
            p.estoque,
            c.nome AS categoria

        FROM produtos p

        INNER JOIN categorias c
            ON p.categoria_id = c.id

        $filtros

        ORDER BY p.nome ASC

        LIMIT $produtosPorPagina
        OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute($parametros);

    $produtos = $stmt->fetchAll();

} catch (PDOException $e) {

    $produtos = [];

    $totalProdutos = 0;

    $totalPaginas = 1;

    $paginaAtual = 1;
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Produtos - Art&Co</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS principal -->
    <link
        rel="stylesheet"
        href="../src/css/style.css"
    >

    <style>

        /*
        =========================================
        FUNDO GERAL DA PÁGINA
        =========================================

        Um único fundo escuro para toda a página.
        As manchas coloridas são criadas com
        gradientes suaves.
        */

        body.pagina-produtos {

            min-height: 100vh;

            margin: 0;

            background-color: #11131c;

            background-image:

                radial-gradient(
                    ellipse at 10% 20%,
                    rgba(143, 0, 255, 0.16),
                    transparent 30%
                ),

                radial-gradient(
                    ellipse at 85% 15%,
                    rgba(0, 201, 167, 0.14),
                    transparent 28%
                ),

                radial-gradient(
                    ellipse at 70% 50%,
                    rgba(38, 94, 255, 0.10),
                    transparent 32%
                ),

                radial-gradient(
                    ellipse at 15% 75%,
                    rgba(255, 0, 180, 0.08),
                    transparent 30%
                ),

                radial-gradient(
                    ellipse at 90% 85%,
                    rgba(255, 193, 7, 0.08),
                    transparent 25%
                );

            background-attachment: fixed;

        }


        /*
        =========================================
        CABEÇALHO DA PÁGINA
        =========================================
        */

        .cabecalho-produtos {

            position: relative;

            padding: 55px 0 35px;

            background: transparent;

            text-align: left;

            overflow: hidden;

        }


        /*
        Mancha suave atrás do título.
        Ela não é uma caixa branca.
        */

        .cabecalho-produtos::before {

            content: "";

            position: absolute;

            top: -100px;

            left: -150px;

            width: 650px;

            height: 350px;

            background:

                radial-gradient(
                    ellipse,
                    rgba(255, 255, 255, 0.18) 0%,
                    rgba(255, 255, 255, 0.08) 40%,
                    transparent 72%
                );

            filter: blur(45px);

            pointer-events: none;

        }


        .cabecalho-produtos .container {

            position: relative;

            z-index: 2;

        }


        /*
        =========================================
        TÍTULO
        =========================================
        */

        .cabecalho-produtos h1 {

            margin-bottom: 8px;

            font-size: 2.8rem;

            font-weight: 800;

            color: #ffffff;

            letter-spacing: -0.5px;

        }


        .cabecalho-produtos p {

            margin-bottom: 0;

            color: #aeb6c8;

            font-size: 1rem;

        }


        /*
        =========================================
        ÁREA DOS PRODUTOS
        =========================================
        */

        main.container {

            position: relative;

            z-index: 2;

            background: transparent;

        }


        /*
        =========================================
        CARD DO PRODUTO
        =========================================
        */

        .card-produto {

            height: auto;

            overflow: hidden;

            border: 1px solid rgba(
                255,
                255,
                255,
                0.12
            );

            border-radius: 14px;

            background-color: #ffffff;

            box-shadow:

                0 8px 25px
                rgba(0, 0, 0, 0.25);

            transition:

                transform 0.25s ease,

                box-shadow 0.25s ease;

        }


        /*
        =========================================
        EFEITO AO PASSAR O MOUSE
        =========================================
        */

        .card-produto:hover {

            transform: translateY(-5px);

            box-shadow:

                0 14px 30px
                rgba(0, 0, 0, 0.35);

        }


        /*
        =========================================
        ÁREA DA IMAGEM
        =========================================
        */

        .produto-imagem {

            width: 100%;

            height: 235px;

            overflow: hidden;

            background-color: #ffffff;

            border-radius:
                14px 14px 0 0;

        }


        .produto-imagem img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;

        }


        /*
        =========================================
        CORPO DO CARD
        =========================================
        */

        .card-produto .card-body {

            display: flex;

            flex-direction: column;

            padding: 18px;

        }


        /*
        =========================================
        CATEGORIA
        =========================================
        */

        .card-produto .badge {

            display: inline-block;

            width: fit-content;

            padding: 0;

            margin-bottom: 7px;

            background-color:
                transparent !important;

            color:
                #8a45e8 !important;

            font-size: 0.85rem;

            font-weight: 700;

        }


        /*
        =========================================
        NOME DO PRODUTO
        =========================================
        */

        .card-produto .card-title {

            margin-bottom: 7px;

            color: #182033;

            font-size: 1.05rem;

            font-weight: 700;

            line-height: 1.35;

        }


        /*
        =========================================
        DESCRIÇÃO
        =========================================
        */

        .card-produto .card-text {

            min-height: 42px;

            margin-bottom: 8px;

            color: #6f7785;

            font-size: 0.88rem;

            line-height: 1.5;

        }


        /*
        =========================================
        PREÇO
        =========================================
        */

        .preco-produto {

            margin-top: 8px;

            font-size: 1.55rem;

            font-weight: 800;

            color: #8b20c4;

        }


        /*
        =========================================
        ESTOQUE
        =========================================
        */

        .estoque-produto {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-top: 10px;

            color: #07945a;

            font-size: 0.88rem;

            font-weight: 600;

        }


        /*
        =========================================
        BOLINHA DO ESTOQUE
        =========================================
        */

        .estoque-produto::before {

            content: "✓";

            display: inline-flex;

            align-items: center;

            justify-content: center;

            width: 20px;

            height: 20px;

            flex-shrink: 0;

            border-radius: 50%;

            background-color: #07945a;

            color: #ffffff;

            font-size: 0.72rem;

            font-weight: bold;

        }


        /*
        =========================================
        BOTÃO VER PRODUTO
        =========================================
        */

        .btn-ver-produto {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 100%;

            min-height: 42px;

            margin-top: 15px;

            border: none;

            border-radius: 10px;

            background:

                linear-gradient(
                    90deg,
                    #8f00ff,
                    #00c9a7
                );

            color: #ffffff;

            font-weight: 700;

            text-decoration: none;

            transition:

                transform 0.2s ease,

                box-shadow 0.2s ease;

        }


        .btn-ver-produto:hover {

            color: #ffffff;

            transform: translateY(-2px);

            box-shadow:

                0 5px 15px
                rgba(0, 0, 0, 0.18);

        }


        /*
        =========================================
        PAGINAÇÃO
        =========================================
        */

        .pagination {

            gap: 8px;

        }


        .pagination .page-link {

            display: flex;

            align-items: center;

            justify-content: center;

            min-width: 42px;

            height: 42px;

            border:
                1px solid #e0e3e8;

            border-radius: 50%;

            background-color: #ffffff;

            color: #333b4d;

            font-weight: 600;

            box-shadow:

                0 3px 8px
                rgba(0, 0, 0, 0.07);

            transition:

                all 0.2s ease;

        }


        .pagination .page-link:hover {

            background:

                linear-gradient(
                    90deg,
                    #8f00ff,
                    #00c9a7
                );

            border-color: transparent;

            color: #ffffff;

            transform: translateY(-2px);

        }


        .pagination
        .page-item.active
        .page-link {

            background:

                linear-gradient(
                    90deg,
                    #8f00ff,
                    #00c9a7
                );

            border-color: transparent;

            color: #ffffff;

        }


        /*
        =========================================
        TEXTO DA PAGINAÇÃO
        =========================================
        */

        .pagina-produtos
        .text-muted {

            color:
                #aeb6c8 !important;

        }


        /*
        =========================================
        MENSAGEM DE NENHUM PRODUTO
        =========================================
        */

        .pagina-produtos .alert {

            border: none;

            border-radius: 14px;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.20);

        }


        /*
        =========================================
        RESPONSIVIDADE
        =========================================
        */

        @media (max-width: 991.98px) {

            .produto-imagem {

                height: 220px;

            }

        }


        @media (max-width: 575.98px) {

            .cabecalho-produtos {

                padding:
                    40px 0 30px;

            }


            .cabecalho-produtos h1 {

                font-size: 2.2rem;

            }


            .produto-imagem {

                height: 230px;

            }

        }

    </style>

</head>


<body class="pagina-produtos">


    <!-- =========================================
         MENU
    ========================================= -->

    <?php

    $base = "../";

    require_once "../componentes/navbar.php";

    ?>


    <!-- =========================================
         CABEÇALHO DA PÁGINA
    ========================================= -->

    <section class="cabecalho-produtos">

        <div class="container">

            <h1>

                <?php if ($busca !== ""): ?>

                    Resultados para:
                    "<?= htmlspecialchars($busca) ?>" 🔎

                <?php elseif ($categoriaSelecionada !== null): ?>

                    Produtos da categoria

                <?php else: ?>

                    Nossos Produtos

                <?php endif; ?>

            </h1>


            <p>

                Encontre os materiais perfeitos
                para suas criações.

            </p>

        </div>

    </section>


    <!-- =========================================
         PRODUTOS
    ========================================= -->

    <main class="container pb-5">

        <div class="row g-4">


            <?php if (count($produtos) > 0): ?>


                <?php foreach ($produtos as $produto): ?>

                    <div class="col-12 col-sm-6 col-lg-3">

                        <div class="card card-produto">


                            <!-- Imagem -->

                            <div class="produto-imagem">

                                <img
                                    src="../src/img/produto-padrao.jpg"
                                    alt="<?= htmlspecialchars(
                                        $produto["nome"]
                                    ) ?>"
                                >

                            </div>


                            <!-- Corpo do card -->

                            <div class="card-body">


                                <!-- Categoria -->

                                <span class="badge">

                                    <?= htmlspecialchars(
                                        $produto["categoria"]
                                    ) ?>

                                </span>


                                <!-- Nome -->

                                <h5 class="card-title">

                                    <?= htmlspecialchars(
                                        $produto["nome"]
                                    ) ?>

                                </h5>


                                <!-- Descrição -->

                                <p class="card-text">

                                    <?= htmlspecialchars(
                                        $produto["descricao"]
                                    ) ?>

                                </p>


                                <!-- Preço -->

                                <div class="preco-produto">

                                    R$

                                    <?= number_format(
                                        $produto["preco"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                </div>


                                <!-- Estoque -->

                                <div class="estoque-produto">

                                    Estoque:

                                    <?= $produto["estoque"] ?>

                                    unidades

                                </div>


                                <!-- Botão -->

                                <a
                                    href="produto.php?id=<?= $produto["id"] ?>"
                                    class="btn-ver-produto"
                                >

                                    🛒&nbsp; Ver produto

                                </a>


                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>


            <?php else: ?>


                <div class="col-12">

                    <div class="alert alert-info text-center">

                        <?php if ($busca !== ""): ?>

                            <strong>

                                Nenhum produto encontrado.

                            </strong>

                            <br>

                            Não encontramos produtos para

                            "<?= htmlspecialchars($busca) ?>".


                        <?php else: ?>

                            <strong>

                                Nenhum produto cadastrado.

                            </strong>

                            <br>

                            No momento não existem
                            produtos disponíveis.

                        <?php endif; ?>

                    </div>

                </div>


            <?php endif; ?>


        </div>


        <?php if ($totalPaginas > 1): ?>


            <!-- =========================================
                 PAGINAÇÃO
            ========================================= -->

            <nav
                class="d-flex justify-content-center mt-5"
                aria-label="Navegação de páginas"
            >

                <ul class="pagination">


                    <!-- Página anterior -->

                    <li
                        class="page-item
                        <?= $paginaAtual <= 1
                            ? "disabled"
                            : "" ?>"
                    >

                        <a
                            class="page-link"
                            href="?pagina=<?= $paginaAtual - 1 ?><?php
                                if (
                                    $categoriaSelecionada !== null
                                ):
                            ?>&categoria=<?= $categoriaSelecionada ?><?php
                                endif;

                                if ($busca !== ""):
                            ?>&busca=<?= urlencode($busca) ?><?php
                                endif;
                            ?>"
                        >

                            ←

                        </a>

                    </li>


                    <!-- Número das páginas -->

                    <?php for (
                        $pagina = 1;
                        $pagina <= $totalPaginas;
                        $pagina++
                    ): ?>

                        <li
                            class="page-item
                            <?= $pagina === $paginaAtual
                                ? "active"
                                : "" ?>"
                        >

                            <a
                                class="page-link"
                                href="?pagina=<?= $pagina ?><?php
                                    if (
                                        $categoriaSelecionada !== null
                                    ):
                                ?>&categoria=<?= $categoriaSelecionada ?><?php
                                    endif;

                                    if ($busca !== ""):
                                ?>&busca=<?= urlencode($busca) ?><?php
                                    endif;
                                ?>"
                            >

                                <?= $pagina ?>

                            </a>

                        </li>

                    <?php endfor; ?>


                    <!-- Próxima página -->

                    <li
                        class="page-item
                        <?= $paginaAtual >= $totalPaginas
                            ? "disabled"
                            : "" ?>"
                    >

                        <a
                            class="page-link"
                            href="?pagina=<?= $paginaAtual + 1 ?><?php
                                if (
                                    $categoriaSelecionada !== null
                                ):
                            ?>&categoria=<?= $categoriaSelecionada ?><?php
                                endif;

                                if ($busca !== ""):
                            ?>&busca=<?= urlencode($busca) ?><?php
                                endif;
                            ?>"
                        >

                            →

                        </a>

                    </li>


                </ul>

            </nav>


            <p class="text-center text-muted mt-2">

                Página <?= $paginaAtual ?>

                de <?= $totalPaginas ?>

                •

                <?= $totalProdutos ?>

                produto(s)

            </p>


        <?php endif; ?>


    </main>


    <?php

    require_once "../componentes/footer.php";

    ?>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>


</body>

</html>