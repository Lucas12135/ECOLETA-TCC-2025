<?php
header('Content-Type: application/json');

try {
    // Conexão com banco
    include_once("../BANCO/conexao.php");

    // Busca coletores + endereços + config
    $sql = "
        SELECT 
            c.id,
            c.nome_completo,
            c.foto_perfil,
            c.created_at,
            c.avaliacao_media,
            c.total_avaliacoes,
            c.telefone,
            c.meio_transporte AS meio_transporte_coletor,
            e.rua,
            e.numero,
            e.complemento,
            e.bairro,
            e.cidade,
            e.estado,
            e.cep,
            COALESCE(cc.raio_atuacao, 5) AS raio_atuacao,
            COALESCE(cc.meio_transporte, c.meio_transporte) AS meio_transporte
        FROM coletores c
        LEFT JOIN enderecos e ON e.id = c.id_endereco
        LEFT JOIN coletores_config cc ON cc.id_coletor = c.id
    ";

    $stmt = $conn->query($sql);
    $coletores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Processar dados
    $resultado = [];
    foreach ($coletores as $c) {
        // Montar endereço completo - validar campos vazios
        $endereco = "";
        if (!empty($c['rua']) && !empty($c['numero']) && !empty($c['bairro']) && !empty($c['cidade']) && !empty($c['estado']) && !empty($c['cep'])) {
            $endereco = "{$c['rua']}, {$c['numero']}, {$c['bairro']}, {$c['cidade']} - {$c['estado']}, {$c['cep']}";
            if (!empty($c['complemento'])) {
                $endereco .= " ({$c['complemento']})";
            }
        }

        $resultado[] = [
            'id' => intval($c['id']),
            'nome_completo' => $c['nome_completo'],
            'foto_perfil' => $c['foto_perfil'] ? $c['foto_perfil'] : null,
            'created_at' => $c['created_at'] ?? null,
            'avaliacao_media' => floatval($c['avaliacao_media'] ?? 0),
            'total_avaliacoes' => intval($c['total_avaliacoes'] ?? 0),
            'telefone' => $c['telefone'] ?? null,
            'raio_atuacao' => intval($c['raio_atuacao'] ?? 5),
            'meio_transporte' => $c['meio_transporte'] ?? 'a_pe',
            'endereco_completo' => $endereco,
            'rua' => $c['rua'] ?? null,
            'numero' => $c['numero'] ?? null,
            'complemento' => $c['complemento'] ?? null,
            'bairro' => $c['bairro'] ?? null,
            'cidade' => $c['cidade'] ?? null,
            'estado' => $c['estado'] ?? null,
            'cep' => $c['cep'] ?? null
        ];
    }

    echo json_encode($resultado);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar coletores: ' . $e->getMessage()
    ]);
}
