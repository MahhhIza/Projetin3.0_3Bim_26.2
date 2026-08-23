<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.php");
    exit;
}

$nomeUsuario = $_SESSION["usuario_nome"];
$tipoUsuario = $_SESSION["usuario_tipo"];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Art&Co - Painel</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">

        <div class="container">

            <a class="navbar-brand fw-bold" href="painel.php">
                Art&Co
            </a>

            <div class="d-flex align-items-center">

                <span class="text-white me-3">
                    Olá, <?= htmlspecialchars($nomeUsuario) ?>
                </span>

                <a
                    href="logout.php"
                    class="btn btn-outline-light btn-sm"
                >
                    Sair
                </a>

            </div>

        </div>

    </nav>


    <!-- =====================================================
         CONTEÚDO PRINCIPAL
    ====================================================== -->

    <main class="container py-5">

        <!-- ALERT -->

        <div class="alert alert-success" role="alert">

            <strong>Login realizado com sucesso!</strong>

            Você está acessando o painel da Art&Co.

        </div>


        <!-- TÍTULO -->

        <div class="mb-4">

            <h1 class="fw-bold">
                Painel Art&Co
            </h1>

            <p class="text-muted">
                Sistema de gerenciamento da loja de produtos artísticos.
            </p>

        </div>


        <!-- =================================================
             CARDS
        ================================================== -->

        <div class="row g-4">


            <!-- CARD PRODUTOS -->

            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            🎨 Produtos
                        </h5>

                        <p class="card-text text-muted">
                            Consulte os produtos disponíveis na loja.
                        </p>

                        <a
                            href="#"
                            class="btn btn-primary"
                        >
                            Ver produtos
                        </a>

                    </div>

                </div>

            </div>


            <!-- CARD VENDAS -->

            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            🛒 Vendas
                        </h5>

                        <p class="card-text text-muted">
                            Registre e consulte as vendas realizadas.
                        </p>

                        <a
                            href="#"
                            class="btn btn-success"
                        >
                            Ver vendas
                        </a>

                    </div>

                </div>

            </div>


            <!-- CARD DASHBOARD -->

            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            📊 Dashboard
                        </h5>

                        <p class="card-text text-muted">
                            Visualize os principais dados da loja.
                        </p>

                        <a
                            href="#"
                            class="btn btn-warning"
                        >
                            Ver dashboard
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             INFORMAÇÃO DO USUÁRIO
        ================================================== -->

        <div class="card mt-4 shadow-sm">

            <div class="card-body">

                <h5 class="card-title">
                    Informações da conta
                </h5>

                <p class="mb-1">
                    <strong>Nome:</strong>
                    <?= htmlspecialchars($nomeUsuario) ?>
                </p>

                <p class="mb-0">
                    <strong>Tipo:</strong>
                    <?= htmlspecialchars($tipoUsuario) ?>
                </p>

            </div>

        </div>

    </main>


    <!-- Bootstrap JavaScript -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>