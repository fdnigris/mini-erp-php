<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Services\MailService;

class OrderController extends Controller {
    private $order;
    private $cart;
    private $mailService;

    public function __construct() {
        $this->order = new Order();
        $this->cart = new Cart();
        $this->mailService = new MailService();
    }

    public function index() {
        $orders = $this->order->all();
        $this->view('orders/index', ['orders' => $orders]);
    }

    public function show($id = null) {
        if (!$id) {
            $_SESSION['error'] = 'ID do pedido não especificado.';
            $this->redirect('index.php?route=orders');
            return;
        }

        $order = $this->order->getOrderWithItems($id);
        if (!$order) {
            $_SESSION['error'] = 'Pedido não encontrado.';
            $this->redirect('index.php?route=orders');
            return;
        }

        $this->view('orders/show', ['order' => $order]);
    }

    public function create() {
        if ($this->cart->isEmpty()) {
            $this->redirect('index.php?route=cart');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'customer_name' => 'required',
                'customer_email' => 'required',
                'customer_cep' => 'required'
            ]);

            if (empty($errors)) {
                try {
                    $orderId = $this->order->createOrder($_POST, $this->cart);
                    
                    // Buscar o pedido completo com itens
                    $order = $this->order->getOrderWithItems($orderId);
                    
                    // Enviar e-mail de confirmação
                    $emailSent = $this->mailService->sendOrderConfirmation($order, $order['items']);
                    
                    if (!$emailSent) {
                        error_log("Falha ao enviar e-mail para o pedido #{$orderId}");
                    }

                    // Limpar o carrinho e cupom
                    $this->cart->clear();
                    unset($_SESSION['cart']['coupon']);

                    $_SESSION['success'] = 'Pedido realizado com sucesso! Um e-mail de confirmação foi enviado.';
                    $this->redirect("index.php?route=orders&action=show&id=" . $orderId);
                } catch (\Exception $e) {
                    $_SESSION['error'] = 'Erro ao processar o pedido. Por favor, tente novamente.';
                    $this->view('orders/create', [
                        'cart' => $this->cart,
                        'errors' => ['system' => $e->getMessage()]
                    ]);
                }
            } else {
                $this->view('orders/create', [
                    'cart' => $this->cart,
                    'errors' => $errors
                ]);
            }
        } else {
            $this->view('orders/create', ['cart' => $this->cart]);
        }
    }

    public function updateStatus($id = null) {
        if (!$id) {
            $_SESSION['error'] = 'ID do pedido não especificado.';
            $this->redirect('index.php?route=orders');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            try {
                if ($this->order->updateStatus($id, $_POST['status'])) {
                    $_SESSION['success'] = 'Status do pedido atualizado com sucesso!';
                } else {
                    throw new \Exception('Não foi possível atualizar o status.');
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Erro ao atualizar status do pedido: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Método ou status inválido.';
        }
        
        $this->redirect("index.php?route=orders&action=show&id=" . $id);
    }
} 