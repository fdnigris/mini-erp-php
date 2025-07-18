<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="card-title">
            <i class="fas fa-box me-2"></i>Produtos
        </h1>
        <a href="index.php?route=products&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Produto
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($product['name']); ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">
                                        R$ <?php echo number_format($product['price'], 2, ',', '.'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-semibold me-2">
                                            <?php echo isset($product['quantity']) ? $product['quantity'] : '0'; ?>
                                        </span>
                                        <?php if ($product['quantity'] <= 5): ?>
                                            <span class="badge bg-danger" data-bs-toggle="tooltip" title="Estoque baixo">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </span>
                                        <?php elseif ($product['quantity'] <= 10): ?>
                                            <span class="badge bg-warning" data-bs-toggle="tooltip" title="Estoque médio">
                                                <i class="fas fa-exclamation"></i>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Estoque ok">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group float-end">
                                        <a href="index.php?route=products&action=edit&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-info"
                                           data-bs-toggle="tooltip"
                                           title="Editar produto">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                onclick="deleteProduct(<?php echo $product['id']; ?>)"
                                                data-bs-toggle="tooltip"
                                                title="Excluir produto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <a href="index.php?route=cart&action=add&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-success"
                                           data-bs-toggle="tooltip"
                                           title="Adicionar ao carrinho">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box fa-3x mb-3"></i>
                                    <p class="mb-0">Nenhum produto cadastrado</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este produto?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>
                    Excluir
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function deleteProduct(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmBtn = document.getElementById('confirmDelete');
    confirmBtn.href = 'index.php?route=products&action=delete&id=' + id;
    modal.show();
}
</script> 