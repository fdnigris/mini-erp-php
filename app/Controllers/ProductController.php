<?php

namespace App\Controllers;

use App\Models\Product;

class ProductController extends Controller {
    private $product;

    public function __construct() {
        $this->product = new Product();
    }

    public function index() {
        $products = $this->product->getAllWithInventory();
        $this->view('products/index', ['products' => $products]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'name' => 'required',
                'price' => 'required'
            ]);

            if (empty($errors)) {
                $productId = $this->product->create([
                    'name' => $_POST['name'],
                    'price' => $_POST['price']
                ]);

                // Adicionar estoque inicial para variação
                if (isset($_POST['variations']) && is_array($_POST['variations'])) {
                    foreach ($_POST['variations'] as $variation) {
                        if (!empty($variation['name'])) {
                            $variationId = $this->product->addVariation($productId, $variation['name']);
                            // Adicionar estoque para produto sem variações
                            $this->product->updateInventory(
                                $productId,
                                $variationId,
                                $variation['quantity'] ?? 0
                            );
                        }
                    }
                } else {
                    // Adicionar estoque para produto sem variações
                    $this->product->updateInventory(
                        $productId,
                        null,
                        $_POST['quantity'] ?? 0
                    );
                }

                $this->redirect('index.php?route=products');
            }

            $this->view('products/create', ['errors' => $errors]);
        } else {
            $this->view('products/create');
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('index.php?route=products');
        }

        $product = $this->product->find($id);
        $variations = $this->product->getVariations($id);
        $inventory = $this->product->getInventory($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'name' => 'required',
                'price' => 'required'
            ]);

            if (empty($errors)) {
                $this->product->update($id, [
                    'name' => $_POST['name'],
                    'price' => $_POST['price']
                ]);

                // Atualizar estoque
                if (isset($_POST['variations']) && is_array($_POST['variations'])) {
                    foreach ($_POST['variations'] as $variation) {
                        if (!empty($variation['id']) && !empty($variation['quantity'])) {
                            $this->product->updateInventory(
                                $id,
                                $variation['id'],
                                $variation['quantity']
                            );
                        }
                    }
                } else {
                    $this->product->updateInventory(
                        $id,
                        null,
                        $_POST['quantity'] ?? 0
                    );
                }

                $this->redirect('index.php?route=products');
            }

            $this->view('products/edit', [
                'product' => $product,
                'variations' => $variations,
                'inventory' => $inventory,
                'errors' => $errors
            ]);
        } else {
            $this->view('products/edit', [
                'product' => $product,
                'variations' => $variations,
                'inventory' => $inventory
            ]);
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->product->delete($id);
        }
        $this->redirect('index.php?route=products');
    }

    public function addVariation() {
        $productId = $_GET['product_id'] ?? null;
        if (!$productId) {
            $this->redirect('index.php?route=products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'name' => 'required'
            ]);

            if (empty($errors)) {
                $variationId = $this->product->addVariation(
                    $productId,
                    $_POST['name']
                );

                $this->product->updateInventory(
                    $productId,
                    $variationId,
                    $_POST['quantity'] ?? 0
                );
            }
        }
        $this->redirect('index.php?route=products&action=edit&id=' . $productId);
    }
} 