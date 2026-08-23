<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["carrinho"])) {
    header("Location: carrinho.php");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];

try {

    $pdo->beginTransaction();

    $total = 0;

    foreach ($_SESSION["carrinho"] as $item) {

        $sql = "
            SELECT id, preco, estoque
            FROM produtos
            WHERE id = :id
              AND ativo = 1
            FOR UPDATE
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":id" => $item["id"]
        ]);

        $produto = $stmt->fetch();

        if (!$produto) {
            throw new Exception("Produto não encontrado.");
        }

        if ($produto["estoque"] < $item["quantidade"]) {
            throw new Exception(
                "Estoque insuficiente para um dos produtos."
            );
        }

        $total +=
            $produto["preco"] * $item["quantidade"];
    }

    /*
     * Cria a venda
     */
    $sql = "
        INSERT INTO vendas
        (usuario_id, total)
        VALUES
        (:usuario_id, :total)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":usuario_id" => $usuarioId,
        ":total" => $total
    ]);

    $vendaId = $pdo->lastInsertId();

    /*
     * Insere os itens da venda
     * e atualiza o estoque.
     */
    foreach ($_SESSION["carrinho"] as $item) {

        $sql = "
            SELECT preco, estoque
            FROM produtos
            WHERE id = :id
            FOR UPDATE
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":id" => $item["id"]
        ]);

        $produto = $stmt->fetch();

        $sql = "
            INSERT INTO itens_venda
            (
                venda_id,
                produto_id,
                quantidade,
                valor_unitario
            )
            VALUES
            (
                :venda_id,
                :produto_id,
                :quantidade,
                :valor_unitario
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":venda_id" => $vendaId,
            ":produto_id" => $item["id"],
            ":quantidade" => $item["quantidade"],
            ":valor_unitario" => $produto["preco"]
        ]);

        $sql = "
            UPDATE produtos
            SET estoque = estoque - :quantidade
            WHERE id = :id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":quantidade" => $item["quantidade"],
            ":id" => $item["id"]
        ]);
    }

    $pdo->commit();

    $_SESSION["carrinho"] = [];

    $_SESSION["compra_sucesso"] = true;

    header("Location: finalizar_compra.php?sucesso=1");
    exit;

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $erro = $e->getMessage();
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

    <title>Finalizar compra - Art&Co</title>

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

    <?php if (isset($_GET["sucesso"])): ?>

        <div class="text-center">

            <div class="display-1">
                ✅
            </div>

            <h1 class="fw-bold mt-3">
                Compra realizada com sucesso!
            </h1>

            <p class="text-muted mt-3">
                Obrigado pela sua compra na Art&Co.
            </p>

            <a
                href="produtos/produtos.php"
                class="btn btn-primary mt-3"
            >
                Continuar comprando
            </a>

            <a
                href="painel.php"
                class="btn btn-outline-secondary mt-3"
            >
                Ir para o painel
            </a>

        </div>

    <?php elseif (isset($erro)): ?>

        <div class="alert alert-danger">

            <strong>
                Não foi possível finalizar a compra.
            </strong>

            <br>

            <?= htmlspecialchars($erro) ?>

        </div>

        <a
            href="carrinho.php"
            class="btn btn-secondary"
        >
            Voltar ao carrinho
        </a>

    <?php endif; ?>

</main>

</body>

</html>