<?php

session_start();

require_once "../protecao/acesso.php";

exigirPerfil(["admin"]);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administração - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../src/css/style.css"
    >
</head>

<body>

<?php
$base = "../";
require_once "../componentes/navbar.php";
?>

<main class="container py-5">

    <h1 class="fw-bold mb-2">
        👑 Administração Art&Co
    </h1>

    <p class="text-muted mb-5">
        Gerenciamento do sistema.
    </p>

    <div class="row g-4">

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>👥 Usuários</h5>
                    <p class="text-muted">
                        Gerencie os usuários cadastrados.
                    </p>

                    <a
                        href="usuarios/index.php"
                        class="btn btn-primary"
                    >
                        Gerenciar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">

    <div class="card shadow-sm h-100">

        <div class="card-body">

            <h5 class="card-title fw-bold">
                Categorias
            </h5>

            <p class="card-text text-muted">
                Cadastre, edite e exclua categorias de produtos.
            </p>

            <a
                href="categorias/index.php"
                class="btn btn-primary"
            >
                Gerenciar categorias
            </a>

        </div>

    </div>

</div>

    </div>

</main>

<?php
require_once "../componentes/footer.php";
?>

</body>
</html>