<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Finalizar Compra</h2>
            </div>
            <div class="card-body">
                <form action="/cart/checkout" method="POST" id="checkoutForm">
                    <h4 class="mb-3">Informações do Cliente</h4>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nome Completo</label>
                        <input type="text" 
                               class="form-control" 
                               id="customer_name" 
                               name="customer_name" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="customer_email" class="form-label">E-mail</label>
                        <input type="email" 
                               class="form-control" 
                               id="customer_email" 
                               name="customer_email" 
                               required>
                    </div>

                    <h4 class="mb-3 mt-4">Endereço de Entrega</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="customer_cep" class="form-label">CEP</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_cep" 
                                           name="customer_cep" 
                                           maxlength="8"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button"
                                            onclick="searchCep()">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="address-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_street" class="form-label">Rua</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_street" 
                                           name="customer_street" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="customer_number" class="form-label">Número</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_number" 
                                           name="customer_number" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="customer_complement" class="form-label">Complemento</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_complement" 
                                           name="customer_complement">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="customer_neighborhood" class="form-label">Bairro</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_neighborhood" 
                                           name="customer_neighborhood" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_city" class="form-label">Cidade</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_city" 
                                           name="customer_city" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="customer_state" class="form-label">Estado</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_state" 
                                           name="customer_state" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Confirmar Pedido
                        </button>
                        <a href="/cart" class="btn btn-outline-secondary">
                            Voltar ao Carrinho
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Resumo do Pedido</h4>
            </div>
            <div class="card-body">
                <?php foreach ($cart->getItems() as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <?php if (isset($item['variation_name'])): ?>
                                <small class="text-muted">
                                    Variação: <?php echo htmlspecialchars($item['variation_name']); ?>
                                </small>
                            <?php endif; ?>
                            <br>
                            <small class="text-muted">
                                Quantidade: <?php echo $item['quantity']; ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <strong>
                                R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?>
                            </strong>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr>

                <table class="table table-sm">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-end">
                            R$ <?php echo number_format($cart->getSubtotal(), 2, ',', '.'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Frete:</td>
                        <td class="text-end">
                            R$ <?php echo number_format($cart->getShipping(), 2, ',', '.'); ?>
                            <?php if ($cart->getShipping() == 0): ?>
                                <span class="badge bg-success">Grátis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td class="text-end">
                            <strong>
                                R$ <?php echo number_format($cart->getTotal(), 2, ',', '.'); ?>
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function searchCep() {
    const cep = document.getElementById('customer_cep').value.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        alert('CEP inválido');
        return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert('CEP não encontrado');
                return;
            }

            document.getElementById('customer_street').value = data.logradouro;
            document.getElementById('customer_neighborhood').value = data.bairro;
            document.getElementById('customer_city').value = data.localidade;
            document.getElementById('customer_state').value = data.uf;

            document.getElementById('address-fields').style.display = 'block';
            document.getElementById('customer_number').focus();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao buscar CEP');
        });
}

// Máscara para o CEP
document.getElementById('customer_cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 8) value = value.slice(0, 8);
    e.target.value = value;
});
</script> 