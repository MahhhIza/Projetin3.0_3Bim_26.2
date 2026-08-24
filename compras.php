<?php

session_start();

require_once "config.php";

/*
 * Apenas usuários logados podem acessar.
 */
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];

$compras = [];

try {

    /*
     * Busca somente as compras
     * pertencentes ao usuário logado.
     */
    $sql = "
        SELECT
            v.id,
            v.data_venda,
            v.total,
            COUNT(iv.id) AS quantidade_itens
        FROM vendas v
        INNER JOIN itens_venda iv
            ON iv.venda_id = v.id
        WHERE v.usuario_id = ?
        GROUP BY
            v.id,
            v.data_venda,
            v.total
        ORDER BY v.data_venda DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuarioId]);

    $compras = $stmt->fetchAll();

} catch (PDOException $e) {

    $compras = [];

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

    <title>Minhas compras - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="src/css/style.css"
    >

    <style>

        body {
            background-color: #f8f9fa;
        }

        .cabecalho-compras {
            padding: 45px 0 30px;
        }

        .cabecalho-compras h1 {
            font-weight: bold;
        }

        .card-compra {
            border: none;
            border-radius: 12px;
            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .card-compra:hover {
            transform: translateY(-3px);
        }

        .numero-compra {
            font-weight: bold;
            color: #7b1fa2;
        }

        .valor-compra {
            font-size: 21px;
            font-weight: bold;
            color: #7b1fa2;
        }

    </style>

</head>

<body>

<?php

$base = "";

require_once "componentes/navbar.php";

?>


<!-- =========================================
     CABEÇALHO
========================================= -->

<section class="cabecalho-compras">

    <div class="container">

        <h1>
            🛍️ Minhas compras
        </h1>

        <p class="text-muted">
            Confira o histórico das compras realizadas na Art&Co.
        </p>

    </div>

</section>


<!-- =========================================
     COMPRAS
========================================= -->

<main class="container pb-5">

    <?php if (count($compras) > 0): ?>

        <div class="row g-4">

            <?php foreach ($compras as $compra): ?>

                <div class="col-12 col-md-6">

                    <div class="card card-compra h-100">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between align-items-start">

                                <div>

                                    <div class="numero-compra">

                                        Compra #<?= $compra["id"] ?>

                                    </div>

                                    <small class="text-muted">

                                        <?= date(
                                            "d/m/Y H:i",
                                            strtotime($compra["data_venda"])
                                        ) ?>

                                    </small>

                                </div>

                                <span class="badge bg-success">

                                    Concluída

                                </span>

                            </div>


                            <hr>


                            <p class="mb-2">

                                <strong>
                                    <?= $compra["quantidade_itens"] ?>
                                </strong>

                                item(ns)

                            </p>


                            <div class="valor-compra">

                                R$

                                <?= number_format(
                                    $compra["total"],
                                    2,
                                    ",",
                                    "."
                                ) ?>

                            </div>


                            <a
                                href="compra.php?id=<?= $compra["id"] ?>"
                                class="btn btn-outline-primary w-100 mt-3"
                            >

                                Ver detalhes

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


    <?php else: ?>

        <div class="alert alert-info text-center">

            <h5 class="fw-bold">
                Você ainda não realizou nenhuma compra.
            </h5>

            <p class="mb-3">
                Quando você realizar uma compra,
                ela aparecerá aqui.
            </p>

            <a
                href="produtos/produtos.php"
                class="btn btn-primary"
            >

                Ver produtos

            </a>

        </div>

    <?php endif; ?>

</main>

<?php
require_once "componentes/footer.php";
?>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>