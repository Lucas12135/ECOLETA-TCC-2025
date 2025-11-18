<?php
/**
 * Script para adicionar colunas necessárias à tabela coletas
 * para suportar solicitações de coleta com endereço completo
 */

require_once 'conexao.php';

try {
    echo "Iniciando alterações na tabela 'coletas'...\n\n";

    // Lista de alterações a executar
    $alteracoes = [
        "ALTER TABLE `coletas` ADD COLUMN `id_coletor` INT DEFAULT NULL AFTER `id_gerador`" => "Adicionando coluna id_coletor",
        "ALTER TABLE `coletas` ADD CONSTRAINT `fk_coletas_coletor` FOREIGN KEY (`id_coletor`) REFERENCES `coletores`(`id`) ON DELETE SET NULL" => "Adicionando chave estrangeira para id_coletor",
        "ALTER TABLE `coletas` ADD COLUMN `tipo_coleta` ENUM('automatico', 'especifico') DEFAULT 'automatico' AFTER `status`" => "Adicionando coluna tipo_coleta",
        "ALTER TABLE `coletas` ADD COLUMN `cep` VARCHAR(9) DEFAULT NULL AFTER `periodo`" => "Adicionando coluna cep",
        "ALTER TABLE `coletas` ADD COLUMN `rua` VARCHAR(255) DEFAULT NULL AFTER `cep`" => "Adicionando coluna rua",
        "ALTER TABLE `coletas` ADD COLUMN `numero` VARCHAR(10) DEFAULT NULL AFTER `rua`" => "Adicionando coluna numero",
        "ALTER TABLE `coletas` ADD COLUMN `complemento` VARCHAR(255) DEFAULT NULL AFTER `numero`" => "Adicionando coluna complemento",
        "ALTER TABLE `coletas` ADD COLUMN `bairro` VARCHAR(100) DEFAULT NULL AFTER `complemento`" => "Adicionando coluna bairro",
        "ALTER TABLE `coletas` ADD COLUMN `cidade` VARCHAR(100) DEFAULT NULL AFTER `bairro`" => "Adicionando coluna cidade",
        "ALTER TABLE `coletas` ADD COLUMN `estado` VARCHAR(2) DEFAULT NULL AFTER `cidade`" => "Adicionando coluna estado",
        "ALTER TABLE `coletas` ADD COLUMN `latitude` DECIMAL(10, 8) DEFAULT NULL AFTER `estado`" => "Adicionando coluna latitude",
        "ALTER TABLE `coletas` ADD COLUMN `longitude` DECIMAL(11, 8) DEFAULT NULL AFTER `latitude`" => "Adicionando coluna longitude",
        "ALTER TABLE `coletas` ADD INDEX `idx_id_coletor` (`id_coletor`)" => "Adicionando índice id_coletor",
        "ALTER TABLE `coletas` ADD INDEX `idx_tipo_coleta` (`tipo_coleta`)" => "Adicionando índice tipo_coleta",
    ];

    $executadas = 0;
    $puladas = 0;
    $erros = [];

    foreach ($alteracoes as $sql => $descricao) {
        try {
            echo "➜ $descricao... ";
            $conn->exec($sql);
            echo "✅\n";
            $executadas++;
        } catch (PDOException $e) {
            // Se for erro de coluna duplicada ou constraint, é porque já existe
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'Duplicate key') !== false ||
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⏭️  (já existe)\n";
                $puladas++;
            } else {
                echo "❌ Erro: " . $e->getMessage() . "\n";
                $erros[] = $descricao . " - " . $e->getMessage();
            }
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 Resumo:\n";
    echo "  ✅ Alterações executadas: $executadas\n";
    echo "  ⏭️  Colunas/Índices já existentes: $puladas\n";
    echo "  ❌ Erros: " . count($erros) . "\n";
    
    if (!empty($erros)) {
        echo "\nErros encontrados:\n";
        foreach ($erros as $erro) {
            echo "  - $erro\n";
        }
    }
    
    echo "\n✅ Processo concluído!\n";
    echo "A tabela 'coletas' foi atualizada com sucesso para suportar solicitações de coleta.\n";

} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    exit(1);
}
?>
