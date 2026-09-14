<?php

session_start();
require_once "../config.php";

$produtosPorPagina = 8;

$paginaAtual = filter_input(
    INPUT_GET,
    "pagina",
    FILTER_VALIDATE_INT
);

if ($paginaAtual === false || $paginaAtual === null || $paginaAtual < 1) {
    $paginaAtual = 1;
}

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

$busca = trim($_GET["busca"] ?? "");
$parametros = [];
$filtros = "WHERE p.ativo = TRUE";

if ($categoriaSelecionada !== null) {
    $filtros .= " AND c.id = ?";
    $parametros[] = $categoriaSelecionada;
}

if ($busca !== "") {
    $filtros .= " AND (
        p.nome LIKE ?
        OR p.descricao LIKE ?
    )";

    $parametros[] = "%" . $busca . "%";
    $parametros[] = "%" . $busca . "%";
}

try {
    // BD - SELECT / COUNT
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

    $totalPaginas = max(
        1,
        (int) ceil($totalProdutos / $produtosPorPagina)
    );

    if ($paginaAtual > $totalPaginas) {
        $paginaAtual = $totalPaginas;
    }

    $offset = ($paginaAtual - 1) * $produtosPorPagina;

    // BD - SELECT / JOIN
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

    <!-- DW - Bootstrap -->
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

    <?php
    $base = "../";
    require_once "../componentes/navbar.php";
    ?>

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

    <main class="container pb-5">
        <div class="row g-4">

            <?php if (count($produtos) > 0): ?>

                <?php foreach ($produtos as $produto): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card card-produto">

                            <div class="produto-imagem">
                                <img
                                    src="../src/img/produto-padrao.jpg"
                                    alt="<?= htmlspecialchars(
                                        $produto["nome"]
                                    ) ?>"
                                >
                            </div>

                            <div class="card-body">
                                <span class="badge">
                                    <?= htmlspecialchars(
                                        $produto["categoria"]
                                    ) ?>
                                </span>

                                <h5 class="card-title">
                                    <?= htmlspecialchars(
                                        $produto["nome"]
                                    ) ?>
                                </h5>

                                <p class="card-text">
                                    <?= htmlspecialchars(
                                        $produto["descricao"]
                                    ) ?>
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

                                <div class="estoque-produto">
                                    Estoque:
                                    <?= $produto["estoque"] ?>
                                    unidades
                                </div>

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
                    <!-- DW - Bootstrap / Alert -->
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

            <!-- DW - Bootstrap / Pagination -->
            <nav
                class="d-flex justify-content-center mt-5"
                aria-label="Navegação de páginas"
            >
                <ul class="pagination">

                    <li
                        class="page-item
                        <?= $paginaAtual <= 1 ? "disabled" : "" ?>"
                    >
                        <a
                            class="page-link"
                            href="?pagina=<?= $paginaAtual - 1 ?><?php
                                if ($categoriaSelecionada !== null):
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

                    <li
                        class="page-item
                        <?= $paginaAtual >= $totalPaginas
                            ? "disabled"
                            : "" ?>"
                    >
                        <a
                            class="page-link"
                            href="?pagina=<?= $paginaAtual + 1 ?><?php
                                if ($categoriaSelecionada !== null):
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

    <?php require_once "../componentes/footer.php"; ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>

</html>