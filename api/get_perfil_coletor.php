<?php
session_start();
require_once '../BANCO/conexao.php';

header('Content-Type: application/json');

try {
    // Verificar se o ID foi passado
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID do coletor não fornecido');
    }

    $idColetor = intval($_GET['id']);

    // Buscar dados do coletor
    $sql = "SELECT 
                c.id,
                c.nome_completo,
                c.email,
                c.telefone,
                c.foto_perfil,
                c.tipo_coletor,
                cc.meio_transporte,
                c.avaliacao_media,
                c.total_avaliacoes,
                c.coletas,
                c.total_oleo
            FROM coletores c
            LEFT JOIN coletores_config cc ON c.id = cc.id_coletor
            WHERE c.id = :id_coletor";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_coletor', $idColetor, PDO::PARAM_INT);
    $stmt->execute();
    
    $coletor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$coletor) {
        throw new Exception('Coletor não encontrado');
    }

    // Montar caminho correto da foto
    // Retornar como caminho relativo à raiz (funciona em qualquer página)
    if (!empty($coletor['foto_perfil'])) {
        $coletor['foto_url'] = 'uploads/profile_photos/' . $coletor['foto_perfil'];
    } else {
        $coletor['foto_url'] = 'img/avatar-default.png';
    }

    echo json_encode([
        'sucesso' => true,
        'coletor' => $coletor
    ]);

} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
?>

