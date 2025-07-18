<?php

namespace App\Models;

class Order extends Model {
    protected $table = 'orders';

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public function createOrder($customerData, Cart $cart) {
        $this->db->beginTransaction();

        try {
            // Criar o pedido
            $orderId = $this->create([
                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_cep' => $customerData['customer_cep'],
                'customer_address' => $customerData['customer_address'] ?? '',
                'subtotal' => $cart->getSubtotal(),
                'shipping_cost' => $cart->getShipping(),
                'discount' => isset($_SESSION['cart']['coupon']) ? $_SESSION['cart']['coupon']['discount'] : 0,
                'total' => $cart->getTotal() - (isset($_SESSION['cart']['coupon']) ? $_SESSION['cart']['coupon']['discount'] : 0),
                'status' => self::STATUS_PENDING,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Salvar os itens do pedido
            foreach ($cart->getItems() as $item) {
                $stmt = $this->db->prepare("
                    INSERT INTO order_items (
                        order_id, 
                        product_id, 
                        variation_id, 
                        quantity, 
                        price
                    ) VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['variation_id'],
                    $item['quantity'],
                    $item['price']
                ]);

                // Atualizar o estoque
                $product = new Product();
                $currentStock = $product->getInventory($item['product_id'], $item['variation_id'])[0]['quantity'];
                $product->updateInventory(
                    $item['product_id'],
                    $item['variation_id'],
                    $currentStock - $item['quantity']
                );
            }

            // Se houver cupom, salvar a relação
            if (isset($_SESSION['cart']['coupon'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO order_coupons (
                        order_id, 
                        coupon_id, 
                        discount_amount
                    ) VALUES (?, ?, ?)
                ");

                $stmt->execute([
                    $orderId,
                    $_SESSION['cart']['coupon']['id'],
                    $_SESSION['cart']['coupon']['discount']
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrderWithItems($orderId) {
        // Buscar informações do pedido
        $order = $this->find($orderId);
        if (!$order) return null;

        // Buscar itens do pedido
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, pv.name as variation_name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            LEFT JOIN product_variations pv ON oi.variation_id = pv.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll();

        // Buscar informações do cupom se houver
        $stmt = $this->db->prepare("
            SELECT oc.*, c.code
            FROM order_coupons oc
            JOIN coupons c ON oc.coupon_id = c.id
            WHERE oc.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $order['coupon'] = $stmt->fetch();

        return $order;
    }

    public function updateStatus($orderId, $newStatus) {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED
        ];

        error_log("Tentando atualizar status do pedido {$orderId} para {$newStatus}");

        if (!in_array($newStatus, $validStatuses)) {
            error_log("Status inválido: {$newStatus}");
            throw new \InvalidArgumentException('Status inválido');
        }

        // Verificar se o pedido existe
        $order = $this->find($orderId);
        if (!$order) {
            error_log("Pedido {$orderId} não encontrado");
            throw new \InvalidArgumentException('Pedido não encontrado');
        }

        error_log("Status atual do pedido: " . $order['status']);

        try {
            $result = $this->update($orderId, [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            error_log("Resultado da atualização: " . ($result ? "sucesso" : "falha"));

            // Verificar se a atualização funcionou
            $updatedOrder = $this->find($orderId);
            error_log("Novo status após atualização: " . $updatedOrder['status']);

            return $result;
        } catch (\Exception $e) {
            error_log("Erro ao atualizar status: " . $e->getMessage());
            throw $e;
        }
    }

    public function getStatusLabel($status) {
        $labels = [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PAID => 'Pago',
            self::STATUS_SHIPPED => 'Enviado',
            self::STATUS_DELIVERED => 'Entregue',
            self::STATUS_CANCELLED => 'Cancelado'
        ];

        return $labels[$status] ?? $status;
    }

    public function getStatusClass($status) {
        $classes = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'info',
            self::STATUS_SHIPPED => 'primary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger'
        ];

        return $classes[$status] ?? 'secondary';
    }

    public function getRecent($limit = 5) {
        // Convertendo o limite para inteiro para garantir
        $limit = (int) $limit;
        
        $sql = "SELECT o.*, COUNT(oi.id) as total_items 
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                GROUP BY o.id 
                ORDER BY o.created_at DESC 
                LIMIT " . $limit;
        
        return $this->db->query($sql)->fetchAll();
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $stmt->fetch()['total'];
    }

    public function countByStatus($status) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetch()['total'];
    }

    public function getTotalRevenue() {
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total), 0) as total 
            FROM {$this->table} 
            WHERE status != 'cancelled'
        ");
        return $stmt->fetch()['total'];
    }
} 