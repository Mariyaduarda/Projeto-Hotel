<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../model/Pessoa.php';

class Funcionario {
    private $conn;
    private string $table_name = "funcionario";

    private int     $id_pessoa;
    private ?string $cargo = null;
    private ?float  $salario = null;
    private ?string $data_contratacao = null;
    private ?int    $numero_ctps = null;
    private ?string $turno = null;

    private const CARGOS_VALIDOS = ['Recepcionista', 'Gerente', 'Camareira', 'Seguranca', 'Manutencao', 'Chef', 'Garcom'];
    private const TURNOS_VALIDOS = ['Manha', 'Tarde', 'Noite', 'Integral'];

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setIdPessoa(int $id): void { $this->id_pessoa = $id; }
    public function getIdPessoa(): int { return $this->id_pessoa; }

    public function setCargo(?string $cargo): void { $this->cargo = $cargo; }
    public function getCargo(): ?string { return $this->cargo; }

    public function setSalario(?float $salario): void { $this->salario = $salario; }
    public function getSalario(): ?float { return $this->salario; }

    public function setDataContratacao(?string $data): void { $this->data_contratacao = $data; }
    public function getDataContratacao(): ?string { return $this->data_contratacao; }

    public function setNumeroCtps(?int $ctps): void { $this->numero_ctps = $ctps; }
    public function getNumeroCtps(): ?int { return $this->numero_ctps; }

    public function setTurno(?string $turno): void { $this->turno = $turno; }
    public function getTurno(): ?string { return $this->turno; }

    // CREATE
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_pessoa, cargo, salario, data_contratacao, numero_ctps, turno) 
                  VALUES (:id_pessoa, :cargo, :salario, :data_contratacao, :numero_ctps, :turno)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_pessoa", $this->id_pessoa, \PDO::PARAM_INT);
        $stmt->bindParam(":cargo", $this->cargo);
        $stmt->bindParam(":salario", $this->salario);
        $stmt->bindParam(":data_contratacao", $this->data_contratacao);
        $stmt->bindParam(":numero_ctps", $this->numero_ctps, \PDO::PARAM_INT);
        $stmt->bindParam(":turno", $this->turno);

        return $stmt->execute();
    }

    // READ ALL - CORRIGIDO
    public function read(): \PDOStatement {
        $query = "SELECT 
                    f.id_pessoa AS id,
                    p.nome,
                    p.documento AS cpf,
                    p.email,
                    p.telefone,
                    p.sexo,
                    p.data_nascimento,
                    e.cidade,
                    e.estado,
                    e.logradouro,
                    e.numero,
                    e.bairro,
                    e.cep,
                    f.cargo,
                    f.salario,
                    f.turno,
                    f.data_contratacao,
                    f.numero_ctps,
                    CURRENT_TIMESTAMP as data_criacao
                  FROM funcionario f
                  INNER JOIN pessoa p ON f.id_pessoa = p.id_pessoa
                  LEFT JOIN endereco e ON p.endereco_id_endereco = e.id_endereco
                  ORDER BY p.nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE
    public function readOne(): bool {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pessoa = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_pessoa, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row){
            $this->cargo = $row['cargo'];
            $this->salario = $row['salario'] ? (float)$row['salario'] : null;
            $this->data_contratacao = $row['data_contratacao'];
            $this->numero_ctps = $row['numero_ctps'] ? (int)$row['numero_ctps'] : null;
            $this->turno = $row['turno'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET cargo = :cargo,
                      salario = :salario,
                      data_contratacao = :data_contratacao,
                      numero_ctps = :numero_ctps,
                      turno = :turno
                  WHERE id_pessoa = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cargo", $this->cargo);
        $stmt->bindParam(":salario", $this->salario);
        $stmt->bindParam(":data_contratacao", $this->data_contratacao);
        $stmt->bindParam(":numero_ctps", $this->numero_ctps, \PDO::PARAM_INT);
        $stmt->bindParam(":turno", $this->turno);
        $stmt->bindParam(":id", $this->id_pessoa, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE
    public function delete(): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pessoa = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_pessoa, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toArray(): array {
        return [
            'id_pessoa' => $this->id_pessoa,
            'cargo' => $this->cargo,
            'salario' => $this->salario,
            'data_contratacao' => $this->data_contratacao,
            'numero_ctps' => $this->numero_ctps,
            'turno' => $this->turno
        ];
    }

    public function validar(): array {
        $erros = [];

        if ($this->cargo && !in_array($this->cargo, self::CARGOS_VALIDOS)) {
            $erros[] = "Cargo invalido. Valores permitidos: " . implode(', ', self::CARGOS_VALIDOS);
        }

        if ($this->salario && $this->salario < 0) {
            $erros[] = "Salario nao pode ser negativo.";
        }

        if ($this->turno && !in_array($this->turno, self::TURNOS_VALIDOS)) {
            $erros[] = "Turno invalido. Valores permitidos: " . implode(', ', self::TURNOS_VALIDOS);
        }

        return $erros;
    }

    // Buscar funcionario com dados completos
    public function readComplete(): ?array {
        $query = "SELECT f.*, p.*, e.*
                  FROM " . $this->table_name . " f
                  INNER JOIN pessoa p ON f.id_pessoa = p.id_pessoa
                  LEFT JOIN endereco e ON p.endereco_id_endereco = e.id_endereco
                  WHERE f.id_pessoa = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_pessoa, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }

    public static function getCargosValidos(): array { return self::CARGOS_VALIDOS; }
    public static function getTurnosValidos(): array { return self::TURNOS_VALIDOS; }
}
?>