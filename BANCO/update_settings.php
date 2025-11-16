<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Recebe e decodifica os dados JSON
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['setting']) || !isset($dados['value'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    // Lista de configurações permitidas
    $configuracoesPermitidas = [
        'twoFactorAuth',
        'emailNotifications',
        'smsNotifications',
        'pushNotifications',
        'preferredTime',
        'collectionFrequency',
        'shareStats'
    ];

    // Verifica se a configuração é permitida
    if (!in_array($dados['setting'], $configuracoesPermitidas)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Configuração inválida']);
        exit;
    }

    // Converte o valor para JSON
    $valor = json_encode($dados['value']);

    // Verifica se a configuração já existe
    $stmt = $conn->prepare("SELECT id FROM configuracoes WHERE usuario_id = ? AND configuracao = ?");
    $stmt->bind_param("is", $_SESSION['usuario_id'], $dados['setting']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Atualiza a configuração existente
        $stmt = $conn->prepare("UPDATE configuracoes SET valor = ? WHERE usuario_id = ? AND configuracao = ?");
        $stmt->bind_param("sis", $valor, $_SESSION['usuario_id'], $dados['setting']);
    } else {
        // Insere nova configuração
        $stmt = $conn->prepare("INSERT INTO configuracoes (usuario_id, configuracao, valor) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['usuario_id'], $dados['setting'], $valor);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Configuração atualizada com sucesso']);
    } else {
        throw new Exception("Erro ao atualizar configuração");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar configuração']);
}

$conn->close();
?>