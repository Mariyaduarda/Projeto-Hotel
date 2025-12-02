<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../utils/Validacoes.php';
require_once __DIR__ . '/../model/Endereco.php';

class Pessoa {
    private $conn;
    private string $table_name = "pessoa";

    private ?int    $id_pessoa = null;
    private ?string $nome = null;
    private ?string $sexo = null;
    private ?string $data_nascimento = null;
    private ?string $documento = null;
    private ?string $telefone = null;
    private ?string $email = null;
    private ?string $tipo_pessoa = null;
    private ?int     $endereco_id_endereco = null;

    private const SEXOS_VALIDOS = ['M', 'F', 'Outro'];
    private const TIPOS_VALIDOS = ['hospede', 'funcionario'];

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setId(int $id): void { $this->id_pessoa = $id; }
    public function getId(): ?int { return $this->id_pessoa; }

    public function setNome(?string $nome): void { $this->nome = $nome; }
    public function getNome(): ?string { return $this->nome; }

    public function setSexo(?string $sexo): void { $this->sexo = $sexo; }
    public function getSexo(): ?string { return $this->sexo; }

    public function setDataNascimento(?string $data): void { $this->data_nascimento = $data; }
    public function getDataNascimento(): ?string { return $this->data_nascimento; }

    public function setDocumento(?string $documento): void { $this->documento = $documento; }
    public function getDocumento(): ?string { return $this->documento; }

    public function setTelefone(?string $telefone): void { $this->telefone = $telefone; }
    public function getTelefone(): ?string { return $this->telefone; }

    public function setEmail(?string $email): void { $this->email = $email; }
    public function getEmail(): ?string { return $this->email; }

    public function setTipoPessoa(?string $tipo): void { $this->tipo_pessoa = $tipo; }
    public function getTipoPessoa(): ?string { return $this->tipo_pessoa; }

    public function setEnderecoId(int $endereco_id): void { $this->endereco_id_endereco = $endereco_id; }
    public function getEnderecoId(): int { return $this->endereco_id_endereco; }

    // CREATE
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, sexo, data_nascimento, documento, telefone, email, tipo_pessoa, endereco_id_endereco) 
                  VALUES (:nome, :sexo, :data_nascimento, :documento, :telefone, :email, :tipo_pessoa, :endereco_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":data_nascimento", $this->data_nascimento);
        $stmt->bindParam(":documento", $this->documento);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":tipo_pessoa", $this->tipo_pessoa);
        $stmt->bindParam(":endereco_id", $this->endereco_id_endereco, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id_pessoa = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // READ ALL
    public function read(): \PDOStatement {
        $query = "SELECT p.*, e.cidade, e.estado 
                  FROM " . $this->table_name . " p
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
            $this->nome = $row['nome'];
            $this->sexo = $row['sexo'];
            $this->data_nascimento = $row['data_nascimento'];
            $this->documento = $row['documento'];
            $this->telefone = $row['telefone'];
            $this->email = $row['email'];
            $this->tipo_pessoa = $row['tipo_pessoa'];
            $this->endereco_id_endereco = (int)$row['endereco_id_endereco'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET nome = :nome,
                      sexo = :sexo,
                      data_nascimento = :data_nascimento,
                      documento = :documento,
                      telefone = :telefone,
                      email = :email,
                      tipo_pessoa = :tipo_pessoa,
                      endereco_id_endereco = :endereco_id
                  WHERE id_pessoa = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":data_nascimento", $this->data_nascimento);
        $stmt->bindParam(":documento", $this->documento);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":tipo_pessoa", $this->tipo_pessoa);
        $stmt->bindParam(":endereco_id", $this->endereco_id_endereco, \PDO::PARAM_INT);
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
            'nome' => $this->nome,
            'sexo' => $this->sexo,
            'data_nascimento' => $this->data_nascimento,
            'documento' => $this->documento,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'tipo_pessoa' => $this->tipo_pessoa,
            'endereco_id_endereco' => $this->endereco_id_endereco
        ];
    }

    public function validar(): array {
        $erros = [];

        if (empty($this->nome)) {
            $erros[] = "Nome é obrigatório.";
        }

        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email invalido.";
        }

        if ($this->sexo && !in_array($this->sexo, self::SEXOS_VALIDOS)) {
            $erros[] = "Sexo invalido. Valores permitidos: " . implode(', ', self::SEXOS_VALIDOS);
        }

        if ($this->tipo_pessoa && !in_array($this->tipo_pessoa, self::TIPOS_VALIDOS)) {
            $erros[] = "Tipo de pessoa invalido.";
        }

        if (empty($this->endereco_id_endereco)) {
            $erros[] = "Endereco é obrigatório.";
        }

        return $erros;
    }

    public static function getSexosValidos(): array { return self::SEXOS_VALIDOS; }
    public static function getTiposValidos(): array { return self::TIPOS_VALIDOS; }
}
?>