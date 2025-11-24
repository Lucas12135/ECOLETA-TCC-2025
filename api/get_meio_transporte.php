<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['error' => 'Usuário não autenticado', 'meio_transporte' => 'carro']);
    exit;
}

include_once('../BANCO/conexao.php');

try {
    // Buscar meio de transporte do coletor
    $stmt = $conn->prepare("
        SELECT meio_transporte 
        FROM coletores_config 
        WHERE id_coletor = :id_coletor
        LIMIT 1
    ");
    
    $stmt->bindParam(':id_coletor', $_SESSION['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && !empty($result['meio_transporte'])) {
        echo json_encode([
            'success' => true,
            'meio_transporte' => $result['meio_transporte']
        ]);
    } else {
        // Se não encontrar, retornar carro como padrão
        echo json_encode([
            'success' => true,
            'meio_transporte' => 'carro'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Erro ao buscar meio de transporte',
        'meio_transporte' => 'carro'
    ]);
}
?>