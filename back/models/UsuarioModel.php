<?php

function usuarioExiste($con, $username, $email) {

    $sql = "SELECT id_usuario
            FROM usuarios
            WHERE username = :username OR email = :email";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":username" => $username,
        ":email" => $email
    ]);

    return $stmt->fetch() !== false;
}


function inserirUsuario($con, $nome, $username, $email, $senha) {

    $sql = "INSERT INTO usuarios
            (nome, username, email, senha)
            VALUES
            (:nome, :username, :email, :senha)";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":nome" => $nome,
        ":username" => $username,
        ":email" => $email,
        ":senha" => $senha
    ]);
}


function buscarUsuarioLogin($con, $identificador) {

    $sql = "SELECT *
            FROM usuarios
            WHERE username = :identificador
            OR email = :identificador
            LIMIT 1";

    $stmt = $con->prepare($sql);

    $stmt->execute([
        ":identificador" => $identificador
    ]);

    return $stmt->fetch();
}