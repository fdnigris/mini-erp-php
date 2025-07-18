<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Cupons de Desconto</h1>
    <a href="index.php?route=coupons&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Cupom
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Valor Mínimo</th>
                        <th>Validade</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($coupons)): ?>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($coupon['code']); ?></td>
                                <td>
                                    <?php echo $coupon['discount_type'] === 'percentage' ? 'Porcentagem' : 'Valor Fixo'; ?>
                                </td>
                                <td>
                                    <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                        <?php echo $coupon['discount_value']; ?>%
                                    <?php else: ?>
                                        R$ <?php echo number_format($coupon['discount_value'], 2, ',', '.'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    R$ <?php echo number_format($coupon['min_purchase'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php 
                                        $valid_from = new DateTime($coupon['valid_from']);
                                        $valid_until = new DateTime($coupon['valid_until']);
                                        echo $valid_from->format('d/m/Y') . ' até ' . $valid_until->format('d/m/Y');
                                    ?>
                                </td>
                                <td>
                                    <?php if ($coupon['is_active']): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="index.php?route=coupons&action=edit&id=<?php echo $coupon['id']; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?route=coupons&action=toggle&id=<?php echo $coupon['id']; ?>" 
                                           class="btn btn-sm <?php echo $coupon['is_active'] ? 'btn-warning' : 'btn-success'; ?>"
                                           title="<?php echo $coupon['is_active'] ? 'Desativar' : 'Ativar'; ?>">
                                            <i class="fas fa-power-off"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                onclick="deleteCoupon(<?php echo $coupon['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum cupom cadastrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function deleteCoupon(id) {
    if (confirm('Tem certeza que deseja excluir este cupom?')) {
        window.location.href = 'index.php?route=coupons&action=delete&id=' + id;
    }
}
</script> 