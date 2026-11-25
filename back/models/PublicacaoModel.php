<?php

function inserirPublicacao($con, $idUsuario, $legenda, $imagem) {

    $sql = "INSERT INTO publicacoes
            (id_usuario, legenda, imagem_url, data_publicacao)
            VALUES
            (:id_usuario, :legenda, :imagem_url, NOW())";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":id_usuario" => $idUsuario,
        ":legenda" => $legenda,
        ":imagem_url" => $imagem
    ]);
}


function listarPublicacoes($con) {

    $sql = "SELECT
                p.id_publicacao,
                p.id_usuario,
                p.legenda,
                p.imagem_url,
                p.data_publicacao,
                u.nome,
                u.username
            FROM publicacoes p
            INNER JOIN usuarios u
                ON p.id_usuario = u.id_usuario
            ORDER BY p.data_publicacao DESC";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


function atualizarPublicacao($con, $id, $idUsuario, $legenda, $imagem) {

    $sql = "UPDATE publicacoes
            SET legenda = :legenda,
                imagem_url = :imagem_url
            WHERE id_publicacao = :id
            AND id_usuario = :id_usuario";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":legenda" => $legenda,
        ":imagem_url" => $imagem,
        ":id" => $id,
        ":id_usuario" => $idUsuario
    ]);

    return $stmt->rowCount() > 0;
}


function removerPublicacao($con, $id, $idUsuario) {

    $sql = "DELETE FROM publicacoes
            WHERE id_publicacao = :id
            AND id_usuario = :id_usuario";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":id" => $id,
        ":id_usuario" => $idUsuario
    ]);

    return $stmt->rowCount() > 0;
}