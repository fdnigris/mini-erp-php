<?php
// Verificar se o header existe
if (file_exists(__DIR__ . '/../includes/header.php')) {
    require_once __DIR__ . '/../includes/header.php';
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Carrinho</h1>
        <div>
            <a href="index.php?route=products" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Continuar Comprando
            </a>
            <?php if (!$cart->isEmpty()): ?>
                <a href="index.php?route=cart&action=clear" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Limpar Carrinho
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if ($cart->isEmpty()): ?>
        <div class="alert alert-info">
            Seu carrinho está vazio.
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart->getItems() as $itemKey => $item): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($item['name']) ?>
                                                <?php if (isset($item['variation_name'])): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($item['variation_name']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td style="width: 200px;">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" 
                                                           class="form-control quantity-input" 
                                                           value="<?= $item['quantity'] ?>"
                                                           min="1"
                                                           data-product-id="<?= $item['product_id'] ?>"
                                                           data-variation-id="<?= $item['variation_id'] ?? '' ?>">
                                                </div>
                                            </td>
                                            <td>R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                                            <td>R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                                            <td>
                                                <form action="index.php?route=cart&action=remove&id=<?= $item['product_id'] ?>" method="POST" style="display: inline;">
                                                    <?php if (isset($item['variation_id'])): ?>
                                                        <input type="hidden" name="variation_id" value="<?= $item['variation_id'] ?>">
                                                    <?php endif; ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover item">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resumo do Pedido</h5>
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
                            <div class="mb-3">
                                <small class="text-muted">
                                    Cupom aplicado: <?= $_SESSION['cart']['coupon']['code'] ?>
                                    <a href="index.php?route=coupons&action=remove" class="text-danger ms-2">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <form id="coupon-form" class="d-flex gap-2">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="coupon-code" 
                                               name="code" 
                                               placeholder="Código do cupom">
                                        <button type="submit" class="btn btn-outline-primary" title="Aplicar cupom">
                                            <i class="fas fa-ticket-alt"></i>
                                        </button>
                                    </div>
                                </form>
                                <div id="coupon-message" class="mt-2"></div>
                            </div>
                        <?php endif; ?>

                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5 mb-0">Total:</span>
                            <span class="h5 mb-0" id="cart-total">
                                R$ <?= number_format(
                                    $cart->getTotal() - (isset($_SESSION['cart']['coupon']) ? $_SESSION['cart']['coupon']['discount'] : 0),
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </span>
                        </div>

                        <a href="index.php?route=orders&action=create" class="btn btn-primary w-100">
                            <i class="bi bi-cart-check"></i> Finalizar Compra
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.quantity-input').forEach(input => {
    let timeout;
    input.addEventListener('change', function() {
        const productId = this.dataset.productId;
        const variationId = this.dataset.variationId;
        const quantity = this.value;

        console.log('Atualizando quantidade:', {
            productId,
            variationId,
            quantity
        });

        // Define um novo timeout para atualizar após 500ms sem mudanças
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            if (variationId) {
                formData.append('variation_id', variationId);
            }
            formData.append('quantity', quantity);

            console.log('Enviando requisição:', formData.toString());

            fetch('index.php?route=cart&action=update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            })
            .then(response => {
                console.log('Status da resposta:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Resposta recebida:', data);
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                    // Reset to previous value if error
                    this.value = this.defaultValue;
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('Erro ao atualizar quantidade');
                // Reset to previous value if error
                this.value = this.defaultValue;
            });
        }, 500);
    });
});

const couponForm = document.getElementById('coupon-form');
if (couponForm) {
    couponForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('coupon-code').value;
        const messageDiv = document.getElementById('coupon-message');

        fetch('index.php?route=coupons&action=apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `code=${code}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.className = 'alert alert-success mt-2';
                messageDiv.textContent = data.message;
                setTimeout(() => location.reload(), 1000);
            } else {
                messageDiv.className = 'alert alert-danger mt-2';
                messageDiv.textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.className = 'alert alert-danger mt-2';
            messageDiv.textContent = 'Erro ao aplicar cupom';
        });
    });
}
</script>

<style>
.quantity-input {
    text-align: center;
    font-weight: 500;
}

.quantity-input::-webkit-inner-spin-button,
.quantity-input::-webkit-outer-spin-button {
    opacity: 1;
    height: 24px;
}

#coupon-form .input-group {
    width: 100%;
}

#coupon-form .btn {
    padding: 0.5rem 1rem;
}

#coupon-form .btn i {
    font-size: 1.1rem;
}

.btn-outline-danger:hover i {
    transform: scale(1.1);
    transition: transform 0.2s;
}
</style>

<?php
// Verificar se o footer existe
if (file_exists(__DIR__ . '/../includes/footer.php')) {
    require_once __DIR__ . '/../includes/footer.php';
}
?> 