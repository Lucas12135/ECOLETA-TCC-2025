<?php
session_start();
header('Content-Type: application/json');

// Validar sessão
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

require_once 'conexao.php';

$id_usuario = $_SESSION['id_usuario'];
$nova_senha = isset($_POST['nova_senha']) ? trim($_POST['nova_senha']) : null;

try {
    // Validar entrada
    if (empty($nova_senha)) {
        throw new Exception('Senha não fornecida.');
    }

    // Validar força da senha
    if (
        strlen($nova_senha) < 8 ||
        !preg_match('/[A-Z]/', $nova_senha) ||
        !preg_match('/[a-z]/', $nova_senha) ||
        !preg_match('/[0-9]/', $nova_senha) ||
        !preg_match('/[^A-Za-z0-9]/', $nova_senha)
    ) {
        throw new Exception('Senha não atende aos requisitos de segurança.');
    }

    // Hashear a senha
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Tentar atualizar na tabela coletores primeiro
    $stmt = $conn->prepare("UPDATE coletores SET senha = :senha WHERE id = :id");
    $stmt->bindParam(':senha', $senha_hash, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    $linhas_afetadas = $stmt->rowCount();

    // Se não atualizou na tabela coletores, tenta na tabela geradores
    if ($linhas_afetadas === 0) {
        $stmt = $conn->prepare("UPDATE geradores SET senha = :senha WHERE id = :id");
        $stmt->bindParam(':senha', $senha_hash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $linhas_afetadas = $stmt->rowCount();
    }

    // Verificar se a atualização foi bem-sucedida
    if ($linhas_afetadas > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Senha alterada com sucesso!'
        ]);
    } else {
        throw new Exception('Nenhuma linha foi atualizada. Tente novamente.');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
