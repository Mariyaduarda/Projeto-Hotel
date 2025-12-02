<?php

namespace model;

require_once __DIR__ . '/../database/Database.php';

class Reserva {
    private $conn;
    private string $table_name = "reserva";

    private ?int    $idreserva = null;
    private ?float  $valor_reserva = null;
    private ?string $data_reserva = null;
    private ?string $data_checkin_previsto = null;
    private ?string $data_checkout_previsto = null;
    private string  $status = 'pendente';
    private int     $id_funcionario;
    private int     $id_hospede;
    private int     $id_quarto;

    private const STATUS_VALIDOS = ['pendente', 'confirmada', 'cancelada', 'concluida', 'em_andamento'];

    public function __construct($db){
        $this->conn = $db;
    }

    // GETTERS / SETTERS
    public function setId(int $id): void { $this->idreserva = $id; }
    public function getId(): ?int { return $this->idreserva; }

    public function setValorReserva(?float $valor): void { $this->valor_reserva = $valor; }
    public function getValorReserva(): ?float { return $this->valor_reserva; }

    public function setDataReserva(?string $data): void { $this->data_reserva = $data; }
    public function getDataReserva(): ?string { return $this->data_reserva; }

    public function setDataCheckin(?string $data): void { $this->data_checkin_previsto = $data; }
    public function getDataCheckin(): ?string { return $this->data_checkin_previsto; }

    public function setDataCheckout(?string $data): void { $this->data_checkout_previsto = $data; }
    public function getDataCheckout(): ?string { return $this->data_checkout_previsto; }

    public function setStatus(string $status): void { $this->status = $status; }
    public function getStatus(): string { return $this->status; }

    public function setFuncionarioId(int $id): void { $this->id_funcionario = $id; }
    public function getFuncionarioId(): int { return $this->id_funcionario; }

    public function setHospedeId(int $id): void { $this->id_hospede = $id; }
    public function getHospedeId(): int { return $this->id_hospede; }

    public function setQuartoId(int $id): void { $this->id_quarto = $id; }
    public function getQuartoId(): int { return $this->id_quarto; }

    // Métodos adicionais para compatibilidade com o controller
    public function setValorTotal(float $valor): void { $this->valor_reserva = $valor; }
    public function setNumHospedes(int $num): void { /* Campo nao existe na tabela */ }
    public function setObservacoes(?string $obs): void { /* Campo nao existe na tabela */ }

