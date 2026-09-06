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

try {

    // Verifica se existem produtos vinculados
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM produtos
         WHERE categoria_id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $quantidadeProdutos =
        (int) $stmt->fetchColumn();


    if ($quantidadeProdutos > 0) {

        header(
            "Location: index.php?erro=categoria_utilizada"
        );

        exit;
    }


    // Exclui a categoria
    $stmt = $pdo->prepare(
        "DELETE FROM categorias
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);


    if ($stmt->rowCount() === 0) {

        header(
            "Location: index.php?erro=nao_encontrada"
        );

        exit;
    }


    header(
        "Location: index.php?sucesso=categoria_excluida"
    );

    exit;

} catch (PDOException $e) {

    header(
        "Location: index.php?erro=banco"
    );

    exit;
}