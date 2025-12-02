<?php

namespace Controller;

require_once __DIR__ . '/../model/Funcionario.php';
require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../model/Endereco.php';
require_once __DIR__ . '/../database/Database.php';

use PDO;
use Exception;
use database\Database;
use model\Funcionario;
use model\Pessoa;
use model\Endereco;

class FuncionarioController {
    private $db;
    private $funcionario;
    private $pessoa;
    private $endereco;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->funcionario = new Funcionario($this->db);
        $this->pessoa = new Pessoa($this->db);
        $this->endereco = new Endereco($this->db);
    }

    public function criar(array $dados): array {
        try {
            $this->db->beginTransaction();

            // 1. Criar endereco primeiro
            $this->endereco->setLogradouro($dados['endereco'] ?? null);
            $this->endereco->setNumero($dados['numero'] ?? null);
            $this->endereco->setBairro($dados['bairro'] ?? null);
            $this->endereco->setCidade($dados['cidade'] ?? null);
            $this->endereco->setEstado($dados['estado'] ?? null);
            $this->endereco->setPais($dados['pais'] ?? 'Brasil');
            $this->endereco->setCep($dados['cep'] ?? null);

            if (!$this->endereco->create()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao criar endereco.']];
            }

            // 2. Criar pessoa com o ID do endereco
            $this->pessoa->setNome($dados['nome']);
            $this->pessoa->setSexo($dados['sexo'] ?? null);
            $this->pessoa->setDataNascimento($dados['data_nascimento'] ?? null);
            $this->pessoa->setDocumento($dados['cpf'] ?? null);
            $this->pessoa->setTelefone($dados['telefone'] ?? null);
            $this->pessoa->setEmail($dados['email'] ?? null);
            $this->pessoa->setTipoPessoa('funcionario');
            $this->pessoa->setEnderecoId($this->endereco->getId());

            // Validar pessoa DEPOIS de setar o endereco_id
            $errosPessoa = $this->pessoa->validar();
            if (!empty($errosPessoa)) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => $errosPessoa];
            }

            if (!$this->pessoa->create()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao criar pessoa.']];
            }

            // 3. Criar funcionario
            $this->funcionario->setIdPessoa($this->pessoa->getId());
            $this->funcionario->setCargo($dados['cargo'] ?? null);
            $this->funcionario->setSalario(
                isset($dados['salario']) && $dados['salario'] !== '' 
                    ? (float)$dados['salario'] 
                    : null
            );
            $this->funcionario->setDataContratacao($dados['data_contratacao'] ?? date('Y-m-d'));
            $this->funcionario->setNumeroCtps(
                isset($dados['numero_ctps']) && $dados['numero_ctps'] !== '' 
                    ? (int)$dados['numero_ctps'] 
                    : null
            );
            $this->funcionario->setTurno($dados['turno'] ?? null);

            // Validar funcionario
            $errosFuncionario = $this->funcionario->validar();
            if (!empty($errosFuncionario)) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => $errosFuncionario];
            }

            if (!$this->funcionario->create()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao criar funcionario.']];
            }

            $this->db->commit();

            return [
                'sucesso' => true,
                'mensagem' => 'Funcionario criado com sucesso!',
                'id' => $this->pessoa->getId()
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function lista(): array {
        try {
            $stmt = $this->funcionario->read();
            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['sucesso' => true, 'dados' => $funcionarios];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro ao lista: ' . $e->getMessage()]];
        }
    }

    public function buscarPorId(int $id): array {
        try {
            $this->funcionario->setIdPessoa($id);
            $dados = $this->funcionario->readComplete();
            
            if ($dados) {
                return ['sucesso' => true, 'dados' => $dados];
            }
            return ['sucesso' => false, 'erros' => ['Funcionario nao encontrado.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function atualizar(int $id, array $dados): array {
        try {
            $this->db->beginTransaction();

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

            $this->funcionario->setIdPessoa($id);
            $this->funcionario->setCargo($dados['cargo'] ?? null);
            $this->funcionario->setSalario($dados['salario'] ?? null);
            $this->funcionario->setDataContratacao($dados['data_contratacao']);
            $this->funcionario->setNumeroCtps($dados['numero_ctps'] ?? null);
            $this->funcionario->setTurno($dados['turno'] ?? null);

            if (!$this->funcionario->update()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao atualizar funcionario.']];
            }

            $this->db->commit();
            return ['sucesso' => true, 'mensagem' => 'Funcionario atualizado!'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }

    public function deletar(int $id): array {
        try {
            $this->db->beginTransaction();

            $this->funcionario->setIdPessoa($id);
            if (!$this->funcionario->delete()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao excluir funcionario.']];
            }

            $this->pessoa->setId($id);
            if (!$this->pessoa->delete()) {
                $this->db->rollBack();
                return ['sucesso' => false, 'erros' => ['Erro ao excluir pessoa.']];
            }

            $this->db->commit();
            return ['sucesso' => true, 'mensagem' => 'Funcionario excluído!'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['sucesso' => false, 'erros' => ['Erro: ' . $e->getMessage()]];
        }
    }
}
?>