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

    <style>
    body {
        background-color: #f8f9fa;
    }

    .card-compra {
        border: none;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card-compra:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 18px rgba(0, 0, 0, 0.10);
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

    .status-compra {
        font-size: 0.8rem;
        padding: 6px 10px;
        border-radius: 20px;
    }
</style>

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
                Compra realizada com sucesso!
            </h2>

            <p class="mb-0">
                Obrigado por comprar na Art&Co.
            </p>

        </div>

    </div>

    <h3 class="titulo-pagina">
        Minhas compras
    </h3>

    <p class="subtitulo-pagina">
        Confira o histórico das compras realizadas na Art&Co.
    </p>

    <?php if (count($vendas) > 0): ?>

        <div class="row g-4">
    <?php foreach ($vendas as $venda): ?>
        <div class="col-12 col-md-6">
            <div class="card card-compra h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="numero-compra">
                                Compra #<?= $venda["id"] ?>
                            </div>

                            <small class="text-muted">
                                <?= date(
                                    "d/m/Y H:i",
                                    strtotime($venda["data_venda"])
                                ) ?>
                            </small>
                        </div>

                        <span class="badge bg-success status-compra">
                            Concluída
                        </span>
                    </div>

                    <hr>

                    <p class="mb-2">
                        Compra realizada com sucesso
                    </p>

                    <div class="valor-compra">
                        R$
                        <?= number_format(
                            $venda["total"],
                            2,
                            ",",
                            "."
                        ) ?>
                    </div>

                    <a
                        href="venda.php?id=<?= $venda["id"] ?>"
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

        <div class="alert alert-info">

            Você ainda não possui compras registradas.

        </div>

    <?php endif; ?>

    <div class="text-center mt-4">

        <a
            href="produtos/produtos.php"
            class="btn btn-artco"
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