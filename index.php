<?php

session_start();

require_once "config.php";

$usuarioLogado = isset($_SESSION["usuario_id"]);

if ($usuarioLogado) {

    $nomeUsuario = $_SESSION["usuario_nome"];

}


/*
 * Busca as categorias cadastradas no banco
 */

try {

    $sqlCategorias = "SELECT id, nome
                      FROM categorias
                      ORDER BY nome ASC";

    $stmtCategorias = $pdo->prepare($sqlCategorias);

    $stmtCategorias->execute();

    $categorias = $stmtCategorias->fetchAll();

} catch (PDOException $e) {

    $categorias = [];

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

        /* =========================================
   NAVBAR ART&CO
========================================= */

.navbar-artco {
    background-color: #202124;
}

.navbar-artco .container {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
}

/* Logo */

.logo-artco {
    font-size: 27px;
    font-weight: bold;
    white-space: nowrap;
    margin-right: 30px;

    background: linear-gradient(
        90deg,
        #9b00ff,
        #00b894
    );

    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Área principal da navbar */

.menu-artco {
    display: flex;
    align-items: center;
    width: 100%;
}

/* Links */

.links-artco {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-right: auto;
}

.links-artco .nav-link {
    color: #bfc0c2;
    white-space: nowrap;
}

.links-artco .nav-link.active {
    color: #ffffff;
}

.links-artco .nav-link:hover {
    color: #ffffff;
}

/* Pesquisa */

.pesquisa-artco {
    display: flex;
    align-items: center;
    margin-right: 15px;
}

.pesquisa-artco .form-control {
    width: 225px;
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

/* Área do usuário */

.usuario-navbar {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

/* Botão cadastrar */

.btn-cadastrar {
    background-color: #ffc400;
    border: none;
    color: #111;
}

.btn-cadastrar:hover {
    background-color: #e6b000;
}

/* Linha colorida */

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

<nav class="navbar navbar-artco">

    <div class="container">

        <!-- LOGO -->

        <a
            class="navbar-brand logo-artco"
            href="index.php"
        >
            Art&Co
        </a>


        <!-- ÁREA PRINCIPAL -->

        <div class="menu-artco">


            <!-- LINKS -->

            <div class="links-artco">

                <a
                    class="nav-link active"
                    href="index.php"
                >
                    Início
                </a>


                <a
                    class="nav-link"
                    href="#"
                >
                    Produtos
                </a>


                <!-- CATEGORIAS -->

                <div class="dropdown">

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

                        <?php if (count($categorias) > 0): ?>

                            <?php foreach ($categorias as $categoria): ?>

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="#"
                                    >

                                        <?= htmlspecialchars($categoria["nome"]) ?>

                                    </a>

                                </li>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <li>

                                <span class="dropdown-item-text">

                                    Nenhuma categoria cadastrada.

                                </span>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>


            <!-- PESQUISA -->

            <form
                class="pesquisa-artco"
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

            <?php if (!$usuarioLogado): ?>

                <!-- VISITANTE -->

                <div class="usuario-navbar">

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

                <div class="usuario-navbar">

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