<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controller\BoloController;

// Carregar variáveis de ambiente
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Configurações de cabeçalho para API REST / CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obter a rota atual
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriSegments = explode('/', trim($uri, '/'));

// Pega o ID caso informado na URI (ex: /bolos/1)
$id = isset($uriSegments[1]) && is_numeric($uriSegments[1]) ? (int)$uriSegments[1] : null;

$controller = new BoloController();

// Roteamento RESTful
if (isset($uriSegments[0]) && $uriSegments[0] === 'bolos') {
    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->show($id);
            } else {
                $controller->index();
            }
            break;

        case 'POST':
            $controller->store();
            break;

        case 'PUT':
            if ($id) {
                $controller->update($id);
            } else {
                http_response_code(400);
                echo json_encode(["status" => false, "mensagem" => "ID é necessário para atualizar"]);
            }
            break;

        case 'DELETE':
            if ($id) {
                $controller->destroy($id);
            } else {
                http_response_code(400);
                echo json_encode(["status" => false, "mensagem" => "ID é necessário para deletar"]);
            }
            break;

        default:
            http_response_code(455);
            echo json_encode(["status" => false, "mensagem" => "Método não permitido"]);
            break;
    }
} else {
    
    http_response_code(404);
    echo json_encode(["status" => false, "mensagem" => "Endpoint não encontrado. Acesse /bolos"]);
}
