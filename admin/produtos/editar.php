<?php

session_start();

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$id = $_GET["id"] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

$id = (int) $id;

$mensagem = "";
$produto = null;

/*
 * Busca o produto
 */
$sql = "
    SELECT
        id,
        nome,
        descricao,
        preco,
        estoque,
        categoria_id,
        ativo
    FROM produtos
    WHERE id = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":id" => $id
]);

$produto = $stmt->fetch();

if (!$produto) {
    header("Location: index.php");
    exit;
}

/*
 * Busca as categorias
 */
$sqlCategorias = "
    SELECT id, nome
    FROM categorias
    ORDER BY nome ASC
";

$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();

$categorias = $stmtCategorias->fetchAll();

/*
 * Atualiza o produto
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $preco = $_POST["preco"] ?? "";
    $estoque = $_POST["estoque"] ?? "";
    $categoriaId = $_POST["categoria_id"] ?? "";
    $ativo = isset($_POST["ativo"]) ? 1 : 0;

    if (
        $nome === "" ||
        $descricao === "" ||
        $preco === "" ||
        $estoque === "" ||
        $categoriaId === ""
    ) {

        $mensagem = "Preencha todos os campos.";

    } else {

        try {

            $sql = "
                UPDATE produtos
                SET
                    nome = :nome,
                    descricao = :descricao,
                    preco = :preco,
                    estoque = :estoque,
                    categoria_id = :categoria_id,
                    ativo = :ativo
                WHERE id = :id
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":descricao" => $descricao,
                ":preco" => $preco,
                ":estoque" => $estoque,
                ":categoria_id" => $categoriaId,
                ":ativo" => $ativo,
                ":id" => $id
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            $mensagem = "Erro ao atualizar o produto.";
        }
    }

    /*
     * Atualiza os valores exibidos no formulário
     */
    $produto["nome"] = $nome;
    $produto["descricao"] = $descricao;
    $produto["preco"] = $preco;
    $produto["estoque"] = $estoque;
    $produto["categoria_id"] = $categoriaId;
    $produto["ativo"] = $ativo;
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

    <title>Editar Produto - Art&Co</title>

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

    <div class="row justify-content-center">

        <div class="col-12 col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <h1 class="fw-bold mb-4">
                        ✏️ Editar produto
                    </h1>

                    <?php if ($mensagem !== ""): ?>

                        <div class="alert alert-warning">
                            <?= htmlspecialchars($mensagem) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label
                                for="nome"
                                class="form-label"
                            >
                                Nome do produto
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                class="form-control"
                                value="<?= htmlspecialchars($produto["nome"]) ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label
                                for="descricao"
                                class="form-label"
                            >
                                Descrição
                            </label>

                            <textarea
                                id="descricao"
                                name="descricao"
                                class="form-control"
                                rows="4"
                                required
                            ><?= htmlspecialchars($produto["descricao"]) ?></textarea>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="preco"
                                    class="form-label"
                                >
                                    Preço
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="preco"
                                    name="preco"
                                    class="form-control"
                                    value="<?= htmlspecialchars($produto["preco"]) ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label
                                    for="estoque"
                                    class="form-label"
                                >
                                    Estoque
                                </label>

                                <input
                                    type="number"
                                    min="0"
                                    id="estoque"
                                    name="estoque"
                                    class="form-control"
                                    value="<?= htmlspecialchars($produto["estoque"]) ?>"
                                    required
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="categoria_id"
                                class="form-label"
                            >
                                Categoria
                            </label>

                            <select
                                id="categoria_id"
                                name="categoria_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Selecione uma categoria
                                </option>

                                <?php foreach ($categorias as $categoria): ?>

                                    <option
                                        value="<?= $categoria["id"] ?>"
                                        <?= $produto["categoria_id"] == $categoria["id"] ? "selected" : "" ?>
                                    >
                                        <?= htmlspecialchars($categoria["nome"]) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                id="ativo"
                                name="ativo"
                                class="form-check-input"
                                <?= $produto["ativo"] ? "checked" : "" ?>
                            >

                            <label
                                for="ativo"
                                class="form-check-label"
                            >
                                Produto ativo
                            </label>

                        </div>

                        <div class="d-flex justify-content-between">

                            <a
                                href="index.php"
                                class="btn btn-outline-secondary"
                            >
                                ← Voltar
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Salvar alterações
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</main>

<?php
require_once "../../componentes/footer.php";
?>

</body>
</html>