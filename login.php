<?php

session_start();

require_once "config.php";

$mensagem = "";

if (isset($_GET["cadastro"]) && $_GET["cadastro"] === "sucesso") {
    $mensagem = "Cadastro realizado com sucesso! Agora faça login.";
}

$loginSucesso = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    if ($email === "" || $senha === "") {
        $mensagem = "Preencha todos os campos.";
    } else {
        try {
            // BD - SELECT
            $sql = "SELECT id, nome, email, senha, tipo
                    FROM usuarios
                    WHERE email = :email";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":email" => $email
            ]);

            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario["senha"])) {
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nome"] = $usuario["nome"];
                $_SESSION["usuario_tipo"] = $usuario["tipo"];

                if (
                    $_SESSION["usuario_tipo"] === "admin" ||
                    $_SESSION["usuario_tipo"] === "vendedor"
                ) {
                    header("Location: painel.php");
                } else {
                    header("Location: index.php");
                }

                exit;
            } else {
                $mensagem = "E-mail ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao realizar o login.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Art&Co</title>

    <!-- DW - Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="src/css/style.css">
</head>

<body>

<?php
$base = "";
require_once "componentes/navbar.php";
?>

<main class="container py-5">
    <div class="formulario-pagina">

        <div class="formulario-cabecalho text-center">
            <h1 class="titulo-pagina">Login</h1>

            <p class="subtitulo-pagina">
                Acesse sua conta para continuar na Art&Co.
            </p>
        </div>

        <div class="formulario-card">
            <div class="card-body">

                <?php if ($mensagem !== ""): ?>
                    <!-- DW - Bootstrap / Alert -->
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label for="email" class="formulario-label">
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
                        <label for="senha" class="formulario-label">
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
                        Entrar
                    </button>

                </form>

                <p class="text-center text-muted mt-4 mb-0">
                    Ainda não possui uma conta?

                    <a href="cadastro.php" class="link-cad-log">
                        Cadastre-se
                    </a>
                </p>

            </div>
        </div>

    </div>
</main>

<?php
require_once "componentes/footer.php";
?>

<!-- DW - Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>