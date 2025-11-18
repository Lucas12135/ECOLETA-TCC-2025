<?php
/**
 * Script para adicionar campos necessários à tabela coletas
 * Este script adiciona a coluna id_coletor caso ela não exista
 */

require_once 'conexao.php';

try {
    // Verificar se a coluna id_coletor existe
    $sql_check = "SHOW COLUMNS FROM coletas WHERE Field = 'id_coletor'";
    $stmt = $conn->prepare($sql_check);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Coluna não existe, adicionar
        echo "Adicionando coluna id_coletor à tabela coletas...<br>";
        
        $sql_alter = "ALTER TABLE `coletas`
                      ADD COLUMN `id_coletor` int(11) DEFAULT NULL AFTER `id_gerador`,
                      ADD KEY `idx_coletas_coletor` (`id_coletor`),
                      ADD CONSTRAINT `coletas_ibfk_coletor` FOREIGN KEY (`id_coletor`) REFERENCES `coletores` (`id`)";
        
        $conn->exec($sql_alter);
        echo "✓ Coluna id_coletor adicionada com sucesso!<br>";
    } else {
        echo "✓ Coluna id_coletor já existe.<br>";
    }
    
    // Verificar se as colunas tipo_coleta, rua, numero, etc. existem
    $campos_necessarios = [
        'tipo_coleta' => "VARCHAR(20) DEFAULT 'automatico'",
        'rua' => "VARCHAR(255)",
        'numero' => "VARCHAR(10)",
        'complemento' => "VARCHAR(255)",
        'bairro' => "VARCHAR(100)",
        'cidade' => "VARCHAR(100)",
        'latitude' => "DECIMAL(10, 8)",
        'longitude' => "DECIMAL(11, 8)"
    ];
    
    foreach ($campos_necessarios as $campo => $tipo) {
        $sql_check_campo = "SHOW COLUMNS FROM coletas WHERE Field = '{$campo}'";
        $stmt = $conn->prepare($sql_check_campo);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            echo "Adicionando coluna {$campo} à tabela coletas...<br>";
            $sql_add = "ALTER TABLE `coletas` ADD COLUMN `{$campo}` {$tipo}";
            $conn->exec($sql_add);
            echo "✓ Coluna {$campo} adicionada com sucesso!<br>";
        }
    }
    
    echo "<br>Todas as migrações foram aplicadas com sucesso!";
    
} catch (PDOException $e) {
    echo "Erro ao aplicar migrações: " . $e->getMessage();
}
?>
