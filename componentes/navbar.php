<?php

$base = $base ?? "";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = isset($_SESSION["usuario_id"]);
$nomeUsuario = $_SESSION["usuario_nome"] ?? "Usuário";
$tipoUsuario = $_SESSION["usuario_tipo"] ?? "usuario";

$categorias = [];

try {
    require_once $base . "config.php";

    // BD - SELECT
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

<!-- DW - Bootstrap / Navbar / Dropdown / Collapse -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-artco">
    <div class="container">

        <a
            class="navbar-brand logo-artco"
            href="<?= $base ?>index.php"
        >
            Art&Co
        </a>

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

        <div
            class="collapse navbar-collapse menu-artco"
            id="menuArtco"
        >
            <div class="links-artco">

                <a
                    class="nav-link"
                    href="<?= $base ?>index.php"
                >
                    Início
                </a>

                <a
                    class="nav-link"
                    href="<?= $base ?>produtos/produtos.php"
                >
                    Produtos
                </a>

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

            <?php if (!$usuarioLogado): ?>

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

                <div class="usuario-navbar">
                    <span class="text-white">
                        Olá,
                        <?= htmlspecialchars($nomeUsuario) ?>
                    </span>

                    <?php if ($tipoUsuario === "usuario"): ?>

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

                        <a
                            href="<?= $base ?>painel.php"
                            class="btn btn-outline-light btn-sm"
                        >
                            Painel
                        </a>

                    <?php endif; ?>

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

<div class="linha-colorida"></div>