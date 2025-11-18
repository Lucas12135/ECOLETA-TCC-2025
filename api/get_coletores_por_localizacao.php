<?php
header('Content-Type: application/json');
session_start();
require_once '../BANCO/conexao.php';

try {
    // Obter parâmetros de localização
    $cidade = isset($_POST['cidade']) ? trim($_POST['cidade']) : null;
    $bairro = isset($_POST['bairro']) ? trim($_POST['bairro']) : null;
    
    if (!$cidade) {
        throw new Exception('Cidade não informada');
    }
    
    // Construir query para buscar coletores ativos na região
    $sql = "SELECT 
                c.id,
                c.nome_completo,
                c.foto_perfil,
                c.avaliacao_media,
                c.total_avaliacoes,
                c.raio_atuacao,
                c.meio_transporte,
                c.telefone,
                c.experiencia,
                e.cidade,
                e.bairro,
                e.rua,
                e.numero,
                e.cep
            FROM coletores c
            LEFT JOIN enderecos e ON c.id_endereco = e.id
            WHERE c.status = 'ativo'
            AND e.cidade LIKE :cidade";
    
    $params = [':cidade' => '%' . $cidade . '%'];
    
    // Se bairro foi informado, adicionar filtro
    if (!empty($bairro)) {
        $sql .= " AND e.bairro LIKE :bairro";
        $params[':bairro'] = '%' . $bairro . '%';
    }
    
    // Ordenar por avaliação e disponibilidade
    $sql .= " ORDER BY c.avaliacao_media DESC, c.total_avaliacoes DESC
            LIMIT 20";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $coletores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Processar dados
    $resultado = [];
    foreach ($coletores as $coletor) {
        $avaliacao = $coletor['avaliacao_media'] ? floatval($coletor['avaliacao_media']) : 0;
        
        $resultado[] = [
            'id' => intval($coletor['id']),
            'nome_completo' => $coletor['nome_completo'],
            'foto_perfil' => $coletor['foto_perfil'] ?: null,
            'avaliacao_media' => round($avaliacao, 1),
            'total_avaliacoes' => intval($coletor['total_avaliacoes'] ?? 0),
            'raio_atuacao' => $coletor['raio_atuacao'] ?? null,
            'meio_transporte' => $coletor['meio_transporte'] ?? null,
            'telefone' => $coletor['telefone'] ?? null,
            'experiencia' => $coletor['experiencia'] ?? null,
            'localizacao' => ($coletor['bairro'] ? $coletor['bairro'] . ', ' : '') . $coletor['cidade']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'total' => count($resultado),
        'coletores' => $resultado
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>
