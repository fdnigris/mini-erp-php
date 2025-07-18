<?php

namespace App\Models;

class Product extends Model {
    protected $table = 'products';

    public function getVariations($productId = null) {
        $id = $productId ?? $this->id;
        $stmt = $this->db->prepare("SELECT * FROM product_variations WHERE product_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function addVariation($productId, $name) {
        $stmt = $this->db->prepare("INSERT INTO product_variations (product_id, name) VALUES (?, ?)");
        $stmt->execute([$productId, $name]);
        return $this->db->lastInsertId();
    }

    public function getInventory($productId = null, $variationId = null) {
        $sql = "SELECT i.*, p.name as product_name";
        
        // Só adiciona o JOIN com variações se houver variação
        if ($variationId !== null) {
            $sql .= ", v.name as variation_name";
        }
        
        $sql .= " FROM inventory i 
                  JOIN products p ON i.product_id = p.id";
        
        // Só adiciona o JOIN com variações se houver variação
        if ($variationId !== null) {
            $sql .= " LEFT JOIN product_variations v ON i.variation_id = v.id";
        }
        
        $sql .= " WHERE 1=1";
        $params = [];

        if ($productId) {
            $sql .= " AND i.product_id = ?";
            $params[] = $productId;
        }
        
        if ($variationId !== null) {
            $sql .= " AND i.variation_id = ?";
            $params[] = $variationId;
        } else {
            $sql .= " AND i.variation_id IS NULL";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateInventory($productId, $variationId, $quantity) {
        // Primeiro, vamos limpar registros duplicados se existirem
        if ($variationId === null) {
            $stmt = $this->db->prepare(
                "DELETE FROM inventory WHERE product_id = ? AND variation_id IS NULL"
            );
            $stmt->execute([$productId]);
        }

        // Agora vamos verificar se já existe um registro
        $stmt = $this->db->prepare(
            "SELECT id FROM inventory WHERE product_id = ? AND (variation_id = ? OR (variation_id IS NULL AND ? IS NULL))"
        );
        $stmt->execute([$productId, $variationId, $variationId]);
        $inventory = $stmt->fetch();

        if ($inventory) {
            // Atualiza o registro existente
            $stmt = $this->db->prepare(
                "UPDATE inventory SET quantity = ? WHERE product_id = ? AND (variation_id = ? OR (variation_id IS NULL AND ? IS NULL))"
            );
            return $stmt->execute([$quantity, $productId, $variationId, $variationId]);
        } else {
            // Cria um novo registro
            $stmt = $this->db->prepare(
                "INSERT INTO inventory (product_id, variation_id, quantity) VALUES (?, ?, ?)"
            );
            return $stmt->execute([$productId, $variationId, $quantity]);
        }
    }

    public function checkStock($productId, $variationId, $quantity) {
        $sql = "SELECT quantity FROM inventory WHERE product_id = ?";
        $params = [$productId];

        if ($variationId !== null) {
            $sql .= " AND variation_id = ?";
            $params[] = $variationId;
        } else {
            $sql .= " AND variation_id IS NULL";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $inventory = $stmt->fetch();

        return $inventory && $inventory['quantity'] >= $quantity;
    }

    public function getAllWithInventory() {
        $sql = "SELECT p.*, COALESCE(i.quantity, 0) as quantity 
                FROM products p 
                LEFT JOIN inventory i ON p.id = i.product_id AND i.variation_id IS NULL
                ORDER BY p.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $stmt->fetch()['total'];
    }

    public function getLowStock($limit = 5) {
        // Convertendo o limite para inteiro para garantir
        $limit = (int) $limit;
        
        $sql = "SELECT p.*, COALESCE(i.quantity, 0) as quantity 
                FROM products p 
                LEFT JOIN inventory i ON p.id = i.product_id AND i.variation_id IS NULL
                WHERE COALESCE(i.quantity, 0) <= 5
                ORDER BY i.quantity ASC
                LIMIT " . $limit;
        
        return $this->db->query($sql)->fetchAll();
    }

    public function getTotalValue() {
        $sql = "SELECT COALESCE(SUM(p.price * COALESCE(i.quantity, 0)), 0) as total 
                FROM products p 
                LEFT JOIN inventory i ON p.id = i.product_id AND i.variation_id IS NULL";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
} 