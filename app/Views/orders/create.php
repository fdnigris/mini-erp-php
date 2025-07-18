<?php
// Verificar se o header existe
if (file_exists(__DIR__ . '/../includes/header.php')) {
    require_once __DIR__ . '/../includes/header.php';
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Finalizar Pedido</h1>
        <a href="index.php?route=cart" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Cliente</h5>
                </div>
                <div class="card-body">
                    <form action="index.php?route=orders&action=create" method="POST">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="customer_email" class="form-label">E-mail</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>" 
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="customer_cep" class="form-label">CEP</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="customer_cep" 
                                               name="customer_cep" 
                                               value="<?= htmlspecialchars($_POST['customer_cep'] ?? '') ?>" 
                                               maxlength="9"
                                               required>
                                        <button type="button" 
                                                class="btn btn-outline-primary" 
                                                id="buscar-cep">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div id="cep-feedback" class="form-text"></div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">Endereço</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_address" 
                                           name="customer_address" 
                                           value="<?= htmlspecialchars($_POST['customer_address'] ?? '') ?>" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Finalizar Pedido
                            </button>
                            <a href="index.php?route=cart" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar ao Carrinho
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cart->getItems() as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <?= htmlspecialchars($item['name']) ?>
                                <?php if (isset($item['variation_name'])): ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($item['variation_name']) ?>
                                    </small>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    Qtd: <?= $item['quantity'] ?>
                                </small>
                            </div>
                            <strong>
                                R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?>
                            </strong>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>R$ <?= number_format($cart->getSubtotal(), 2, ',', '.') ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <strong>R$ <?= number_format($cart->getShipping(), 2, ',', '.') ?></strong>
                    </div>

                    <?php if (isset($_SESSION['cart']['coupon'])): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Desconto:</span>
                            <strong class="text-success">
                                - R$ <?= number_format($_SESSION['cart']['coupon']['discount'], 2, ',', '.') ?>
                            </strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">
                                Cupom aplicado: <?= htmlspecialchars($_SESSION['cart']['coupon']['code']) ?>
                            </small>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="h5 mb-0">Total:</span>
                        <span class="h5 mb-0">
                            R$ <?= number_format(
                                $cart->getTotal() - (isset($_SESSION['cart']['coupon']) ? $_SESSION['cart']['coupon']['discount'] : 0),
                                2,
                                ',',
                                '.'
                            ) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('customer_cep');
    const addressInput = document.getElementById('customer_address');
    const cepFeedback = document.getElementById('cep-feedback');
    const buscarCepBtn = document.getElementById('buscar-cep');

    // Máscara para o CEP
    cepInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        e.target.value = value;
    });

    // Função para consultar CEP
    function consultarCep() {
        const cep = cepInput.value.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            cepFeedback.textContent = 'CEP deve conter 8 dígitos';
            cepFeedback.className = 'text-danger';
            return;
        }

        // Feedback visual
        buscarCepBtn.disabled = true;
        buscarCepBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        cepFeedback.textContent = 'Consultando CEP...';
        cepFeedback.className = 'text-muted';

        // Consultar CEP diretamente na API do ViaCEP
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    cepFeedback.textContent = 'CEP não encontrado';
                    cepFeedback.className = 'text-danger';
                    addressInput.value = '';
                } else {
                    cepFeedback.textContent = 'CEP encontrado!';
                    cepFeedback.className = 'text-success';
                    // Formatar endereço
                    const endereco = [
                        data.logradouro,
                        data.bairro,
                        `${data.localidade} - ${data.uf}`
                    ].filter(Boolean).join(', ');
                    addressInput.value = endereco;
                }
            })
            .catch(error => {
                cepFeedback.textContent = 'Erro ao consultar CEP';
                cepFeedback.className = 'text-danger';
                console.error('Erro:', error);
            })
            .finally(() => {
                buscarCepBtn.disabled = false;
                buscarCepBtn.innerHTML = '<i class="fas fa-search"></i>';
            });
    }

    // Eventos para consultar CEP
    buscarCepBtn.addEventListener('click', consultarCep);
    
    cepInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            consultarCep();
        }
    });
});
</script>

<?php
// Verificar se o footer existe
if (file_exists(__DIR__ . '/../includes/footer.php')) {
    require_once __DIR__ . '/../includes/footer.php';
}
?> 