<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$mensagem_sucesso = '';
$mensagem_erro = '';

// Processar formulário de configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aqui você pode salvar as configurações em um arquivo ou banco de dados
    $mensagem_sucesso = "Configurações atualizadas com sucesso!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Admin Dashboard</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-logo">
                <img src="../img/logo.png" alt="Ecoleta">
                <div class="admin-logo-text">
                    <h2>Ecoleta</h2>
                    <p>Admin</p>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="dashboard.php">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="geradores.php">
                    <i class="ri-user-line"></i>
                    <span>Geradores</span>
                </a>
                <a href="coletores.php">
                    <i class="ri-team-line"></i>
                    <span>Coletores</span>
                </a>
                <a href="coletas.php">
                    <i class="ri-oil-line"></i>
                    <span>Coletas</span>
                </a>
                <a href="relatorios.php">
                    <i class="ri-bar-chart-line"></i>
                    <span>Relatórios</span>
                </a>
                <a href="configuracoes.php" class="active">
                    <i class="ri-settings-line"></i>
                    <span>Configurações</span>
                </a>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <div>
                    <h1>Configurações do Sistema</h1>
                    <p style="color: #64748b; margin-top: 5px;">Gerencie as configurações gerais da plataforma</p>
                </div>
                <a href="../index.php" class="admin-logout">
                    <i class="ri-logout-circle-line"></i>
                    Sair
                </a>
            </header>

            <?php if ($mensagem_sucesso): ?>
                <div style="background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #22c55e;">
                    <?php echo $mensagem_sucesso; ?>
                </div>
            <?php endif; ?>

            <!-- Configurações Gerais -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-settings-gear-line"></i>
                    Configurações Gerais
                </div>

                <form method="POST" style="display: grid; gap: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nome da Plataforma</label>
                            <input type="text" value="Ecoleta" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email de Contato</label>
                            <input type="email" value="contato@ecoleta.com" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Descrição da Plataforma</label>
                        <textarea style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; height: 100px;">Plataforma para coleta e reciclagem de óleo usado</textarea>
                    </div>

                    <button type="submit" style="padding: 12px 20px; background: #22c55e; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; width: fit-content;">
                        <i class="ri-save-line"></i>
                        Salvar Configurações
                    </button>
                </form>
            </div>

            <!-- Configurações de Email -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-mail-line"></i>
                    Configurações de Email
                </div>

                <form method="POST" style="display: grid; gap: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Servidor SMTP</label>
                            <input type="text" placeholder="smtp.gmail.com" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Porta</label>
                            <input type="number" value="587" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email</label>
                            <input type="email" placeholder="seu-email@gmail.com" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Senha</label>
                            <input type="password" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
                        </div>
                    </div>

                    <button type="submit" style="padding: 12px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; width: fit-content;">
                        <i class="ri-mail-check-line"></i>
                        Testar Email
                    </button>
                </form>
            </div>

            <!-- Informações do Sistema -->
            <div class="admin-section">
                <div class="section-title">
                    <i class="ri-information-line"></i>
                    Informações do Sistema
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Versão</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1e293b;">1.0.0</div>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">PHP Version</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1e293b;"><?php echo phpversion(); ?></div>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Servidor</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1e293b;"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></div>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Data e Hora</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1e293b;"><?php echo date('d/m/Y H:i:s'); ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <link rel="stylesheet" href="admin-styles.css">
</body>
</html>
