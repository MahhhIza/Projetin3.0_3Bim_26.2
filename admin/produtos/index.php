<?php

session_start();

require_once "../../config.php";
require_once "../../protecao/acesso.php";

$sucesso = $_GET["sucesso"] ?? "";
$erro = $_GET["erro"] ?? "";

$mensagemSucesso = "";
$mensagemErro = "";

if ($sucesso === "produto_excluido") {
    $mensagemSucesso = "Produto excluído com sucesso.";
}

if ($erro === "produto_vendido") {
    $mensagemErro =
        "Este produto não pode ser excluído porque já foi utilizado em uma venda.";
}

if ($erro === "nao_encontrado") {
    $mensagemErro =
        "Produto não encontrado.";
}

if ($erro === "banco") {
    $mensagemErro =
        "Não foi possível excluir o produto. Tente novamente.";
}

if ($erro === "id") {
    $mensagemErro =
        "Produto inválido.";
}

exigirPerfil(["admin"]);

$produtos = [];

try {

    $sql = "
        SELECT
            p.id,
            p.nome,
            p.preco,
            p.estoque,
            p.ativo,
            c.nome AS categoria
        FROM produtos p
        INNER JOIN categorias c
            ON p.categoria_id = c.id
        ORDER BY p.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $produtos = $stmt->fetchAll();

} catch (PDOException $e) {

    $produtos = [];

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

    <title>Gerenciar Produtos - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
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

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Gerenciar Produtos
            </h1>

            <p class="text-muted mb-0">
                Cadastre e gerencie os produtos da Art&Co.
            </p>

        </div>

        <a
            href="cadastrar.php"
            class="btn btn-primary"
        >
            + Novo produto
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

    <?php if (count($produtos) > 0): ?>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>

                                <th>Produto</th>

                                <th>Categoria</th>

                                <th>Preço</th>

                                <th>Estoque</th>

                                <th>Status</th>

                                <th>Ações</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($produtos as $produto): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($produto["nome"]) ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars($produto["categoria"]) ?>

                                    </td>

                                    <td>

                                        R$

                                        <?= number_format(
                                            $produto["preco"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= $produto["estoque"] ?>

                                    </td>

                                    <td>

                                        <?php if ($produto["ativo"]): ?>

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

                                        <a href="editar.php?id=<?= $produto["id"] ?>"
                                        class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                    
                                    <form action="excluir.php"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $produto["id"] ?>"
                                    >
                                    
                                    <button
                                    type="submit"
                                    class="btn btn-sm btn-danger">
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

    <?php else: ?>

        <div class="alert alert-info text-center">

            Nenhum produto cadastrado.

        </div>

    <?php endif; ?>

</main>

<?php
require_once "componentes/footer.php";
?>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>