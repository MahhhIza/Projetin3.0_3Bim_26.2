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

            $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
                    VALUES (:nome, :email, :senha, 'usuario')";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":email" => $email,
                ":senha" => $senhaHash
            ]);

            header("Location: login.php?cadastro=sucesso");
            exit;

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
    <div class="formulario-pagina">

        <div class="formulario-cabecalho text-center">
            <h1 class="titulo-pagina">Criar conta</h1>

            <p class="subtitulo-pagina">
                Cadastre-se para começar a aproveitar a Art&Co.
            </p>
        </div>

        <div class="formulario-card">
            <div class="card-body">

                <?php if ($mensagem !== ""): ?>
                    <div class="alert alert-info text-center">
                        <?= htmlspecialchars($mensagem) ?>
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
                            id="email"
                            name="email"
                            class="form-control"
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
                            id="senha"
                            name="senha"
                            class="form-control"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="btn btn-artco w-100"
                    >
                        Cadastrar
                    </button>

                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    Já possui uma conta?
                    <a href="login.php" class="link-cad-log">Entrar</a>
                </p>

            </div>
        </div>

    </div>
</main>

    <?php
    require_once "componentes/footer.php";
    ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>