<?php

namespace App\Model;

use App\Config\Database;
use PDO;

class BoloModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM bolos ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM bolos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $bolo = $stmt->fetch();
        return $bolo ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO bolos (nome, sabor, preco, descricao) VALUES (:nome, :sabor, :preco, :descricao)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome' => $data['nome'],
            'sabor' => $data['sabor'],
            'preco' => $data['preco'],
            'descricao' => $data['descricao'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE bolos SET nome = :nome, sabor = :sabor, preco = :preco, descricao = :descricao WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nome' => $data['nome'],
            'sabor' => $data['sabor'],
            'preco' => $data['preco'],
            'descricao' => $data['descricao'] ?? null
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bolos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}