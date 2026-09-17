<?php

namespace Controllers;

use Models\Bolo;
use Exception;

class BoloController
{
    private Bolo $bolo;

    public function __construct(Bolo $bolo)
    {
        $this->bolo = $bolo;
    }

    public function processar(string $metodo, ?string $id): void
    {
        header("Content-Type: application/json; charset=UTF-8");

        if ($id !== null) {
            match ($metodo) {
                "GET" => $this->mostrar((int) $id),
                "PATCH" => $this->atualizar((int) $id),
                "DELETE" => $this->remover((int) $id),
                default => $this->metodoInvalido(["GET", "PATCH", "DELETE"])
            };

            return;
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->cadastrar(),
            default => $this->metodoInvalido(["GET", "POST"])
        };
    }

    private function cadastrar(): void
    {
        $dados = $this->lerCorpoRequisicao();
        $erros = $this->validarDados($dados);

        if (!empty($erros)) {
            http_response_code(422);
            echo json_encode(["erros" => $erros]);
            return;
        }

        try {
            if ($this->bolo->existeComNome($dados["nome"])) {
                http_response_code(409);
                echo json_encode(["erros" => ["Já existe um bolo cadastrado com esse nome."]]);
                return;
            }

            $novoId = $this->bolo->cadastrar($dados["nome"], $dados["tipo"], $dados["descricao"] ?? null, (float) $dados["preco"]);
            $boloCriado = $this->bolo->buscarPorId($novoId);

            http_response_code(201);
            echo json_encode($boloCriado);

        } catch (Exception $erro) {
            http_response_code(500);
            echo json_encode(["erro" => $erro->getMessage()]);
        }
    }

    private function listar(): void
    {
        try {
            $bolos = $this->bolo->buscarTodos();

            http_response_code(200);
            echo json_encode($bolos);

        } catch (Exception $erro) {
            http_response_code(500);
            echo json_encode(["erro" => $erro->getMessage()]);
        }
    }

    private function mostrar(int $id): void
    {
        try {
            $bolo = $this->bolo->buscarPorId($id);

            if ($bolo === null) {
                http_response_code(404);
                echo json_encode(["erro" => "Bolo não encontrado."]);
                return;
            }

            http_response_code(200);
            echo json_encode($bolo);

        } catch (Exception $erro) {
            http_response_code(500);
            echo json_encode(["erro" => $erro->getMessage()]);
        }
    }

    private function atualizar(int $id): void
    {
        try {
            $boloAtual = $this->bolo->buscarPorId($id);

            if ($boloAtual === null) {
                http_response_code(404);
                echo json_encode(["erro" => "Bolo não encontrado."]);
                return;
            }

            $dados = $this->lerCorpoRequisicao();

            $nome = $dados["nome"] ?? $boloAtual["nome"];
            $tipo = $dados["tipo"] ?? $boloAtual["tipo"];
            $descricao = $dados["descricao"] ?? $boloAtual["descricao"];
            $preco = $dados["preco"] ?? $boloAtual["preco"];

            $erros = $this->validarDados(["nome" => $nome, "tipo" => $tipo, "preco" => $preco]);

            if (!empty($erros)) {
                http_response_code(422);
                echo json_encode(["erros" => $erros]);
                return;
            }

            if ($nome !== $boloAtual["nome"] && $this->bolo->existeComNome($nome, $id)) {
                http_response_code(409);
                echo json_encode(["erros" => ["Já existe um bolo cadastrado com esse nome."]]);
                return;
            }

            $this->bolo->atualizar($id, $nome, $tipo, $descricao, (float) $preco);
            $boloAtualizado = $this->bolo->buscarPorId($id);

            http_response_code(200);
            echo json_encode($boloAtualizado);

        } catch (Exception $erro) {
            http_response_code(500);
            echo json_encode(["erro" => $erro->getMessage()]);
        }
    }

    private function remover(int $id): void
    {
        try {
            $bolo = $this->bolo->buscarPorId($id);

            if ($bolo === null) {
                http_response_code(404);
                echo json_encode(["erro" => "Bolo não encontrado."]);
                return;
            }

            $this->bolo->remover($id);

            http_response_code(204);

        } catch (Exception $erro) {
            http_response_code(500);
            echo json_encode(["erro" => $erro->getMessage()]);
        }
    }

    private function lerCorpoRequisicao(): array
    {
        $conteudo = file_get_contents("php://input");
        $dados = json_decode($conteudo, true);

        return is_array($dados) ? $dados : [];
    }

    private function validarDados(array $dados): array
    {
        $erros = [];

        if (empty($dados["nome"])) {
            $erros[] = "Informe o nome do bolo.";
        }

        if (empty($dados["tipo"])) {
            $erros[] = "Informe o tipo do bolo.";
        }

        if (!isset($dados["preco"]) || !is_numeric($dados["preco"]) || $dados["preco"] <= 0) {
            $erros[] = "Informe um preço válido, maior que zero.";
        }

        return $erros;
    }

    private function metodoInvalido(array $permitidos): void
    {
        header("Allow: " . implode(", ", $permitidos));
        http_response_code(405);
        echo json_encode(["erro" => "Método não permitido."]);
    }
}
