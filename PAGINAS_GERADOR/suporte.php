<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte - Gerador</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../CSS/home-gerador.css">
    <link rel="stylesheet" href="../CSS/gerador-suporte.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/libras.css">
    <link rel="stylesheet" href="../CSS/acessibilidade.css">
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
                    <li>
                        <a href="perfil.php" class="nav-link">
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
                    <li class="active">
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
                    <h1>Central de Suporte</h1>
                    <p>Encontre ajuda ou entre em contato com nossa equipe.</p>
                </div>
                <div class="header-actions">
                </div>
            </header>
            <div class="support-content">
                <!-- Seção FAQ -->
                <div class="faq-section">
                    <h2 class="section-title"><i class="ri-question-line"></i> Perguntas Frequentes</h2>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como solicitar uma coleta de óleo? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            Acesse a seção "Solicitar Coleta" no menu lateral, preencha as informações sobre quantidade de óleo, endereço e data/horário preferencial. Um coletor será designado para atendê-lo assim que possível.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como devo armazenar o óleo até a coleta? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            Armazene o óleo usado em garrafas PET transparentes bem vedadas. Nunca misture o óleo com água ou outros líquidos. Mantenha em local fresco e protegido da luz solar direta até o dia da coleta.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Posso agendar coletas regulares? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            Sim! Na seção "Solicitar Coleta", você pode configurar coletas periódicas (semanal, quinzenal ou mensal). Isso é ideal para estabelecimentos comerciais que geram óleo com frequência.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Qual a quantidade mínima de óleo para coleta? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            Coletamos a partir de 1 litro de óleo usado. Para quantidades menores, recomendamos armazenar até acumular ao menos essa quantidade para tornar a coleta mais eficiente.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como cancelar uma coleta agendada? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            No "Histórico", localize a coleta agendada e clique no botão de cancelamento. Pedimos que cancele com pelo menos 2 horas de antecedência para não prejudicar o planejamento do coletor.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Recebo alguma compensação pela doação do óleo? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            O serviço de coleta é gratuito e você estará contribuindo para o meio ambiente. Alguns coletores podem oferecer brindes ou pontos de fidelidade, mas o principal benefício é a destinação correta do óleo usado.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como avaliar o coletor após a coleta? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            Após cada coleta concluída, você receberá uma notificação para avaliar o serviço do coletor. Você pode dar uma nota de 1 a 5 estrelas e deixar um comentário opcional sobre a experiência.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            O que acontece com o óleo coletado? <i class="ri-arrow-down-s-line"></i>
                        </div>
                        <div class="faq-answer">
                            O óleo coletado é encaminhado para empresas especializadas que fazem a reciclagem. Ele pode ser transformado em biodiesel, sabão, tintas e outros produtos, evitando a contaminação de água e solo.
                        </div>
                    </div>
                </div>

                <!-- Seção de Contato -->
                <div class="contact-section">
                    <!-- Métodos de Contato -->
                    <div class="contact-card">
                        <h2 class="section-title"><i class="ri-contacts-line"></i> Formas de Contato</h2>
                        <div class="contact-methods">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="ri-mail-line"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">E-mail</span>
                                    <span class="contact-value">suporte@ecoleta.com.br</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="ri-whatsapp-line"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">WhatsApp</span>
                                    <span class="contact-value">(11) 98765-4321</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="ri-phone-line"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Telefone</span>
                                    <span class="contact-value">(11) 3456-7890</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dicas Importantes -->
                    <div class="contact-card tips-card">
                        <h2 class="section-title"><i class="ri-lightbulb-line"></i> Dicas Importantes</h2>
                        <div class="tips-list">
                            <div class="tip-item">
                                <i class="ri-check-line"></i>
                                <span>Nunca descarte óleo na pia ou ralo</span>
                            </div>
                            <div class="tip-item">
                                <i class="ri-check-line"></i>
                                <span>Use garrafas PET transparentes</span>
                            </div>
                            <div class="tip-item">
                                <i class="ri-check-line"></i>
                                <span>Deixe o óleo esfriar antes de armazenar</span>
                            </div>
                            <div class="tip-item">
                                <i class="ri-check-line"></i>
                                <span>Mantenha as garrafas bem vedadas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Contato -->
                    <div class="contact-card">
                        <h2 class="section-title"><i class="ri-message-3-line"></i> Envie sua Mensagem</h2>
                        <form class="contact-form" action="#" method="POST">
                            <div class="form-group">
                                <label for="subject">Assunto</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Selecione um assunto</option>
                                    <option value="duvida-coleta">Dúvida sobre Coleta</option>
                                    <option value="problema-agendamento">Problema no Agendamento</option>
                                    <option value="problema-tecnico">Problema Técnico</option>
                                    <option value="sugestao">Sugestão</option>
                                    <option value="reclamacao">Reclamação</option>
                                    <option value="elogio">Elogio</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="message">Mensagem</label>
                                <textarea id="message" name="message" placeholder="Descreva sua mensagem em detalhes" required></textarea>
                            </div>
                            <button type="submit" class="submit-btn">Enviar Mensagem</button>
                        </form>
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
        <!-- Painel de Acessibilidade -->
        <div class="accessibility-overlay"></div>
        <div class="accessibility-panel">
            <div class="accessibility-header">
                <h3>Acessibilidade</h3>
                <button class="accessibility-close">×</button>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Tamanho de Texto</div>
                <div class="size-control">
                    <span class="size-label">A</span>
                    <input type="range" class="size-slider" min="50" max="150" value="100">
                    <span class="size-label" style="font-weight: bold;">A</span>
                    <span class="size-value">100%</span>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Visão</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <select id="contrast-level">
                            <option value="none">Sem Contraste</option>
                            <option value="wcag-aa">Contraste WCAG AA</option>
                        </select>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="inverted-mode">
                        <span>Modo Invertido</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="reading-guide">
                        <span>Linha Guia de Leitura</span>
                    </label>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Fonte</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="sans-serif">
                        <span>Fonte Sem Serifa</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="dyslexia-font">
                        <span>Fonte Dislexia</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="monospace-font">
                        <span>Fonte Monoespacida</span>
                    </label>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Espaçamento</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="increased-spacing">
                        <span>Aumentar Espaçamento</span>
                    </label>
                </div>
            </div>
            <div class="accessibility-group">
                <div class="accessibility-group-title">Navegação</div>
                <div class="accessibility-options">
                    <label class="accessibility-option">
                        <input type="checkbox" id="expanded-focus">
                        <span>Foco Expandido</span>
                    </label>
                    <label class="accessibility-option">
                        <input type="checkbox" id="large-cursor">
                        <span>Cursor Maior</span>
                    </label>
                </div>
            </div>
            <button class="accessibility-reset-btn">Restaurar Padrões</button>
        </div>
        <!-- Botão de Libras Separado -->
        <div class="libras-button" id="librasButton" onclick="toggleAccessibility(event)" title="Libras">
            👋
        </div>
        <div vw class="enabled">
            <div vw-access-button class="active"></div>
            <div vw-plugin-wrapper>
                <div class="vw-plugin-top-wrapper"></div>
            </div>
        </div>


        <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
        <script>
            // Script para o FAQ
            document.querySelectorAll('.faq-question').forEach(question => {
                question.addEventListener('click', () => {
                    const faqItem = question.parentElement;
                    faqItem.classList.toggle('expanded');
                });
            });
        </script>

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
        <script src="../JS/home-gerador.js"></script>
        <script src="../JS/navbar.js"></script>
        <script src="../JS/acessibilidade.js"></script><script src="../JS/libras.js"></script>
</body>

</html>


