<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../model/Pessoa.php';

class Hospede {
    private $conn;
    private string $table_name = "hospede";

    private int     $id_pessoa;
    private ?string $preferencias = null;
    private ?string $historico = null;

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setIdPessoa(int $id): void { $this->id_pessoa = $id; }
    public function getIdPessoa(): int { return $this->id_pessoa; }

    public function setPreferencias(?string $pref): void { $this->preferencias = $pref; }
    public function getPreferencias(): ?string { return $this->preferencias; }

    public function setHistorico(?string $hist): void { $this->historico = $hist; }
    public function getHistorico(): ?string { return $this->historico; }

    // CREATE
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_pessoa, preferencias, historico) 
                  VALUES (:id_pessoa, :preferencias, :historico)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_pessoa", $this->id_pessoa, \PDO::PARAM_INT);
        $stmt->bindParam(":preferencias", $this->preferencias);
        $stmt->bindParam(":historico", $this->historico);

        return $stmt->execute();
    }

    // READ ALL
    public function read(): \PDOStatement {
        $query = "SELECT h.*,
                    p.id_pessoa AS id, 
                    p.nome,
                    p.email, 
                    p.telefone, 
                    p.documento AS cpf,
                    p.data_criacao
                  FROM " . $this->table_name . " h
                  INNER JOIN pessoa p ON h.id_pessoa = p.id_pessoa
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
            $this->preferencias = $row['preferencias'];
            $this->historico = $row['historico'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET preferencias = :preferencias,
                      historico = :historico
                  WHERE id_pessoa = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":preferencias", $this->preferencias);
        $stmt->bindParam(":historico", $this->historico);
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
            'preferencias' => $this->preferencias,
            'historico' => $this->historico
        ];
    }

    // Buscar hóspede com dados completos (pessoa + endereco)
    public function readComplete(): ?array {
        $query = "SELECT h.*, p.*, e.*
                  FROM " . $this->table_name . " h
                  INNER JOIN pessoa p ON h.id_pessoa = p.id_pessoa
                  LEFT JOIN endereco e ON p.endereco_id_endereco = e.id_endereco
                  WHERE h.id_pessoa = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_pessoa, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }
}
?>