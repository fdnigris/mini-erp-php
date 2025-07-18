<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mailer;

    public function __construct() {
        require_once __DIR__ . '/../../vendor/autoload.php';
        require_once __DIR__ . '/../../config/mail.php';

        $this->mailer = new PHPMailer(true);

        // Configurações do servidor
        $this->mailer->isSMTP();
        $this->mailer->Host = MAIL_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = MAIL_USERNAME;
        $this->mailer->Password = MAIL_PASSWORD;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = MAIL_PORT;
        $this->mailer->CharSet = 'UTF-8';

        // Remetente
        $this->mailer->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    }

    public function sendOrderConfirmation($order, $items) {
        try {
            // Destinatário
            $this->mailer->addAddress($order['customer_email'], $order['customer_name']);

            // Conteúdo
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Pedido #{$order['id']} Confirmado";

            // Corpo do e-mail
            $body = $this->getOrderEmailTemplate($order, $items);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $body));

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: " . $e->getMessage());
            return false;
        }
    }

    private function getOrderEmailTemplate($order, $items) {
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #2c3e50; text-align: center;'>Pedido Confirmado!</h2>
            <p style='color: #34495e;'>Olá {$order['customer_name']},</p>
            <p style='color: #34495e;'>Seu pedido #{$order['id']} foi confirmado com sucesso!</p>

            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='color: #2c3e50; margin-top: 0;'>Detalhes do Pedido</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <th style='text-align: left; padding: 8px; border-bottom: 1px solid #dee2e6;'>Produto</th>
                        <th style='text-align: center; padding: 8px; border-bottom: 1px solid #dee2e6;'>Qtd</th>
                        <th style='text-align: right; padding: 8px; border-bottom: 1px solid #dee2e6;'>Preço</th>
                        <th style='text-align: right; padding: 8px; border-bottom: 1px solid #dee2e6;'>Total</th>
                    </tr>";

        foreach ($items as $item) {
            $price = number_format($item['price'], 2, ',', '.');
            $total = number_format($item['price'] * $item['quantity'], 2, ',', '.');
            $html .= "
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6;'>{$item['product_name']}</td>
                        <td style='text-align: center; padding: 8px; border-bottom: 1px solid #dee2e6;'>{$item['quantity']}</td>
                        <td style='text-align: right; padding: 8px; border-bottom: 1px solid #dee2e6;'>R$ {$price}</td>
                        <td style='text-align: right; padding: 8px; border-bottom: 1px solid #dee2e6;'>R$ {$total}</td>
                    </tr>";
        }

        $subtotal = number_format($order['subtotal'], 2, ',', '.');
        $shipping = number_format($order['shipping_cost'], 2, ',', '.');
        $discount = number_format($order['discount'], 2, ',', '.');
        $total = number_format($order['total'], 2, ',', '.');

        $html .= "
                </table>
                <div style='margin-top: 15px;'>
                    <p style='text-align: right; margin: 5px 0;'>Subtotal: R$ {$subtotal}</p>
                    <p style='text-align: right; margin: 5px 0;'>Frete: R$ {$shipping}</p>";

        if ($order['discount'] > 0) {
            $html .= "
                    <p style='text-align: right; margin: 5px 0; color: #27ae60;'>Desconto: -R$ {$discount}</p>";
        }

        $html .= "
                    <p style='text-align: right; margin: 5px 0; font-weight: bold; color: #2c3e50;'>Total: R$ {$total}</p>
                </div>
            </div>

            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='color: #2c3e50; margin-top: 0;'>Endereço de Entrega</h3>
                <p style='margin: 5px 0;'>CEP: {$order['customer_cep']}</p>
                <p style='margin: 5px 0;'>{$order['customer_address']}</p>
            </div>

            <p style='color: #7f8c8d; font-size: 12px; text-align: center; margin-top: 30px;'>
                Este é um e-mail automático, por favor não responda.
            </p>
        </div>";

        return $html;
    }
} 