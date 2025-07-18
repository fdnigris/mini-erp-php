<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="card-title">
            <i class="fas fa-shopping-bag me-2"></i>Pedidos
        </h1>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <span class="fw-semibold">#<?= $order['id'] ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($order['customer_name']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($order['customer_email']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                    <div class="text-muted small"><?= date('H:i', strtotime($order['created_at'])) ?> hrs</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">
                                        R$ <?= number_format($order['total'], 2, ',', '.') ?>
                                    </div>
                                    <?php if ($order['discount'] > 0): ?>
                                        <div class="text-success small">
                                            <i class="fas fa-tag me-1"></i>
                                            -R$ <?= number_format($order['discount'], 2, ',', '.') ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= (new \App\Models\Order())->getStatusClass($order['status']) ?>">
                                        <i class="fas fa-circle me-1 small"></i>
                                        <?= (new \App\Models\Order())->getStatusLabel($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group float-end">
                                        <a href="index.php?route=orders&action=show&id=<?= $order['id'] ?>" 
                                           class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                                           title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                            <span>Detalhes</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-shopping-bag fa-3x mb-3"></i>
                                    <p class="mb-0">Nenhum pedido encontrado</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-group .form-inline {
    display: inline-block;
}

.btn-group form {
    margin: 0;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group > :first-child .btn {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group > :last-child .btn {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.btn-primary {
    background: linear-gradient(45deg, #2980b9, #3498db);
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #3498db, #2980b9);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-primary i {
    font-size: 0.9em;
}

.gap-2 {
    gap: 0.5rem !important;
}

/* Animação suave ao passar o mouse */
.btn-primary:hover i {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
</style> 