<?php

session_start();

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

$mensagemErro = "";

$nome = "";
$email = "";
$tipo = "cliente";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $tipo = $_POST["tipo"] ?? "cliente";

    if ($nome === "") {

        $mensagemErro = "Informe o nome do usuário.";

    } elseif ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagemErro = "Informe um e-mail válido.";

    } elseif (strlen($senha) < 6) {

        $mensagemErro =
            "A senha deve possuir pelo menos 6 caracteres.";

    } elseif (!in_array($tipo, ["admin", "vendedor", "cliente"], true)) {

        $mensagemErro = "Tipo de usuário inválido.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "SELECT id
                 FROM usuarios
                 WHERE email = :email
                 LIMIT 1"
            );

            $stmt->execute([
                ":email" => $email
            ]);

            if ($stmt->fetch()) {

                $mensagemErro =
                    "Este e-mail já está cadastrado.";

            } else {

                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare(
                    "INSERT INTO usuarios
                    (nome, email, senha, tipo, ativo)
                    VALUES
                    (:nome, :email, :senha, :tipo, 1)"
                );

                $stmt->execute([
                    ":nome" => $nome,
                    ":email" => $email,
                    ":senha" => $senhaHash,
                    ":tipo" => $tipo
                ]);

                header(
                    "Location: index.php?sucesso=usuario_criado"
                );

                exit;
            }

        } catch (PDOException $e) {

            $mensagemErro =
                "Não foi possível cadastrar o usuário.";
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

    <title>Novo Usuário - Art&Co</title>

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

        <div class="col-12 col-md-8 col-lg-6">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <h1 class="fw-bold mb-2">
                        Novo usuário
                    </h1>

                    <p class="text-muted mb-4">
                        Cadastre um novo usuário no sistema.
                    </p>

                    <?php if ($mensagemErro !== ""): ?>

                        <div class="alert alert-danger">

                            <?= htmlspecialchars($mensagemErro) ?>

                        </div>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="cadastrar.php"
                    >

                        <div class="mb-3">

                            <label
                                for="nome"
                                class="form-label fw-semibold"
                            >
                                Nome
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nome"
                                name="nome"
                                value="<?= htmlspecialchars($nome) ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label fw-semibold"
                            >
                                E-mail
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($email) ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label
                                for="senha"
                                class="form-label fw-semibold"
                            >
                                Senha
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="senha"
                                name="senha"
                                minlength="6"
                                required
                            >

                            <div class="form-text">
                                A senha deve possuir pelo menos 6 caracteres.
                            </div>

                        </div>

                        <div class="mb-4">

                            <label
                                for="tipo"
                                class="form-label fw-semibold"
                            >
                                Tipo de usuário
                            </label>

                            <select
                                class="form-select"
                                id="tipo"
                                name="tipo"
                                required
                            >

                                <option
                                    value="cliente"
                                    <?= $tipo === "cliente" ? "selected" : "" ?>
                                >
                                    Cliente
                                </option>

                                <option
                                    value="vendedor"
                                    <?= $tipo === "vendedor" ? "selected" : "" ?>
                                >
                                    Vendedor
                                </option>

                                <option
                                    value="admin"
                                    <?= $tipo === "admin" ? "selected" : "" ?>
                                >
                                    Administrador
                                </option>

                            </select>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Cadastrar usuário
                            </button>

                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >
                                Cancelar
                            </a>

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