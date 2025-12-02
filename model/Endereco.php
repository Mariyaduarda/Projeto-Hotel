<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../utils/Validacoes.php';
require_once __DIR__ . '/../utils/Formatter.php';

class Endereco {
    private $conn;
    private string $table_name = "endereco";

    private ?int    $id_endereco = null;
    private ?string $logradouro = null;
    private ?int    $numero = null;
    private ?string $bairro = null;
    private ?string $cidade = null;
    private ?string $estado = null;
    private ?string $pais = null;
    private ?string $cep = null;

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setId(int $id): void { $this->id_endereco = $id; }
    public function getId(): ?int { return $this->id_endereco; }

    public function setLogradouro(?string $logradouro): void { $this->logradouro = $logradouro; }
    public function getLogradouro(): ?string { return $this->logradouro; }

    public function setNumero(?int $numero): void { $this->numero = $numero; }
    public function getNumero(): ?int { return $this->numero; }

    public function setBairro(?string $bairro): void { $this->bairro = $bairro; }
    public function getBairro(): ?string { return $this->bairro; }

    public function setCidade(?string $cidade): void { $this->cidade = $cidade; }
    public function getCidade(): ?string { return $this->cidade; }

    public function setEstado(?string $estado): void { $this->estado = $estado; }
    public function getEstado(): ?string { return $this->estado; }

    public function setPais(?string $pais): void { $this->pais = $pais; }
    public function getPais(): ?string { return $this->pais; }

    public function setCep(?string $cep): void { $this->cep = $cep; }
    public function getCep(): ?string { return $this->cep; }

    // CREATE
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . " 
                  (logradouro, numero, bairro, cidade, estado, pais, cep) 
                  VALUES (:logradouro, :numero, :bairro, :cidade, :estado, :pais, :cep)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":logradouro", $this->logradouro);
        $stmt->bindParam(":numero", $this->numero, \PDO::PARAM_INT);
        $stmt->bindParam(":bairro", $this->bairro);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":pais", $this->pais);
        $stmt->bindParam(":cep", $this->cep);

        if ($stmt->execute()) {
            $this->id_endereco = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // READ ALL
    public function read(): \PDOStatement {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY cidade ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE
    public function readOne(): bool {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_endereco = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_endereco, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row){
            $this->logradouro = $row['logradouro'];
            $this->numero = $row['numero'] ? (int)$row['numero'] : null;
            $this->bairro = $row['bairro'];
            $this->cidade = $row['cidade'];
            $this->estado = $row['estado'];
            $this->pais = $row['pais'];
            $this->cep = $row['cep'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET logradouro = :logradouro,
                      numero = :numero,
                      bairro = :bairro,
                      cidade = :cidade,
                      estado = :estado,
                      pais = :pais,
                      cep = :cep
                  WHERE id_endereco = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":logradouro", $this->logradouro);
        $stmt->bindParam(":numero", $this->numero, \PDO::PARAM_INT);
        $stmt->bindParam(":bairro", $this->bairro);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":pais", $this->pais);
        $stmt->bindParam(":cep", $this->cep);
        $stmt->bindParam(":id", $this->id_endereco, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE
    public function delete(): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_endereco = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_endereco, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toArray(): array {
        return [
            'id_endereco' => $this->id_endereco,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'pais' => $this->pais,
            'cep' => $this->cep
        ];
    }

    public function validar(): array {
        $erros = [];

        if (empty($this->cidade)) {
            $erros[] = "Cidade é obrigatória.";
        }

        if (empty($this->estado)) {
            $erros[] = "Estado é obrigatório.";
        }

        if ($this->cep && !preg_match('/^\d{5}-?\d{3}$/', $this->cep)) {
            $erros[] = "CEP invalido.";
        }

        return $erros;
    }
}
?>