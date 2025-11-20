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
        $sql = "UPDATE coletores SET ";
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

    // Atualizar disponibilidade
    if (isset($data['disponibilidade'])) {
        $sql = "UPDATE coletores_config SET disponibilidade = :disponibilidade WHERE id_coletor = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':disponibilidade' => $data['disponibilidade'],
            ':id' => $id_usuario
        ]);
    }

    // Atualizar raio de atuação
    if (isset($data['raio_atuacao'])) {
        $sql = "UPDATE coletores_config SET raio_atuacao = :raio WHERE id_coletor = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':raio' => (int)$data['raio_atuacao'],
            ':id' => $id_usuario
        ]);
    }

    // Atualizar meio de transporte
    if (!empty($data['meio_transporte'])) {
        $sql = "UPDATE coletores_config SET meio_transporte = :transporte WHERE id_coletor = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':transporte' => $data['meio_transporte'],
            ':id' => $id_usuario
        ]);
    }

    // Atualizar endereço
    if (isset($data['endereco'])) {
        $endereco = $data['endereco'];

        // Buscar id do endereço
        $sql = "SELECT id_endereco FROM coletores WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_endereco = $result['id_endereco'] ?? null;

        if ($id_endereco) {
            // Atualizar endereço existente
            $sql = "UPDATE enderecos SET 
                    rua = :rua, 
                    numero = :numero, 
                    complemento = :complemento, 
                    bairro = :bairro, 
                    cidade = :cidade, 
                    estado = :estado, 
                    cep = :cep 
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':rua' => $endereco['rua'] ?? '',
                ':numero' => $endereco['numero'] ?? '',
                ':complemento' => $endereco['complemento'] ?? '',
                ':bairro' => $endereco['bairro'] ?? '',
                ':cidade' => $endereco['cidade'] ?? '',
                ':estado' => $endereco['estado'] ?? '',
                ':cep' => $endereco['cep'] ?? '',
                ':id' => $id_endereco
            ]);
        } else {
            // Criar novo endereço
            $sql = "INSERT INTO enderecos (rua, numero, complemento, bairro, cidade, estado, cep) 
                    VALUES (:rua, :numero, :complemento, :bairro, :cidade, :estado, :cep)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':rua' => $endereco['rua'] ?? '',
                ':numero' => $endereco['numero'] ?? '',
                ':complemento' => $endereco['complemento'] ?? '',
                ':bairro' => $endereco['bairro'] ?? '',
                ':cidade' => $endereco['cidade'] ?? '',
                ':estado' => $endereco['estado'] ?? '',
                ':cep' => $endereco['cep'] ?? ''
            ]);

            $id_endereco = $conn->lastInsertId();

            // Atualizar referência no coletor
            $sql = "UPDATE coletores SET id_endereco = :id_endereco WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id_endereco' => $id_endereco,
                ':id' => $id_usuario
            ]);
        }
    }

    // Atualizar horários de funcionamento
    if (isset($data['horarios'])) {
        $horarios = $data['horarios'];

        foreach ($horarios as $dia => $horario) {
            // Buscar se já existe registro para este dia
            $sql = "SELECT id FROM horarios_funcionamento WHERE id_coletor = :id AND dia_semana = :dia";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id' => $id_usuario,
                ':dia' => $dia
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Atualizar registro existente
                $sql = "UPDATE horarios_funcionamento SET 
                        ativo = :ativo, 
                        hora_abertura = :abertura, 
                        hora_fechamento = :fechamento 
                        WHERE id_coletor = :id AND dia_semana = :dia";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':ativo' => isset($horario['active']) && $horario['active'] ? 1 : 0,
                    ':abertura' => $horario['open'] ?? '08:00',
                    ':fechamento' => $horario['close'] ?? '17:00',
                    ':id' => $id_usuario,
                    ':dia' => $dia
                ]);
            } else {
                // Inserir novo registro
                $sql = "INSERT INTO horarios_funcionamento (id_coletor, dia_semana, ativo, hora_abertura, hora_fechamento) 
                        VALUES (:id, :dia, :ativo, :abertura, :fechamento)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':id' => $id_usuario,
                    ':dia' => $dia,
                    ':ativo' => isset($horario['active']) && $horario['active'] ? 1 : 0,
                    ':abertura' => $horario['open'] ?? '08:00',
                    ':fechamento' => $horario['close'] ?? '17:00'
                ]);
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Configurações atualizadas com sucesso!'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()
    ]);
}
