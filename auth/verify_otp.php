<?php
session_start();
require __DIR__ . '/../BANCO/conexao.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Método inválido.';
        exit;
    }

    // Receber email e código do formulário
    $email   = trim($_POST['email'] ?? '');
    $code    = trim($_POST['code'] ?? '');
    $purpose = trim($_POST['purpose'] ?? 'cadastro_gerador');

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo 'E-mail inválido.';
        exit;
    }

    // Validar código
    if (!preg_match('/^[0-9]{6}$/', $code)) {
        http_response_code(400);
        echo 'Código inválido. Use exatamente 6 dígitos.';
        exit;
    }

    // Busca o último OTP válido, não usado e não expirado
    $stmt = $conn->prepare("
        SELECT *
        FROM otp_tokens
        WHERE email = :e
          AND purpose = :p
          AND used = 0
          AND expires_at > NOW()
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute([
        ':e' => $email,
        ':p' => $purpose,
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(400);
        echo 'Nenhum código válido encontrado ou o código expirou.';
        exit;
    }

    // Recalcula o hash do código informado
    $hashInput = hash('sha256', $row['otp_salt'] . $code);

    if (!hash_equals($row['otp_hash'], $hashInput)) {
        // Código incorreto: decrementa tentativas
        $stmt = $conn->prepare("
            UPDATE otp_tokens
            SET attempts_left = attempts_left - 1
            WHERE id = :id
        ");
        $stmt->execute([':id' => $row['id']]);

        http_response_code(400);
        echo 'Código incorreto. Verifique e tente novamente.';
        exit;
    }

    // ---------- Código correto: marca como usado ----------
    $stmt = $conn->prepare("
        UPDATE otp_tokens
        SET used = 1
        WHERE id = :id
    ");
    $stmt->execute([':id' => $row['id']]);

    // Garante que o array de cadastro existe na sessão
    if (!isset($_SESSION['cadastro'])) {
        $_SESSION['cadastro'] = [];
    }

    // Salva o email verificado na sessão
    $_SESSION['cadastro']['email']            = $email;
    $_SESSION['cadastro']['email_verificado'] = true;

    // Verifica se já existe um gerador com esse e-mail
    $stmt = $conn->prepare("
        SELECT id
        FROM geradores
        WHERE email = :e
        LIMIT 1
    ");
    $stmt->execute([':e' => $email]);
    $gerador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gerador) {
        // Gerador já existe, salva o ID na sessão
        $_SESSION['cadastro']['gerador_id'] = $gerador['id'];
    }

    // Verifica se já existe um coletor com esse e-mail
    $stmt = $conn->prepare("
        SELECT id
        FROM coletores
        WHERE email = :e
        LIMIT 1
    ");
    $stmt->execute([':e' => $email]);
    $coletor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coletor) {
        // Coletor já existe, salva o ID na sessão
        $_SESSION['cadastro']['coletor_id'] = $coletor['id'];
    }

    // ===== FLUXO PARA CADASTRO DE GERADOR =====
    if ($purpose === 'cadastro_gerador') {
        // Redireciona para o formulário de registro
        header('Location: ../CADASTRO_GERADOR/registro.php');
        exit;
    }

    // ===== FLUXO PARA CADASTRO DE COLETOR =====
    if ($purpose === 'cadastro_coletor') {
        // Redireciona para o formulário de registro
        header('Location: ../CADASTRO_COLETOR/registro.php');
        exit;
    }

    // Se tiver outros purposes (recuperação de senha, etc) trata aqui depois.
    header('Location: ../index.php');
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Erro no servidor ao verificar o código: ' . htmlspecialchars($e->getMessage());
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erro inesperado ao verificar o código: ' . htmlspecialchars($e->getMessage());
}
