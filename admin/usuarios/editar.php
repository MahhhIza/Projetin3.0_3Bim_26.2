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
        "SELECT id, nome, email, tipo, ativo
         FROM usuarios
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {

        header(
            "Location: index.php?erro=nao_encontrado"
        );

        exit;
    }

} catch (PDOException $e) {

    header(
        "Location: index.php?erro=banco"
    );

    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $tipo = $_POST["tipo"] ?? "";
    $ativo = isset($_POST["ativo"]) ? 1 : 0;
    $senha = $_POST["senha"] ?? "";

    $tiposPermitidos = [
        "admin",
        "vendedor",
        "cliente"
    ];

    if ($nome === "") {

        $erro = "Informe o nome do usuário.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Informe um e-mail válido.";

    } elseif (!in_array($tipo, $tiposPermitidos, true)) {

        $erro = "Tipo de usuário inválido.";

    } elseif ($senha !== "" && strlen($senha) < 6) {

        $erro =
            "A nova senha deve possuir pelo menos 6 caracteres.";

    } else {

        try {

            // Verifica se o e-mail já pertence a outro usuário
            $stmt = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM usuarios
                 WHERE email = :email
                 AND id <> :id"
            );

            $stmt->execute([
                ":email" => $email,
                ":id" => $id
            ]);

            if ((int) $stmt->fetchColumn() > 0) {

                $erro =
                    "Este e-mail já está sendo utilizado.";

            } else {

                if ($senha !== "") {

                    $senhaHash = password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $pdo->prepare(
                        "UPDATE usuarios
                         SET nome = :nome,
                             email = :email,
                             senha = :senha,
                             tipo = :tipo,
                             ativo = :ativo
                         WHERE id = :id"
                    );

                    $stmt->execute([
                        ":nome" => $nome,
                        ":email" => $email,
                        ":senha" => $senhaHash,
                        ":tipo" => $tipo,
                        ":ativo" => $ativo,
                        ":id" => $id
                    ]);

                } else {

                    $stmt = $pdo->prepare(
                        "UPDATE usuarios
                         SET nome = :nome,
                             email = :email,
                             tipo = :tipo,
                             ativo = :ativo
                         WHERE id = :id"
                    );

                    $stmt->execute([
                        ":nome" => $nome,
                        ":email" => $email,
                        ":tipo" => $tipo,
                        ":ativo" => $ativo,
                        ":id" => $id
                    ]);
                }

                header(
                    "Location: index.php?sucesso=usuario_editado"
                );

                exit;
            }

        } catch (PDOException $e) {

            $erro =
                "Não foi possível atualizar o usuário.";
        }
    }

    $usuario["nome"] = $nome;
    $usuario["email"] = $email;
    $usuario["tipo"] = $tipo;
    $usuario["ativo"] = $ativo;
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

    <title>Editar Usuário - Art&Co</title>

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
                Editar Usuário
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
                        required
                        value="<?= htmlspecialchars(
                            $usuario["nome"]
                        ) ?>"
                    >

                </div>


                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label"
                    >
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars(
                            $usuario["email"]
                        ) ?>"
                    >

                </div>


                <div class="mb-3">

                    <label
                        for="senha"
                        class="form-label"
                    >
                        Nova senha
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control"
                        minlength="6"
                    >

                    <div class="form-text">
                        Deixe vazio para manter a senha atual.
                    </div>

                </div>


                <div class="mb-3">

                    <label
                        for="tipo"
                        class="form-label"
                    >
                        Tipo de usuário
                    </label>

                    <select
                        id="tipo"
                        name="tipo"
                        class="form-select"
                        required
                    >

                        <option
                            value="cliente"
                            <?= $usuario["tipo"] === "cliente"
                                ? "selected"
                                : "" ?>
                        >
                            Cliente
                        </option>

                        <option
                            value="vendedor"
                            <?= $usuario["tipo"] === "vendedor"
                                ? "selected"
                                : "" ?>
                        >
                            Vendedor
                        </option>

                        <option
                            value="admin"
                            <?= $usuario["tipo"] === "admin"
                                ? "selected"
                                : "" ?>
                        >
                            Administrador
                        </option>

                    </select>

                </div>


                <div class="form-check mb-4">

                    <input
                        type="checkbox"
                        id="ativo"
                        name="ativo"
                        class="form-check-input"
                        <?= (int) $usuario["ativo"] === 1
                            ? "checked"
                            : "" ?>
                    >

                    <label
                        for="ativo"
                        class="form-check-label"
                    >
                        Usuário ativo
                    </label>

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