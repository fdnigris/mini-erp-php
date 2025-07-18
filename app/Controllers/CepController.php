<?php

namespace App\Controllers;

use App\Services\CepService;

class CepController extends Controller {
    private $cepService;

    public function __construct() {
        $this->cepService = new CepService();
    }

    public function lookup() {
        if (!isset($_GET['cep'])) {
            $this->json([
                'error' => true,
                'message' => 'CEP não informado'
            ]);
            return;
        }

        $result = $this->cepService->getAddress($_GET['cep']);
        $this->json($result);
    }
} 