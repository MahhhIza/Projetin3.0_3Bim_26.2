<?php

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$sucesso = $_GET["sucesso"] ?? "";
$erro = $_GET["erro"] ?? "";

$mensagemSucesso = "";
$mensagemErro = "";

if ($sucesso === "usuario_criado") {
    $mensagemSucesso = "Usuário cadastrado com sucesso.";
}

if ($sucesso === "usuario_editado") {
    $mensagemSucesso = "Usuário atualizado com sucesso.";
}

if ($sucesso === "usuario_excluido") {
    $mensagemSucesso = "Usuário excluído com sucesso.";
}

if ($erro === "usuario_vinculado") {
    $mensagemErro =
        "Este usuário não pode ser excluído porque possui vendas registradas.";
}

if ($erro === "usuario_atual") {
    $mensagemErro =
        "Você não pode excluir o usuário que está conectado.";
}

if ($erro === "nao_encontrado") {
    $mensagemErro = "Usuário não encontrado.";
}

if ($erro === "id") {
    $mensagemErro = "Usuário inválido.";
}

if ($erro === "banco") {
    $mensagemErro =
        "Não foi possível realizar a operação. Tente novamente.";
}

/*
 * =========================================
 * PAGINAÇÃO
 * =========================================
 */

$usuariosPorPagina = 6;

$paginaAtual = filter_input(
    INPUT_GET,
    "pagina",
    FILTER_VALIDATE_INT
);

if (
    $paginaAtual === false ||
    $paginaAtual === null ||
    $paginaAtual < 1
) {
    $paginaAtual = 1;
}

$usuarios = [];

$totalUsuarios = 0;
$totalPaginas = 1;

try {

    /*
     * =========================================
     * TOTAL DE USUÁRIOS
     * =========================================
     */

    $sqlTotal = "
        SELECT COUNT(*)
        FROM usuarios
    ";

    $stmtTotal = $pdo->query($sqlTotal);

    $totalUsuarios = (int) $stmtTotal->fetchColumn();

    /*
     * =========================================
     * TOTAL DE PÁGINAS
     * =========================================
     */

    $totalPaginas = max(
        1,
        (int) ceil(
            $totalUsuarios / $usuariosPorPagina
        )
    );

    /*
     * Se a página informada não existir,
     * volta para a última página.
     */

    if ($paginaAtual > $totalPaginas) {
        $paginaAtual = $totalPaginas;
    }

    /*
     * =========================================
     * OFFSET
     * =========================================
     */

    $offset =
        ($paginaAtual - 1) *
        $usuariosPorPagina;

    /*
     * =========================================
     * USUÁRIOS DA PÁGINA
     * =========================================
     */

    $sql = "
        SELECT
            id,
            nome,
            email,
            tipo,
            ativo

        FROM usuarios

        ORDER BY nome ASC

        LIMIT $usuariosPorPagina
        OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    $usuarios =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $usuarios = [];

    $totalUsuarios = 0;

    $totalPaginas = 1;

    $paginaAtual = 1;

    $mensagemErro =
        "Não foi possível carregar os usuários.";
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

    <title>Gerenciar Usuários - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../../src/css/style.css"
    >

</head>

<body>

<?php

$base = "../../";

require_once "../../componentes/navbar.php";

?>

<main class="container py-5">

    <!-- CABEÇALHO -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Usuários
            </h1>

            <p class="text-muted mb-0">
                Gerencie os usuários do sistema.
            </p>

        </div>

        <a
            href="cadastrar.php"
            class="btn btn-primary"
        >
            + Novo usuário
        </a>

    </div>

    <!-- MENSAGENS -->

    <?php if ($mensagemSucesso !== ""): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($mensagemSucesso) ?>

        </div>

    <?php endif; ?>

    <?php if ($mensagemErro !== ""): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($mensagemErro) ?>

        </div>

    <?php endif; ?>

    <!-- LISTA DE USUÁRIOS -->

    <?php if (count($usuarios) > 0): ?>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ações</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($usuarios as $usuario): ?>

                            <tr>

                                <td>
                                    <?= (int) $usuario["id"] ?>
                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $usuario["nome"]
                                    ) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $usuario["email"]
                                    ) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $usuario["tipo"]
                                    ) ?>

                                </td>

                                <td>

                                    <?php if (
                                        (int) $usuario["ativo"] === 1
                                    ): ?>

                                        <span class="badge bg-success">
                                            Ativo
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            Inativo
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <a
                                        href="editar.php?id=<?= (int) $usuario["id"] ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="excluir.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $usuario["id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Excluir
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- PAGINAÇÃO -->

        <?php if ($totalPaginas > 1): ?>

            <nav
                class="d-flex justify-content-center mt-4"
                aria-label="Navegação dos usuários"
            >

                <ul class="pagination">

                    <!-- ANTERIOR -->

                    <li
                        class="page-item
                        <?= $paginaAtual <= 1 ? "disabled" : "" ?>"
                    >

                        <a
                            class="page-link"
                            href="?pagina=<?= $paginaAtual - 1 ?>"
                        >
                            ← Anterior
                        </a>

                    </li>

                    <!-- NÚMEROS DAS PÁGINAS -->

                    <?php for (
                        $pagina = 1;
                        $pagina <= $totalPaginas;
                        $pagina++
                    ): ?>

                        <li
                            class="page-item
                            <?= $pagina === $paginaAtual ? "active" : "" ?>"
                        >

                            <a
                                class="page-link"
                                href="?pagina=<?= $pagina ?>"
                            >
                                <?= $pagina ?>
                            </a>

                        </li>

                    <?php endfor; ?>

                    <!-- PRÓXIMA -->

                    <li
                        class="page-item
                        <?= $paginaAtual >= $totalPaginas ? "disabled" : "" ?>"
                    >

                        <a
                            class="page-link"
                            href="?pagina=<?= $paginaAtual + 1 ?>"
                        >
                            Próxima →
                        </a>

                    </li>

                </ul>

            </nav>

            <p class="text-center text-muted mt-2">

                Página <?= $paginaAtual ?>
                de <?= $totalPaginas ?>

                •

                <?= $totalUsuarios ?> usuário(s)

            </p>

        <?php endif; ?>

    <?php else: ?>

        <div class="alert alert-info text-center">

            Nenhum usuário cadastrado.

        </div>

    <?php endif; ?>

    <!-- VOLTAR -->

    <div class="mt-4">

        <a
            href="../index.php"
            class="btn btn-secondary"
        >
            ← Voltar para administração
        </a>

    </div>

</main>

<?php

require_once "../../componentes/footer.php";

?>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>