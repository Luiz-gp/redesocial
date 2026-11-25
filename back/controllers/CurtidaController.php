<?php

require_once __DIR__ . "/../models/CurtidaModel.php";

function readCurtida($con) {

    $curtidas = listarCurtidas($con);

    echo json_encode($curtidas);
}


function toggleCurtida($con) {

    $dados = json_decode(file_get_contents("php://input"), true);

    $idUsuario = intval($dados["id_usuario"] ?? 0);
    $idPublicacao = intval($dados["id_publicacao"] ?? 0);

    if ($idUsuario <= 0 || $idPublicacao <= 0) {
        http_response_code(400);

        echo json_encode([
            "erro" => "Dados inválidos para curtir."
        ]);

        return;
    }

    $resultado = alternarCurtida($con, $idUsuario, $idPublicacao);

    if ($resultado === "curtida") {
        echo json_encode([
            "mensagem" => "Publicação curtida!",
            "curtido" => true
        ]);
    } elseif ($resultado === "descurtida") {
        echo json_encode([
            "mensagem" => "Curtida removida!",
            "curtido" => false
        ]);
    } else {
        http_response_code(500);

        echo json_encode([
            "erro" => "Erro ao processar curtida."
        ]);
    }
}