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

    // Verifica se o produto já foi utilizado em alguma venda
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) 
         FROM itens_venda 
         WHERE produto_id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $quantidadeVendas = (int) $stmt->fetchColumn();

    if ($quantidadeVendas > 0) {

        header(
            "Location: index.php?erro=produto_vendido"
        );

        exit;
    }

    // Exclui o produto
    $stmt = $pdo->prepare(
        "DELETE FROM produtos 
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
        "Location: index.php?sucesso=produto_excluido"
    );

    exit;

} catch (PDOException $e) {

    header(
        "Location: index.php?erro=banco"
    );

    exit;
}