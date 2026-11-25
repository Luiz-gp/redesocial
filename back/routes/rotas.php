<?php

require_once __DIR__ . "/../controllers/UsuarioController.php";
require_once __DIR__ . "/../controllers/PublicacaoController.php";
require_once __DIR__ . "/../controllers/CurtidaController.php";

$rota = $_GET["rota"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

switch ($rota) {

    // USUÁRIO
    case "usuario":

        if ($metodo === "POST") {
            createUsuario($con);
        } else {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido."]);
        }

        break;

    // LOGIN
    case "login":

        if ($metodo === "POST") {
            loginUsuario($con);
        } else {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido."]);
        }

        break;

    // PUBLICAÇÕES
    case "publicacao":

        if ($metodo === "GET") {
            readPublicacao($con);
        } elseif ($metodo === "POST") {
            createPublicacao($con);
        } elseif ($metodo === "PUT") {
            updatePublicacao($con);
        } elseif ($metodo === "DELETE") {
            deletePublicacao($con);
        } else {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido."]);
        }

        break;

    // CURTIDAS
    case "curtida":

        if ($metodo === "GET") {
            readCurtida($con);
        } elseif ($metodo === "POST") {
            toggleCurtida($con);
        } else {
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido."]);
        }

        break;

    default:

        http_response_code(404);

        echo json_encode([
            "erro" => "Rota não encontrada."
        ]);

        break;
}