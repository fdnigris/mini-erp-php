<?php

namespace App\Models;

class Coupon extends Model {
    protected $table = 'coupons';

    public function validate($code, $subtotal = 0) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE code = ? 
            AND is_active = 1 
            AND valid_from <= CURRENT_DATE 
            AND valid_until >= CURRENT_DATE
            AND min_purchase <= ?
        ");
        
        $stmt->execute([$code, $subtotal]);
        return $stmt->fetch();
    }

    public function calculateDiscount($coupon, $subtotal) {
        if ($coupon['discount_type'] === 'percentage') {
            return min($subtotal * ($coupon['discount_value'] / 100), $subtotal);
        } else {
            return min($coupon['discount_value'], $subtotal);
        }
    }

    public function getActiveCoupons() {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE is_active = 1 
            AND valid_until >= CURRENT_DATE 
            ORDER BY valid_until ASC
        ");
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function toggleStatus($id) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_active = NOT is_active 
            WHERE id = ?
        ");
        
        return $stmt->execute([$id]);
    }

    public function formatDiscount($coupon) {
        if ($coupon['discount_type'] === 'percentage') {
            return $coupon['discount_value'] . '%';
        } else {
            return 'R$ ' . number_format($coupon['discount_value'], 2, ',', '.');
        }
    }
} 