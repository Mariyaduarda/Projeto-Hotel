<?php 
namespace Controller;

require_once '../model/ItemConsumido.php';
require_once '../model/Item.php';

use database\Database;
use Router\ItemConsumido;
$db = new Database(); 

class ItemConsumidoController {
    private $itemConsumido;
    private $item;

    public function __construct() {
        $this->itemConsumido = new ItemConsumido();
        $this->item = new Item();
    }

    // Adicionar item ao consumo
    public function adicionar() {
        try {
            $this->itemConsumido->item_id_item = $_POST['item_id_item'];
            $this->itemConsumido->consumo_id_consumo = $_POST['consumo_id_consumo'];
            $this->itemConsumido->quantidade = $_POST['quantidade'];

            if($this->itemConsumido->criar()) {
                $_SESSION['sucesso'] = "Item adicionado ao consumo com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao adicionar item.";
            }
            
            header("Location: ../view/consumo/editar_consumo.php?id=" . $_POST['consumo_id_consumo']);
        } catch(Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
            header("Location: ../view/consumo/editar_consumo.php?id=" . $_POST['consumo_id_consumo']);
        }
    }

    // Listar itens de um consumo
    public function listaPorConsumo($consumo_id) {
        try {
            $resultado = $this->itemConsumido->listaPorConsumo($consumo_id);
            $itens = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total
            $total = $this->itemConsumido->calcularTotalConsumo($consumo_id);
            
            include '../view/consumo/itens_consumo.php';
        } catch(Exception $e) {
            $_SESSION['erro'] = "Erro ao lista itens: " . $e->getMessage();
            header("Location: ../view/consumo/lista_consumos.php");
        }
    }

    // Atualizar quantidade
    public function atualizarQuantidade() {
        try {
            $this->itemConsumido->item_id_item = $_POST['item_id_item'];
            $this->itemConsumido->consumo_id_consumo = $_POST['consumo_id_consumo'];
            $this->itemConsumido->quantidade = $_POST['quantidade'];

            if($this->itemConsumido->atualizar()) {
                $_SESSION['sucesso'] = "Quantidade atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar quantidade.";
            }
            
            header("Location: ../view/consumo/editar_consumo.php?id=" . $_POST['consumo_id_consumo']);
        } catch(Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
            header("Location: ../view/consumo/editar_consumo.php?id=" . $_POST['consumo_id_consumo']);
        }
    }

    // Remover item do consumo
    public function remover() {
        try {
            $this->itemConsumido->item_id_item = $_POST['item_id_item'];
            $this->itemConsumido->consumo_id_consumo = $_POST['consumo_id_consumo'];

            if($this->itemConsumido->deletar()) {
                $_SESSION['sucesso'] = "Item removido do consumo!";
            } else {
                $_SESSION['erro'] = "Erro ao remover item.";
            }
            
            header("Location: ../view/consumo/editar_consumo.php?id=" . $_POST['consumo_id_consumo']);
        } catch(Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
            header("Location: ../view/consumo/editar_consumo.php?id=" . $_POST['consumo_id_consumo']);
        }
    }

    // Calcular total do consumo
    public function calcularTotal($consumo_id) {
        try {
            $total = $this->itemConsumido->calcularTotalConsumo($consumo_id);
            echo json_encode(['total' => $total]);
        } catch(Exception $e) {
            echo json_encode(['erro' => $e->getMessage()]);
        }
    }
}

?>