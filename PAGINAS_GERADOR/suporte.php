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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Biblioteca de ícones -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Barra Lateral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-placeholder">
                    <img src="../img/logo.png" alt="Logo Ecoleta" class="logo">
                </div>
                <span class="logo-text">Ecoleta</span>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-link">
                        <a href="../index.php" class="nav-link">
                            <i class="ri-arrow-left-line"></i>
                            <span>Voltar</span>
                        </a>
                    </li>
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
                        <a href="solicitar-coleta.php" class="nav-link">
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
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="welcome-message">
                    <h1>Central de Suporte</h1>
                    <p>Encontre ajuda ou entre em contato com nossa equipe.</p>
                </div>
                <div class="header-actions">
                    <div class="action-buttons">
                        <button class="notification-btn" title="Notificações">
                            <i class="ri-notification-3-line"></i>
                            <span class="notification-badge">2</span>
                        </button>
                        <!-- Popup de Notificações -->
                        <div class="notifications-popup">
                            <div class="notifications-header">
                                <h3>Notificações</h3>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-check-double-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Sua coleta foi confirmada para amanhã às 10:00</p>
                                            <span class="notification-time">Há 15 minutos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-content">
                                        <i class="ri-truck-line notification-icon"></i>
                                        <div class="notification-text">
                                            <p>Coletor a caminho do seu endereço</p>
                                            <span class="notification-time">Há 1 hora</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <script>
        // Script para o FAQ
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                faqItem.classList.toggle('expanded');
            });
        });

        // Notificações
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
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U"></script>
    <script src="../JS/home-gerador.js"></script>
</body>

</html>