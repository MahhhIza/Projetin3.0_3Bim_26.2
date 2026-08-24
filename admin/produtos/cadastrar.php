<?php

session_start();

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $preco = trim($_POST["preco"] ?? "");
    $estoque = trim($_POST["estoque"] ?? "");
    $categoriaId = $_POST["categoria_id"] ?? "";

    if (
        $nome === "" ||
        $descricao === "" ||
        $preco === "" ||
        $estoque === "" ||
        $categoriaId === ""
    ) {

        $mensagem = "Preencha todos os campos.";
        $tipoMensagem = "danger";

    } elseif (!is_numeric($preco) || $preco < 0) {

        $mensagem = "Informe um preço válido.";
        $tipoMensagem = "danger";

    } elseif (!is_numeric($estoque) || $estoque < 0) {

        $mensagem = "Informe um estoque válido.";
        $tipoMensagem = "danger";

    } else {

        try {

            $sql = "
                INSERT INTO produtos
                (
                    nome,
                    descricao,
                    preco,
                    estoque,
                    categoria_id,
                    ativo
                )
                VALUES
                (
                    :nome,
                    :descricao,
                    :preco,
                    :estoque,
                    :categoria_id,
                    1
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":descricao" => $descricao,
                ":preco" => $preco,
                ":estoque" => $estoque,
                ":categoria_id" => $categoriaId
            ]);

            $mensagem = "Produto cadastrado com sucesso!";
            $tipoMensagem = "success";

        } catch (PDOException $e) {

            $mensagem = "Erro ao cadastrar o produto.";
            $tipoMensagem = "danger";
        }
    }
}

/*
 * Busca as categorias para o formulário.
 */

$categorias = [];

try {

    $sql = "
        SELECT id, nome
        FROM categorias
        ORDER BY nome ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $categorias = $stmt->fetchAll();

} catch (PDOException $e) {

    $categorias = [];
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

    <title>Cadastrar Produto - Art&Co</title>

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
                        Cadastrar produto
                    </h1>

                    <?php if ($mensagem !== ""): ?>

                        <div class="alert alert-<?= $tipoMensagem ?>">

                            <?= htmlspecialchars($mensagem) ?>

                        </div>

                    <?php endif; ?>

                    <?php if (count($categorias) === 0): ?>

                        <div class="alert alert-warning">

                            Nenhuma categoria cadastrada.

                            <br>

                            Cadastre uma categoria antes de criar um produto.

                        </div>

                    <?php else: ?>

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
                                ></textarea>

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
                                        id="preco"
                                        name="preco"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
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
                                        id="estoque"
                                        name="estoque"
                                        class="form-control"
                                        min="0"
                                        required
                                    >

                                </div>

                            </div>

                            <div class="mb-4">

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
                                        >
                                            <?= htmlspecialchars($categoria["nome"]) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

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
                                    Cadastrar produto
                                </button>

                            </div>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>