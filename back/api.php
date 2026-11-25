<?php

header("Content-Type: application/json; charset=utf-8");

try {

    require_once __DIR__ . "/config.php";
    require_once __DIR__ . "/routes/rotas.php";

} catch (Throwable $erro) {

    http_response_code(500);

    echo json_encode([
        "status" => "erro",
        "mensagem" => $erro->getMessage(),
        "arquivo" => $erro->getFile(),
        "linha" => $erro->getLine()
    ]);
}