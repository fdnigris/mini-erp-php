<?php

namespace App\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    private $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index()
    {
        // Buscar produtos com estoque
        $products = $this->product->getAllWithInventory();

        $this->view('home/index', [
            'products' => $products
        ]);
    }
} 