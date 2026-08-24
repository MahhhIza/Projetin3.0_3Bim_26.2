<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];

$compraSucesso = isset($_SESSION["compra_sucesso"]);

unset($_SESSION["compra_sucesso"]);

try {

    $sql = "SELECT 
                v.id,
                v.data_venda,
                v.total
            FROM vendas v
            WHERE v.usuario_id = ?
            ORDER BY v.data_venda DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuarioId]);

    $vendas = $stmt->fetchAll();

} catch (PDOException $e) {

    $vendas = [];
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

    <title>Compra realizada - Art&Co</title>

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

    <div class="text-center mb-5">

        <div
            class="alert alert-success"
            role="alert"
        >

            <h2 class="fw-bold">
                Compra realizada com sucesso! 🎉
            </h2>

            <p class="mb-0">
                Obrigado por comprar na Art&Co.
            </p>

        </div>

    </div>

    <h3 class="fw-bold mb-4">
        Minhas compras
    </h3>

    <?php if (count($vendas) > 0): ?>

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>
                            Nº da venda
                        </th>

                        <th>
                            Data
                        </th>

                        <th>
                            Total
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($vendas as $venda): ?>

                        <tr>

                            <td>
                                <a
                                    href="venda.php?id=<?= $venda["id"] ?>"
                                    class="text-decoration-none fw-bold"
                                >
                                    #<?= $venda["id"] ?>
                                </a>
                            </td>

                            <td>
                                <?= date(
                                    "d/m/Y H:i",
                                    strtotime($venda["data_venda"])
                                ) ?>
                            </td>

                            <td class="fw-bold">
                                R$
                                <?= number_format(
                                    $venda["total"],
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

    <?php else: ?>

        <div class="alert alert-info">

            Você ainda não possui compras registradas.

        </div>

    <?php endif; ?>

    <div class="text-center mt-4">

        <a
            href="produtos/produtos.php"
            class="btn btn-primary"
        >
            Continuar comprando
        </a>

    </div>

</main>

<?php
require_once "componentes/footer.php";
?>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>