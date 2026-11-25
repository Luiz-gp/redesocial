<?php

require_once __DIR__ . "/../models/UsuarioModel.php";

function createUsuario($con) {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!$dados) {
        http_response_code(400);
        echo json_encode(["erro" => "Dados inválidos."]);
        return;
    }

    $nome = trim($dados["nome"] ?? "");
    $username = trim($dados["username"] ?? "");
    $email = trim($dados["email"] ?? "");
    $senha = $dados["senha"] ?? "";

    if ($nome === "" || $username === "" || $email === "" || $senha === "") {
        http_response_code(400);
        echo json_encode(["erro" => "Preencha todos os campos."]);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["erro" => "E-mail inválido."]);
        return;
    }

    if (strlen($senha) < 6) {
        http_response_code(400);
        echo json_encode(["erro" => "A senha deve ter pelo menos 6 caracteres."]);
        return;
    }

    if (usuarioExiste($con, $username, $email)) {
        http_response_code(409);
        echo json_encode(["erro" => "Username ou e-mail já cadastrado."]);
        return;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $resultado = inserirUsuario(
        $con,
        $nome,
        $username,
        $email,
        $senhaHash
    );

    if ($resultado) {
        http_response_code(201);

        echo json_encode([
            "mensagem" => "Usuário cadastrado com sucesso!"
        ]);
    } else {
        http_response_code(500);

        echo json_encode([
            "erro" => "Não foi possível cadastrar o usuário."
        ]);
    }
}


function loginUsuario($con) {

    $dados = json_decode(file_get_contents("php://input"), true);

    $identificador = trim($dados["identificador"] ?? "");
    $senha = $dados["senha"] ?? "";

    if ($identificador === "" || $senha === "") {
        http_response_code(400);
        echo json_encode(["erro" => "Informe usuário e senha."]);
        return;
    }

    $usuario = buscarUsuarioLogin($con, $identificador);

    if (!$usuario || !password_verify($senha, $usuario["senha"])) {
        http_response_code(401);
        echo json_encode(["erro" => "Usuário ou senha incorretos."]);
        return;
    }

    unset($usuario["senha"]);

    echo json_encode([
        "mensagem" => "Login realizado!",
        "usuario" => $usuario
    ]);
}