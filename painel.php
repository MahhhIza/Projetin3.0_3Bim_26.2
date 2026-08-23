<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel - Art&Co</title>

</head>

<body>

    <h1>Art&Co</h1>

    <h2>Bem-vindo, <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>!</h2>

    <p>
        Você está logado no sistema.
    </p>

    <p>
        Tipo de usuário:
        <strong>
            <?= htmlspecialchars($_SESSION["usuario_tipo"]) ?>
        </strong>
    </p>

    <p>
        <a href="logout.php">Sair do sistema</a>
    </p>

</body>

</html>