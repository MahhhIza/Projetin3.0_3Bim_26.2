<?php

session_start();

require_once "../config.php";

$id = $_GET["id"] ?? null;

$produto = null;

if ($id !== null) {

    try {

        $sql = "SELECT
                    p.id,
                    p.nome,
                    p.descricao,
                    p.preco,
                    p.estoque,
                    c.nome AS categoria
                FROM produtos p
                INNER JOIN categorias c
                    ON p.categoria_id = c.id
                WHERE p.id = ?
                AND p.ativo = TRUE";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$id]);

        $produto = $stmt->fetch();

    } catch (PDOException $e) {

        $produto = null;
    }
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

    <title>Produto - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../src/css/style.css"
    >

    <style>
    .produto-imagem {
        width: 100%;
        height: 300px;
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

<?php

$base = "../";

require_once "../componentes/navbar.php";

?>

<main class="container py-5">

    <?php if ($produto): ?>

        <div class="row justify-content-center">

            <div class="col-12 col-lg-8">

                <div class="card shadow-sm border-0">

                    <div class="produto-imagem">
                        <img
                            src="../src/img/produto-padrao.jpg"
                            alt="<?= htmlspecialchars($produto["nome"]) ?>"
                        >
                    </div>

                    <div class="card-body p-4">

                        <span class="badge bg-secondary mb-3">
                            <?= htmlspecialchars($produto["categoria"]) ?>
                        </span>

                        <h1 class="fw-bold">
                            <?= htmlspecialchars($produto["nome"]) ?>
                        </h1>

                        <p class="text-muted mt-3">
                            <?= htmlspecialchars($produto["descricao"]) ?>
                        </p>

                        <h2 class="preco-produto mt-4">
                            R$
                            <?= number_format(
                                $produto["preco"],
                                2,
                                ",",
                                "."
                            ) ?>
                        </h2>

                        <p class="mt-3">
                            <strong>Estoque:</strong>
                            <?= $produto["estoque"] ?>
                            unidades
                        </p>

                        <a
                            href="../carrinho.php?adicionar=<?= $produto["id"] ?>"
                            class="btn btn-primary mt-3"
                        >
                            🛒 Adicionar ao carrinho
                        </a>
                        
                        <a
                            href="produtos.php"
                            class="btn btn-secondary mt-3"
                        >
                            Voltar para produtos
                        </a>

                    </div>

                </div>

            </div>

        </div>

    <?php else: ?>

        <div class="alert alert-warning text-center">

            <strong>Produto não encontrado.</strong>

            <br>

            O produto solicitado não está disponível.

            <br>

            <a
                href="produtos.php"
                class="btn btn-primary mt-3"
            >
                Voltar para produtos
            </a>

        </div>

    <?php endif; ?>

</main>

<?php
require_once "componentes/footer.php";
?>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>