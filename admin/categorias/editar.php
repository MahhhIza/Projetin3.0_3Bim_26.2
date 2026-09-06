<?php

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if ($id === false || $id === null || $id < 1) {

    header("Location: index.php?erro=id");
    exit;
}

$erro = "";

try {

    $stmt = $pdo->prepare(
        "SELECT id, nome, descricao
         FROM categorias
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {

        header("Location: index.php?erro=nao_encontrada");
        exit;
    }

} catch (PDOException $e) {

    header("Location: index.php?erro=banco");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if ($nome === "") {

        $erro = "Informe o nome da categoria.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "UPDATE categorias
                 SET nome = :nome,
                     descricao = :descricao
                 WHERE id = :id"
            );

            $stmt->execute([
                ":nome" => $nome,
                ":descricao" => $descricao,
                ":id" => $id
            ]);

            header(
                "Location: index.php?sucesso=categoria_editada"
            );

            exit;

        } catch (PDOException $e) {

            $erro =
                "Não foi possível atualizar a categoria.";
        }
    }

    $categoria["nome"] = $nome;
    $categoria["descricao"] = $descricao;
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

    <title>Editar Categoria - Art&Co</title>

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

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <h1 class="fw-bold mb-4">
                Editar Categoria
            </h1>


            <?php if ($erro !== ""): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($erro) ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="mb-3">

                    <label
                        for="nome"
                        class="form-label"
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
                            $categoria["nome"]
                        ) ?>"
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
                    ><?= htmlspecialchars(
                        $categoria["descricao"] ?? ""
                    ) ?></textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Salvar alterações
                </button>

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>