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
    if (!isset($_GET['cep'])) {
        throw new Exception('CEP não fornecido');
    }

    $cep = preg_replace('/\D/', '', $_GET['cep']); // Remove formatação

    if (strlen($cep) < 5) {
        throw new Exception('CEP inválido');
    }

    // Buscar coletores que têm disponibilidade e estão na mesma cidade/região
    // Filtrando por CEP e buscando coletores próximos
    
    // Primeiro, buscar a cidade do CEP fornecido
    $sql_cep = "
        SELECT DISTINCT cidade, estado FROM enderecos 
        WHERE REPLACE(cep, '-', '') LIKE :cep
        LIMIT 1
    ";
    
    $stmt_cep = $conn->prepare($sql_cep);
    $stmt_cep->bindValue(':cep', $cep . '%');
    $stmt_cep->execute();
    $resultado_cep = $stmt_cep->fetch(PDO::FETCH_ASSOC);
    
    // Se encontrar a cidade, buscar coletores dessa cidade. Caso contrário, retornar lista geral de coletores disponíveis
    $sql = "
        SELECT 
            col.id,
            col.nome_completo,
            col.telefone,
            col.email,
            col.foto_perfil,
            e.cep as cep_coletor,
            e.cidade,
            e.estado,
            col.avaliacao_media,
            col.total_avaliacoes,
            col.coletas as total_coletas
        FROM coletores col
        LEFT JOIN enderecos e ON col.id_endereco = e.id
        LEFT JOIN coletores_config cc ON col.id = cc.id_coletor
        WHERE cc.disponibilidade = 'disponivel'
    ";
    
    if ($resultado_cep) {
        $sql .= " AND e.cidade = :cidade AND e.estado = :estado";
    }
    
    $sql .= "
        ORDER BY col.avaliacao_media DESC, col.coletas DESC
        LIMIT 10
    ";

    $stmt = $conn->prepare($sql);
    
    if ($resultado_cep) {
        $stmt->bindValue(':cidade', $resultado_cep['cidade']);
        $stmt->bindValue(':estado', $resultado_cep['estado']);
    }
    
    $stmt->execute();
    $coletores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatar resposta
    $coletores_formatados = [];
    foreach ($coletores as $coletor) {
        $coletores_formatados[] = [
            'id' => $coletor['id'],
            'nome' => $coletor['nome_completo'],
            'telefone' => $coletor['telefone'],
            'email' => $coletor['email'],
            'foto' => $coletor['foto_perfil'] ? '../uploads/profile_photos/' . $coletor['foto_perfil'] : '../img/avatar-default.png',
            'cidade' => $coletor['cidade'],
            'estado' => $coletor['estado'],
            'media_avaliacao' => $coletor['avaliacao_media'] ? round($coletor['avaliacao_media'], 1) : 0,
            'total_coletas' => $coletor['total_coletas'] ?? 0
        ];
    }

    echo json_encode([
        'success' => true,
        'coletores' => $coletores_formatados
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
