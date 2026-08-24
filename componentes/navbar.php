<?php

/*
 * Se a página não definir $base,
 * usamos o caminho da raiz.
 */
$base = $base ?? "";

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
 * Dados do usuário logado.
 */
$nomeUsuario = $_SESSION["usuario_nome"] ?? "Usuário";
$tipoUsuario = $_SESSION["usuario_tipo"] ?? "usuario";

/*
 * Busca as categorias cadastradas no banco.
 */
$categorias = [];

try {

    require_once $base . "config.php";

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

<!-- =========================================
     NAVBAR ART&CO
========================================= -->

<nav class="navbar navbar-artco">

    <div class="container">

        <!-- LOGO -->

        <a
            class="navbar-brand logo-artco"
            href="<?= $base ?>index.php"
        >
            Art&Co
        </a>


        <div class="menu-artco">

            <!-- =========================================
                 LINKS PRINCIPAIS
            ========================================= -->

            <div class="links-artco">

                <!-- INÍCIO -->

                <a
                    class="nav-link"
                    href="<?= $base ?>index.php"
                >
                    Início
                </a>


                <!-- PRODUTOS -->

                <a
                    class="nav-link"
                    href="<?= $base ?>produtos/produtos.php"
                >
                    Produtos
                </a>


                <!-- =========================================
                     CATEGORIAS
                ========================================= -->

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
                                        href="<?= $base ?>produtos/produtos.php?categoria=<?= $categoria["id"] ?>"
                                    >
                                        <?= htmlspecialchars($categoria["nome"]) ?>
                                    </a>

                                </li>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <li>

                                <span class="dropdown-item-text text-muted">
                                    Nenhuma categoria cadastrada.
                                </span>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>


            <!-- =========================================
                 PESQUISA
            ========================================= -->

            <form
                class="pesquisa-artco"
                action="<?= $base ?>produtos/produtos.php"
                method="GET"
                role="search"
            >

                <input
                    class="form-control me-2"
                    type="search"
                    name="busca"
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


            <!-- =========================================
                 USUÁRIO
            ========================================= -->

            <?php if (!$usuarioLogado): ?>

                <!-- VISITANTE -->

                <div class="usuario-navbar">

                    <a
                        href="<?= $base ?>login.php"
                        class="btn btn-outline-light"
                    >
                        Login
                    </a>

                    <a
                        href="<?= $base ?>cadastro.php"
                        class="btn btn-buscar"
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


                    <?php if ($tipoUsuario === "usuario"): ?>

                        <!-- USUÁRIO COMUM -->

                        <a
                            href="<?= $base ?>compras.php"
                            class="btn btn-outline-light btn-sm"
                        >
                            Compras
                        </a>


                    <?php elseif (
                        $tipoUsuario === "vendedor"
                        || $tipoUsuario === "admin"
                    ): ?>

                        <!-- VENDEDOR / ADMIN -->

                        <a
                            href="<?= $base ?>painel.php"
                            class="btn btn-outline-light btn-sm"
                        >
                            Painel
                        </a>

                    <?php endif; ?>


                    <!-- SAIR -->

                    <a
                        href="<?= $base ?>logout.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Sair
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</nav>


<!-- =========================================
     LINHA COLORIDA
========================================= -->

<div class="linha-colorida"></div>