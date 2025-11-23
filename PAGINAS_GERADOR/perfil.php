<?php
session_start();

/* ==============================================
   1) Normalização das variáveis de sessão
   ============================================== */
if (isset($_SESSION['id_usuario']) && !isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = $_SESSION['id_usuario'];
}
if (isset($_SESSION['nome_usuario']) && !isset($_SESSION['nome'])) {
    $_SESSION['nome'] = $_SESSION['nome_usuario'];
}
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] !== 'gerador') {
    // Opcional: bloquear acesso se não for gerador
    // header('Location: ../index.php'); exit();
}

/* ==============================================
   2) Verificação de login
   ============================================== */
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../LOGINS/login-gerador/login.php');
    exit();
}

$usuarioId = (int) $_SESSION['usuario_id'];

/* ==============================================
   3) Conexão com o banco
   ============================================== */
$db = null;
try {
    // Tente primeiro a conexão usada nos logins
    if (file_exists('../BANCO/conexao.php')) {
        include_once('../BANCO/conexao.php'); // deve definir $conn (PDO)
        if (isset($conn) && $conn instanceof PDO) {
            $db = $conn;
        }
    }

    // Alternativa: conexão em outro caminho/padrão
    if (!$db && file_exists('../config/database.php')) {
        include_once('../config/database.php'); // deve definir $pdo (PDO)
        if (isset($pdo) && $pdo instanceof PDO) {
            $db = $pdo;
        }
    }

    if (!$db) {
        throw new Exception('Conexão com o banco não encontrada.');
    }

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* ==============================================
       4) Carregar dados do gerador no banco
       Campos comuns: id, nome_completo, email, telefone, cpf,
       endereço(s) se houver no mesmo registro (ajuste conforme schema)
       ============================================== */
    $stmt = $db->prepare("
        SELECT 
            g.id,
            g.nome_completo,
            g.email,
            g.telefone,
            g.cpf,
            g.foto_perfil,
            g.id_endereco,
            e.rua,
            e.numero,
            e.complemento,
            e.bairro,
            e.cidade,
            e.cep
        FROM geradores g
        LEFT JOIN enderecos e ON g.id_endereco = e.id
        WHERE g.id = :id
        LIMIT 1
    ");
    $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();
    $gerador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$gerador) {
        // fallback mínimo para não explodir a tela
        $gerador = [
            'id'            => $usuarioId,
            'nome_completo' => $_SESSION['nome'] ?? 'Nome do Usuário',
            'email'         => $_SESSION['email'] ?? '',
            'telefone'      => $_SESSION['telefone'] ?? '',
            'cpf'           => $_SESSION['cpf'] ?? '',
            'cep'           => '',
            'rua'           => '',
            'numero'        => '',
            'complemento'   => '',
            'bairro'        => '',
            'cidade'        => '',
            'foto_perfil'   => null,
        ];
    }
} catch (Throwable $e) {
    // Em produção, logue o erro
    // error_log($e->getMessage());
    //$erroConexao = $e->getMessage();
    // fallback para evitar white screen
    $gerador = [
        'id'            => $usuarioId,
        'nome_completo' => $_SESSION['nome'] ?? 'Nome do Usuário',
        'email'         => $_SESSION['email'] ?? '',
        'telefone'      => $_SESSION['telefone'] ?? '',
        'cpf'           => $_SESSION['cpf'] ?? '',
        'cep'           => '',
        'rua'           => '',
        'numero'        => '',
        'complemento'   => '',
        'bairro'        => '',
        'cidade'        => '',
        'foto_perfil'   => null,
    ];
}

