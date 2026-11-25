<?php

function listarCurtidas($con) {

    $sql = "SELECT id_usuario, id_publicacao
            FROM curtidas";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


function alternarCurtida($con, $idUsuario, $idPublicacao) {

    $sql = "SELECT id_curtida
            FROM curtidas
            WHERE id_usuario = :id_usuario
            AND id_publicacao = :id_publicacao";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":id_usuario" => $idUsuario,
        ":id_publicacao" => $idPublicacao
    ]);

    $curtida = $stmt->fetch();

    if ($curtida) {

        $sql = "DELETE FROM curtidas
                WHERE id_usuario = :id_usuario
                AND id_publicacao = :id_publicacao";

        $stmt = $con->prepare($sql);

        $stmt->execute([
            ":id_usuario" => $idUsuario,
            ":id_publicacao" => $idPublicacao
        ]);

        return "descurtida";
    }

    $sql = "INSERT INTO curtidas
            (id_usuario, id_publicacao)
            VALUES
            (:id_usuario, :id_publicacao)";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":id_usuario" => $idUsuario,
        ":id_publicacao" => $idPublicacao
    ]);

    return "curtida";
}