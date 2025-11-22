<?php
require_once dirname(__DIR__) . '/BANCO/conexao.php';

header('Content-Type: application/json');

// Função para calcular distância usando Haversine
function calcularDistancia($lat1, $lon1, $lat2, $lon2)
{
    $R = 6371; // Raio da Terra em km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $R * $c;
}

// Função para converter CEP em coordenadas aproximadas
function obterCoordenadaspByCEP($cep)
{
    $cep_limpo = preg_replace('/\D/', '', $cep);

    try {
        // Usar curl para melhor controle
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/{$cep_limpo}/json/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);

        if ($response === false) {
            error_log("CURL Error: " . curl_error($ch));
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['erro'])) {
            error_log("CEP não encontrado: " . $cep_limpo);
            return null;
        }

        // Retorna coordenadas aproximadas (simplificado)
        // Para maior precisão, usar um serviço de geocodificação
        return [
            'latitude' => -15.8267, // Exemplo para Brasília
            'longitude' => -47.8822
        ];
    } catch (Exception $e) {
        error_log("Exception em obterCoordenadaspByCEP: " . $e->getMessage());
        return null;
    }
}

try {
    $cep = $_GET['cep'] ?? null;
    $latitude = $_GET['latitude'] ?? null;
    $longitude = $_GET['longitude'] ?? null;
    $raio_maximo = 50; // Raio máximo de busca em km

    // Se temos coordenadas, usar direto
    if ($latitude && $longitude) {
        $lat_usuario = floatval($latitude);
        $lon_usuario = floatval($longitude);
    } elseif ($cep) {
        // Converter CEP em coordenadas
        $coords = obterCoordenadaspByCEP($cep);
        if (!$coords) {
            echo json_encode([
                'success' => false,
                'message' => 'CEP não encontrado'
            ]);
            exit;
        }
        $lat_usuario = $coords['latitude'];
        $lon_usuario = $coords['longitude'];
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'CEP ou coordenadas são necessários'
        ]);
        exit;
    }

    // Buscar todos os coletores com suas informações
    $sql = "SELECT 
                c.id,
                c.nome_completo AS nome,
                c.foto_perfil,
                c.email,
                c.telefone,
                c.avaliacao_media,
                c.total_avaliacoes,
                c.created_at AS data_criacao,
                e.cep,
                e.rua,
                e.numero,
                e.bairro,
                e.cidade,
                e.estado,
                COALESCE(cc.raio_atuacao, 10) as raio_atuacao,
                COALESCE(cc.disponibilidade, 'disponivel') as disponibilidade,
                COALESCE(cc.meio_transporte, 'Não informado') as meio_transporte
            FROM coletores c
            LEFT JOIN enderecos e ON c.id_endereco = e.id
            LEFT JOIN coletores_config cc ON cc.id_coletor = c.id";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $coletores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular distância e filtrar
    $coletores_filtrados = [];

    foreach ($coletores as $coletor) {
        // Se o coletor não tem endereço, pular
        if (!$coletor['rua'] || !$coletor['bairro']) {
            continue;
        }

        // Usar coordenadas aproximadas baseadas na cidade
        // Para implementação completa, usar serviço de geocodificação
        $lat_coletor = $lat_usuario + (rand(-5, 5) / 100); // Simulação simplificada
        $lon_coletor = $lon_usuario + (rand(-5, 5) / 100);

        $distancia = calcularDistancia($lat_usuario, $lon_usuario, $lat_coletor, $lon_coletor);

        // Verificar se está dentro do raio de atuação do coletor
        $raio_atuacao = floatval($coletor['raio_atuacao']) ?? 0;

        if ($distancia <= $raio_atuacao && $distancia <= $raio_maximo) {
            $coletor['distancia'] = $distancia;
            $coletor['avaliacao_media'] = floatval($coletor['avaliacao_media']) ?? 0;
            $coletor['total_avaliacoes'] = intval($coletor['total_avaliacoes']) ?? 0;
            $coletores_filtrados[] = $coletor;
        }
    }

    // Ordenar por avaliação média (descendente) e depois por distância
    usort($coletores_filtrados, function ($a, $b) {
        if ($a['avaliacao_media'] == $b['avaliacao_media']) {
            return $a['distancia'] <=> $b['distancia'];
        }
        return $b['avaliacao_media'] <=> $a['avaliacao_media'];
    });

    // Limitar a 10 resultados
    $coletores_filtrados = array_slice($coletores_filtrados, 0, 10);

    echo json_encode([
        'success' => true,
        'coletores' => $coletores_filtrados,
        'total' => count($coletores_filtrados),
        'usuario_coordenadas' => [
            'latitude' => $lat_usuario,
            'longitude' => $lon_usuario
        ]
    ]);
} catch (Throwable $e) {
    // Capturar qualquer tipo de erro
    error_log("Erro em get_coletores_proximos.php: " . $e->getMessage() . " | " . $e->getFile() . ":" . $e->getLine());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar coletores: ' . $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
