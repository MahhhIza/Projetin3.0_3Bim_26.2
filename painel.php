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

    <title>Art&Co - Início</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
    rel="stylesheet"
    href="src/css/style.css"
    >
    
    <style>

        /* ==============================
           IDENTIDADE VISUAL ART&CO
        ============================== */

        body {
            background-color: #f8f9fa;
        }

        .navbar-artco {
            background-color: #202124;
        }

        .logo-artco {
            font-size: 27px;
            font-weight: bold;

            background: linear-gradient(
                90deg,
                #9b00ff,
                #00b894
            );

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .navbar-artco .nav-link {
            color: #bfc0c2;
        }

        .navbar-artco .nav-link.active {
            color: #ffffff;
        }

        .navbar-artco .nav-link:hover {
            color: white;
        }

        .linha-colorida {
            height: 4px;

            background: linear-gradient(
                90deg,
                #ff00cc,
                #9b00ff,
                #00b894,
                #ffc400
            );
        }

        .btn-buscar {
            background: linear-gradient(
                90deg,
                #9b00ff,
                #00b894
            );

            border: none;
            color: white;
        }

        .btn-buscar:hover {
            color: white;
            opacity: 0.9;
        }

        .btn-cadastrar {
            background-color: #ffc400;
            border: none;
            color: #111;
        }

        .btn-cadastrar:hover {
            background-color: #e6b000;
        }

    </style>

</head>

<body>


<?php
$base = "";
require_once "componentes/navbar.php";
?>


    <!-- ==========================================
         CONTEÚDO
    =========================================== -->

    <main class="container py-5">


        <div class="mb-4">

            <h1 class="fw-bold">
                Bem-vindo à Art&Co 🎨
            </h1>

            <p class="text-muted">
                Materiais artísticos para transformar suas ideias em arte.
            </p>

        </div>


        <!-- CARDS -->

        <!-- DASHBOARD -->
<div class="row g-4">

    <!-- FATURAMENTO -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">
                    💰 Faturamento
                </h5>

                <p
                    id="faturamentoTotal"
                    class="fs-4 fw-bold text-success"
                >
                    Carregando...
                </p>

                <p class="card-text text-muted">
                    Faturamento total dos produtos.
                </p>
            </div>
        </div>
    </div>

    <!-- QUANTIDADE VENDIDA -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">
                    📦 Vendas
                </h5>

                <p
                    id="quantidadeTotal"
                    class="fs-4 fw-bold text-primary"
                >
                    Carregando...
                </p>

                <p class="card-text text-muted">
                    Quantidade total vendida.
                </p>
            </div>
        </div>
    </div>

    <!-- PRODUTOS -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">
                    🎨 Produtos
                </h5>

                <p
                    id="totalProdutos"
                    class="fs-4 fw-bold text-dark"
                >
                    Carregando...
                </p>

                <p class="card-text text-muted">
                    Produtos cadastrados.
                </p>
            </div>
        </div>
    </div>

    <!-- ESTOQUE -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">
                    🏷️ Estoque
                </h5>

                <p
                    id="estoqueTotal"
                    class="fs-4 fw-bold text-warning"
                >
                    Carregando...
                </p>

                <p class="card-text text-muted">
                    Unidades disponíveis.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- MENSAGEM DA DASHBOARD -->
<div
    id="mensagemDashboard"
    class="alert alert-info text-center mt-4 d-none"
>
</div>


    </main>


    <!-- Bootstrap JavaScript -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

    <script type="module" src="src/js/dashboard.js"></script>

</body>

</html>