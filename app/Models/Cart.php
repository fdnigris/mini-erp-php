<?php

namespace App\Models;

class Cart {
    private $items = [];
    private $subtotal = 0;
    private $shipping = 0;
    private $total = 0;

    public function __construct() {
        if (isset($_SESSION['cart'])) {
            $this->items = $_SESSION['cart']['items'] ?? [];
            $this->subtotal = $_SESSION['cart']['subtotal'] ?? 0;
            $this->shipping = $_SESSION['cart']['shipping'] ?? 0;
            $this->total = $_SESSION['cart']['total'] ?? 0;
        }
    }

    public function addItem($productId, $variationId = null, $quantity = 1) {
        $product = (new Product())->find($productId);
        if (!$product) {
            return false;
        }

        // Verificar estoque
        if (!(new Product())->checkStock($productId, $variationId, $quantity)) {
            return false;
        }

        $itemKey = $productId . '-' . ($variationId ?? 'null');

        if (isset($this->items[$itemKey])) {
            $newQuantity = $this->items[$itemKey]['quantity'] + $quantity;
            if (!(new Product())->checkStock($productId, $variationId, $newQuantity)) {
                return false;
            }
            $this->items[$itemKey]['quantity'] = $newQuantity;
        } else {
            $this->items[$itemKey] = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];

            if ($variationId) {
                $variation = (new Product())->getVariations($productId);
                foreach ($variation as $var) {
                    if ($var['id'] == $variationId) {
                        $this->items[$itemKey]['variation_name'] = $var['name'];
                        break;
                    }
                }
            }
        }

        $this->updateTotals();
        $this->save();
        return true;
    }

    public function updateQuantity($productId, $variationId = null, $quantity) {
        $itemKey = $productId . '-' . ($variationId ?? 'null');
        
        if (!isset($this->items[$itemKey])) {
            error_log("Item não encontrado no carrinho: " . $itemKey);
            return false;
        }

        if ($quantity <= 0) {
            unset($this->items[$itemKey]);
            $this->updateTotals();
            $this->save();
            error_log("Item removido do carrinho por quantidade <= 0");
            return true;
        }

        // Verificar estoque disponível
        $product = new Product();
        $inventory = $product->getInventory($productId, $variationId);
        
        error_log("Verificando estoque para produto ID: " . $productId . 
                 ", variação: " . ($variationId ?? 'null') . 
                 ", quantidade solicitada: " . $quantity);
        error_log("Resultado do inventário: " . print_r($inventory, true));
        
        if (empty($inventory)) {
            error_log("Nenhum registro de inventário encontrado");
            return false;
        }

        $stockQuantity = $inventory[0]['quantity'];
        error_log("Quantidade em estoque: " . $stockQuantity);
        
        if ($stockQuantity < $quantity) {
            error_log("Quantidade solicitada maior que estoque");
            return false;
        }

        $this->items[$itemKey]['quantity'] = $quantity;
        $this->updateTotals();
        $this->save();
        error_log("Quantidade atualizada com sucesso");
        return true;
    }

    public function removeItem($productId, $variationId = null) {
        $itemKey = $productId . '-' . ($variationId ?? 'null');
        
        if (isset($this->items[$itemKey])) {
            unset($this->items[$itemKey]);
            $this->updateTotals();
            $this->save();
            return true;
        }
        return false;
    }

    public function clear() {
        $this->items = [];
        $this->updateTotals();
        $this->save();
    }

    private function calculateShipping() {
        if ($this->subtotal >= 200) {
            return 0; // Frete grátis
        } elseif ($this->subtotal >= 52 && $this->subtotal <= 166.59) {
            return 15;
        } else {
            return 20;
        }
    }

    private function updateTotals() {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $this->shipping = $this->calculateShipping();
        $this->total = $this->subtotal + $this->shipping;
    }

    private function save() {
        $_SESSION['cart'] = [
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'shipping' => $this->shipping,
            'total' => $this->total
        ];
    }

    public function getItems() {
        return $this->items;
    }

    public function getSubtotal() {
        return $this->subtotal;
    }

    public function getShipping() {
        return $this->shipping;
    }

    public function getTotal() {
        return $this->total;
    }

    public function isEmpty() {
        return empty($this->items);
    }
} 