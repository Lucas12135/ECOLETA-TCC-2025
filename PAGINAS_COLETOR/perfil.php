<?php
session_start();
require_once '../BANCO/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Buscar dados do coletor incluindo foto
$id_coletor = $_SESSION['id_usuario'];
$sql = "SELECT c.id, c.nome_completo, c.email, c.telefone, c.cpf_cnpj, 
               c.foto_perfil, c.avaliacao_media, c.total_avaliacoes,
               e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.cep
        FROM coletores c
        LEFT JOIN enderecos e ON c.id_endereco = e.id
        WHERE c.id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id_coletor, PDO::PARAM_INT);
$stmt->execute();
$coletor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$coletor) {
    header('Location: ../index.php');
    exit;
}

// Buscar estatísticas de coleta do coletor
$sql_stats = "SELECT 
                COUNT(hc.id) as total_coletas,
                COALESCE(SUM(hc.quantidade_coletada), 0) as total_oleo,
                COALESCE(AVG((ac.pontualidade + ac.profissionalismo + ac.qualidade_servico) / 3), 0) as avaliacao_media,
                COUNT(DISTINCT ac.id) as total_avaliacoes
              FROM historico_coletas hc
              LEFT JOIN avaliacoes_coletores ac ON hc.id = ac.id_historico_coleta
              WHERE hc.id_coletor = :id_coletor AND hc.status = 'concluida'";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bindParam(':id_coletor', $id_coletor, PDO::PARAM_INT);
$stmt_stats->execute();
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// Preparar dados para exibição
$nome_completo = $coletor['nome_completo'] ?? 'Coletor';
$avaliacao_media = !empty($stats['avaliacao_media']) ? $stats['avaliacao_media'] : $coletor['avaliacao_media'] ?? 0;
$total_avaliacoes = !empty($stats['total_avaliacoes']) ? $stats['total_avaliacoes'] : $coletor['total_avaliacoes'] ?? 0;
$coletas = !empty($stats['total_coletas']) ? $stats['total_coletas'] : 0;
$total_oleo = !empty($stats['total_oleo']) ? $stats['total_oleo'] : 0;

