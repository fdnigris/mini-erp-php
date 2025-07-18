<?php

namespace App\Controllers;

use App\Models\Order;

class WebhookController extends Controller {
    public function updateOrderStatus() {
        // Aceita apenas requisições POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['error' => 'Método não permitido']);
            return;
        }

        // Recebe o JSON do body
        $data = json_decode(file_get_contents('php://input'), true);

        // Valida os dados recebidos
        if (!isset($data['order_id']) || !isset($data['status'])) {
            http_response_code(400);
            $this->json(['error' => 'Dados inválidos']);
            return;
        }

        try {
            $order = new Order();
            
            // Se o status for 'cancelled', deleta o pedido
            if ($data['status'] === 'cancelled') {
                if ($order->delete($data['order_id'])) {
                    $this->json(['message' => 'Pedido cancelado e removido com sucesso']);
                } else {
                    throw new \Exception('Erro ao remover pedido');
                }
            } 
            // Caso contrário, atualiza o status
            else {
                if ($order->updateStatus($data['order_id'], $data['status'])) {
                    $this->json(['message' => 'Status do pedido atualizado com sucesso']);
                } else {
                    throw new \Exception('Erro ao atualizar status do pedido');
                }
            }
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['error' => $e->getMessage()]);
        }
    }
} 