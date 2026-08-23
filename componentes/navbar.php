<?php

/*
 * Garante que a sessão esteja disponível.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
 * Verifica se existe um usuário logado.
 */

$usuarioLogado = isset($_SESSION["usuario_id"]);


/*
 * Nome do usuário logado.
 */

$nomeUsuario = $_SESSION["usuario_nome"] ?? "Usuário";

?>

<!-- =========================================
     NAVBAR ART&CO
========================================= -->

<nav class="navbar navbar-artco">

    <div class="container">

        <!-- LOGO -->

        <a
            class="navbar-brand logo-artco"
            href="/artco/index.php"
        >
            Art&Co
        </a>


        <div class="menu-artco">


            <!-- LINKS -->

            <div class="links-artco">

                <a
                    class="nav-link active"
                    href="/artco/index.php"
                >
                    Início
                </a>


                <a
                    class="nav-link"
                    href="/artco/produtos/produtos.php"
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

                        <li>
                            <a
                                class="dropdown-item"
                                href="#"
                            >
                                Aquarela
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item"
                                href="#"
                            >
                                Desenho
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item"
                                href="#"
                            >
                                Papéis
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item"
                                href="#"
                            >
                                Pintura
                            </a>
                        </li>

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

                <div class="usuario-navbar">

                    <a
                        href="/artco/login.php"
                        class="btn btn-outline-light"
                    >
                        Login
                    </a>

                    <a
                        href="/artco/cadastro.php"
                        class="btn btn-cadastrar"
                    >
                        Cadastrar
                    </a>

                </div>


            <?php else: ?>

                <div class="usuario-navbar">

                    <span class="text-white">

                        Olá,
                        <?= htmlspecialchars($nomeUsuario) ?>

                    </span>


                    <a
                        href="/artco/painel.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Minha conta
                    </a>


                    <a
                        href="/artco/logout.php"
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