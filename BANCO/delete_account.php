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

if (!isset($dados['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Senha não fornecida']);
    exit;
}

try {
    // Verifica a senha do usuário
    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if (!password_verify($dados['password'], $usuario['senha'])) {
        echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
        exit;
    }

    // Inicia uma transação
    $conn->begin_transaction();

    try {
        // Delete todas as configurações do usuário
        $stmt = $conn->prepare("DELETE FROM configuracoes WHERE usuario_id = ?");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();

        // Delete todas as coletas do usuário
        $stmt = $conn->prepare("DELETE FROM coletas WHERE gerador_id = ?");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();

        // Delete o usuário
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();

        // Commit da transação
        $conn->commit();

        // Destroi a sessão
        session_destroy();

        echo json_encode(['success' => true, 'message' => 'Conta excluída com sucesso']);

    } catch (Exception $e) {
        // Rollback em caso de erro
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir conta']);
}

$conn->close();
?>