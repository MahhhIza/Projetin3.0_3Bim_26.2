<?php

session_start();

$usuarioLogado = isset($_SESSION["usuario_id"]);

if ($usuarioLogado) {

    $nomeUsuario = $_SESSION["usuario_nome"];

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

    <title>Art&Co - Materiais Artísticos</title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f8f9fa;
        }

        /* ==============================
           NAVBAR
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
            color: #ffffff;
        }

        /* ==============================
           LINHA COLORIDA
        ============================== */

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

        /* ==============================
           BOTÃO BUSCAR
        ============================== */

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

        /* ==============================
           BOTÃO CADASTRAR
        ============================== */

        .btn-cadastrar {

            background-color: #ffc400;

            border: none;

            color: #111;
        }

        .btn-cadastrar:hover {

            background-color: #e6b000;

        }

        /* ==============================
           DESTAQUE
        ============================== */

        .hero {

            padding: 80px 20px;

            background-color: white;

            text-align: center;

        }

        .hero h1 {

            font-size: 42px;

            font-weight: bold;

        }

        .hero p {

            font-size: 18px;

            color: #6c757d;

            max-width: 650px;

            margin: 15px auto 25px;

        }

    </style>

</head>

<body>


<!-- ==================================================
     NAVBAR
=================================================== -->

<nav class="navbar navbar-expand-lg navbar-artco">

    <div class="container">

        <!-- LOGO -->

        <a
            class="navbar-brand logo-artco"
            href="index.php"
        >
            Art&Co
        </a>


        <!-- BOTÃO MOBILE -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuArtco"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="menuArtco"
        >


            <!-- MENU -->

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="index.php"
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
                >

                <button
                    class="btn btn-buscar"
                    type="submit"
                >
                    Buscar
                </button>

            </form>


            <!-- ==================================================
                 USUÁRIO
            =================================================== -->

            <?php if (!$usuarioLogado): ?>

                <!-- VISITANTE -->

                <div class="d-flex gap-2">

                    <a
                        href="login.php"
                        class="btn btn-outline-light"
                    >
                        Login
                    </a>

                    <a
                        href="cadastro.php"
                        class="btn btn-cadastrar"
                    >
                        Cadastrar
                    </a>

                </div>


            <?php else: ?>

                <!-- USUÁRIO LOGADO -->

                <div class="d-flex align-items-center gap-2">

                    <span class="text-white">

                        Olá,
                        <?= htmlspecialchars($nomeUsuario) ?>

                    </span>

                    <a
                        href="painel.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Minha conta
                    </a>

                    <a
                        href="logout.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Sair
                    </a>

                </div>

            <?php endif; ?>


        </div>

    </div>

</nav>


<!-- LINHA COLORIDA -->

<div class="linha-colorida"></div>


<!-- ==================================================
     DESTAQUE DA LOJA
=================================================== -->

<section class="hero">

    <div class="container">

        <h1>
            Crie, imagine, transforme. 🎨
        </h1>

        <p>

            Encontre materiais artísticos para dar vida
            às suas ideias na Art&Co.

        </p>


        <a
            href="#"
            class="btn btn-primary btn-lg"
        >
            Explorar produtos
        </a>

    </div>

</section>


<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>