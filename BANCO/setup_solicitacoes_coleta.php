<?php
require_once 'conexao.php';

try {
    // Ler arquivo SQL
    $sql = file_get_contents(__DIR__ . '/create_solicitacoes_coleta_table.sql');
    
    // Executar SQL
    $conn->exec($sql);
    
    echo "✅ Tabela 'solicitacoes_coleta' criada com sucesso!";
    
} catch (PDOException $e) {
    echo "❌ Erro ao criar tabela: " . htmlspecialchars($e->getMessage());
}
?>
