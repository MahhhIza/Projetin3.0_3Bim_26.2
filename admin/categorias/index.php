<?php

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$sucesso = $_GET["sucesso"] ?? "";
$erro = $_GET["erro"] ?? "";

$mensagemSucesso = "";
$mensagemErro = "";

if ($sucesso === "categoria_criada") {
    $mensagemSucesso = "Categoria cadastrada com sucesso.";
}

if ($sucesso === "categoria_editada") {
    $mensagemSucesso = "Categoria atualizada com sucesso.";
}

if ($sucesso === "categoria_excluida") {
    $mensagemSucesso = "Categoria excluída com sucesso.";
}

if ($erro === "categoria_utilizada") {
    $mensagemErro =
        "Esta categoria não pode ser excluída porque possui produtos vinculados.";
}

if ($erro === "nao_encontrada") {
    $mensagemErro = "Categoria não encontrada.";
}

if ($erro === "banco") {
    $mensagemErro =
        "Não foi possível realizar a operação. Tente novamente.";
}

if ($erro === "id") {
    $mensagemErro = "Categoria inválida.";
}

try {

    $stmt = $pdo->query(
        "SELECT id, nome, descricao
         FROM categorias
         ORDER BY nome ASC"
    );

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $categorias = [];

    $mensagemErro =
        "Não foi possível carregar as categorias.";
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

    <title>Gerenciar Categorias - Art&Co</title>

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

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Categorias
            </h1>

            <p class="text-muted mb-0">
                Gerencie as categorias dos produtos.
            </p>

        </div>

        <a
            href="cadastrar.php"
            class="btn btn-primary"
        >
            + Nova categoria
        </a>

    </div>


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


    <?php if (count($categorias) === 0): ?>

        <div class="alert alert-info">
            Nenhuma categoria cadastrada.
        </div>

    <?php else: ?>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Ações</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($categorias as $categoria): ?>

                            <tr>

                                <td>
                                    <?= (int) $categoria["id"] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $categoria["nome"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $categoria["descricao"] ?? ""
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        href="editar.php?id=<?= (int) $categoria["id"] ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="excluir.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $categoria["id"] ?>"
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

    <?php endif; ?>


    <div class="mt-4">

        <a
            href="../index.php"
            class="btn btn-secondary"
        >
            ← Voltar para administração
        </a>

    </div>

</div>

</body>

</html>