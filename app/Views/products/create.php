<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title">
                    <i class="fas fa-box-open me-2"></i>Novo Produto
                </h2>
            </div>
            <div class="card-body">
                <form action="index.php?route=products&action=create" method="POST">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nome do Produto</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   required
                                   autofocus>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Preço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="price" 
                                       name="price" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">Quantidade em Estoque</label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   min="0" 
                                   value="0">
                            <span class="input-group-text">unidades</span>
                        </div>
                        <div class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Deixe 0 se o produto não tiver estoque inicial
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span>Variações do Produto</span>
                            <button type="button" 
                                    class="btn btn-outline-primary btn-sm"
                                    onclick="addVariation()">
                                <i class="fas fa-plus me-1"></i> Nova Variação
                            </button>
                        </label>
                        <div id="variations-container">
                            <!-- Variações serão adicionadas aqui -->
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?route=products" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Salvar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let variationCount = 0;

function addVariation() {
    const container = document.getElementById('variations-container');
    const variationHtml = `
        <div class="card mb-2 variation-item">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Nome da Variação</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="variations[${variationCount}][name]" 
                                   placeholder="Ex: Tamanho M, Cor Azul"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-2">
                            <label class="form-label">Quantidade</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       name="variations[${variationCount}][quantity]" 
                                       min="0" 
                                       value="0">
                                <span class="input-group-text">un</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" 
                                class="btn btn-outline-danger mb-2" 
                                onclick="removeVariation(this)"
                                data-bs-toggle="tooltip"
                                title="Remover variação">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', variationHtml);
    variationCount++;

    // Reativar tooltips para os novos elementos
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
}

function removeVariation(button) {
    const item = button.closest('.variation-item');
    item.classList.add('fade');
    setTimeout(() => {
        item.remove();
    }, 150);
}
</script>

<style>
.variation-item {
    transition: all 0.3s ease;
}
.variation-item.fade {
    opacity: 0;
    transform: translateX(20px);
}
</style> 