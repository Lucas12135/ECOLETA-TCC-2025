<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter dados JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id_coleta']) || !isset($input['quantidade_coletada'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados obrigatórios não fornecidos']);
    exit;
}

$id_coleta = $input['id_coleta'];
$quantidade_coletada = $input['quantidade_coletada'];
$observacoes = $input['observacoes'] ?? '';
$id_coletor = $_SESSION['id_usuario'];

include_once('../BANCO/conexao.php');

try {
    if ($conn) {
        // Verificar se a coleta existe e foi aceita por este coletor
        $stmt_check = $conn->prepare("
            SELECT * FROM coletas 
            WHERE id = :id_coleta 
            AND id_coletor = :id_coletor 
            AND status IN ('agendada', 'em_andamento')
        ");
        $stmt_check->bindParam(':id_coleta', $id_coleta);
        $stmt_check->bindParam(':id_coletor', $id_coletor);
        $stmt_check->execute();
        
        $coleta = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if (!$coleta) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Coleta não encontrada ou não pode ser concluída']);
            exit;
        }
        
        // Iniciar transação
        $conn->beginTransaction();
        
        // 1. Atualizar status da coleta para concluída
        $stmt_update = $conn->prepare("
            UPDATE coletas 
            SET status = 'concluida'
            WHERE id = :id_coleta
        ");
        $stmt_update->bindParam(':id_coleta', $id_coleta);
        $stmt_update->execute();
        
        // 2. Registrar no histórico de coletas
        $data_inicio = $coleta['data_agendada'] ?? date('Y-m-d H:i:s');
        $data_conclusao = date('Y-m-d H:i:s');
        
        $stmt_historico = $conn->prepare("
            INSERT INTO historico_coletas (
                id_coleta, id_coletor, id_gerador, data_inicio, data_conclusao, 
                quantidade_coletada, observacoes, status
            ) VALUES (
                :id_coleta, :id_coletor, :id_gerador, :data_inicio, :data_conclusao,
                :quantidade_coletada, :observacoes, 'concluida'
            )
        ");
        
        $stmt_historico->bindParam(':id_coleta', $id_coleta);
        $stmt_historico->bindParam(':id_coletor', $id_coletor);
        $stmt_historico->bindParam(':id_gerador', $coleta['id_gerador']);
        $stmt_historico->bindParam(':data_inicio', $data_inicio);
        $stmt_historico->bindParam(':data_conclusao', $data_conclusao);
        $stmt_historico->bindParam(':quantidade_coletada', $quantidade_coletada);
        $stmt_historico->bindParam(':observacoes', $observacoes);
        
        if (!$stmt_historico->execute()) {
            throw new Exception('Erro ao registrar no histórico');
        }
        
        $id_historico = $conn->lastInsertId();
        
        // 3. Registrar ganhos do coletor (se houver uma tabela de ganhos)
        // Assumindo que há uma política de pagamento
        $valor_ganho = $quantidade_coletada * 0.50; // Ex: R$ 0.50 por litro
        
        $stmt_ganhos = $conn->prepare("
            INSERT INTO ganhos_coletores (
                id_coletor, id_historico_coleta, valor_ganho, status_pagamento
            ) VALUES (
                :id_coletor, :id_historico, :valor_ganho, 'pendente'
            )
        ");
        
        $stmt_ganhos->bindParam(':id_coletor', $id_coletor);
        $stmt_ganhos->bindParam(':id_historico', $id_historico);
        $stmt_ganhos->bindParam(':valor_ganho', $valor_ganho);
        
        $stmt_ganhos->execute();
        
        // 4. Atualizar estatísticas do coletor
        $stmt_stats = $conn->prepare("
            UPDATE coletores 
            SET coletas = coletas + 1,
                total_oleo = total_oleo + :quantidade_oleo
            WHERE id = :id_coletor
        ");
        
        $stmt_stats->bindParam(':quantidade_oleo', $quantidade_coletada);
        $stmt_stats->bindParam(':id_coletor', $id_coletor);
        $stmt_stats->execute();
        
        // Confirmar transação
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Coleta concluída com sucesso',
            'id_historico' => $id_historico,
            'quantidade_coletada' => $quantidade_coletada,
            'valor_ganho' => $valor_ganho
        ]);
    }
} catch (Exception $e) {
    // Reverter transação em caso de erro
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao concluir coleta: ' . $e->getMessage()]);
}
?>
