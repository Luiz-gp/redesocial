<?php

require_once __DIR__ . "/../models/PublicacaoModel.php";

function createPublicacao($con) {

    $dados = json_decode(file_get_contents("php://input"), true);

    $idUsuario = intval($dados["id_usuario"] ?? 0);
    $legenda = trim($dados["legenda"] ?? "");
    $imagem = trim($dados["imagem_url"] ?? "");

    if ($idUsuario <= 0 || $legenda === "" || $imagem === "") {
        http_response_code(400);

        echo json_encode([
            "erro" => "Preencha a legenda e a imagem."
        ]);

        return;
    }

    $resultado = inserirPublicacao(
        $con,
        $idUsuario,
        $legenda,
        $imagem
    );

    if ($resultado) {
        http_response_code(201);

        echo json_encode([
            "mensagem" => "Publicação criada com sucesso!"
        ]);
    } else {
        http_response_code(500);

        echo json_encode([
            "erro" => "Erro ao criar publicação."
        ]);
    }
}


function readPublicacao($con) {

    $publicacoes = listarPublicacoes($con);

    echo json_encode($publicacoes);
}


function updatePublicacao($con) {

    $id = intval($_GET["id"] ?? 0);

    $dados = json_decode(file_get_contents("php://input"), true);

    $idUsuario = intval($dados["id_usuario"] ?? 0);
    $legenda = trim($dados["legenda"] ?? "");
    $imagem = trim($dados["imagem_url"] ?? "");

    if ($id <= 0 || $idUsuario <= 0 || $legenda === "" || $imagem === "") {
        http_response_code(400);

        echo json_encode([
            "erro" => "Dados inválidos para atualizar."
        ]);

        return;
    }

    $resultado = atualizarPublicacao(
        $con,
        $id,
        $idUsuario,
        $legenda,
        $imagem
    );

    if ($resultado) {
        echo json_encode([
            "mensagem" => "Publicação atualizada!"
        ]);
    } else {
        http_response_code(400);

        echo json_encode([
            "erro" => "Publicação não encontrada ou não pertence ao usuário."
        ]);
    }
}


function deletePublicacao($con) {

    $id = intval($_GET["id"] ?? 0);
    $idUsuario = intval($_GET["id_usuario"] ?? 0);

    if ($id <= 0 || $idUsuario <= 0) {
        http_response_code(400);

        echo json_encode([
            "erro" => "IDs inválidos."
        ]);

        return;
    }

    $resultado = removerPublicacao($con, $id, $idUsuario);

    if ($resultado) {
        echo json_encode([
            "mensagem" => "Publicação removida!"
        ]);
    } else {
        http_response_code(400);

        echo json_encode([
            "erro" => "Não foi possível remover a publicação."
        ]);
    }
}