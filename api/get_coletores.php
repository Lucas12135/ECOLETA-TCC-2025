<?php
header('Content-Type: application/json');
session_start();

try {
    require_once '../BANCO/conexao.php';

    // Obter parâmetros opcionais para filtro
    $cidade = isset($_GET['cidade']) ? $_GET['cidade'] : null;
    $bairro = isset($_GET['bairro']) ? $_GET['bairro'] : null;
    
    // Construir query base
    $sql = "SELECT 
                c.id,
                c.nome_completo,
                c.foto_perfil,
                c.avaliacao_media,
                c.total_avaliacoes,
                c.raio_atuacao,
                c.meio_transporte,
                c.telefone,
                e.cidade,
                e.bairro,
                e.rua,
                e.numero,
                e.cep
            FROM coletores c
            LEFT JOIN enderecos e ON c.id_endereco = e.id
            WHERE c.status = 'ativo'";
    
    // Adicionar filtros opcionais
    $params = [];
    
    if (!empty($cidade)) {
        $sql .= " AND e.cidade LIKE ?";
        $params[] = '%' . $cidade . '%';
    }
    
    if (!empty($bairro)) {
        $sql .= " AND e.bairro LIKE ?";
        $params[] = '%' . $bairro . '%';
    }
    
    // Ordenar por avaliação e número de avaliações
    $sql .= " ORDER BY c.avaliacao_media DESC, c.total_avaliacoes DESC
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    
    // Executar com parâmetros
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    
    $coletores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Processar dados para exibição
    $resultado = [];
    foreach ($coletores as $coletor) {
        $avaliacao = $coletor['avaliacao_media'] ? floatval($coletor['avaliacao_media']) : 0;
        $localizacao = '';
        
        if ($coletor['bairro'] && $coletor['cidade']) {
            $localizacao = $coletor['bairro'] . ', ' . $coletor['cidade'];
        } elseif ($coletor['cidade']) {
            $localizacao = $coletor['cidade'];
        } else {
            $localizacao = 'Localização não informada';
        }
        
        $resultado[] = [
            'id' => intval($coletor['id']),
            'nome_completo' => $coletor['nome_completo'],
            'foto_perfil' => $coletor['foto_perfil'] ? $coletor['foto_perfil'] : null,
            'avaliacao_media' => round($avaliacao, 1),
            'total_avaliacoes' => intval($coletor['total_avaliacoes'] ?? 0),
            'raio_atuacao' => $coletor['raio_atuacao'] ?? null,
            'meio_transporte' => $coletor['meio_transporte'] ?? null,
            'telefone' => $coletor['telefone'] ?? null,
            'localizacao' => $localizacao,
            'cidade' => $coletor['cidade'] ?? null,
            'bairro' => $coletor['bairro'] ?? null
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
        'message' => 'Erro ao buscar coletores: ' . $e->getMessage()
    ]);
}
?>
