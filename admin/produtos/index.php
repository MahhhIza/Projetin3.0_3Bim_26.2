<?php

session_start();

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gerenciar Produtos - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../../src/css/style.css"
    >

</head>

<body>

<?php

$base = "../../";

require_once "../../componentes/navbar.php";

?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Gerenciar Produtos
            </h1>

            <p class="text-muted mb-0">
                Cadastre e gerencie os produtos da Art&Co.
            </p>

        </div>

        <a
            href="cadastrar.php"
            class="btn btn-primary"
        >
            + Novo produto
        </a>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="alert alert-info mb-0">

                A área de gerenciamento de produtos está funcionando.

            </div>

        </div>

    </div>

</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>