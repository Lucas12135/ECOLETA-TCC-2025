<?php
session_start();
header('Content-Type: application/json');

// Verificar se está logado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

include_once('../BANCO/conexao.php');

try {
    if (!isset($_GET['id_historico'])) {
        throw new Exception('ID histórico não fornecido');
    }

    $id_historico = $_GET['id_historico'];

    // Buscar avaliação existente
    $sql = "
        SELECT 
            ac.id,
            ac.nota,
            ac.pontualidade,
            ac.profissionalismo,
            ac.qualidade_servico,
            ac.comentario
        FROM avaliacoes_coletores ac
        WHERE ac.id_historico_coleta = :id_historico
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_historico', $id_historico, PDO::PARAM_INT);
    $stmt->execute();
    
    $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($avaliacao) {
        echo json_encode([
            'success' => true,
            'avaliacao' => $avaliacao
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Avaliação não encontrada'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
