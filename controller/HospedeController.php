<?php

namespace Controller;

require_once __DIR__ . '/../model/Hospede.php';
require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../model/Endereco.php';
require_once __DIR__ . '/../database/Database.php';

use PDO;
use Exception;
use database\Database;
use model\Hospede;  
use model\Pessoa;
use model\Endereco;
$db = new Database();

class HospedeController {
    private $db;
    private $hospede;
    private $pessoa;
    private $endereco;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->hospede = new Hospede($this->db);
        $this->pessoa = new Pessoa($this->db);
        $this->endereco = new Endereco($this->db);
    }

    public function criar(array $dados): array {
        try {
            $this->db->beginTransaction();

            // Criar endereco
            $this->endereco->setLogradouro($dados['endereco'] ?? null);
            $this->endereco->setNumero(isset($dados['numero']) ? (int)$dados['numero'] : null);
            $this->endereco->setCidade($dados['cidade']);
            $this->endereco->setEstado($dados['estado']);
            $this->endereco->setPais($dados['pais'] ?? 'Brasil');
            $this->endereco->setCep($dados['cep'] ?? null);

            if (!$this->endereco->create()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao criar endereco.']];
            }

            // Criar pessoa
            $this->pessoa->setNome($dados['nome']);
            $this->pessoa->setSexo($dados['sexo'] ?? null);
            $this->pessoa->setDataNascimento($dados['data_nascimento'] ?? null);
            $this->pessoa->setDocumento($dados['cpf'] ?? null);
            $this->pessoa->setTelefone($dados['telefone'] ?? null);
            $this->pessoa->setEmail($dados['email'] ?? null);
            $this->pessoa->setTipoPessoa('hospede');
            $this->pessoa->setEnderecoId($this->endereco->getId());

            if (!$this->pessoa->create()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao criar pessoa.']];
            }

            // criar hospede incluindo obs no historico
            $this->hospede->setIdPessoa($this->pessoa->getId());
            $this->hospede->setPreferencias($dados['preferencias'] ?? null);
            $this->hospede->setHistorico($dados['observacoes'] ?? null);

            if (!$this->hospede->create()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao criar hóspede.']];
            }

            $this->db->commit();
            return [
                'sucesso' => true, 
                'mensagem' => 'Hóspede criado com sucesso!',
                'id' => $this->pessoa->getId()
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function listar(): array {
        try {
            $stmt = $this->hospede->read();
            $hospedes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['sucesso' => true, 'dados' => $hospedes];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function buscarPorId(int $id): array {
        try {
            $this->hospede->setIdPessoa($id);
            $dados = $this->hospede->readComplete();
            
            if ($dados) {
                return ['sucesso' => true, 'dados' => $dados];
            }
            return ['sucesso' => false, 'erros' => ['Hóspede nao encontrado.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function atualizar(int $id, array $dados): array {
        try {
            $this->db->beginTransaction();

            // Atualizar pessoa
            $this->pessoa->setId($id);
            if (!$this->pessoa->readOne()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Pessoa nao encontrada.']];
            }

            $this->pessoa->setNome($dados['nome']);
            $this->pessoa->setSexo($dados['sexo'] ?? null);
            $this->pessoa->setDataNascimento($dados['data_nascimento'] ?? null);
            $this->pessoa->setDocumento($dados['documento'] ?? null);
            $this->pessoa->setTelefone($dados['telefone'] ?? null);
            $this->pessoa->setEmail($dados['email'] ?? null);

            if (!$this->pessoa->update()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao atualizar pessoa.']];
            }

            // Atualizar hóspede
            $this->hospede->setIdPessoa($id);
            $this->hospede->setPreferencias($dados['preferencias'] ?? null);
            $this->hospede->setHistorico($dados['historico'] ?? null);

            if (!$this->hospede->update()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao atualizar hóspede.']];
            }

            $this->db->commit();
            return ['sucesso' => true, 'mensagem' => 'Hóspede atualizado!'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function deletar(int $id): array {
        try {
            $this->db->beginTransaction();

            $this->hospede->setIdPessoa($id);
            if (!$this->hospede->delete()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao excluir hóspede.']];
            }

            $this->pessoa->setId($id);
            if (!$this->pessoa->delete()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao excluir pessoa.']];
            }

            $this->db->commit();
            return ['sucesso' => true, 'mensagem' => 'Hóspede excluído!'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }
}
?>