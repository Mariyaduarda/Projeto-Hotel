<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';

class Consumo {
    private $conn;
    private string $table_name = "consumo";

    private ?int    $id_consumo = null;
    private ?string $data_consumo = null;
    private ?float  $valor_consumacao = null;
    private int     $reserva_idreserva;

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setId(int $id): void { $this->id_consumo = $id; }
    public function getId(): ?int { return $this->id_consumo; }

    public function setDataConsumo(?string $data): void { $this->data_consumo = $data; }
    public function getDataConsumo(): ?string { return $this->data_consumo; }

    public function setValorConsumacao(?float $valor): void { $this->valor_consumacao = $valor; }
    public function getValorConsumacao(): ?float { return $this->valor_consumacao; }

    public function setReservaId(int $reserva_id): void { $this->reserva_idreserva = $reserva_id; }
    public function getReservaId(): int { return $this->reserva_idreserva; }

    // CREATE
    public function create(): bool {
        $query = "INSERT INTO " . $this->table_name . " 
                  (data_consumo, valor_consumacao, reserva_idreserva) 
                  VALUES (:data_consumo, :valor_consumacao, :reserva_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":data_consumo", $this->data_consumo);
        $stmt->bindParam(":valor_consumacao", $this->valor_consumacao);
        $stmt->bindParam(":reserva_id", $this->reserva_idreserva, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id_consumo = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // READ ALL
    public function read(): \PDOStatement {
        $query = "SELECT c.*, r.idreserva, h.id_pessoa, p.nome as nome_hospede
                  FROM " . $this->table_name . " c
                  LEFT JOIN reserva r ON c.reserva_idreserva = r.idreserva
                  LEFT JOIN hospede h ON r.id_hospede = h.id_pessoa
                  LEFT JOIN pessoa p ON h.id_pessoa = p.id_pessoa
                  ORDER BY c.data_consumo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE
    public function readOne(): bool {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_consumo = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_consumo, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row){
            $this->data_consumo = $row['data_consumo'];
            $this->valor_consumacao = $row['valor_consumacao'] ? (float)$row['valor_consumacao'] : null;
            $this->reserva_idreserva = (int)$row['reserva_idreserva'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET data_consumo = :data_consumo,
                      valor_consumacao = :valor_consumacao,
                      reserva_idreserva = :reserva_id
                  WHERE id_consumo = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":data_consumo", $this->data_consumo);
        $stmt->bindParam(":valor_consumacao", $this->valor_consumacao);
        $stmt->bindParam(":reserva_id", $this->reserva_idreserva, \PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id_consumo, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE
    public function delete(): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_consumo = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id_consumo, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toArray(): array {
        return [
            'id_consumo' => $this->id_consumo,
            'data_consumo' => $this->data_consumo,
            'valor_consumacao' => $this->valor_consumacao,
            'reserva_idreserva' => $this->reserva_idreserva
        ];
    }

    public function validar(): array {
        $erros = [];

        if (empty($this->reserva_idreserva)) {
            $erros[] = "Reserva é obrigatória.";
        }

        if ($this->valor_consumacao && $this->valor_consumacao < 0) {
            $erros[] = "Valor de consumacao nao pode ser negativo.";
        }

        return $erros;
    }

    // Adicionar item ao consumo
    public function adicionarItem(int $item_id, int $quantidade): bool {
        $query = "INSERT INTO item_has_consumo (item_id_item, consumo_id_consumo, quantidade) 
                  VALUES (:item_id, :consumo_id, :quantidade)
                  ON DUPLICATE KEY UPDATE quantidade = quantidade + :quantidade";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":item_id", $item_id, \PDO::PARAM_INT);
        $stmt->bindParam(":consumo_id", $this->id_consumo, \PDO::PARAM_INT);
        $stmt->bindParam(":quantidade", $quantidade, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Listar itens do consumo
    public function listaItens(): array {
        $query = "SELECT i.*, ic.quantidade 
                  FROM item_has_consumo ic
                  INNER JOIN item i ON ic.item_id_item = i.id_item
                  WHERE ic.consumo_id_consumo = :consumo_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":consumo_id", $this->id_consumo, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Buscar consumos por reserva
    public function buscarPorReserva(int $reserva_id): array {
        $query = "SELECT * FROM " . $this->table_name . " WHERE reserva_idreserva = :reserva_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reserva_id", $reserva_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Calcular valor total com base nos itens
    public function calcularValorTotal(): float {
        $itens = $this->listaItens();
        $total = 0;
        
        foreach ($itens as $item) {
            $total += (float)$item['valor'] * (int)$item['quantidade'];
        }
        
        return $total;
    }
}
?>