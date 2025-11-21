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

if (!isset($input['id_coleta'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID da coleta não fornecido']);
    exit;
}

$id_coleta = $input['id_coleta'];
$id_coletor = $_SESSION['id_usuario'];

include_once('../BANCO/conexao.php');

try {
    if ($conn) {
        // Verificar se a coleta existe e se foi aceita por este coletor
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
            echo json_encode(['success' => false, 'message' => 'Coleta não encontrada ou não pode ser cancelada']);
            exit;
        }
        
        // Cancelar a coleta
        $stmt_update = $conn->prepare("
            UPDATE coletas 
            SET status = 'cancelada'
            WHERE id = :id_coleta
        ");
        $stmt_update->bindParam(':id_coleta', $id_coleta);
        
        if ($stmt_update->execute()) {
            echo json_encode(['success' => true, 'message' => 'Coleta cancelada com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar coleta']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
