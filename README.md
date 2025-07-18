# Mini ERP

Sistema de gerenciamento para controle de Pedidos, Produtos, Cupons e Estoque.

## 📁 Estrutura do Projeto

```
mini-erp/
├── app/                    # Código principal da aplicação
│   ├── Controllers/       # Controladores da aplicação
│   │   ├── CartController.php      # Gerencia o carrinho de compras
│   │   ├── CepController.php       # Consulta de CEP via ViaCEP
│   │   ├── Controller.php          # Controlador base
│   │   ├── CouponController.php    # Gerencia cupons de desconto
│   │   ├── HomeController.php      # Página inicial
│   │   ├── OrderController.php     # Gerencia pedidos
│   │   ├── ProductController.php   # Gerencia produtos
│   │   └── WebhookController.php   # Recebe atualizações externas
│   │
│   ├── Models/           # Modelos de dados
│   │   ├── Cart.php             # Lógica do carrinho
│   │   ├── Coupon.php          # Lógica de cupons
│   │   ├── Model.php           # Modelo base
│   │   ├── Order.php           # Lógica de pedidos
│   │   └── Product.php         # Lógica de produtos
│   │
│   ├── Services/         # Serviços da aplicação
│   │   ├── CepService.php      # Integração com ViaCEP
│   │   ├── Database.php        # Conexão com banco de dados
│   │   └── MailService.php     # Envio de e-mails
│   │
│   └── Views/            # Templates e layouts
│       ├── cart/              # Views do carrinho
│       ├── coupons/           # Views de cupons
│       ├── layouts/           # Layout principal
│       ├── orders/            # Views de pedidos
│       └── products/          # Views de produtos
│
├── config/               # Configurações
│   ├── app.php              # Configurações gerais
│   ├── database.php         # Configurações do banco
│   ├── mail.php            # Configurações de e-mail
│   └── routes.php          # Mapeamento de rotas
│
├── database/             # Banco de dados
│   └── migrations/         # Scripts SQL
│       └── database.sql    # Script completo do banco
│
├── public/               # Arquivos públicos
│   ├── assets/            # Recursos estáticos
│   └── index.php          # Ponto de entrada
│
└── storage/              # Armazenamento
    ├── cache/             # Cache da aplicação
    └── logs/              # Logs do sistema
```

## 🚀 Funcionalidades

### 1. Produtos
- Cadastro com nome, preço e estoque
- Suporte a variações de produtos
- Controle de estoque por variação
- Upload de imagens (em desenvolvimento)

### 2. Carrinho
- Adição/remoção de produtos
- Atualização de quantidades
- Cálculo automático de frete:
  * R$ 15,00 para pedidos entre R$ 52,00 e R$ 166,59
  * Frete grátis acima de R$ 200,00
  * R$ 20,00 para outros valores

### 3. Cupons
- Tipos: percentual ou valor fixo
- Validade por data
- Valor mínimo de compra
- Ativação/desativação

### 4. Pedidos
- Checkout completo
- Validação de CEP via ViaCEP
- E-mail de confirmação
- Status: pendente, pago, enviado, entregue, cancelado

### 5. Integrações
- ViaCEP para consulta de endereços
- SMTP para envio de e-mails
- Webhook para atualizações externas

## 🛠️ Tecnologias

- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 5
- Font Awesome
- JavaScript (Vanilla)

## ⚙️ Configuração

1. Arquivos de Configuração:
   - Copie `config/database.php.example` para `config/database.php`
   - Copie `config/mail.php.example` para `config/mail.php`
   - Configure as credenciais em cada arquivo

2. Banco de Dados:
   ```sql
   -- Execute o script de migração:
   php database/migrate.php
   ```
   
   Ou execute diretamente o script SQL:
   ```sql
   -- Execute o script completo:
   database/migrations/database.sql
   ```
   
   O script inclui:
   - Criação do banco e tabelas
   - Índices para otimização
   - Dados iniciais para teste

## 📧 E-mail

O sistema usa SMTP para envio de e-mails. Para configurar:
1. Ative verificação em 2 etapas no Gmail
2. Gere uma senha de app
3. Configure em `config/mail.php`

## 🔄 Webhook

Endpoint para atualizações externas:
```
POST /webhook/order/status
{
  "order_id": 123,
  "status": "paid"
}
```

## 🔍 Consulta de CEP

Integração com ViaCEP para:
- Validação de CEP
- Autopreenchimento de endereço
- Formato: 00000-000

## 📦 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP:
  * PDO
  * PDO_MySQL
  * mbstring
  * curl 