<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["carrinho"])) {

    $_SESSION["carrinho"] = [];

}

/*
 * Adicionar produto ao carrinho
 */
if (isset($_GET["adicionar"]) && is_numeric($_GET["adicionar"])) {

    $produtoId = (int) $_GET["adicionar"];

    $sql = "
        SELECT id, nome, preco, estoque
        FROM produtos
        WHERE id = :id
          AND ativo = 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":id" => $produtoId
    ]);

    $produto = $stmt->fetch();

    if ($produto) {

        $estoqueAtual = (int) $produto["estoque"];

        if ($estoqueAtual > 0) {

            if (isset($_SESSION["carrinho"][$produtoId])) {

                $quantidadeAtual =
                    (int) $_SESSION["carrinho"][$produtoId]["quantidade"];

                if ($quantidadeAtual < $estoqueAtual) {

                    $_SESSION["carrinho"][$produtoId]["quantidade"]++;

                }

            } else {

                $_SESSION["carrinho"][$produtoId] = [

                    "id" => $produto["id"],

                    "nome" => $produto["nome"],

                    "preco" => $produto["preco"],

                    "quantidade" => 1

                ];

            }

        }

    }

    header("Location: carrinho.php");

    exit;

}

/*
 * Remover produto do carrinho
 */
if (isset($_GET["remover"]) && is_numeric($_GET["remover"])) {

    $produtoId = (int) $_GET["remover"];

    unset($_SESSION["carrinho"][$produtoId]);

    header("Location: carrinho.php");

    exit;

}

/*
 * Verificar estoque atual dos produtos
 */
$produtosIndisponiveis = [];

$total = 0;

foreach ($_SESSION["carrinho"] as $id => $item) {

    $sqlEstoque = "
        SELECT nome, estoque, ativo
        FROM produtos
        WHERE id = :id
    ";

    $stmtEstoque = $pdo->prepare($sqlEstoque);

    $stmtEstoque->execute([
        ":id" => $id
    ]);

    $produtoAtual = $stmtEstoque->fetch();

    if (!$produtoAtual || (int) $produtoAtual["ativo"] !== 1) {

        $produtosIndisponiveis[] = [
            "nome" => $item["nome"],
            "quantidade" => (int) $item["quantidade"],
            "estoque" => 0
        ];

        continue;

    }

    $estoqueAtual = (int) $produtoAtual["estoque"];

    $quantidadeCarrinho = (int) $item["quantidade"];

    if ($estoqueAtual <= 0 || $quantidadeCarrinho > $estoqueAtual) {

        $produtosIndisponiveis[] = [
            "nome" => $produtoAtual["nome"],
            "quantidade" => $quantidadeCarrinho,
            "estoque" => $estoqueAtual
        ];

        continue;

    }

    $subtotal =
        (float) $item["preco"] * $quantidadeCarrinho;

    $total += $subtotal;

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

    <title>Carrinho - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="src/css/style.css"
    >

</head>

<body>

<?php

$base = "";

require_once "componentes/navbar.php";

?>

<main class="container py-5">

    <h1 class="fw-bold mb-4">

        🛒 Meu Carrinho

    </h1>

    <?php if (empty($_SESSION["carrinho"])): ?>

        <div class="alert alert-info">

            Seu carrinho está vazio.

        </div>

        <a
            href="produtos/produtos.php"
            class="btn btn-primary"
        >

            Ver produtos

        </a>

    <?php else: ?>

        <?php if (!empty($produtosIndisponiveis)): ?>

            <div class="alert alert-danger">

                <h5 class="alert-heading fw-bold">

                    Atenção: há produtos com estoque insuficiente.

                </h5>

                <p class="mb-2">

                    Verifique os itens abaixo antes de finalizar a compra:

                </p>

                <ul class="mb-0">

                    <?php foreach ($produtosIndisponiveis as $produtoIndisponivel): ?>

                        <li>

                            <strong>
                                <?= htmlspecialchars($produtoIndisponivel["nome"]) ?>
                            </strong>

                            <?php if ($produtoIndisponivel["estoque"] <= 0): ?>

                                — produto esgotado.

                            <?php else: ?>

                                — você adicionou
                                <?= $produtoIndisponivel["quantidade"] ?>
                                unidade(s), mas há apenas
                                <?= $produtoIndisponivel["estoque"] ?>
                                disponível(is).

                            <?php endif; ?>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>Produto</th>

                        <th>Preço</th>

                        <th>Quantidade</th>

                        <th>Subtotal</th>

                        <th></th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($_SESSION["carrinho"] as $item): ?>

                    <?php

                    $subtotal =
                        (float) $item["preco"]
                        * (int) $item["quantidade"];

                    ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars($item["nome"]) ?>

                        </td>

                        <td>

                            R$

                            <?= number_format(
                                $item["preco"],
                                2,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= $item["quantidade"] ?>

                        </td>

                        <td class="fw-bold">

                            R$

                            <?= number_format(
                                $subtotal,
                                2,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <a
                                href="carrinho.php?remover=<?= $item["id"] ?>"
                                class="btn btn-sm btn-outline-danger"
                            >

                                Remover

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <div class="text-end mt-4">

            <h3 class="fw-bold">

                Total:

                <span class="preco-produto">

                    R$

                    <?= number_format(
                        $total,
                        2,
                        ",",
                        "."
                    ) ?>

                </span>

            </h3>

        </div>

        <div class="d-flex justify-content-between mt-4">

            <a
                href="produtos/produtos.php"
                class="btn btn-outline-secondary"
            >

                ← Continuar comprando

            </a>

            <?php if (empty($produtosIndisponiveis)): ?>

                <a
                    href="finalizar_compra.php"
                    class="btn btn-success"
                >

                    Finalizar compra

                </a>

            <?php else: ?>

                <span
                    class="btn btn-secondary disabled"
                    aria-disabled="true"
                >

                    Corrija o estoque para finalizar

                </span>

            <?php endif; ?>

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