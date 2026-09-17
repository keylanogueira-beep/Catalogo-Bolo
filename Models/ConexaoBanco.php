<?php

namespace Models;

require_once __DIR__ . "/../Config/configuracao.php";

use PDO;
use PDOException;

class ConexaoBanco
{
    private static $conexao;

    public static function obterConexao(): PDO
    {
        if (empty(self::$conexao)) {
            try {
                self::$conexao = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL
                ]);
            } catch (PDOException $erro) {
                error_log($erro->getMessage());
                http_response_code(500);
                die(json_encode($erro->getMessage()));
            }
        }

        return self::$conexao;
    }
}
