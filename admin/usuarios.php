<?php

session_start();

require_once "../config.php";
require_once "../protecao/acesso.php";

exigirPerfil(["admin"]);

try {

    $sql = "
        SELECT id, nome, email, tipo
        FROM usuarios
        ORDER BY nome ASC
    ";

    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll();

} catch (PDOException $e) {

    $usuarios = [];

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

    <title>Usuários - Administração Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../src/css/style.css"
    >

</head>

<body>

<?php

$base = "../";

require_once "../componentes/navbar.php";

?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                👥 Usuários
            </h1>

            <p class="text-muted">
                Gerenciamento dos usuários cadastrados.
            </p>

        </div>

        <a
            href="index.php"
            class="btn btn-outline-secondary"
        >
            ← Voltar
        </a>

    </div>

    <?php if (empty($usuarios)): ?>

        <div class="alert alert-info text-center">

            Nenhum usuário cadastrado.

        </div>

    <?php else: ?>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Nome</th>

                                <th>E-mail</th>

                                <th>Perfil</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($usuarios as $usuario): ?>

                            <tr>

                                <td>
                                    <?= $usuario["id"] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($usuario["nome"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($usuario["email"]) ?>
                                </td>

                                <td>

                                    <?php if ($usuario["tipo"] === "admin"): ?>

                                        <span class="badge bg-danger">
                                            Administrador
                                        </span>

                                    <?php elseif ($usuario["tipo"] === "vendedor"): ?>

                                        <span class="badge bg-warning text-dark">
                                            Vendedor
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-primary">
                                            Usuário
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>