    // CREATE
    public function create(): bool {
        // Define data_reserva como hoje se nao foi informada
        if (empty($this->data_reserva)) {
            $this->data_reserva = date('Y-m-d');
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (valor_reserva, data_reserva, data_checkin_previsto, data_checkout_previsto, 
                   status, id_funcionario, id_hospede, id_quarto) 
                  VALUES (:valor, :data_reserva, :checkin, :checkout, :status, :funcionario, :hospede, :quarto)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":valor", $this->valor_reserva);
        $stmt->bindParam(":data_reserva", $this->data_reserva);
        $stmt->bindParam(":checkin", $this->data_checkin_previsto);
        $stmt->bindParam(":checkout", $this->data_checkout_previsto);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":funcionario", $this->id_funcionario, \PDO::PARAM_INT);
        $stmt->bindParam(":hospede", $this->id_hospede, \PDO::PARAM_INT);
        $stmt->bindParam(":quarto", $this->id_quarto, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->idreserva = (int)$this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // READ ALL
    public function read(): \PDOStatement {
        $query = "SELECT r.*, 
                         p_hosp.nome as nome_hospede,
                         p_func.nome as nome_funcionario,
                         q.numero as numero_quarto,
                         q.tipo_quarto
                  FROM " . $this->table_name . " r
                  LEFT JOIN hospede h ON r.id_hospede = h.id_pessoa
                  LEFT JOIN pessoa p_hosp ON h.id_pessoa = p_hosp.id_pessoa
                  LEFT JOIN funcionario f ON r.id_funcionario = f.id_pessoa
                  LEFT JOIN pessoa p_func ON f.id_pessoa = p_func.id_pessoa
                  LEFT JOIN quarto q ON r.id_quarto = q.id_quarto
                  ORDER BY r.data_reserva DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ ONE
    public function readOne(): bool {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idreserva = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idreserva, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row){
            $this->valor_reserva = $row['valor_reserva'] ? (float)$row['valor_reserva'] : null;
            $this->data_reserva = $row['data_reserva'];
            $this->data_checkin_previsto = $row['data_checkin_previsto'];
            $this->data_checkout_previsto = $row['data_checkout_previsto'];
            $this->status = $row['status'];
            $this->id_funcionario = (int)$row['id_funcionario'];
            $this->id_hospede = (int)$row['id_hospede'];
            $this->id_quarto = (int)$row['id_quarto'];
            return true;
        }
        return false;
    }

    // UPDATE
    public function update(): bool {
        $query = "UPDATE " . $this->table_name . "
                  SET valor_reserva = :valor,
                      data_reserva = :data_reserva,
                      data_checkin_previsto = :checkin,
                      data_checkout_previsto = :checkout,
                      status = :status,
                      id_funcionario = :funcionario,
                      id_hospede = :hospede,
                      id_quarto = :quarto
                  WHERE idreserva = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":valor", $this->valor_reserva);
        $stmt->bindParam(":data_reserva", $this->data_reserva);
        $stmt->bindParam(":checkin", $this->data_checkin_previsto);
        $stmt->bindParam(":checkout", $this->data_checkout_previsto);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":funcionario", $this->id_funcionario, \PDO::PARAM_INT);
        $stmt->bindParam(":hospede", $this->id_hospede, \PDO::PARAM_INT);
        $stmt->bindParam(":quarto", $this->id_quarto, \PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->idreserva, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // DELETE
    public function delete(): bool {
        // Só permite deletar reservas pendentes ou canceladas
        if (!in_array($this->status, ['pendente', 'cancelada'])) {
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE idreserva = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idreserva, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toArray(): array {
        return [
            'idreserva' => $this->idreserva,
            'valor_reserva' => $this->valor_reserva,
            'data_reserva' => $this->data_reserva,
            'data_checkin_previsto' => $this->data_checkin_previsto,
            'data_checkout_previsto' => $this->data_checkout_previsto,
            'status' => $this->status,
            'id_funcionario' => $this->id_funcionario,
            'id_hospede' => $this->id_hospede,
            'id_quarto' => $this->id_quarto
        ];
    }

    public function validar(): array {
        $erros = [];

        if (empty($this->id_hospede)) {
            $erros[] = "Hóspede é obrigatório.";
        }

        if (empty($this->id_quarto)) {
            $erros[] = "Quarto é obrigatório.";
        }

        if (empty($this->id_funcionario)) {
            $erros[] = "Funcionario é obrigatório.";
        }

        if (empty($this->data_checkin_previsto)) {
            $erros[] = "Data de check-in é obrigatória.";
        }

        if (empty($this->data_checkout_previsto)) {
            $erros[] = "Data de check-out é obrigatória.";
        }

        if ($this->data_checkin_previsto && $this->data_checkout_previsto) {
            if (strtotime($this->data_checkout_previsto) <= strtotime($this->data_checkin_previsto)) {
                $erros[] = "Data de check-out deve ser posterior à data de check-in.";
            }
        }

        if (!in_array($this->status, self::STATUS_VALIDOS)) {
            $erros[] = "Status invalido.";
        }

        return $erros;
    }

    // Calcular valor total baseado no preco da diaria e número de dias
    public function calcularValorTotal(float $preco_diaria): float {
        if (empty($this->data_checkin_previsto) || empty($this->data_checkout_previsto)) {
            return 0;
        }

        $checkin = new DateTime($this->data_checkin_previsto);
        $checkout = new DateTime($this->data_checkout_previsto);
        $diff = $checkin->diff($checkout);
        $dias = $diff->days;

        return $preco_diaria * $dias;
    }

    // Cancelar reserva
    public function cancelar(): bool {
        $this->status = 'cancelada';
        
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelada' WHERE idreserva = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idreserva, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Confirmar reserva
    public function confirmar(): bool {
        $this->status = 'confirmada';
        
        $query = "UPDATE " . $this->table_name . " SET status = 'confirmada' WHERE idreserva = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idreserva, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Concluir reserva (checkout)
    public function concluir(): bool {
        $this->status = 'concluida';
        
        $query = "UPDATE " . $this->table_name . " SET status = 'concluida' WHERE idreserva = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idreserva, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Listar por período
    public function listaPorPeriodo(string $data_inicio, string $data_fim): array {
        $query = "SELECT r.*, 
                         p_hosp.nome as nome_hospede,
                         q.numero as numero_quarto
                  FROM " . $this->table_name . " r
                  LEFT JOIN hospede h ON r.id_hospede = h.id_pessoa
                  LEFT JOIN pessoa p_hosp ON h.id_pessoa = p_hosp.id_pessoa
                  LEFT JOIN quarto q ON r.id_quarto = q.id_quarto
                  WHERE r.data_checkin_previsto BETWEEN :inicio AND :fim
                  ORDER BY r.data_checkin_previsto ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":inicio", $data_inicio);
        $stmt->bindParam(":fim", $data_fim);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Listar por status
    public function listaPorStatus(string $status): array {
        $query = "SELECT r.*, 
                         p_hosp.nome as nome_hospede,
                         q.numero as numero_quarto
                  FROM " . $this->table_name . " r
                  LEFT JOIN hospede h ON r.id_hospede = h.id_pessoa
                  LEFT JOIN pessoa p_hosp ON h.id_pessoa = p_hosp.id_pessoa
                  LEFT JOIN quarto q ON r.id_quarto = q.id_quarto
                  WHERE r.status = :status
                  ORDER BY r.data_reserva DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getStatusValidos(): array { return self::STATUS_VALIDOS; }
}
?>