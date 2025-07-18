<?php

namespace App\Services;

class CepService {
    private $baseUrl = 'https://viacep.com.br/ws/';

    public function getAddress($cep) {
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) !== 8) {
            return [
                'error' => true,
                'message' => 'CEP deve conter 8 dígitos'
            ];
        }

        try {
            $url = $this->baseUrl . $cep . '/json';
            $response = file_get_contents($url);
            
            if (!$response) {
                throw new \Exception('Não foi possível consultar o CEP');
            }

            $data = json_decode($response, true);

            if (isset($data['erro']) && $data['erro'] === true) {
                return [
                    'error' => true,
                    'message' => 'CEP não encontrado'
                ];
            }

            // Formata o endereço
            $address = $this->formatAddress($data);

            return [
                'error' => false,
                'address' => $address,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Erro ao consultar CEP: ' . $e->getMessage()
            ];
        }
    }

    private function formatAddress($data) {
        $parts = [];

        if (!empty($data['logradouro'])) {
            $parts[] = $data['logradouro'];
        }
        if (!empty($data['bairro'])) {
            $parts[] = $data['bairro'];
        }
        if (!empty($data['localidade'])) {
            $parts[] = $data['localidade'];
        }
        if (!empty($data['uf'])) {
            $parts[] = $data['uf'];
        }

        return implode(', ', $parts);
    }
} 