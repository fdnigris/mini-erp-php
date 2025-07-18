<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller {
    private $cart;

    public function __construct() {
        $this->cart = new Cart();
    }

    public function index() {
        $this->view('cart/index', [
            'cart' => $this->cart
        ]);
    }

    public function add() {
        $productId = $_GET['id'] ?? null;
        if (!$productId) {
            $_SESSION['error'] = 'Produto não especificado.';
            $this->redirect('index.php?route=products');
            return;
        }

        $quantity = $_POST['quantity'] ?? 1;
        $variationId = $_POST['variation_id'] ?? null;

        if ($this->cart->addItem($productId, $variationId, $quantity)) {
            $_SESSION['success'] = 'Produto adicionado ao carrinho!';
        } else {
            $_SESSION['error'] = 'Não foi possível adicionar o produto. Verifique o estoque.';
        }

        $this->redirect('index.php?route=cart');
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $variationId = $_POST['variation_id'] ?? null;
            $quantity = $_POST['quantity'] ?? null;

            error_log("Recebida requisição de atualização: " . print_r($_POST, true));

            if (!$productId || !$quantity) {
                $this->json([
                    'success' => false,
                    'message' => 'Dados inválidos. Produto e quantidade são obrigatórios.'
                ]);
                return;
            }

            // Se variation_id for uma string vazia, converte para null
            if ($variationId === '') {
                $variationId = null;
            }

            // Verificar estoque disponível antes de atualizar
            $product = new Product();
            $inventory = $product->getInventory($productId, $variationId);
            
            error_log("Resultado da consulta de inventário: " . print_r($inventory, true));
            
            if (empty($inventory)) {
                $this->json([
                    'success' => false,
                    'message' => 'Produto não encontrado no estoque.'
                ]);
                return;
            }

            $stockQuantity = $inventory[0]['quantity'];
            
            if ($stockQuantity < $quantity) {
                $this->json([
                    'success' => false,
                    'message' => "Quantidade indisponível. Estoque atual: {$stockQuantity} unidades."
                ]);
                return;
            }

            if ($this->cart->updateQuantity($productId, $variationId, $quantity)) {
                $this->json([
                    'success' => true,
                    'subtotal' => $this->cart->getSubtotal(),
                    'shipping' => $this->cart->getShipping(),
                    'total' => $this->cart->getTotal()
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Não foi possível atualizar a quantidade.'
                ]);
            }
        }
    }

    public function remove() {
        $productId = $_GET['id'] ?? null;
        if (!$productId) {
            $_SESSION['error'] = 'Produto não especificado.';
            $this->redirect('index.php?route=cart');
            return;
        }

        $variationId = $_POST['variation_id'] ?? null;
        
        if ($this->cart->removeItem($productId, $variationId)) {
            $_SESSION['success'] = 'Produto removido do carrinho!';
        } else {
            $_SESSION['error'] = 'Não foi possível remover o produto.';
        }

        $this->redirect('index.php?route=cart');
    }

    public function clear() {
        $this->cart->clear();
        $_SESSION['success'] = 'Carrinho esvaziado com sucesso!';
        $this->redirect('index.php?route=cart');
    }

    public function checkout() {
        if ($this->cart->isEmpty()) {
            $this->redirect('index.php?route=cart');
            return;
        }
        $this->redirect('index.php?route=orders&action=create');
    }
} 