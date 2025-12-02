<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';

class Pagamento {
    private $conn;
    private string $table_name = "pagamento";

    private ?int    $id_pagamento = null;
    private ?string $data_pagamento = null;
    private ?float  $valor_total = null;
    private ?string $metodo_pagamento = null;
    private int     $reserva_idreserva;

    private const METODOS_VALIDOS = ['Dinheiro', 'Cartao de Crédito', 'Cartao de Débito', 'PIX', 'Transferência'];

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setId(int $id): void { $this->id_pagamento = $id; }
    public function getId(): ?int { return $this->id_pagamento; }

    public function setDataPagamento(?string $data): void { $this->data_pagamento = $data; }
    public function getDataPagamento(): ?string { return $this->data_pagamento; }

    public function setValorTotal(?float $valor): void { $this->valor_total = $valor; }
    public function getValorTotal(): ?float { return $this->valor_total; }

    public function setMetodoPagamento(?string $metodo): void { $this->metodo_pagamento = $metodo; }
    public function getMetodoPagamento(): ?string { return $this->metodo_pagamento; }

    public function setReservaId(int $reserva_id): void { $this->reserva_idreserva = $reserva_id; }
    public function getReservaId(): int { return $this->reserva_idreserva; }

    // CREATE
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . " 
                  (data_pagamento, valor_total, metodo_pagamento, reserva_idreserva) 
                  VALUES (:data_pagamento, :valor_total, :metodo_pagamento, :reserva_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":data_pagamento", $this->data_pagamento);
        $stmt->bindParam(":valor_total", $this->valor_total);
        $stmt->bindParam(":metodo_pagamento", $this->metodo_pagamento);
        $stmt->bindParam(":reserva_id", $this->reserva_idreserva, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id_pagamento = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // READ ALL
    public function read(): \PDOStatement {
        $query = "SELECT p.*, r.idreserva, h.id_pessoa, pe.nome as nome_hospede
                  FROM " . $this->table_name . " p
                  LEFT JOIN reserva r ON p.reserva_idreserva = r.idreserva
                  LEFT JOIN hospede h ON r.id_hospede = h.id_pessoa
                  LEFT JOIN pessoa pe ON h.id_pessoa = pe.id_pessoa
                  ORDER BY p.data_pagamento DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE
    public function readOne(): bool {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pagamento = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_pagamento, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row){
            $this->data_pagamento = $row['data_pagamento'];
            $this->valor_total = $row['valor_total'] ? (float)$row['valor_total'] : null;
            $this->metodo_pagamento = $row['metodo_pagamento'];
            $this->reserva_idreserva = (int)$row['reserva_idreserva'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET data_pagamento = :data_pagamento,
                      valor_total = :valor_total,
                      metodo_pagamento = :metodo_pagamento,
                      reserva_idreserva = :reserva_id
                  WHERE id_pagamento = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":data_pagamento", $this->data_pagamento);
        $stmt->bindParam(":valor_total", $this->valor_total);
        $stmt->bindParam(":metodo_pagamento", $this->metodo_pagamento);
        $stmt->bindParam(":reserva_id", $this->reserva_idreserva, \PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id_pagamento, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE
    public function delete(): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pagamento = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_pagamento, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toArray(): array {
        return [
            'id_pagamento' => $this->id_pagamento,
            'data_pagamento' => $this->data_pagamento,
            'valor_total' => $this->valor_total,
            'metodo_pagamento' => $this->metodo_pagamento,
            'reserva_idreserva' => $this->reserva_idreserva
        ];
    }

    public function validar(): array {
        $erros = [];

        if (empty($this->reserva_idreserva)) {
            $erros[] = "Reserva é obrigatória.";
        }

        if ($this->valor_total && $this->valor_total < 0) {
            $erros[] = "Valor total nao pode ser negativo.";
        }

        if ($this->metodo_pagamento && !in_array($this->metodo_pagamento, self::METODOS_VALIDOS)) {
            $erros[] = "Método de pagamento invalido. Valores permitidos: " . implode(', ', self::METODOS_VALIDOS);
        }

        return $erros;
    }

    // Buscar pagamentos por reserva
    public function buscarPorReserva(int $reserva_id): array {
        $query = "SELECT * FROM " . $this->table_name . " WHERE reserva_idreserva = :reserva_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reserva_id", $reserva_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getMetodosValidos(): array { return self::METODOS_VALIDOS; }
}
?>