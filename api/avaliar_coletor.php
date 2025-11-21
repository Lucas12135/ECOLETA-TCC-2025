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

// Validar dados obrigatórios
$required_fields = ['id_historico', 'id_coletor', 'nota', 'pontualidade', 'profissionalismo', 'qualidade_servico'];
foreach ($required_fields as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Campo obrigatório faltando: $field"]);
        exit;
    }
}

$id_historico = $input['id_historico'];
$id_coletor = $input['id_coletor'];
$id_gerador = $_SESSION['id_usuario'];
$nota = intval($input['nota']);
$pontualidade = intval($input['pontualidade']);
$profissionalismo = intval($input['profissionalismo']);
$qualidade_servico = intval($input['qualidade_servico']);
$comentario = $input['comentario'] ?? '';

// Validar valores
if ($nota < 1 || $nota > 5 || $pontualidade < 1 || $pontualidade > 5 || 
    $profissionalismo < 1 || $profissionalismo > 5 || $qualidade_servico < 1 || $qualidade_servico > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valores de avaliação inválidos']);
    exit;
}

include_once('../BANCO/conexao.php');

try {
    if ($conn) {
        // Verificar se já existe uma avaliação para este histórico
        $stmt_check = $conn->prepare("
            SELECT id FROM avaliacoes_coletores 
            WHERE id_historico_coleta = :id_historico 
            AND id_gerador = :id_gerador
        ");
        $stmt_check->bindParam(':id_historico', $id_historico);
        $stmt_check->bindParam(':id_gerador', $id_gerador);
        $stmt_check->execute();
        $existing = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Atualizar avaliação existente
            $stmt_update = $conn->prepare("
                UPDATE avaliacoes_coletores 
                SET nota = :nota,
                    pontualidade = :pontualidade,
                    profissionalismo = :profissionalismo,
                    qualidade_servico = :qualidade_servico,
                    comentario = :comentario
                WHERE id = :id
            ");
            $stmt_update->bindParam(':id', $existing['id']);
            $stmt_update->bindParam(':nota', $nota);
            $stmt_update->bindParam(':pontualidade', $pontualidade);
            $stmt_update->bindParam(':profissionalismo', $profissionalismo);
            $stmt_update->bindParam(':qualidade_servico', $qualidade_servico);
            $stmt_update->bindParam(':comentario', $comentario);

            if ($stmt_update->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Avaliação atualizada com sucesso'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar avaliação']);
            }
        } else {
            // Criar nova avaliação
            $stmt_insert = $conn->prepare("
                INSERT INTO avaliacoes_coletores (
                    id_historico_coleta, id_gerador, id_coletor, 
                    nota, pontualidade, profissionalismo, qualidade_servico, comentario
                ) VALUES (
                    :id_historico, :id_gerador, :id_coletor,
                    :nota, :pontualidade, :profissionalismo, :qualidade_servico, :comentario
                )
            ");
            
            $stmt_insert->bindParam(':id_historico', $id_historico);
            $stmt_insert->bindParam(':id_gerador', $id_gerador);
            $stmt_insert->bindParam(':id_coletor', $id_coletor);
            $stmt_insert->bindParam(':nota', $nota);
            $stmt_insert->bindParam(':pontualidade', $pontualidade);
            $stmt_insert->bindParam(':profissionalismo', $profissionalismo);
            $stmt_insert->bindParam(':qualidade_servico', $qualidade_servico);
            $stmt_insert->bindParam(':comentario', $comentario);

            if ($stmt_insert->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Avaliação enviada com sucesso',
                    'id_avaliacao' => $conn->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao registrar avaliação']);
            }
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
