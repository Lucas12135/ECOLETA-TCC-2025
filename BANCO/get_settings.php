<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

try {
    // Busca todas as configurações do usuário
    $stmt = $conn->prepare("SELECT configuracao, valor FROM configuracoes WHERE usuario_id = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $configuracoes = [];
    while ($row = $result->fetch_assoc()) {
        $configuracoes[$row['configuracao']] = json_decode($row['valor']);
    }

    // Define valores padrão para configurações não encontradas
    $defaults = [
        'twoFactorAuth' => false,
        'emailNotifications' => true,
        'smsNotifications' => false,
        'pushNotifications' => true,
        'preferredTime' => 'morning',
        'collectionFrequency' => 'weekly',
        'shareStats' => false
    ];

    // Mescla as configurações salvas com os valores padrão
    $configuracoes = array_merge($defaults, $configuracoes);

    echo json_encode(['success' => true, 'data' => $configuracoes]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar configurações']);
}

$conn->close();
?>