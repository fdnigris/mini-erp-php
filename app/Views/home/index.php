<!-- Cabeçalho com Informações de Frete -->
<div class="page-header">
    <div class="container">
        <h1 class="text-center mb-4">Nossos Produtos</h1>
        
        <!-- Cards de Frete -->
        <div class="shipping-info">
            <div class="shipping-card">
                <div class="shipping-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="shipping-text">
                    <h3>Frete Grátis</h3>
                    <p>Acima de R$ 200,00</p>
                </div>
            </div>

            <div class="shipping-card">
                <div class="shipping-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="shipping-text">
                    <h3>Frete R$ 15,00</h3>
                    <p>Entre R$ 52,00 e R$ 166,59</p>
                </div>
            </div>

            <div class="shipping-card">
                <div class="shipping-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="shipping-text">
                    <h3>Frete R$ 20,00</h3>
                    <p>Para demais valores</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Produtos -->
<div class="container mt-5">
    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="product-card">
                        <!-- Imagem do Produto -->
                        <div class="product-image">
                            <i class="fas fa-box"></i>
                        </div>
                        
                        <!-- Informações do Produto -->
                        <div class="product-info">
                            <h2 class="product-title"><?= htmlspecialchars($product['name']) ?></h2>
                            <div class="product-price">
                                R$ <?= number_format($product['price'], 2, ',', '.') ?>
                            </div>

                            <!-- Status do Estoque -->
                            <?php if ($product['quantity'] <= 0): ?>
                                <div class="stock-status out">
                                    <i class="fas fa-times-circle"></i>
                                    Fora de Estoque
                                </div>
                            <?php elseif ($product['quantity'] <= 5): ?>
                                <div class="stock-status low">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Últimas <?= $product['quantity'] ?> unidades
                                </div>
                            <?php else: ?>
                                <div class="stock-status in">
                                    <i class="fas fa-check-circle"></i>
                                    <?= $product['quantity'] ?> em estoque
                                </div>
                            <?php endif; ?>

                            <!-- Botão de Compra -->
                            <?php if ($product['quantity'] > 0): ?>
                                <a href="index.php?route=cart&action=add&id=<?= $product['id'] ?>" 
                                   class="buy-button">
                                    <i class="fas fa-cart-plus"></i>
                                    Comprar Agora
                                </a>
                            <?php else: ?>
                                <button class="buy-button disabled" disabled>
                                    <i class="fas fa-ban"></i>
                                    Indisponível
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h2>Nenhum Produto Disponível</h2>
                    <p>Não encontramos produtos cadastrados no momento.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Cabeçalho */
.page-header {
    background: linear-gradient(135deg, #1a2980, #26d0ce);
    padding: 2rem 0;
    margin-bottom: 2rem;
    color: white;
}

.page-header h1 {
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

/* Cards de Frete */
.shipping-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.shipping-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    backdrop-filter: blur(5px);
    transition: transform 0.3s ease;
}

.shipping-card:hover {
    transform: translateY(-5px);
}

.shipping-icon {
    background: rgba(255, 255, 255, 0.2);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.shipping-icon i {
    font-size: 1.5rem;
}

.shipping-text {
    flex: 1;
}

.shipping-text h3 {
    font-size: 1.1rem;
    margin: 0;
    font-weight: 600;
}

.shipping-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Cards de Produtos */
.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image i {
    font-size: 4rem;
    color: #adb5bd;
}

.product-info {
    padding: 1.5rem;
}

.product-title {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

/* Status do Estoque */
.stock-status {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.stock-status i {
    margin-right: 0.5rem;
}

.stock-status.in {
    background: #d4edda;
    color: #155724;
}

.stock-status.low {
    background: #fff3cd;
    color: #856404;
}

.stock-status.out {
    background: #f8d7da;
    color: #721c24;
}

/* Botão de Compra */
.buy-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 0.75rem;
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #1a2980, #26d0ce);
    color: white;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.buy-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,41,128,0.3);
    color: white;
}

.buy-button i {
    margin-right: 0.5rem;
}

.buy-button.disabled {
    background: #dee2e6;
    cursor: not-allowed;
}

/* Estado Vazio */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #f8f9fa;
    border-radius: 15px;
}

.empty-state i {
    font-size: 4rem;
    color: #adb5bd;
    margin-bottom: 1rem;
}

.empty-state h2 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6c757d;
}

/* Responsividade */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem 0;
    }

    .page-header h1 {
        font-size: 2rem;
    }

    .shipping-info {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .shipping-card {
        padding: 1rem;
    }

    .shipping-icon {
        width: 40px;
        height: 40px;
    }

    .shipping-text h3 {
        font-size: 1rem;
    }

    .shipping-text p {
        font-size: 0.8rem;
    }
}
</style> 