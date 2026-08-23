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


    <!-- ==========================================
         NAVBAR ART&CO
    =========================================== -->

    <nav class="navbar navbar-expand-lg navbar-artco">

        <div class="container">

            <!-- LOGO -->

            <a
                class="navbar-brand logo-artco"
                href="painel.php"
            >
                Art&Co
            </a>


            <!-- BOTÃO MOBILE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuArtco"
                aria-controls="menuArtco"
                aria-expanded="false"
                aria-label="Abrir menu"
            >

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- MENU -->

            <div
                class="collapse navbar-collapse"
                id="menuArtco"
            >

                <!-- LINKS -->

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">

                        <a
                            class="nav-link active"
                            href="painel.php"
                        >
                            Início
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="#"
                        >
                            Produtos
                        </a>

                    </li>


                    <!-- CATEGORIAS -->

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Categorias
                        </a>


                        <ul class="dropdown-menu">

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                >
                                    🎨 Tintas
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                >
                                    🖌️ Pincéis
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                >
                                    ✏️ Desenho
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                >
                                    📄 Papéis
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                >
                                    🖼️ Telas
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                >
                                    🌈 Aquarela
                                </a>
                            </li>

                        </ul>

                    </li>

                </ul>


                <!-- PESQUISA -->

                <form
                    class="d-flex me-3"
                    role="search"
                >

                    <input
                        class="form-control me-2"
                        type="search"
                        placeholder="Buscar materiais..."
                        aria-label="Buscar"
                    >

                    <button
                        class="btn btn-buscar"
                        type="submit"
                    >
                        Buscar
                    </button>

                </form>


                <!-- USUÁRIO -->

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

        </div>

    </nav>


    <!-- LINHA COLORIDA -->

    <div class="linha-colorida"></div>


    <!-- ==========================================
         CONTEÚDO
    =========================================== -->

    <main class="container py-5">


        <div class="alert alert-success">

            <strong>Login realizado com sucesso!</strong>

            Bem-vindo à Art&Co.

        </div>


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