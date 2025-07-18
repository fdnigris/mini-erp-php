<?php

// Aqui definimos pra onde cada URL vai
// Exemplo: /produtos vai pro ProductController
return [
    'home' => 'HomeController',      // Página inicial
    'products' => 'ProductController', // Gerenciamento de produtos
    'cart' => 'CartController',       // Carrinho de compras
    'orders' => 'OrderController',    // Pedidos
    'coupons' => 'CouponController',  // Cupons de desconto
    'cep' => 'CepController',         // Consulta de CEP
    'webhook' => 'WebhookController'  // Recebe notificações externas
]; 