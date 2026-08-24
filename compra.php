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
$vendaId = $_GET["id"] ?? null;

$compra = null;
$itens = [];

if ($vendaId !== null && is_numeric($vendaId)) {

    try {

        /*
         * Busca a compra.
         *
         * O usuario_id é verificado junto com o id da venda.
         * Assim, um usuário não consegue visualizar
         * a compra de outro usuário.
         */
        $sql = "
            SELECT
                id,
                data_venda,
                total
            FROM vendas
            WHERE id = ?
              AND usuario_id = ?
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            (int) $vendaId,
            $usuarioId
        ]);

        $compra = $stmt->fetch();


        /*
         * Se a compra existir, busca seus produtos.
         */
        if ($compra) {

            $sql = "
                SELECT
                    iv.quantidade,
                    iv.valor_unitario,
                    p.nome
                FROM itens_venda iv
                INNER JOIN produtos p
                    ON p.id = iv.produto_id
                WHERE iv.venda_id = ?
                ORDER BY p.nome ASC
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                (int) $vendaId
            ]);

            $itens = $stmt->fetchAll();
        }

    } catch (PDOException $e) {

        $compra = null;
        $itens = [];

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

    <title>Detalhes da compra - Art&Co</title>

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

        .cabecalho-compra {
            padding: 45px 0 30px;
        }

        .cabecalho-compra h1 {
            font-weight: bold;
        }

        .card-detalhes {
            border: none;
            border-radius: 12px;
            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .preco-compra {
            font-size: 22px;
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
     CONTEÚDO
========================================= -->

<main class="container py-5">

    <?php if ($compra): ?>

        <!-- CABEÇALHO -->

        <div class="cabecalho-compra">

            <h1>
                🧾 Compra #<?= $compra["id"] ?>
            </h1>

            <p class="text-muted">

                Realizada em

                <?= date(
                    "d/m/Y H:i",
                    strtotime($compra["data_venda"])
                ) ?>

            </p>

        </div>


        <!-- PRODUTOS -->

        <div class="card card-detalhes">

            <div class="card-body p-4">

                <h4 class="fw-bold mb-4">
                    Produtos da compra
                </h4>


                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>
                                    Produto
                                </th>

                                <th class="text-center">
                                    Quantidade
                                </th>

                                <th class="text-end">
                                    Valor unitário
                                </th>

                                <th class="text-end">
                                    Subtotal
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($itens as $item): ?>

                                <?php

                                $subtotal =
                                    $item["quantidade"]
                                    * $item["valor_unitario"];

                                ?>

                                <tr>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item["nome"]
                                        ) ?>

                                    </td>

                                    <td class="text-center">

                                        <?= $item["quantidade"] ?>

                                    </td>

                                    <td class="text-end">

                                        R$

                                        <?= number_format(
                                            $item["valor_unitario"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>

                                    </td>

                                    <td class="text-end fw-bold">

                                        R$

                                        <?= number_format(
                                            $subtotal,
                                            2,
                                            ",",
                                            "."
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- TOTAL -->

                <hr>

                <div class="text-end">

                    <span class="text-muted">
                        Total da compra:
                    </span>

                    <div class="preco-compra">

                        R$

                        <?= number_format(
                            $compra["total"],
                            2,
                            ",",
                            "."
                        ) ?>

                    </div>

                </div>


                <!-- BOTÕES -->

                <div class="d-flex justify-content-between mt-4">

                    <a
                        href="compras.php"
                        class="btn btn-outline-secondary"
                    >
                        ← Minhas compras
                    </a>

                    <a
                        href="produtos/produtos.php"
                        class="btn btn-primary"
                    >
                        Continuar comprando
                    </a>

                </div>

            </div>

        </div>


    <?php else: ?>

        <!-- COMPRA NÃO ENCONTRADA -->

        <div class="alert alert-warning text-center">

            <h4 class="fw-bold">
                Compra não encontrada.
            </h4>

            <p>
                A compra solicitada não existe
                ou não pertence à sua conta.
            </p>

            <a
                href="compras.php"
                class="btn btn-primary"
            >
                Voltar para minhas compras
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