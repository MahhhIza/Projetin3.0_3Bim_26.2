<?php

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if ($nome === "") {

        $erro = "Informe o nome da categoria.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO categorias (nome, descricao)
                 VALUES (:nome, :descricao)"
            );

            $stmt->execute([
                ":nome" => $nome,
                ":descricao" => $descricao
            ]);

            header(
                "Location: index.php?sucesso=categoria_criada"
            );

            exit;

        } catch (PDOException $e) {

            $erro =
                "Não foi possível cadastrar a categoria.";
        }
    }
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

    <title>Nova Categoria - Art&Co</title>

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

    <div class="formulario-pagina">

        <div class="formulario-cabecalho">
            <h1 class="titulo-pagina">
                Nova categoria
            </h1>

            <p class="subtitulo-pagina">
                Cadastre uma nova categoria de produtos.
            </p>
        </div>

        <div class="formulario-card">

            <div class="card-body">

                <?php if ($erro !== ""): ?>

                    <div class="alert alert-danger">
                        <?= htmlspecialchars($erro) ?>
                    </div>

                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">

                        <label
                            for="nome"
                            class="formulario-label"
                        >
                            Nome
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="form-control"
                            maxlength="100"
                            required
                            value="<?= htmlspecialchars(
                                $_POST["nome"] ?? ""
                            ) ?>"
                        >

                    </div>

                    <div class="mb-4">

                        <label
                            for="descricao"
                            class="formulario-label"
                        >
                            Descrição
                        </label>

                        <textarea
                            id="descricao"
                            name="descricao"
                            class="form-control"
                            rows="4"
                        ><?= htmlspecialchars(
                            $_POST["descricao"] ?? ""
                        ) ?></textarea>

                    </div>

                    <div class="d-flex justify-content-between">

                        <a
                            href="index.php"
                            class="btn btn-voltar"
                        >
                            Voltar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-artco"
                        >
                            Cadastrar categoria
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>

<?php

require_once "../../componentes/footer.php";

?>

</body>

</html>