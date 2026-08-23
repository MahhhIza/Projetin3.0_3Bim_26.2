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

        <div class="row g-4">


            <!-- PRODUTOS -->

            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            🎨 Produtos
                        </h5>

                        <p class="card-text text-muted">
                            Encontre tintas, pincéis, papéis e outros
                            materiais artísticos.
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


            <!-- CATEGORIAS -->

            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            🖌️ Categorias
                        </h5>

                        <p class="card-text text-muted">
                            Explore nossos materiais separados por categoria.
                        </p>

                        <a
                            href="#"
                            class="btn btn-success"
                        >
                            Explorar categorias
                        </a>

                    </div>

                </div>

            </div>


            <!-- CONTA -->

            <div class="col-md-4">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            👤 Minha conta
                        </h5>

                        <p class="card-text text-muted">
                            Acesse suas informações e acompanhe suas compras.
                        </p>

                        <a
                            href="#"
                            class="btn btn-warning"
                        >
                            Minha conta
                        </a>

                    </div>

                </div>

            </div>


        </div>


    </main>


    <!-- Bootstrap JavaScript -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>