// URL da foto
$foto_url = $coletor['foto_perfil'] ? '../uploads/profile_photos/' . $coletor['foto_perfil'] : '../img/profile-placeholder.jpg';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/perfil.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Navbar -->
        <header class="sidebar">
            <div class="sidebar-header">
                <div class="logo-placeholder">
                    <img src="../img/logo.png" alt="Logo Ecoleta" class="logo">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>

            <button class="menu-mobile-button" onclick="toggleMobileMenu()">
                <i class="ri-menu-line"></i>
            </button>

            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="home.php" class="nav-link">
                            <i class="ri-home-4-line"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="perfil.php" class="nav-link">
                            <i class="ri-user-line"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="agendamentos.php" class="nav-link">
                            <i class="ri-calendar-line"></i>
                            <span>Agendamentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="historico.php" class="nav-link">
                            <i class="ri-history-line"></i>
                            <span>Histórico</span>
                        </a>
                    </li>
                    <li>
                        <a href="configuracoes.php" class="nav-link">
                            <i class="ri-settings-3-line"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                    <li>
                        <a href="suporte.php" class="nav-link">
                            <i class="ri-customer-service-2-line"></i>
                            <span>Suporte</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Página do perfil</h1>
                    <p>Confira suas informações e configurações do seu perfil.</p>
                </div>
                <div class="header-actions">
                    <div class="action-buttons">
                        <button class="notification-btn" title="Notificações">
                            <i class="ri-notification-3-line"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <!-- Popup de Notificações -->
                        <div class="notifications-popup">
                            <div class="notifications-header">
                                <h3>Notificações</h3>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-calendar-check-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Nova coleta agendada para hoje às 14:30</p>
                                            <span class="notification-time">Há 5 minutos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-map-pin-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Alteração no endereço de coleta - Rua das Palmeiras, 789</p>
                                            <span class="notification-time">Há 30 minutos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-message-3-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Mensagem do gerador sobre a coleta #123</p>
                                            <span class="notification-time">Há 1 hora</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="profile-content">

                </header>

                <div class="profile-content">
                    <!-- Cabeçalho do Perfil -->
                    <div class="profile-header">
                        <div class="profile-info">
                            <div class="profile-photo">
                                <img src="<?php echo htmlspecialchars($foto_url); ?>" alt="Foto do perfil">
                            </div>
                            <div class="profile-text">
                                <h2 class="profile-name"><?php echo htmlspecialchars($nome_completo); ?></h2>
                                <div class="rating">
                                    <?php 
                                        // Exibir estrelas baseado na avaliação
                                        $estrelas_cheias = floor($avaliacao_media);
                                        $meia_estrela = ($avaliacao_media - $estrelas_cheias) >= 0.5;
                                        
                                        for ($i = 0; $i < $estrelas_cheias; $i++) {
                                            echo '<i class="ri-star-fill star"></i>';
                                        }
                                        if ($meia_estrela) {
                                            echo '<i class="ri-star-half-fill star"></i>';
                                            $i++;
                                        }
                                        while ($i < 5) {
                                            echo '<i class="ri-star-line star"></i>';
                                            $i++;
                                        }
                                    ?>
                                    <span style="color: var(--cor-branco); margin-left: 0.5rem;"><?php echo number_format($avaliacao_media, 1); ?></span>
                                </div>
                            </div>
                        </div>
                        <button class="btn-edit-profile">
                            <i class="ri-edit-line"></i>
                            Editar Perfil
                        </button>
                    </div>

                    <!-- Estatísticas do Perfil -->
                    <div class="profile-stats">
                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="ri-oil-line"></i>
                                Total de Óleo Coletado
                            </div>
                            <div class="stat-value"><?php echo number_format($total_oleo, 2); ?></div>
                            <div class="stat-label">litros</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="ri-calendar-check-line"></i>
                                Coletas Realizadas
                            </div>
                            <div class="stat-value"><?php echo $coletas; ?></div>
                            <div class="stat-label">coletas</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <i class="ri-star-smile-line"></i>
                                Avaliação Média
                            </div>
                            <div class="rating" style="margin-top: 0.5rem;">
                                <?php 
                                        // Exibir estrelas baseado na avaliação
                                        $estrelas_cheias = floor($avaliacao_media);
                                        $meia_estrela = ($avaliacao_media - $estrelas_cheias) >= 0.5;
                                        
                                        for ($i = 0; $i < $estrelas_cheias; $i++) {
                                            echo '<i class="ri-star-fill star"></i>';
                                        }
                                        if ($meia_estrela) {
                                            echo '<i class="ri-star-half-fill star"></i>';
                                            $i++;
                                        }
                                        while ($i < 5) {
                                            echo '<i class="ri-star-line star"></i>';
                                            $i++;
                                        }
                                    ?>
                            </div>
                            <div class="stat-label">baseado em <?php echo $total_avaliacoes; ?> avaliações</div>
                        </div>
                    </div>
                </div>
        </main>
    </div>
    <div class="right">
      <div class="accessibility-button" onclick="toggleAccessibility(event)" title="Ferramentas de Acessibilidade">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="25" height="25" fill="white">
          <title>accessibility</title>
          <g>
            <circle cx="24" cy="7" r="4" />
            <path d="M40,13H8a2,2,0,0,0,0,4H19.9V27L15.1,42.4a2,2,0,0,0,1.3,2.5H17a2,2,0,0,0,1.9-1.4L23.8,28h.4l4.9,15.6A2,2,0,0,0,31,45h.6a2,2,0,0,0,1.3-2.5L28.1,27V17H40a2,2,0,0,0,0-4Z" />
          </g>
        </svg>
      </div>
<div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>


    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script src="../JS/navbar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gerenciar notificações
            const notificationBtn = document.querySelector('.notification-btn');
            const notificationsPopup = document.querySelector('.notifications-popup');

            document.addEventListener('click', function(event) {
                const isClickInsidePopup = notificationsPopup.contains(event.target);
                const isClickOnButton = notificationBtn.contains(event.target);

                if (!isClickInsidePopup && !isClickOnButton) {
                    notificationsPopup.classList.remove('show');
                }
            });

            notificationBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                notificationsPopup.classList.toggle('show');
            });

            // Botão de editar perfil
            const editBtn = document.querySelector('.btn-edit-profile');
            editBtn.addEventListener('click', function() {
                // Aqui você pode adicionar a lógica para editar o perfil
                alert('Funcionalidade de edição será implementada!');
            });
        });
    </script>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/libras.js"></script>
</body>

</html>