<?php

namespace Models;

use Exception;
use Models\ConexaoBanco;
use PDO;
use PDOException;

class Bolo
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = ConexaoBanco::obterConexao();
    }

    public function cadastrar(string $nome, string $tipo, ?string $descricao, float $preco): int
    {
        try {
            $sql = "INSERT INTO bolos (nome, tipo, descricao, preco, created_at) VALUES (:nome, :tipo, :descricao, :preco, NOW())";

            $comando = $this->conexao->prepare($sql);

            $comando->bindParam(":nome", $nome, PDO::PARAM_STR);
            $comando->bindParam(":tipo", $tipo, PDO::PARAM_STR);
            $comando->bindParam(":descricao", $descricao, PDO::PARAM_STR);
            $comando->bindParam(":preco", $preco);

            $comando->execute();

            return (int) $this->conexao->lastInsertId();

        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            throw new Exception("Não foi possível cadastrar o bolo");
        }
    }

    public function buscarPorId(int $id): ?array
    {
        try {
            $sql = "SELECT id, nome, tipo, descricao, preco, created_at FROM bolos WHERE id = :id";

            $comando = $this->conexao->prepare($sql);
            $comando->bindValue(":id", $id, PDO::PARAM_INT);
            $comando->execute();

            $resultado = $comando->fetch(PDO::FETCH_ASSOC);

            return $resultado ?: null;

        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            throw new Exception("Não foi possível buscar o bolo");
        }
    }

    public function buscarTodos(): array
    {
        try {
            $sql = "SELECT id, nome, tipo, descricao, preco, created_at FROM bolos ORDER BY id";

            $comando = $this->conexao->query($sql);

            return $comando->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            throw new Exception("Não foi possível listar os bolos");
        }
    }

    public function existeComNome(string $nome, ?int $idParaIgnorar = null): bool
    {
        try {
            $sql = "SELECT id FROM bolos WHERE nome = :nome";

            if ($idParaIgnorar !== null) {
                $sql .= " AND id != :idParaIgnorar";
            }

            $comando = $this->conexao->prepare($sql);
            $comando->bindValue(":nome", $nome, PDO::PARAM_STR);

            if ($idParaIgnorar !== null) {
                $comando->bindValue(":idParaIgnorar", $idParaIgnorar, PDO::PARAM_INT);
            }

            $comando->execute();

            return $comando->fetch() !== false;

        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            throw new Exception("Não foi possível verificar o nome do bolo");
        }
    }

    public function atualizar(int $id, string $nome, string $tipo, ?string $descricao, float $preco): bool
    {
        try {
            $sql = "UPDATE bolos SET nome = :nome, tipo = :tipo, descricao = :descricao, preco = :preco WHERE id = :id";

            $comando = $this->conexao->prepare($sql);
            $comando->bindValue(":id", $id, PDO::PARAM_INT);
            $comando->bindParam(":nome", $nome, PDO::PARAM_STR);
            $comando->bindParam(":tipo", $tipo, PDO::PARAM_STR);
            $comando->bindParam(":descricao", $descricao, PDO::PARAM_STR);
            $comando->bindParam(":preco", $preco);

            return $comando->execute();

        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            throw new Exception("Não foi possível atualizar o bolo");
        }
    }

    public function remover(int $id): bool
    {
        try {
            $sql = "DELETE FROM bolos WHERE id = :id";

            $comando = $this->conexao->prepare($sql);
            $comando->bindValue(":id", $id, PDO::PARAM_INT);
            $comando->execute();

            return $comando->rowCount() > 0;

        } catch (PDOException $erro) {
            error_log($erro->getMessage());
            throw new Exception("Não foi possível remover o bolo");
        }
    }
}
