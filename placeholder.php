<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Coletor - Ecoleta</title>
    <!-- Carregando a fonte (Inter) que usamos no outro arquivo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* 1. Configurações Globais (Reset Básico e Cores) */
        :root {
            /* Cores do index.html */
            --cor-amarelo-escuro: #F59E0B;
            --cor-texto: #333;
            --cor-branco: #FFFFFF;
            --cor-borda: #E0E0E0;
            --cor-footer-verde: #386641; /* Verde escuro do rodapé e header */

            /* Novas Cores para esta página */
            --cor-fundo-creme: #F8F4E7; /* Fundo do card de perfil */
            --cor-cinza-botoes: #D9E0E2; /* Cor dos botões (Ajuda, Histórico) */
            --cor-cinza-botoes-hover: #C5CDD0;
            --cor-verde-verificado: #66BB6A;
            --cor-verde-verificado-fundo: #E8F5E9;
            --cor-verde-avaliado: #4CAF50;
            --cor-verde-avaliado-fundo: #E8F5E9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: var(--cor-footer-verde); /* Fundo verde escuro */
            color: var(--cor-texto);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* 2. Estilos do Novo Cabeçalho (Header) */
        .header-perfil {
            background-color: var(--cor-footer-verde);
            color: var(--cor-branco);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-perfil .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-logo-perfil {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .header-logo-img {
            width: 45px;
            height: 45px;
            /* Simulação do logo */
            background-color: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--cor-footer-verde);
        }

        .header-nav-perfil {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-nav {
            padding: 8px 24px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid var(--cor-branco);
        }

        .btn-nav.perfil-active {
            background-color: var(--cor-branco);
            color: var(--cor-footer-verde);
        }

        .btn-nav.home {
            background-color: #5a8a62; /* Tom de verde mais claro */
            color: var(--cor-branco);
            border-color: #5a8a62;
        }
        
        .btn-nav:hover {
            opacity: 0.9;
        }

        .menu-hamburguer-perfil {
            width: 30px;
            height: 24px;
            display: none; /* Escondido em desktop */
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
        }

        .menu-hamburguer-perfil span {
            display: block;
            width: 100%;
            height: 4px;
            background-color: var(--cor-branco);
            border-radius: 2px;
        }
        
        /* 3. Estilos do Conteúdo Principal (Perfil) */
        .main-perfil {
            padding: 40px 20px;
        }

        .perfil-card {
            background-color: var(--cor-fundo-creme);
            border-radius: 15px;
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            
            display: grid;
            grid-template-columns: 1fr 220px; /* Coluna da info | Coluna da foto */
            gap: 30px;
            align-items: flex-start;
        }

        /* 4. Coluna da Esquerda (Informações) */
        .perfil-info h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--cor-texto);
            line-height: 1.2;
        }
        
        .perfil-badges {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        
        .badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .badge.avaliado {
            background-color: var(--cor-verde-avaliado-fundo);
            color: var(--cor-verde-avaliado);
        }
        
        .badge.verificado {
            background-color: var(--cor-verde-verificado-fundo);
            color: var(--cor-verde-verificado);
        }
        
        .botoes-acao {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 25px 0;
        }

        .btn-acao {
            background-color: var(--cor-cinza-botoes);
            color: var(--cor-texto);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-acao:hover {
            background-color: var(--cor-cinza-botoes-hover);
        }

        .btn-acao svg {
            width: 24px;
            height: 24px;
            margin-bottom: 8px;
            fill: var(--cor-texto);
        }
        
        .info-oleo {
            background-color: var(--cor-cinza-botoes);
            border-radius: 12px;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }
        
        .oleo-icone {
            width: 35px;
            height: 35px;
            background-color: var(--cor-amarelo-escuro);
            border: 3px solid var(--cor-branco);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .oleo-icone svg {
            width: 18px;
            height: 18px;
            fill: var(--cor-branco);
        }

        /* 5. Coluna da Direita (Foto) */
        .perfil-foto {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .foto-container {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            border: 6px solid var(--cor-branco);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .foto-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-editar-perfil {
            background-color: var(--cor-footer-verde);
            color: var(--cor-branco);
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-editar-perfil:hover {
            background-color: #4a7d52;
        }

        /* 7. Responsividade */
        @media (max-width: 768px) {
            .header-nav-perfil .btn-nav {
                display: none; /* Esconde botões de desktop */
            }
            .menu-hamburguer-perfil {
                display: flex; /* Mostra menu hamburguer */
            }

            .perfil-card {
                grid-template-columns: 1fr; /* Coluna única */
            }
            
            .perfil-foto {
                order: -1; /* Move a foto para cima */
                margin-bottom: 20px;
            }
            
            .perfil-info h1 {
                text-align: center;
                font-size: 2rem;
            }
            
            .perfil-badges {
                justify-content: center;
            }

            .botoes-acao {
                grid-template-columns: 1fr; /* Botões em coluna */
            }

            .footer-container-amarelo {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 30px;
            }

            .footer-links {
                align-items: center;
            }

            .icones-sociais {
                justify-content: center;
            }

            .footer-cta {
                flex-direction: column;
                font-size: 1.25rem;
            }
        }

    </style>
</head>
<body>

    <!-- CABEÇALHO DA PÁGINA DE PERFIL -->
    <header class="header-perfil">
        <div class="container">
            <div class="header-logo-perfil">
                <!-- Usei um emoji como placeholder para o logo -->
                <div class="header-logo-img"><img src="img/logo.png" alt="Logo Reciclagem" class="logo-image"></div>
                <span>Conta da Ecoleta</span>
            </div>

            <nav class="header-nav-perfil">
                <a href="#" class="btn-nav perfil-active">Perfil</a>
                <a href="#" class="btn-nav home">Home</a>
                <div class="menu-hamburguer-perfil" id="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Menu Mobile (links) -->
    <div id="mobile-menu-links">
        <a href="#">Perfil</a>
        <a href="#">Home</a>
    </div>

    <!-- CONTEÚDO PRINCIPAL DO PERFIL -->
    <main class="main-perfil">
        <div class="perfil-card">
            
            <!-- Coluna da Esquerda (Informações) -->
            <section class="perfil-info">
                <h1>Murilo Fontes</h1>

                <div class="perfil-badges">
                    <span class="badge avaliado">
                        <!-- SVG de Estrela -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                        5.0
                    </span>
                    <span class="badge verificado">
                        <!-- SVG de Verificado -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Verificado
                    </span>
                </div>

                <div class="botoes-acao">
                    <a href="#" class="btn-acao">
                        <!-- SVG de Ajuda (?) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-14h2v2h-2V6zm0 4h2v6h-2v-6z"></path></svg>
                        Ajuda
                    </a>
                    <a href="#" class="btn-acao">
                        <!-- SVG de Histórico (Relógio) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2V7zm-1.29 7.71l.71.71 3.54-3.54-.71-.71-2.83 2.83-1.41-1.41-.71.71 2.12 2.12z"></path></svg>
                        Histórico
                    </a>
                    <a href="#" class="btn-acao">
                        <!-- SVG de Mensagens (Envelope) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"></path></svg>
                        Mensagens
                    </a>
                </div>

                <div class="info-oleo">
                    <span>Quantidade de óleo descartada</span>
                    <div class="oleo-icone">
                        <!-- SVG de Gota -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8 0a5.53 5.53 0 0 0-5.5 5.5c0 3.036 3.07 7.55 5.5 10.5 2.43-2.95 5.5-7.464 5.5-10.5A5.53 5.53 0 0 0 8 0z"></path></svg>
                    </div>
                </div>
            </section>

            <!-- Coluna da Direita (Foto) -->
            <section class="perfil-foto">
                <div class="foto-container">
                    <!-- Imagem de placeholder. Troque '...' pelo caminho da imagem real -->
                    <img src="https://placehold.co/200x200/cccccc/333?text=Foto" alt="Foto de Perfil de Murilo Fontes">
                </div>
                <button class="btn-editar-perfil">Editar perfil</button>
            </section>
        </div>
    </main>

    <!-- JavaScript para o menu mobile -->
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            var menuLinks = document.getElementById('mobile-menu-links');
            if (menuLinks.style.display === 'block') {
                menuLinks.style.display = 'none';
            } else {
                menuLinks.style.display = 'block';
            }
        });
    </script>

</body>
</html>
