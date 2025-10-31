<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Ecoleta</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/gerador-perfil.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                        <a href="#" class="nav-link">
                            <i class="ri-user-line"></i>
                            <span>Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="solicitar_coleta.php" class="nav-link">
                            <i class="ri-add-circle-line"></i>
                            <span>Solicitar Coleta</span>
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
            <!-- Cabeçalho do Perfil -->
            <div class="profile-header">
                <div class="profile-cover">
                    <div class="profile-photo-container">
                        <img src="../img/profile-placeholder.jpg" alt="Foto de Perfil" id="profilePhoto" class="profile-photo">
                        <button class="change-photo-btn" onclick="document.getElementById('photoInput').click()">
                            <i class="ri-camera-line"></i>
                        </button>
                        <input type="file" id="photoInput" hidden accept="image/*" onchange="updateProfilePhoto(this)">
                    </div>
                </div>
                <div class="profile-info">
                    <h1><?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Nome do Usuário'; ?></h1>
                    <p class="user-type">Gerador de Óleo</p>
                </div>
            </div>

            <!-- Seções do Perfil -->
            <div class="profile-sections">
                <!-- Informações Pessoais -->
                <section class="profile-section">
                    <h2>Informações Pessoais</h2>
                    <form class="profile-form" id="personalInfoForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome">Nome Completo</label>
                                <input type="text" id="nome" name="nome" value="<?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" value="<?php echo isset($_SESSION['telefone']) ? $_SESSION['telefone'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="cpf">CPF</label>
                                <input type="text" id="cpf" name="cpf" value="<?php echo isset($_SESSION['cpf']) ? $_SESSION['cpf'] : ''; ?>" required readonly>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Endereço Padrão -->
                <section class="profile-section">
                    <h2>Endereço Padrão</h2>
                    <form class="profile-form" id="addressForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cep">CEP</label>
                                <input type="text" id="cep" name="cep" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" name="rua" required>
                            </div>
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input type="text" id="complemento" name="complemento">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" name="bairro" required>
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" required>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Estatísticas -->
                <section class="profile-section">
                    <h2>Suas Estatísticas</h2>
                    <div class="statistics-grid">
                        <div class="stat-card">
                            <i class="ri-oil-line"></i>
                            <div class="stat-info">
                                <span class="stat-value">25L</span>
                                <span class="stat-label">Óleo Reciclado</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="ri-recycle-line"></i>
                            <div class="stat-info">
                                <span class="stat-value">12</span>
                                <span class="stat-label">Coletas Realizadas</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="ri-water-flash-line"></i>
                            <div class="stat-info">
                                <span class="stat-value">500mil L</span>
                                <span class="stat-label">Água Preservada</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Botões de Ação -->
                <div class="profile-actions">
                    <button type="button" class="btn btn-secondary" onclick="resetForms()">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProfile()">Salvar Alterações</button>
                </div>
            </div>
        </main>
    </div>

    <script src="../JS/navbar.js"></script>
    <script src="../JS/perfil.js"></script>
</body>

</html>