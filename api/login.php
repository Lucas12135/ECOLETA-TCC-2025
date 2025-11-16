<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Trata requisições OPTIONS (CORS preflight)
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

    // Obtém dados do corpo da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['email']) || empty($input['senha'])) {
        http_response_code(400);
        $response['message'] = 'Email e senha são obrigatórios.';
        echo json_encode($response);
        exit;
    }

    $email = trim($input['email']);
    $senha = $input['senha'];

    // Busca o gerador pelo e-mail
    $stmt = $conn->prepare('SELECT id, nome_completo, email, senha, status FROM geradores WHERE email = :email LIMIT 1');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verifica senha hash (preferencial)
        if (password_verify($senha, $user['senha'])) {
            // Senha correta
        }
        // Migração: se ainda estiver em texto plano, faz upgrade para hash
        elseif ($user['senha'] === $senha) {
            $newHash = password_hash($senha, PASSWORD_DEFAULT);
            $upd = $conn->prepare('UPDATE geradores SET senha = :senha WHERE id = :id');
            $upd->bindParam(':senha', $newHash);
            $upd->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $upd->execute();
        } else {
            $user = false; // senha incorreta
        }
    }

    if ($user) {
        // (Opcional) bloquear logins inativos/suspensos:
        // if (!in_array($user['status'], ['ativo','pendente'])) {
        //     http_response_code(403);
        //     $response['message'] = 'Conta inativa ou suspensa.';
        //     echo json_encode($response);
        //     exit;
        // }

        // Cria sessão
        $_SESSION['id_usuario']   = $user['id'];
        $_SESSION['nome_usuario'] = $user['nome_completo'];
        $_SESSION['tipo_usuario'] = 'gerador';

        // Prepara dados do usuário para retornar
        $userData = [
            'id' => $user['id'],
            'nome' => $user['nome_completo'],
            'email' => $user['email'],
            'tipo' => 'gerador'
        ];

        http_response_code(200);
        $response['success'] = true;
        $response['message'] = 'Login realizado com sucesso.';
        $response['data'] = $userData;
    } else {
        http_response_code(401);
        $response['message'] = 'Email ou senha inválidos.';
    }

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Erro no servidor: ' . $e->getMessage();
}

echo json_encode($response);
exit;
?>
