<div class="row justify-content-center">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="card-title">
                    <i class="fas fa-shopping-bag me-2"></i>Pedido #<?= $order['id'] ?>
                </h1>
                <a href="index.php?route=orders" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informações do Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase">Cliente</label>
                                            <div class="fw-semibold"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase">E-mail</label>
                                            <div class="fw-semibold"><?= htmlspecialchars($order['customer_email']) ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase">CEP</label>
                                            <div class="fw-semibold"><?= htmlspecialchars($order['customer_cep']) ?></div>
                                        </div>
                                        <?php if (!empty($order['customer_address'])): ?>
                                            <div class="mb-3">
                                                <label class="text-muted small text-uppercase">Endereço</label>
                                                <div class="fw-semibold"><?= htmlspecialchars($order['customer_address']) ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase">Data</label>
                                            <div class="fw-semibold">
                                                <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                                <span class="text-muted">às</span>
                                                <?= date('H:i', strtotime($order['created_at'])) ?> hrs
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase">Status</label>
                                            <div>
                                                <span class="badge bg-<?= (new \App\Models\Order())->getStatusClass($order['status']) ?>">
                                                    <i class="fas fa-circle me-1 small"></i>
                                                    <?= (new \App\Models\Order())->getStatusLabel($order['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form action="index.php?route=orders&action=updateStatus&id=<?= $order['id'] ?>" 
                                      method="POST" 
                                      class="mb-4"
                                      onsubmit="return confirm('Tem certeza que deseja atualizar o status do pedido?')">
                                    <div class="row align-items-end">
                                        <div class="col-md-8">
                                            <label for="status" class="form-label text-muted small text-uppercase">Atualizar Status</label>
                                            <select name="status" id="status" class="form-select" required>
                                                <option value="">Selecione um status...</option>
                                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                                <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Pago</option>
                                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Enviado</option>
                                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-check me-1"></i> Atualizar
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <h6 class="mb-3">
                                    <i class="fas fa-box me-2"></i>Itens do Pedido
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th class="text-center">Quantidade</th>
                                                <th class="text-end">Preço</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></div>
                                                        <?php if (!empty($item['variation_name'])): ?>
                                                            <div class="text-muted small">
                                                                <?= htmlspecialchars($item['variation_name']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center fw-semibold"><?= $item['quantity'] ?></td>
                                                    <td class="text-end">R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                                                    <td class="text-end fw-semibold">R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
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
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-receipt me-2"></i>Resumo do Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <strong>R$ <?= number_format($order['subtotal'], 2, ',', '.') ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Frete:</span>
                                    <strong>R$ <?= number_format($order['shipping_cost'], 2, ',', '.') ?></strong>
                                </div>
                                <?php if ($order['discount'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Desconto:</span>
                                        <strong class="text-success">
                                            - R$ <?= number_format($order['discount'], 2, ',', '.') ?>
                                        </strong>
                                    </div>
                                    <?php if (isset($order['coupon'])): ?>
                                        <div class="mb-2">
                                            <div class="text-success small">
                                                <i class="fas fa-tag me-1"></i>
                                                Cupom: <?= htmlspecialchars($order['coupon']['code']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h5 mb-0">Total:</span>
                                    <span class="h5 mb-0 text-primary">R$ <?= number_format($order['total'], 2, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 