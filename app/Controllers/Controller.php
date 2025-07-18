<?php

namespace App\Controllers;

class Controller {
    protected function view($view, $data = []) {
        // Extrair dados para deixá-los disponíveis na view
        extract($data);
        
        // Define o caminho da view
        $view = "../app/Views/{$view}.php";
        
        // Inclui o layout
        require_once "../app/Views/layouts/main.php";
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateRequest($rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                $errors[$field] = "O campo {$field} é obrigatório";
            }
        }
        return $errors;
    }
} 