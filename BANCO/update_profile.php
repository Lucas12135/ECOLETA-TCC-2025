<?php
session_start();
header('Content-Type: application/json');

// Validar sessão
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

require_once 'conexao.php';

$id_usuario = $_SESSION['id_usuario'];
$nome_completo = isset($_POST['nome_completo']) ? trim($_POST['nome_completo']) : null;
$foto_perfil = null;

try {
    // Processar upload de foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto'];

        // Validar tipo de arquivo
        $mime_types = ['image/jpeg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $mime_types)) {
            throw new Exception('Tipo de arquivo inválido. Use JPG ou PNG.');
        }

        // Validar tamanho (máximo 2MB)
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $max_size) {
            throw new Exception('Arquivo muito grande. Máximo 5MB.');
        }

        // Criar diretório se não existir
        $upload_dir = dirname(__DIR__) . '/uploads/profile_photos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Gerar nome único para o arquivo
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $hash = md5(time() . uniqid());
        $foto_perfil = "{$id_usuario}_{$hash}.{$ext}";
        $file_path = $upload_dir . $foto_perfil;

        // Mover arquivo
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            throw new Exception('Erro ao salvar arquivo de foto.');
        }

        // Deletar foto antiga se existir
        $query = $conn->prepare("SELECT foto_perfil FROM coletores WHERE id = ?");
        $query->execute([$id_usuario]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['foto_perfil'])) {
            $old_photo = $upload_dir . $result['foto_perfil'];
            if (file_exists($old_photo)) {
                unlink($old_photo);
            }
        }
    }

    // Preparar atualização do banco
    $updates = [];
    $params = [];

    if (!empty($nome_completo)) {
        $updates[] = "nome_completo = ?";
        $params[] = $nome_completo;
    }

    if (!empty($foto_perfil)) {
        $updates[] = "foto_perfil = ?";
        $params[] = $foto_perfil;
    }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'Nenhuma alteração foi feita.']);
        exit;
    }

    // Adicionar ID do usuário no final
    $params[] = $id_usuario;

    // Executar update
    $sql = "UPDATE coletores SET " . implode(", ", $updates) . " WHERE id = ?";
    $query = $conn->prepare($sql);
    $query->execute($params);

    echo json_encode([
        'success' => true,
        'message' => 'Perfil atualizado com sucesso!',
        'foto_perfil' => $foto_perfil
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
