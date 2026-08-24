<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];
$vendaId = $_GET["id"] ?? null;

$venda = null;
$itens = [];

if ($vendaId !== null) {

    try {

        // Busca a venda garantindo que pertence ao usuário logado
        $sql = "SELECT
                    v.id,
                    v.data_venda,
                    v.total
                FROM vendas v
                WHERE v.id = ?
                AND v.usuario_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $vendaId,
            $usuarioId
        ]);

        $venda = $stmt->fetch();

        if ($venda) {

            // Busca os produtos da venda
            $sqlItens = "SELECT
                            iv.quantidade,
                            iv.valor_unitario,
                            p.nome AS produto
                         FROM itens_venda iv
                         INNER JOIN produtos p
                             ON p.id = iv.produto_id
                         WHERE iv.venda_id = ?";

            $stmtItens = $pdo->prepare($sqlItens);
            $stmtItens->execute([$vendaId]);

            $itens = $stmtItens->fetchAll();
        }

    } catch (PDOException $e) {

        $venda = null;
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

</head>

<body>

<?php

$base = "";

require_once "componentes/navbar.php";

?>

<main class="container py-5">

    <?php if ($venda): ?>

        <div class="mb-4">

            <h1 class="fw-bold">
                Compra #<?= $venda["id"] ?>
            </h1>

            <p class="text-muted">
                Realizada em
                <?= date(
                    "d/m/Y H:i",
                    strtotime($venda["data_venda"])
                ) ?>
            </p>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <h4 class="fw-bold mb-4">
                    Produtos comprados
                </h4>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>
                                    Produto
                                </th>

                                <th>
                                    Quantidade
                                </th>

                                <th>
                                    Valor unitário
                                </th>

                                <th>
                                    Subtotal
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($itens as $item): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            $item["produto"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $item["quantidade"] ?>
                                    </td>

                                    <td>
                                        R$
                                        <?= number_format(
                                            $item["valor_unitario"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>
                                    </td>

                                    <td class="fw-bold">

                                        R$
                                        <?= number_format(
                                            $item["quantidade"]
                                            * $item["valor_unitario"],
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

                <hr>

                <div class="text-end">

                    <span class="fs-5">
                        Total:
                    </span>

                    <strong class="fs-3 text-success">

                        R$
                        <?= number_format(
                            $venda["total"],
                            2,
                            ",",
                            "."
                        ) ?>

                    </strong>

                </div>

            </div>

        </div>

        <div class="mt-4">

            <a
                href="finalizar.php"
                class="btn btn-secondary"
            >
                ← Voltar para minhas compras
            </a>

        </div>

    <?php else: ?>

        <div class="alert alert-warning text-center">

            <h4 class="fw-bold">
                Compra não encontrada.
            </h4>

            <p>
                A venda solicitada não existe ou não pertence à sua conta.
            </p>

            <a
                href="finalizar.php"
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