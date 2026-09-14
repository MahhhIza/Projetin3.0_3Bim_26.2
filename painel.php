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


        /* ==============================
           PAINEL
        ============================== */

        .painel-cabecalho {
            margin-bottom: 30px;
        }

        .painel-cabecalho .titulo-pagina {
            margin-bottom: 6px;

            text-shadow:
                0 2px 4px rgba(0, 0, 0, 0.12);
        }

        .painel-cabecalho .subtitulo-pagina {
            margin-bottom: 0;
        }


        /* ==============================
           CARDS DE ESTATÍSTICAS
        ============================== */

        .painel-estatistica {
            border: 1px solid #e1e1e1;
            border-radius: 12px;

            background-color: #ffffff;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.06);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .painel-estatistica:hover {
            transform: translateY(-3px);

            box-shadow:
                0 7px 18px rgba(0, 0, 0, 0.10);
        }

        .painel-estatistica .card-body {
            padding: 22px;
        }

        .painel-estatistica .card-title {
            color: #555;
            font-size: 1rem;
            font-weight: 600;

            margin-bottom: 10px;
        }

        .painel-estatistica .estatistica-valor {
            font-size: 2rem;
            font-weight: 800;

            line-height: 1.1;

            margin-bottom: 10px;
        }

        .painel-estatistica .card-text {
            color: #737983;
            font-size: 0.88rem;

            margin-bottom: 0;
        }


        /* ==============================
           CARDS DE ANÁLISE
        ============================== */

        .painel-analise {
            border: 1px solid #e1e1e1;
            border-radius: 12px;

            background-color: #ffffff;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.06);
        }

        .painel-analise .card-body {
            padding: 24px;
        }

        .painel-analise .card-title {
            color: #202124;
            font-size: 1.15rem;
            font-weight: 700;

            margin-bottom: 7px;
        }

        .painel-analise > .card-body > p.text-muted {
            font-size: 0.9rem;
            margin-bottom: 18px;
        }


        /* ==============================
           RANKING DE PRODUTOS
        ============================== */

        #rankingProdutos {
            font-size: 0.92rem;
        }

        #rankingProdutos > div {
            padding: 9px 0;

            border-bottom: 1px solid #eeeeee;
        }

        #rankingProdutos > div:last-child {
            border-bottom: none;
        }


        /* ==============================
           ESTOQUE CRÍTICO
        ============================== */

        #estoqueCritico {
            font-size: 0.92rem;
        }

        #estoqueCritico > div {
            padding: 9px 0;

            border-bottom: 1px solid #eeeeee;
        }

        #estoqueCritico > div:last-child {
            border-bottom: none;
        }


        /* ==============================
           MENSAGEM DA DASHBOARD
        ============================== */

        #mensagemDashboard {
            border-radius: 10px;
            border: none;
        }


        /* ==============================
           GERENCIAMENTO
        ============================== */

        .painel-gerenciamento {
            border: 1px solid #e1e1e1 !important;
            border-radius: 12px;

            background-color: #ffffff;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.06);
        }

        .painel-gerenciamento .card-body {
            padding: 28px;
        }

        .painel-gerenciamento h3 {
            color: #202124;
            font-size: 1.65rem;

            margin-bottom: 8px !important;
        }

        .painel-gerenciamento p {
            color: #6f7785;

            margin-bottom: 20px;
        }


        /* ==============================
           BOTÕES DO PAINEL
        ============================== */

        .painel-gerenciamento .btn {
            border-radius: 7px;
            font-weight: 600;

            padding: 8px 15px;

            transition:
                opacity 0.2s ease,
                box-shadow 0.2s ease;
        }

        .painel-gerenciamento .btn:hover {
            opacity: 0.9;

            box-shadow:
                0 3px 8px rgba(0, 0, 0, 0.12);
        }


        /* ==============================
           RESPONSIVIDADE
        ============================== */

        @media (max-width: 767px) {

            .painel-cabecalho .titulo-pagina {
                font-size: 2.3rem;
            }

            .painel-estatistica .estatistica-valor {
                font-size: 1.8rem;
            }

            .painel-gerenciamento .card-body {
                padding: 22px;
            }

        }

    </style>

</head>

<body>


<?php

$base = "";

require_once "componentes/navbar.php";

?>


<main class="container py-5">


    <div class="painel-cabecalho">

        <h1 class="titulo-pagina">
            Bem-vindo à Art&Co
        </h1>

        <p class="subtitulo-pagina">
            Materiais artísticos para transformar suas ideias em arte.
        </p>

    </div>


    <!-- DASHBOARD -->

    <div class="row g-4">


        <!-- FATURAMENTO -->

        <div class="col-12 col-md-6 col-lg-3">

            <div class="card painel-estatistica h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Faturamento
                    </h5>

                    <p
                        id="faturamentoTotal"
                        class="estatistica-valor text-success"
                    >
                        Carregando...
                    </p>

                    <p class="card-text">
                        Faturamento total dos produtos.
                    </p>

                </div>

            </div>

        </div>


        <!-- QUANTIDADE VENDIDA -->

        <div class="col-12 col-md-6 col-lg-3">

            <div class="card painel-estatistica h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Vendas
                    </h5>

                    <p
                        id="quantidadeTotal"
                        class="estatistica-valor text-primary"
                    >
                        Carregando...
                    </p>

                    <p class="card-text">
                        Quantidade total vendida.
                    </p>

                </div>

            </div>

        </div>


        <!-- PRODUTOS -->

        <div class="col-12 col-md-6 col-lg-3">

            <div class="card painel-estatistica h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Produtos
                    </h5>

                    <p
                        id="totalProdutos"
                        class="estatistica-valor text-dark"
                    >
                        Carregando...
                    </p>

                    <p class="card-text">
                        Produtos cadastrados.
                    </p>

                </div>

            </div>

        </div>


        <!-- ESTOQUE -->

        <div class="col-12 col-md-6 col-lg-3">

            <div class="card painel-estatistica h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Estoque
                    </h5>

                    <p
                        id="estoqueTotal"
                        class="estatistica-valor text-warning"
                    >
                        Carregando...
                    </p>

                    <p class="card-text">
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

            <div class="card painel-analise h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Top 3 produtos mais vendidos
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

            <div class="card painel-analise h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Estoque crítico
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

        <div class="card painel-gerenciamento mt-5">

            <div class="card-body">

                <h3 class="fw-bold mb-3">
                    Gerenciamento
                </h3>

                <p class="text-muted">
                    Gerencie os produtos e a administração da Art&Co.
                </p>

                <div class="d-flex flex-wrap gap-2">

                    <!-- GERENCIAR PRODUTOS -->

                    <a
                        href="admin/produtos/index.php"
                        class="btn btn-artco"
                    >
                        Gerenciar produtos
                    </a>


                    <!-- ADMINISTRAÇÃO -->

                    <a
                        href="admin/index.php"
                        class="btn btn-voltar"
                    >
                        Administração
                    </a>


                    <!-- MINHAS COMPRAS -->

                    <a
                        href="compras.php"
                        class="btn btn-artco"
                    >
                        Minhas compras
                    </a>

                </div>

            </div>

        </div>


    <?php elseif ($tipoUsuario === "vendedor"): ?>

        <div class="card painel-gerenciamento mt-5">

            <div class="card-body">

                <h3 class="fw-bold mb-3">
                    Minhas compras
                </h3>

                <p class="text-muted">
                    Consulte suas compras realizadas na Art&Co.
                </p>

                <a
                    href="compras.php"
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

<script
    type="module"
    src="src/js/dashboard.js">
</script>


</body>

</html>