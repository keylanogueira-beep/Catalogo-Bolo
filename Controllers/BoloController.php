<?php

namespace App\Controller;

use App\Model\BoloModel;

class BoloController
{
    private BoloModel $model;

    public function __construct()
    {
        $this->model = new BoloModel();
    }

    public function index(): void
    {
        $bolos = $this->model->getAll();
        http_response_code(200);
        echo json_encode(["status" => true, "data" => $bolos]);
    }

    public function show(int $id): void
    {
        $bolo = $this->model->getById($id);
        if ($bolo) {
            http_response_code(200);
            echo json_encode(["status" => true, "data" => $bolo]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => false, "mensagem" => "Bolo não encontrado"]);
        }
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['nome']) || empty($data['sabor']) || !isset($data['preco'])) {
            http_response_code(400);
            echo json_encode(["status" => false, "mensagem" => "Campos obrigatórios: nome, sabor, preco"]);
            return;
        }

        $id = $this->model->create($data);
        http_response_code(201);
        echo json_encode(["status" => true, "mensagem" => "Bolo cadastrado com sucesso", "id" => $id]);
    }

    public function update(int $id): void
    {
        if (!$this->model->getById($id)) {
            http_response_code(404);
            echo json_encode(["status" => false, "mensagem" => "Bolo não encontrado"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['nome']) || empty($data['sabor']) || !isset($data['preco'])) {
            http_response_code(400);
            echo json_encode(["status" => false, "mensagem" => "Campos obrigatórios: nome, sabor, preco"]);
            return;
        }

        $this->model->update($id, $data);
        http_response_code(200);
        echo json_encode(["status" => true, "mensagem" => "Bolo atualizado com sucesso"]);
    }

    public function destroy(int $id): void
    {
        if (!$this->model->getById($id)) {
            http_response_code(404);
            echo json_encode(["status" => false, "mensagem" => "Bolo não encontrado"]);
            return;
        }

        $this->model->delete($id);
        http_response_code(200);
        echo json_encode(["status" => true, "mensagem" => "Bolo removido com sucesso"]);
    }
}