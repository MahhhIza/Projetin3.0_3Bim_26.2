<?php

require_once "../../config.php";
require_once "../../protecao/acesso.php";

exigirPerfil(["admin"]);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;
}

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if ($id === false || $id === null || $id < 1) {

    header("Location: index.php?erro=id");
    exit;
}


// Impede que o administrador exclua a própria conta
if (
    isset($_SESSION["usuario_id"]) &&
    (int) $_SESSION["usuario_id"] === $id
) {

    header(
        "Location: index.php?erro=usuario_atual"
    );

    exit;
}


try {

    // Verifica se existem vendas associadas
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM vendas
         WHERE usuario_id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $quantidadeVendas =
        (int) $stmt->fetchColumn();


    if ($quantidadeVendas > 0) {

        header(
            "Location: index.php?erro=usuario_vinculado"
        );

        exit;
    }


    // Exclui o usuário
    $stmt = $pdo->prepare(
        "DELETE FROM usuarios
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);


    if ($stmt->rowCount() === 0) {

        header(
            "Location: index.php?erro=nao_encontrado"
        );

        exit;
    }


    header(
        "Location: index.php?sucesso=usuario_excluido"
    );

    exit;

} catch (PDOException $e) {

    header(
        "Location: index.php?erro=banco"
    );

    exit;
}