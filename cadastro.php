<?php

require_once "config.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    if ($nome === "" || $email === "" || $senha === "") {

        $mensagem = "Preencha todos os campos.";

    } else {

        try {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha)
                    VALUES (:nome, :email, :senha)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":email" => $email,
                ":senha" => $senhaHash
            ]);

            $mensagem = "Cadastro realizado com sucesso!";

        } catch (PDOException $e) {

            if ($e->getCode() === "23000") {
                $mensagem = "Este e-mail já está cadastrado.";
            } else {
                $mensagem = "Erro ao realizar o cadastro.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro - Art&Co</title>

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

        <div class="row justify-content-center">

            <div class="col-12 col-md-7 col-lg-5">

                <div class="card shadow-sm border-0">

                    <div class="card-body p-4">

                        <h1 class="text-center fw-bold mb-4">
                            Criar conta
                        </h1>

                        <?php if ($mensagem !== ""): ?>

                            <div class="alert alert-info text-center">
                                <?= htmlspecialchars($mensagem) ?>
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
                                >

                            </div>

                            <div class="mb-3">

                                <label
                                    for="senha"
                                    class="form-label"
                                >
                                    Senha
                                </label>

                                <input
                                    type="password"
                                    id="senha"
                                    name="senha"
                                    class="form-control"
                                    required
                                >

                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Cadastrar
                            </button>

                        </form>

                        <p class="text-center text-muted mt-4 mb-0">

                            Já possui uma conta?

                            <a href="login.php">
                                Entrar
                            </a>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>