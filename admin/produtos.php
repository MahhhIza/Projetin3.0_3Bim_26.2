<?php

session_start();

require_once "../config.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["usuario_tipo"] !== "admin") {
    header("Location: ../painel.php");
    exit;
}

try {

    $sql = "SELECT
                p.id,
                p.nome,
                p.preco,
                p.estoque,
                p.ativo,
                c.nome AS categoria
            FROM produtos p
            INNER JOIN categorias c
                ON p.categoria_id = c.id
            ORDER BY p.id DESC";

    $stmt = $pdo->query($sql);

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

    <title>Administração - Produtos | Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../src/css/style.css"
    >

</head>

<body>

<?php

$base = "../";

require_once "../componentes/navbar.php";

?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Gerenciar produtos 🎨
            </h1>

            <p class="text-muted">
                Cadastre, edite e gerencie os produtos da Art&Co.
            </p>

        </div>

        <a
            href="produto_novo.php"
            class="btn btn-success"
        >
            + Novo produto
        </a>

    </div>

    <?php if (count($produtos) > 0): ?>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

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
                                <?= $produto["id"] ?>
                            </td>

                            <td class="fw-bold">
                                <?= htmlspecialchars(
                                    $produto["nome"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produto["categoria"]
                                ) ?>
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

                                <a
                                    href="produto_editar.php?id=<?= $produto["id"] ?>"
                                    class="btn btn-sm btn-primary"
                                >
                                    Editar
                                </a>

                                <a
                                    href="produto_excluir.php?id=<?= $produto["id"] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Deseja realmente excluir este produto?');"
                                >
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="alert alert-info">
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