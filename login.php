<?php

session_start();

require_once "config.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    if ($email === "" || $senha === "") {

        $mensagem = "Preencha todos os campos.";

    } else {

        try {

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

                $mensagem = "Login realizado com sucesso!";

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

</head>

<body>

    <h1>Login - Art&Co</h1>

    <?php if ($mensagem !== ""): ?>

        <p>
            <?= htmlspecialchars($mensagem) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <div>

            <label for="email">
                E-mail:
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
            >

        </div>

        <br>

        <div>

            <label for="senha">
                Senha:
            </label>

            <input
                type="password"
                id="senha"
                name="senha"
                required
            >

        </div>

        <br>

        <button type="submit">
            Entrar
        </button>

    </form>

    <p>
        Ainda não possui uma conta?
        <a href="cadastro.php">Cadastre-se</a>
    </p>

</body>

</html>