function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
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
    <link rel="stylesheet" href="../CSS/libras.css">
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
                        <?php
                        $foto = $gerador['foto_perfil'] ? '../uploads/profile_photos/' . htmlspecialchars($gerador['foto_perfil']) : '../img/profile-placeholder.jpg';
                        ?>
                        <img src="<?= e($foto) ?>" alt="Foto de Perfil" id="profilePhoto" class="profile-photo">
                    </div>
                </div>
                <div class="profile-info">
                    <h1><?= e($gerador['nome_completo'] ?? 'Nome do Usuário') ?></h1>
                    <p class="user-type">Produtor de Óleo usado</p>
                    <?php if (!empty($erroConexao ?? '')): ?>
                        <div class="input-error" style="margin-top:.5rem;"><?= e($erroConexao) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Seções do Perfil -->
            <div class="profile-sections">
                <!-- Estatísticas (placeholder estático) -->
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
                                <span class="stat-value">500 mil L</span>
                                <span class="stat-label">Água Preservada</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Ações -->
                <div class="profile-actions">
                    <!-- No modo de visualização, apenas botões utilitários -->
                    <button class="btn-edit-profile">
                        <i class="ri-edit-line"></i>
                        Editar Perfil
                    </button>
                    <a href="../logout.php" class="btn btn-secondary">Sair</a>
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
    </div>

    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <button class="close-modal-btn">&times;</button>
            <h2>Editar Perfil</h2>

            <form id="editProfileForm">
                <!-- Preview e Input de Foto -->
                <div class="form-group">
                    <label>Foto de Perfil</label>
                    <div class="foto-preview-container">
                        <img id="previewFoto" src="<?php echo htmlspecialchars($foto); ?>" alt="Preview da foto" class="foto-preview">
                    </div>
                    <input type="file" id="fotoInput" accept="image/*" style="margin-top: 10px;">
                    <small style="display: block; margin-top: 5px; color: #666;">Formatos aceitos: JPG, PNG (máx. 2MB)</small>
                </div>

                <!-- Nome Completo -->
                <div class="form-group">
                    <label for="nomePerfil">Nome Completo</label>
                    <input type="text" id="nomePerfil" value="<?php echo htmlspecialchars($_SESSION['nome']); ?>" required>
                </div>

                <!-- Botões -->
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('editProfileModal').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn-save">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Botão de editar perfil
            const editBtn = document.querySelector('.btn-edit-profile');
            const editModal = document.getElementById('editProfileModal');
            const closeModalBtn = document.querySelector('.close-modal-btn');
            const editForm = document.getElementById('editProfileForm');
            const previewFoto = document.getElementById('previewFoto');
            const fotoInput = document.getElementById('fotoInput');

            // Abrir modal
            editBtn.addEventListener('click', function() {
                editModal.style.display = 'block';
            });

            // Fechar modal
            closeModalBtn.addEventListener('click', function() {
                editModal.style.display = 'none';
            });

            // Fechar modal ao clicar fora
            window.addEventListener('click', function(event) {
                if (event.target === editModal) {
                    editModal.style.display = 'none';
                }
            });

            // Preview da foto antes de salvar
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewFoto.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Salvar alterações
            editForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData();
                formData.append('nome_completo', document.getElementById('nomePerfil').value);

                // Se uma nova foto foi selecionada
                if (fotoInput.files.length > 0) {
                    formData.append('foto', fotoInput.files[0]);
                }

                try {
                    const response = await fetch('../BANCO/update_profile.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Perfil atualizado com sucesso!');
                        editModal.style.display = 'none';
                        // Recarregar página para mostrar as alterações
                        location.reload();
                    } else {
                        alert('Erro ao atualizar perfil: ' + data.message);
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao atualizar perfil');
                }
            });
        });
        
        // Stubs dos seus scripts
        function toggleMobileMenu() {
            document.body.classList.toggle('menu-open');
        }

        function updateProfilePhoto(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('profilePhoto').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
            // Obs.: upload real deve ser feito via form + PHP
        }
    </script>
    <script src="../JS/navbar.js"></script>
    <script src="../JS/perfil.js"></script>
    <script src="../JS/libras.js"></script>
</body>

</html>