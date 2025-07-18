<?php

namespace App\Controllers;

use App\Models\Coupon;
use App\Models\Cart;

class CouponController extends Controller {
    private $coupon;

    public function __construct() {
        $this->coupon = new Coupon();
    }

    public function index() {
        $coupons = $this->coupon->all();
        $this->view('coupons/index', ['coupons' => $coupons]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'code' => 'required',
                'discount_type' => 'required',
                'discount_value' => 'required',
                'valid_from' => 'required',
                'valid_until' => 'required'
            ]);

            if (empty($errors)) {
                $this->coupon->create([
                    'code' => strtoupper($_POST['code']),
                    'discount_type' => $_POST['discount_type'],
                    'discount_value' => $_POST['discount_value'],
                    'min_purchase' => $_POST['min_purchase'] ?? 0,
                    'valid_from' => $_POST['valid_from'],
                    'valid_until' => $_POST['valid_until'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ]);

                $_SESSION['success'] = 'Cupom criado com sucesso!';
                $this->redirect('index.php?route=coupons');
            }

            $this->view('coupons/create', ['errors' => $errors]);
        } else {
            $this->view('coupons/create');
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('index.php?route=coupons');
            return;
        }

        $coupon = $this->coupon->find($id);
        if (!$coupon) {
            $_SESSION['error'] = 'Cupom não encontrado.';
            $this->redirect('index.php?route=coupons');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'code' => 'required',
                'discount_type' => 'required',
                'discount_value' => 'required',
                'valid_from' => 'required',
                'valid_until' => 'required'
            ]);

            if (empty($errors)) {
                $this->coupon->update($id, [
                    'code' => strtoupper($_POST['code']),
                    'discount_type' => $_POST['discount_type'],
                    'discount_value' => $_POST['discount_value'],
                    'min_purchase' => $_POST['min_purchase'] ?? 0,
                    'valid_from' => $_POST['valid_from'],
                    'valid_until' => $_POST['valid_until'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ]);

                $_SESSION['success'] = 'Cupom atualizado com sucesso!';
                $this->redirect('index.php?route=coupons');
            }

            $this->view('coupons/edit', [
                'coupon' => $coupon,
                'errors' => $errors
            ]);
        } else {
            $this->view('coupons/edit', ['coupon' => $coupon]);
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->coupon->delete($id);
            $_SESSION['success'] = 'Cupom excluído com sucesso!';
        }
        $this->redirect('index.php?route=coupons');
    }

    public function toggle() {
        $id = $_GET['id'] ?? null;
        if ($id && $this->coupon->toggleStatus($id)) {
            $_SESSION['success'] = 'Status do cupom atualizado com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar status do cupom.';
        }
        $this->redirect('index.php?route=coupons');
    }

    public function apply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper($_POST['code']);
            $cart = new Cart();
            
            $couponData = $this->coupon->validate($code, $cart->getSubtotal());
            
            if ($couponData) {
                $discount = $this->coupon->calculateDiscount($couponData, $cart->getSubtotal());
                
                $_SESSION['cart']['coupon'] = [
                    'id' => $couponData['id'],
                    'code' => $couponData['code'],
                    'discount' => $discount
                ];
                
                $this->json([
                    'success' => true,
                    'message' => 'Cupom aplicado com sucesso!',
                    'discount' => $discount,
                    'total' => $cart->getTotal() - $discount
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Cupom inválido ou não aplicável para este pedido.'
                ]);
            }
        }
    }

    public function remove() {
        unset($_SESSION['cart']['coupon']);
        $this->redirect('index.php?route=cart');
    }
} 