<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$data = json_decode(file_get_contents("php://input"), true);

try {
    // Atualizar informações de conta
    if (!empty($data['email']) || !empty($data['phone'])) {
        $sql = "UPDATE geradores SET ";
        $updates = [];
        $params = [];

        if (!empty($data['email'])) {
            $updates[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if (!empty($data['phone'])) {
            $updates[] = "telefone = :phone";
            $params[':phone'] = $data['phone'];
        }

        if (!empty($updates)) {
            $sql .= implode(", ", $updates) . " WHERE id = :id";
            $params[':id'] = $id_usuario;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        }
    }

    // Atualizar endereço
    if (isset($data['endereco'])) {
        $endereco = $data['endereco'];

        // Buscar id do endereço
        $sql = "SELECT id_endereco FROM geradores WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_endereco = $result['id_endereco'] ?? null;

        if ($id_endereco) {
            // Atualizar endereço existente
            $sql = "UPDATE enderecos SET 
                    cep = :cep, 
                    rua = :rua, 
                    numero = :numero, 
                    complemento = :complemento, 
                    bairro = :bairro, 
                    cidade = :cidade,
                    estado = :estado
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':cep' => $endereco['cep'],
                ':rua' => $endereco['rua'],
                ':numero' => $endereco['numero'],
                ':complemento' => $endereco['complemento'],
                ':bairro' => $endereco['bairro'],
                ':cidade' => $endereco['cidade'],
                ':estado' => $endereco['estado'],
                ':id' => $id_endereco
            ]);
        } else {
            // Criar novo endereço
            $sql = "INSERT INTO enderecos (cep, rua, numero, complemento, bairro, cidade, estado) 
                    VALUES (:cep, :rua, :numero, :complemento, :bairro, :cidade, :estado)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':cep' => $endereco['cep'],
                ':rua' => $endereco['rua'],
                ':numero' => $endereco['numero'],
                ':complemento' => $endereco['complemento'],
                ':bairro' => $endereco['bairro'],
                ':cidade' => $endereco['cidade'],
                ':estado' => $endereco['estado']
            ]);

            // Obter ID do endereço recém criado
            $id_endereco = $conn->lastInsertId();

            // Atualizar ID do endereço no gerador
            $sql = "UPDATE geradores SET id_endereco = :id_endereco WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id_endereco' => $id_endereco,
                ':id' => $id_usuario
            ]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Configurações salvas com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
