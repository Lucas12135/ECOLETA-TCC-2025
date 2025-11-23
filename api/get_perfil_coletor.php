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
                id,
                nome_completo,
                email,
                telefone,
                foto_perfil,
                tipo_coletor,
                meio_transporte,
                avaliacao_media,
                total_avaliacoes,
                coletas,
                total_oleo
            FROM coletores
            WHERE id = :id_coletor";
    
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

