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

if (!isset($dados['currentPassword']) || !isset($dados['newPassword'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    // Busca a senha atual do usuário
    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verifica se a senha atual está correta
    if (!password_verify($dados['currentPassword'], $usuario['senha'])) {
        echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
        exit;
    }

    // Hash da nova senha
    $novaSenhaHash = password_hash($dados['newPassword'], PASSWORD_DEFAULT);

    // Atualiza a senha no banco de dados
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->bind_param("si", $novaSenhaHash, $_SESSION['usuario_id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
    } else {
        throw new Exception("Erro ao atualizar senha");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao processar a solicitação']);
}

$conn->close();
?>