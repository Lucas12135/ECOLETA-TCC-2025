<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    include_once('../BANCO/conexao.php');

    if (!isset($conn) || !$conn) {
        http_response_code(500);
        $response['message'] = 'Não foi possível conectar ao banco de dados.';
        echo json_encode($response);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['id'])) {
        http_response_code(400);
        $response['message'] = 'ID do gerador é obrigatório.';
        echo json_encode($response);
        exit;
    }

    $id = intval($input['id']);

    // Busca dados básicos do gerador - usando apenas colunas que existem
    $stmt = $conn->prepare('
        SELECT 
            id, 
            nome_completo, 
            email, 
            telefone, 
            cpf
        FROM geradores 
        WHERE id = :id 
        LIMIT 1
    ');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $gerador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gerador) {
        // Busca endereço da tabela enderecos
        $endereco_completo = 'Endereço não informado';
        
        try {
            $endStmt = $conn->prepare('
                SELECT endereco, numero, complemento, cidade, estado, cep 
                FROM enderecos 
                WHERE id = :id 
                LIMIT 1
            ');
            $endStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $endStmt->execute();
            $endData = $endStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($endData && $endData['endereco']) {
                $endereco_completo = $endData['endereco'] . ', ' . $endData['numero'];
                if ($endData['complemento']) {
                    $endereco_completo .= ', ' . $endData['complemento'];
                }
                if ($endData['cidade'] && $endData['estado']) {
                    $endereco_completo .= ' - ' . $endData['cidade'] . ', ' . $endData['estado'];
                }
            }
        } catch (Exception $e) {
            // Se falhar, mantém endereço padrão
        }

        // Tenta buscar estatísticas
        $total_coletas = 0;
        $total_litros = 0;
        
        try {
            $statsQuery = $conn->prepare('
                SELECT 
                    COUNT(*) as total_coletas,
                    COALESCE(SUM(quantidade_litros), 0) as total_litros
                FROM coletas
                WHERE id_gerador = :id
            ');
            $statsQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $statsQuery->execute();
            $stats = $statsQuery->fetch(PDO::FETCH_ASSOC);
            
            if ($stats) {
                $total_coletas = intval($stats['total_coletas']);
                $total_litros = floatval($stats['total_litros']);
            }
        } catch (Exception $e) {
            // Se a tabela não existir, mantém valores padrão
        }

        $userData = [
            'id' => intval($gerador['id']),
            'nome' => $gerador['nome_completo'],
            'email' => $gerador['email'],
            'telefone' => $gerador['telefone'] ?: 'Não informado',
            'cpf' => $gerador['cpf'] ?: 'Não informado',
            'endereco' => $endereco_completo,
            'foto' => 'https://i.pravatar.cc/150?img=' . ($gerador['id'] % 70),
            'total_coletas' => $total_coletas,
            'total_litros' => $total_litros,
            'avaliacao' => 4.8,
            'impacto_ambiental' => $total_litros * 1000
        ];

        http_response_code(200);
        $response['success'] = true;
        $response['message'] = 'Dados do gerador carregados com sucesso.';
        $response['data'] = $userData;
    } else {
        http_response_code(404);
        $response['message'] = 'Gerador não encontrado.';
    }

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Erro no servidor: ' . $e->getMessage();
}

echo json_encode($response);
exit;
?>
