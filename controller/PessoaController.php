<?php

namespace Controller;

require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../database/Database.php';

class PessoaController {
    private $db;
    private $pessoa;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pessoa = new Pessoa($this->db);
    }

    public function criar(array $dados): array {
        try {
            $this->pessoa->setNome($dados['nome']);
            $this->pessoa->setSexo($dados['sexo'] ?? null);
            $this->pessoa->setDataNascimento($dados['data_nascimento'] ?? null);
            $this->pessoa->setDocumento($dados['documento'] ?? null);
            $this->pessoa->setTelefone($dados['telefone'] ?? null);
            $this->pessoa->setEmail($dados['email'] ?? null);
            $this->pessoa->setTipoPessoa($dados['tipo_pessoa']);
            $this->pessoa->setEnderecoId((int)$dados['endereco_id']);

            $erros = $this->pessoa->validar();
            if (!empty($erros)) {
                return ['sucesso' => false, 'erros' => $erros];
            }

            if ($this->pessoa->create()) {
                return [
                    'sucesso' => true, 
                    'mensagem' => 'Pessoa criada com sucesso!',
                    'id' => $this->pessoa->getId()
                ];
            }

            return ['sucesso' => false, 'erros' => ['Erro ao criar pessoa.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function lista(): array {
        try {
            $stmt = $this->pessoa->read();
            $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['sucesso' => true, 'dados' => $pessoas];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function buscarPorId(int $id): array {
        try {
            $this->pessoa->setId($id);
            if ($this->pessoa->readOne()) {
                return ['sucesso' => true, 'dados' => $this->pessoa->toArray()];
            }
            return ['sucesso' => false, 'erros' => ['Pessoa nao encontrada.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function atualizar(int $id, array $dados): array {
        try {
            $this->pessoa->setId($id);
            if (!$this->pessoa->readOne()) {
                return ['sucesso' => false, 'erros' => ['Pessoa nao encontrada.']];
            }

            $this->pessoa->setNome($dados['nome']);
            $this->pessoa->setSexo($dados['sexo'] ?? null);
            $this->pessoa->setDataNascimento($dados['data_nascimento'] ?? null);
            $this->pessoa->setDocumento($dados['documento'] ?? null);
            $this->pessoa->setTelefone($dados['telefone'] ?? null);
            $this->pessoa->setEmail($dados['email'] ?? null);
            $this->pessoa->setTipoPessoa($dados['tipo_pessoa']);
            $this->pessoa->setEnderecoId((int)$dados['endereco_id']);

            $erros = $this->pessoa->validar();
            if (!empty($erros)) {
                return ['sucesso' => false, 'erros' => $erros];
            }

            if ($this->pessoa->update()) {
                return ['sucesso' => true, 'mensagem' => 'Pessoa atualizada!'];
            }

            return ['sucesso' => false, 'erros' => ['Erro ao atualizar pessoa.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function deletar(int $id): array {
        try {
            $this->pessoa->setId($id);
            if (!$this->pessoa->readOne()) {
                return ['sucesso' => false, 'erros' => ['Pessoa nao encontrada.']];
            }

            if ($this->pessoa->delete()) {
                return ['sucesso' => true, 'mensagem' => 'Pessoa excluída!'];
            }

            return ['sucesso' => false, 'erros' => ['Erro ao excluir pessoa.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }
}
?>