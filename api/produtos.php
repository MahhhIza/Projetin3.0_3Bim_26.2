<?php

require_once "../config.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $sql = "SELECT
                id,
                produto,
                categoria,
                preco,
                estoque,
                quantidade_vendida,
                faturamento
            FROM vw_produtos_analitico
            ORDER BY produto ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $produtos = $stmt->fetchAll();

    echo json_encode($produtos);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível carregar os produtos."
    ]);
}