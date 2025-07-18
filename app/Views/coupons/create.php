<div class="card">
    <div class="card-header">
        <h2>Novo Cupom</h2>
    </div>
    <div class="card-body">
        <form action="index.php?route=coupons&action=create" method="POST">
            <div class="mb-3">
                <label for="code" class="form-label">Código do Cupom</label>
                <input type="text" 
                       class="form-control" 
                       id="code" 
                       name="code" 
                       required
                       pattern="[A-Za-z0-9]+"
                       title="Apenas letras e números são permitidos"
                       onkeyup="this.value = this.value.toUpperCase()">
                <div class="form-text">Apenas letras e números, sem espaços ou caracteres especiais.</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discount_type" class="form-label">Tipo de Desconto</label>
                        <select class="form-select" 
                                id="discount_type" 
                                name="discount_type" 
                                required
                                onchange="updateDiscountLabel()">
                            <option value="percentage">Porcentagem (%)</option>
                            <option value="fixed">Valor Fixo (R$)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discount_value" class="form-label" id="discount_value_label">
                            Valor do Desconto (%)
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="discount_value" 
                               name="discount_value" 
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="min_purchase" class="form-label">Valor Mínimo de Compra (R$)</label>
                <input type="number" 
                       class="form-control" 
                       id="min_purchase" 
                       name="min_purchase" 
                       step="0.01"
                       min="0"
                       value="0">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="valid_from" class="form-label">Válido a partir de</label>
                        <input type="date" 
                               class="form-control" 
                               id="valid_from" 
                               name="valid_from" 
                               required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="valid_until" class="form-label">Válido até</label>
                        <input type="date" 
                               class="form-control" 
                               id="valid_until" 
                               name="valid_until" 
                               required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="is_active" 
                           name="is_active" 
                           checked>
                    <label class="form-check-label" for="is_active">
                        Cupom ativo
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php?route=coupons" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Criar Cupom</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateDiscountLabel() {
    const type = document.getElementById('discount_type').value;
    const label = document.getElementById('discount_value_label');
    
    if (type === 'percentage') {
        label.textContent = 'Valor do Desconto (%)';
    } else {
        label.textContent = 'Valor do Desconto (R$)';
    }
}

// Validar datas
document.getElementById('valid_until').addEventListener('change', function(e) {
    const validFrom = document.getElementById('valid_from').value;
    const validUntil = e.target.value;
    
    if (validFrom && validUntil && validFrom > validUntil) {
        alert('A data final deve ser maior que a data inicial');
        e.target.value = '';
    }
});

document.getElementById('valid_from').addEventListener('change', function(e) {
    const validFrom = e.target.value;
    const validUntil = document.getElementById('valid_until').value;
    
    if (validFrom && validUntil && validFrom > validUntil) {
        alert('A data inicial deve ser menor que a data final');
        e.target.value = '';
    }
});
</script> 