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

    <link
    rel="stylesheet"
    href="../src/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../src/css/produtos.css"
    >

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

            <h1 class="titulo-pagina">

                <?php if ($busca !== ""): ?>

                    Resultados para:
                    "<?= htmlspecialchars($busca) ?>" 🔎

                <?php elseif ($categoriaSelecionada !== null): ?>

                    Produtos da categoria

                <?php else: ?>

                    Nossos Produtos

                <?php endif; ?>

            </h1>


            <p class="subtitulo-pagina">

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