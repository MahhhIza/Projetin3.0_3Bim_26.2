<?php

session_start();

require_once "../config.php";

/*
 * Quantidade de produtos por página.
 */
$produtosPorPagina = 6;

/*
 * Página atual.
 */
$paginaAtual = filter_input(
    INPUT_GET,
    "pagina",
    FILTER_VALIDATE_INT
);

if ($paginaAtual === false || $paginaAtual === null || $paginaAtual < 1) {
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
     *
     * Descobrimos quantos produtos existem
     * depois dos filtros.
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
        (int) ceil($totalProdutos / $produtosPorPagina)
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
    $offset = (
        $paginaAtual - 1
    ) * $produtosPorPagina;

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

    <link
    rel="stylesheet"
    href="../src/css/style.css"
    >

    <style>

        body {

            background-color: #f8f9fa;

        }


        /* =========================================
           CABEÇALHO
        ========================================= */

        .cabecalho-produtos {

            padding: 45px 0 30px;

            text-align: center;

        }


        .cabecalho-produtos h1 {

            font-weight: bold;

        }


        .cabecalho-produtos p {

            color: #6c757d;

        }


        /* =========================================
           CARD DO PRODUTO
        ========================================= */

        .card-produto {

            height: 100%;

            border: none;

            border-radius: 12px;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.08);

            transition: transform 0.2s;

        }


        .card-produto:hover {

            transform: translateY(-4px);

        }


        .produto-icone {

            font-size: 55px;

            text-align: center;

            padding: 25px 10px 10px;

        }


        .preco-produto {

            font-size: 22px;

            font-weight: bold;

            color: #7b1fa2;

        }


        .estoque-produto {

            font-size: 14px;

            color: #6c757d;

        }

        .produto-imagem {
            width: 100%;
            height: 220px;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
        }

        .produto-imagem img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: blur(0.3px);
        }


    </style>

</head>


<body>


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

        Produtos da categoria 🎨

    <?php else: ?>

        Nossos Produtos 🎨

    <?php endif; ?>

</h1>

            <p>
                Encontre os materiais perfeitos para suas criações.
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


                    <div class="col-12 col-md-6 col-lg-4">

                        <div class="card card-produto">


                            <!-- Ícone temporário -->

                            <div class="produto-imagem">
                                <img
                                    src="../src/img/produto-padrao.jpg"
                                    alt="<?= htmlspecialchars($produto["nome"]) ?>"
                                >
                            </div>


                            <div class="card-body">


                                <span class="badge bg-secondary mb-2">

                                    <?= htmlspecialchars($produto["categoria"]) ?>

                                </span>


                                <h5 class="card-title">

                                    <?= htmlspecialchars($produto["nome"]) ?>

                                </h5>


                                <p class="card-text text-muted">

                                    <?= htmlspecialchars($produto["descricao"]) ?>

                                </p>


                                <div class="preco-produto">

                                    R$

                                    <?= number_format(
                                        $produto["preco"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                </div>


                                <div class="estoque-produto mt-2">

                                    Estoque:
                                    <?= $produto["estoque"] ?>
                                    unidades

                                </div>


                                <a
    href="produto.php?id=<?= $produto['id'] ?>"
    class="btn btn-primary w-100 mt-3"
>
    Ver produto
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

                No momento não existem produtos disponíveis.

            <?php endif; ?>

        </div>

    </div>

<?php endif; ?>


        </div>

        <?php if ($totalPaginas > 1): ?>

    <nav
        class="d-flex justify-content-center mt-5"
        aria-label="Navegação de páginas"
    >

        <ul class="pagination">

            <!-- Página anterior -->
            <li
                class="page-item
                <?= $paginaAtual <= 1 ? "disabled" : "" ?>"
            >

                <a
                    class="page-link"
                    href="?pagina=<?= $paginaAtual - 1 ?>
                    <?php if ($categoriaSelecionada !== null): ?>
                        &categoria=<?= $categoriaSelecionada ?>
                    <?php endif; ?>
                    <?php if ($busca !== ""): ?>
                        &busca=<?= urlencode($busca) ?>
                    <?php endif; ?>"
                >
                    ← Anterior
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
                    <?= $pagina === $paginaAtual ? "active" : "" ?>"
                >

                    <a
                        class="page-link"
                        href="?pagina=<?= $pagina ?>
                        <?php if ($categoriaSelecionada !== null): ?>
                            &categoria=<?= $categoriaSelecionada ?>
                        <?php endif; ?>
                        <?php if ($busca !== ""): ?>
                            &busca=<?= urlencode($busca) ?>
                        <?php endif; ?>"
                    >
                        <?= $pagina ?>
                    </a>

                </li>

            <?php endfor; ?>


            <!-- Próxima página -->
            <li
                class="page-item
                <?= $paginaAtual >= $totalPaginas ? "disabled" : "" ?>"
            >

                <a
                    class="page-link"
                    href="?pagina=<?= $paginaAtual + 1 ?>
                    <?php if ($categoriaSelecionada !== null): ?>
                        &categoria=<?= $categoriaSelecionada ?>
                    <?php endif; ?>
                    <?php if ($busca !== ""): ?>
                        &busca=<?= urlencode($busca) ?>
                    <?php endif; ?>"
                >
                    Próxima →
                </a>

            </li>

        </ul>

    </nav>


    <p class="text-center text-muted mt-2">

        Página <?= $paginaAtual ?>
        de <?= $totalPaginas ?>

        •
        <?= $totalProdutos ?> produto(s)

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