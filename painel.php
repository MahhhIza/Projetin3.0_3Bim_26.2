<?php

session_start();

require_once "protecao/acesso.php";

exigirPerfil(["admin", "vendedor"]);

require_once "config.php";

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

<!-- ANÁLISES DA DASHBOARD -->

<div class="row g-4 mt-1">

    <!-- RANKING -->
    <div class="col-12 col-lg-6">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="card-title fw-bold">
                    🏆 Top 3 produtos mais vendidos
                </h5>

                <p class="text-muted">
                    Ranking calculado dinamicamente pela dashboard.
                </p>

                <div id="rankingProdutos">
                    <p class="text-muted mb-0">
                        Carregando...
                    </p>
                </div>

            </div>

        </div>

    </div>


    <!-- ESTOQUE CRÍTICO -->
    <div class="col-12 col-lg-6">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="card-title fw-bold">
                    ⚠️ Estoque crítico
                </h5>

                <p class="text-muted">
                    Produtos com 10 unidades ou menos.
                </p>

                <div id="estoqueCritico">
                    <p class="text-muted mb-0">
                        Carregando...
                    </p>
                </div>

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

<?php if ($tipoUsuario === "admin"): ?>

    <div class="card shadow-sm border-0 mt-5">
        <div class="card-body">

            <h3 class="fw-bold mb-3">
                ⚙️ Gerenciamento
            </h3>

            <p class="text-muted">
                Gerencie os produtos e a administração da Art&Co.
            </p>

            <div class="d-flex flex-wrap gap-2">

                <!-- GERENCIAR PRODUTOS -->
                <a
                    href="admin/produtos/index.php"
                    class="btn btn-primary"
                >
                    Gerenciar produtos
                </a>

                <!-- ADMINISTRAÇÃO -->
                <a
                    href="admin/index.php"
                    class="btn btn-outline-secondary"
                >
                    Administração
                </a>

                <!-- MINHAS COMPRAS -->
                <a
                    href="finalizar.php"
                    class="btn btn-success"
                >
                    🛍️ Minhas compras
                </a>

            </div>

        </div>
    </div>

<?php elseif ($tipoUsuario === "vendedor"): ?>

    <div class="card shadow-sm border-0 mt-5">
        <div class="card-body">

            <h3 class="fw-bold mb-3">
                Minhas compras
            </h3>

            <p class="text-muted">
                Consulte suas compras realizadas na Art&Co.
            </p>

            <a
                href="finalizar.php"
                class="btn btn-success"
            >
                Ver minhas compras
            </a>

        </div>
    </div>

<?php endif; ?>

    </main>

    <?php
    require_once "componentes/footer.php";
    ?>


    <!-- Bootstrap JavaScript -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

    <script type="module" src="src/js/dashboard.js"></script>

</body>

</html>