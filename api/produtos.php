<?php

require_once "../config.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    /*
     * BUSCA
     */
    $busca = trim($_GET["busca"] ?? "");


    /*
     * CATEGORIA
     */
    $categoria = filter_input(
        INPUT_GET,
        "categoria",
        FILTER_VALIDATE_INT
    );

    if ($categoria === false || $categoria === null || $categoria < 1) {
        $categoria = null;
    }


    /*
     * LIMITE
     */
    $limite = filter_input(
        INPUT_GET,
        "limite",
        FILTER_VALIDATE_INT
    );

    if ($limite === false || $limite === null) {
        $limite = 10;
    }

    if ($limite < 1) {
        $limite = 1;
    }

    if ($limite > 100) {
        $limite = 100;
    }


    /*
     * OFFSET
     */
    $offset = filter_input(
        INPUT_GET,
        "offset",
        FILTER_VALIDATE_INT
    );

    if ($offset === false || $offset === null || $offset < 0) {
        $offset = 0;
    }


    /*
     * STORED PROCEDURE
     */
    $sql = "
        CALL sp_produtos_dashboard(
            :busca,
            :categoria,
            :limite,
            :offset
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ":busca",
        $busca,
        PDO::PARAM_STR
    );

    if ($categoria === null) {

        $stmt->bindValue(
            ":categoria",
            null,
            PDO::PARAM_NULL
        );

    } else {

        $stmt->bindValue(
            ":categoria",
            $categoria,
            PDO::PARAM_INT
        );
    }

    $stmt->bindValue(
        ":limite",
        $limite,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        ":offset",
        $offset,
        PDO::PARAM_INT
    );

    $stmt->execute();


    /*
     * RESULTADO
     *
     * Normalizamos os tipos para garantir que
     * o JSON entregue números para o TypeScript.
     */
    $produtosBanco = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $produtos = array_map(
        function (array $produto): array {

            return [
                "id" => (int) $produto["id"],
                "produto" => (string) $produto["produto"],
                "categoria" => (string) $produto["categoria"],
                "preco" => (float) $produto["preco"],
                "estoque" => (int) $produto["estoque"],
                "quantidade_vendida" =>
                    (int) $produto["quantidade_vendida"],
                "faturamento" =>
                    (float) $produto["faturamento"]
            ];
        },
        $produtosBanco
    );


    echo json_encode(
        $produtos,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );


    /* Libera o cursor da Stored Procedure. */
    $stmt->closeCursor();

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível carregar os produtos."
    ]);
}