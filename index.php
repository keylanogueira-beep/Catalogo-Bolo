<?php
session_start();
require_once "vendor/autoload.php";

use Models\Bolo;
use Controllers\BoloController;

$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$partes = explode("/", $caminho);

$recurso = $partes[2] ?? null;
$id = $partes[3] ?? null;

header("Content-Type: application/json; charset=UTF-8");

if ($recurso !== "bolos") {
    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada."]);
    exit;
}

try {
    $bolo = new Bolo();
    $controller = new BoloController($bolo);

    $controller->processar($_SERVER['REQUEST_METHOD'], $id);

} catch (\Throwable $erro) {
    error_log($erro->getMessage());
    http_response_code(500);
    echo json_encode(["erro" => "Erro interno do servidor."]);
}
