<div class="card">
    <div class="card-header">
        <h2>Editar Produto</h2>
    </div>
    <div class="card-body">
        <form action="index.php?route=products&action=edit&id=<?php echo $product['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nome do Produto</label>
                <input type="text" 
                       class="form-control" 
                       id="name" 
                       name="name" 
                       value="<?php echo htmlspecialchars($product['name']); ?>" 
                       required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Preço</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" 
                           class="form-control" 
                           id="price" 
                           name="price" 
                           step="0.01" 
                           min="0" 
                           value="<?php echo $product['price']; ?>" 
                           required>
                </div>
            </div>

            <?php if (empty($variations)): ?>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantidade em Estoque</label>
                    <input type="number" 
                           class="form-control" 
                           id="quantity" 
                           name="quantity" 
                           min="0" 
                           value="<?php echo isset($inventory[0]['quantity']) ? $inventory[0]['quantity'] : 0; ?>">
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Variações do Produto</label>
                    <button type="button" 
                            class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="modal" 
                            data-bs-target="#addVariationModal">
                        <i class="fas fa-plus"></i> Nova Variação
                    </button>
                </div>

                <div id="variations-container">
                    <?php if (!empty($variations)): ?>
                        <?php foreach ($variations as $variation): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong><?php echo htmlspecialchars($variation['name']); ?></strong>
                                            <input type="hidden" 
                                                   name="variations[<?php echo $variation['id']; ?>][id]" 
                                                   value="<?php echo $variation['id']; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-text">Estoque</span>
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="variations[<?php echo $variation['id']; ?>][quantity]" 
                                                       min="0" 
                                                       value="<?php 
                                                           $qty = 0;
                                                           foreach ($inventory as $inv) {
                                                               if ($inv['variation_id'] == $variation['id']) {
                                                                   $qty = $inv['quantity'];
                                                                   break;
                                                               }
                                                           }
                                                           echo $qty;
                                                       ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php?route=products" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Produto</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para adicionar variação -->
<div class="modal fade" id="addVariationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?route=products&action=addVariation&product_id=<?php echo $product['id']; ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Variação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="variation_name" class="form-label">Nome da Variação</label>
                        <input type="text" 
                               class="form-control" 
                               id="variation_name" 
                               name="name" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="variation_quantity" class="form-label">Quantidade Inicial</label>
                        <input type="number" 
                               class="form-control" 
                               id="variation_quantity" 
                               name="quantity" 
                               min="0" 
                               value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div> 