<?php

require_once __DIR__ . '/../config/database.php';

try {
    // Conectar ao banco de dados
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar o banco de dados se não existir
    $db->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $db->exec("USE " . DB_NAME);

    // Executar os arquivos de migração
    $migrations = [
        'schema.sql',
        'orders.sql',
        'order_coupons.sql'
    ];

    foreach ($migrations as $migration) {
        $sql = file_get_contents(__DIR__ . '/migrations/' . $migration);
        if ($sql === false) {
            throw new Exception("Não foi possível ler o arquivo de migração: " . $migration);
        }
        
        echo "Executando migração: " . $migration . "\n";
        $db->exec($sql);
        echo "Migração concluída: " . $migration . "\n";
    }

    echo "Todas as migrações foram executadas com sucesso!\n";

} catch (PDOException $e) {
    die("Erro ao executar migrações: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Erro: " . $e->getMessage() . "\n");
} 