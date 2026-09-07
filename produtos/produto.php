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

        /* =========================================
           IMAGEM DO PRODUTO
        ========================================= */

        .produto-imagem {

            width: 100%;
            height: 300px;

            overflow: hidden;

            border-radius: 12px 12px 0 0;

            position: relative;

        }


        .produto-imagem img {

            width: 100%;
            height: 100%;

            object-fit: cover;

            display: block;

            filter: blur(0.3px);

        }


        /* =========================================
           SELO DE PRODUTO ESGOTADO
        ========================================= */

        .selo-esgotado {

            position: absolute;

            top: 50%;
            left: 50%;

            transform:
                translate(-50%, -50%)
                rotate(-8deg);

            background-color: #dc3545;

            color: #ffffff;

            padding: 14px 40px;

            font-size: 1.7rem;

            font-weight: 800;

            letter-spacing: 1px;

            border-radius: 8px;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.25);

            z-index: 2;

            white-space: nowrap;

        }


        /* =========================================
           AJUSTE PARA TELAS MENORES
        ========================================= */

        @media (max-width: 576px) {

            .selo-esgotado {

                font-size: 1.3rem;

                padding: 10px 25px;

            }

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


                    <!-- =====================================
                         IMAGEM DO PRODUTO
                    ====================================== -->

                    <div class="produto-imagem">

                        <img
                            src="../src/img/produto-padrao.jpg"
                            alt="<?= htmlspecialchars($produto["nome"]) ?>"
                        >


                        <?php if ((int) $produto["estoque"] <= 0): ?>

                            <span class="selo-esgotado">

                                ESGOTADO

                            </span>

                        <?php endif; ?>


                    </div>


                    <div class="card-body p-4">


                        <!-- Categoria -->

                        <span class="badge bg-secondary mb-3">

                            <?= htmlspecialchars($produto["categoria"]) ?>

                        </span>


                        <!-- Nome -->

                        <h1 class="fw-bold">

                            <?= htmlspecialchars($produto["nome"]) ?>

                        </h1>


                        <!-- Descrição -->

                        <p class="text-muted mt-3">

                            <?= htmlspecialchars($produto["descricao"]) ?>

                        </p>


                        <!-- Preço -->

                        <h2 class="preco-produto mt-4">

                            R$

                            <?= number_format(
                                $produto["preco"],
                                2,
                                ",",
                                "."
                            ) ?>

                        </h2>


                        <!-- Estoque -->

                        <?php if ((int) $produto["estoque"] > 0): ?>

                            <p class="mt-3">

                                <strong>Estoque:</strong>

                                <?= $produto["estoque"] ?>

                                unidades

                            </p>


                        <?php else: ?>


                            <div class="alert alert-danger mt-3 mb-0">

                                <strong>Produto esgotado.</strong>

                                <br>

                                Este produto não está disponível
                                para compra no momento.

                            </div>


                        <?php endif; ?>


                        <!-- =====================================
                             BOTÃO DE COMPRA
                        ====================================== -->

                        <?php if ((int) $produto["estoque"] > 0): ?>


                            <a
                                href="../carrinho.php?adicionar=<?= $produto["id"] ?>"
                                class="btn btn-primary mt-3"
                            >

                                🛒 Adicionar ao carrinho

                            </a>


                        <?php else: ?>


                            <span
                                class="btn btn-secondary mt-3 disabled"
                                aria-disabled="true"
                            >

                                Produto esgotado

                            </span>


                        <?php endif; ?>


                        <!-- Botão voltar -->

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

require_once "../componentes/footer.php";

?>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>