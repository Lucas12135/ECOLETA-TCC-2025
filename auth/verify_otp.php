<?php
session_start();
require __DIR__ . '/../BANCO/conexao.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Método inválido.';
        exit;
    }

    $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $code    = trim($_POST['code'] ?? '');
    $purpose = $_POST['purpose'] ?? 'cadastro_gerador';

    if (!$email) {
        echo 'E-mail inválido.';
        exit;
    }

    if (!preg_match('/^[0-9]{6}$/', $code)) {
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
        echo 'Nenhum código válido encontrado ou o código expirou.';
        exit;
    }

    // Checa tentativas restantes
    if ((int) $row['attempts_left'] <= 0) {
        echo 'Você excedeu o número de tentativas. Solicite um novo código.';
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

    // NÃO mexe na senha aqui – ela já foi salva na sessão no login.php
    // Só garante o e-mail e o flag de verificado
    $_SESSION['cadastro']['email']            = $email;
    $_SESSION['cadastro']['email_verificado'] = true;

    // Se já existir um gerador com esse e-mail, só marca como verificado
    $stmt = $conn->prepare("
        SELECT id, email_verificado
        FROM geradores
        WHERE email = :e
        LIMIT 1
    ");
    $stmt->execute([':e' => $email]);
    $gerador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gerador) {
        if ((int) $gerador['email_verificado'] === 0) {
            $up = $conn->prepare("
                UPDATE geradores
                SET email_verificado = 1
                WHERE id = :id
            ");
            $up->execute([':id' => $gerador['id']]);
        }

        // guarda o id para o registro.php poder “continuar” o cadastro
        $_SESSION['cadastro']['gerador_id'] = $gerador['id'];
    }

    // ===== FLUXO PARA CADASTRO DE GERADOR =====
    if ($purpose === 'cadastro_gerador') {
        // Aqui a SENHA continua sendo a que você salvou em $_SESSION['cadastro']['senha']
        // no CADASTRO_GERADOR/login.php. Não fazemos nada com ela aqui, só usamos depois.
        header('Location: ../CADASTRO_GERADOR/registro.php');
        exit;
    }

    // Se tiver outros purposes (recuperação de senha, coletor etc) trata aqui depois.
    header('Location: /');
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo 'Erro no servidor ao verificar o código: ' . htmlspecialchars($e->getMessage());
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erro inesperado ao verificar o código: ' . htmlspecialchars($e->getMessage());
}
