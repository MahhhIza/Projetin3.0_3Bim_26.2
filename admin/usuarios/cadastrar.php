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

    <div class="formulario-pagina">

        <div class="formulario-cabecalho">
            <h1 class="titulo-pagina">
                Novo usuário
            </h1>

            <p class="subtitulo-pagina">
                Cadastre um novo usuário no sistema.
            </p>
        </div>

        <div class="formulario-card">

            <div class="card-body">

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
                            class="formulario-label"
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
                            class="formulario-label"
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
                            class="formulario-label"
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
                            class="formulario-label"
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
                            Cadastrar usuário
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