<?php
session_start();
require_once '../BANCO/conexao.php';

// Verificar se o usuário está logado como coletor
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'coletor') {
    header('Location: ../index.php');
    exit;
}

// Obter ID da coleta da URL
$id_coleta = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id_coleta) {
    header('Location: solicitacoes.php');
    exit;
}

// Buscar detalhes da solicitação
$sql = "SELECT c.*, 
               g.nome_completo as nome_gerador, 
               g.telefone as telefone_gerador,
               g.email as email_gerador,
               g.cpf as cpf_gerador,
               e.rua as gerador_rua,
               e.numero as gerador_numero,
               e.complemento as gerador_complemento,
               e.bairro as gerador_bairro,
               e.cidade as gerador_cidade,
               e.estado as gerador_estado
        FROM coletas c
        JOIN geradores g ON c.id_gerador = g.id
        LEFT JOIN enderecos e ON g.id_endereco = e.id
        WHERE c.id = :id";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id_coleta, PDO::PARAM_INT);
$stmt->execute();
$coleta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$coleta) {
    header('Location: solicitacoes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Solicitação - Coletor</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --cor-primaria: linear-gradient(to right, #223e2a, #2d5238, #386043, #386043, #386043);
            --cor-secondaria: #ffce46;
            --cor-texto-primaria: #4c4c4c;
            --background: #f8f4e7;
            --verde-escuro: #223e2a;
            --cor-borda: #e0e0e0;
            --cor-branco: #fff;
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: var(--cor-primaria);
            color: var(--cor-texto-primaria);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background-color: var(--background);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: var(--verde-escuro);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #666;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-card {
            background-color: var(--background);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .detail-card h3 {
            color: var(--verde-escuro);
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-card h3 i {
            color: var(--cor-secondaria);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--cor-borda);
        }

        .detail-row:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: var(--verde-escuro);
            width: 40%;
        }

        .detail-value {
            color: var(--cor-texto-primaria);
            text-align: right;
            width: 60%;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #223e2a, #2d5238);
            color: var(--cor-branco);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 62, 42, 0.3);
        }

        .btn-secondary {
            background-color: var(--cor-borda);
            color: var(--cor-texto-primaria);
        }

        .btn-secondary:hover {
            background-color: #d0d0d0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-agendada {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .map-container {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1rem;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .info-completa {
            background-color: var(--background);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .info-completa p {
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .info-completa p:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .detail-row {
                flex-direction: column;
                text-align: left;
            }

            .detail-label,
            .detail-value {
                width: 100%;
                text-align: left;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>Detalhes da Solicitação #<?php echo $coleta['id']; ?></h1>
                    <p>Gerador: <?php echo htmlspecialchars($coleta['nome_gerador']); ?></p>
                </div>
                <span class="status-badge status-<?php echo strtolower($coleta['status']); ?>">
                    <?php echo ucfirst($coleta['status']); ?>
                </span>
            </div>
        </div>

        <!-- Informações Principais -->
        <div class="details-grid">
            <!-- Card do Gerador -->
            <div class="detail-card">
                <h3><i class="ri-user-line"></i> Informações do Gerador</h3>
                <div class="detail-row">
                    <span class="detail-label">Nome:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['nome_gerador']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Telefone:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['telefone_gerador']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['email_gerador']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">CPF:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['cpf_gerador']); ?></span>
                </div>
            </div>

            <!-- Card da Coleta -->
            <div class="detail-card">
                <h3><i class="ri-droplet-line"></i> Informações da Coleta</h3>
                <div class="detail-row">
                    <span class="detail-label">Volume:</span>
                    <span class="detail-value"><?php echo number_format($coleta['quantidade_oleo'], 2, ',', '.'); ?> L</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Data Preferencial:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($coleta['data_coleta'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Período:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['periodo']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipo:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(ucfirst($coleta['tipo_coleta'])); ?></span>
                </div>
            </div>

            <!-- Card do Endereço -->
            <div class="detail-card">
                <h3><i class="ri-map-pin-line"></i> Local da Coleta</h3>
                <div class="detail-row">
                    <span class="detail-label">CEP:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['cep']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Endereço:</span>
                    <span class="detail-value">
                        <?php echo htmlspecialchars($coleta['rua']); ?>, 
                        <?php echo htmlspecialchars($coleta['numero']); ?>
                        <?php if ($coleta['complemento']): echo ' - ' . htmlspecialchars($coleta['complemento']); endif; ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bairro:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['bairro']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Cidade:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($coleta['cidade']); ?></span>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <?php if ($coleta['observacoes']): ?>
            <div class="info-completa">
                <h3><i class="ri-chat-3-line"></i> Observações</h3>
                <p><?php echo htmlspecialchars($coleta['observacoes']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Mapa (se houver coordenadas) -->
        <?php if ($coleta['latitude'] && $coleta['longitude']): ?>
            <div class="info-completa">
                <h3><i class="ri-map-2-line"></i> Localização no Mapa</h3>
                <div class="map-container">
                    <div id="map"></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Botões de Ação -->
        <?php if ($coleta['status'] === 'pendente'): ?>
            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="window.location.href='solicitacoes.php'">
                    <i class="ri-arrow-left-line"></i> Voltar
                </button>
                <button class="btn btn-primary" onclick="aceitarSolicitacao(<?php echo $coleta['id']; ?>, '<?php echo htmlspecialchars($coleta['nome_gerador']); ?>')">
                    <i class="ri-check-line"></i> Aceitar Solicitação
                </button>
            </div>
        <?php else: ?>
            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="window.location.href='agendamentos.php'">
                    <i class="ri-arrow-left-line"></i> Ver Agendamentos
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($coleta['latitude'] && $coleta['longitude']): ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
        <script>
            function initMap() {
                const location = {
                    lat: parseFloat(<?php echo $coleta['latitude']; ?>),
                    lng: parseFloat(<?php echo $coleta['longitude']; ?>)
                };

                const map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 15,
                    center: location,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false
                });

                new google.maps.Marker({
                    position: location,
                    map: map,
                    title: 'Local da Coleta'
                });
            }

            window.addEventListener('load', initMap);
        </script>
    <?php endif; ?>

    <script>
        function aceitarSolicitacao(id_coleta, nome_gerador) {
            if (confirm(`Aceitar solicitação de coleta de ${nome_gerador}?`)) {
                fetch('processar_aceitar_solicitacao.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id_coleta=' + id_coleta
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Solicitação aceita com sucesso!');
                        window.location.href = 'agendamentos.php';
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao aceitar solicitação');
                });
            }
        }
    </script>

    <script src="../JS/navbar.js"></script>
    <script src="../JS/libras.js"></script>
</body>

</html>
