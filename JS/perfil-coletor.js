// Dados do usuário logado (esses valores serão definidos no HTML)
let usuarioLogado = false;
let tipoUsuario = null;

// Função para gerar botão de solicitar coleta baseado no tipo de usuário
function gerarBotaoSolicitarColeta(idColetor, nomeColetorEscapado) {
    if (!usuarioLogado) {
        // Usuário não logado - redireciona para cadastro de gerador
        return `
            <button type="button" class="btn-solicitar-coleta" onclick="window.location.href='cadastros.php'">
                <i class="ri-add-circle-line"></i> Solicitar Coleta
            </button>
        `;
    } else if (tipoUsuario === 'coletor') {
        // Usuário é coletor - botão disabled
        return `
            <button type="button" class="btn-solicitar-coleta btn-disabled" disabled title="Coletores não podem solicitar coletas">
                <i class="ri-lock-line"></i> Apenas Geradores
            </button>
        `;
    } else if (tipoUsuario === 'gerador') {
        // Usuário é gerador - botão normal
        return `
            <button type="button" class="btn-solicitar-coleta" onclick="irParaSolicitarColeta(${idColetor})">
                <i class="ri-add-circle-line"></i> Solicitar Coleta
            </button>
        `;
    } else {
        return '';
    }
}

// Função para redirecionar para solicitar coleta com ID do coletor
function irParaSolicitarColeta(idColetor) {
    // Armazena o ID do coletor em sessionStorage
    sessionStorage.setItem('idColetorSelecionado', idColetor);
    // Redireciona para página de solicitação
    window.location.href = 'PAGINAS_GERADOR/solicitar_coleta.php';
}

// Função para abrir modal do perfil do coletor
function abrirPerfilColetor(idColetor) {
    const modal = document.getElementById('modalPerfilColetor');
    const conteudo = document.getElementById('perfilColetorConteudo');
    
    if (!modal || !conteudo) {
        console.error('Modal ou conteúdo não encontrado');
        return;
    }

    modal.classList.add('show');

    fetch(`api/get_perfil_coletor.php?id=${idColetor}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                const coletor = data.coletor;
                const estrelas = Array(5).fill('<i class="ri-star-fill"></i>').slice(0, Math.round(coletor.avaliacao_media)).join('');
                const estrelasBrancas = Array(5 - Math.round(coletor.avaliacao_media)).fill('<i class="ri-star-line"></i>').join('');

                const tipoTransporte = {
                    'carro': '🚗 Carro',
                    'moto': '🏍️ Motocicleta',
                    'bicicleta': '🚴 Bicicleta',
                    'carroca': '🚐 Carroça',
                    'a_pe': '🚶 A Pé'
                };

                conteudo.innerHTML = `
                    <div class="perfil-coletor-conteudo">
                        <div class="perfil-header">
                            <img src="${coletor.foto_url ? coletor.foto_url : 'img/avatar-default.png'}" alt="${coletor.nome_completo}" class="perfil-foto" onerror="this.src='img/avatar-default.png'">
                            <div class="perfil-nome">${coletor.nome_completo}</div>
                            <div class="perfil-tipo">${coletor.tipo_coletor === 'pessoa_fisica' ? 'Pessoa Física' : 'Pessoa Jurídica'}</div>
                        </div>

                        <div class="perfil-info-section">
                            <div class="perfil-info-title">
                                <i class="ri-phone-line"></i> Contato
                            </div>
                            <div class="perfil-info-item">
                                <span class="perfil-info-label">Telefone</span>
                                <span class="perfil-info-value">${coletor.telefone}</span>
                            </div>
                            <div class="perfil-info-item">
                                <span class="perfil-info-label">Email</span>
                                <span class="perfil-info-value">${coletor.email}</span>
                            </div>
                        </div>

                        <div class="perfil-info-section">
                            <div class="perfil-info-title">
                                <i class="ri-star-line"></i> Avaliação
                            </div>
                            <div class="perfil-avaliacao">
                                <div class="perfil-stars">${estrelas}${estrelasBrancas}</div>
                                <div style="margin-top: 8px; color: #333; font-weight: 600;">
                                    ${parseFloat(coletor.avaliacao_media).toFixed(1)} / 5.0 (${coletor.total_avaliacoes} avaliações)
                                </div>
                            </div>
                        </div>

                        <div class="perfil-info-section">
                            <div class="perfil-info-title">
                                <i class="ri-truck-line"></i> Meio de Transporte
                            </div>
                            <div class="perfil-transporte">
                                <div class="perfil-transporte-icon">${tipoTransporte[coletor.meio_transporte]?.split(' ')[0] || '🚗'}</div>
                                <div class="perfil-transporte-info">
                                    <div class="perfil-transporte-label">Transporta com</div>
                                    <div class="perfil-transporte-valor">${tipoTransporte[coletor.meio_transporte] || coletor.meio_transporte}</div>
                                </div>
                            </div>
                        </div>

                        <div class="perfil-info-section">
                            <div class="perfil-info-title">
                                <i class="ri-history-line"></i> Estatísticas
                            </div>
                            <div class="perfil-info-item">
                                <span class="perfil-info-label">Total de Coletas</span>
                                <span class="perfil-info-value">${coletor.coletas}</span>
                            </div>
                            <div class="perfil-info-item">
                                <span class="perfil-info-label">Óleo Total Coletado</span>
                                <span class="perfil-info-value">${parseFloat(coletor.total_oleo).toFixed(1)}L</span>
                            </div>
                        </div>

                        <div class="perfil-info-section" style="margin-top: 30px; padding-top: 25px; border-top: 2px solid #f0f0f0;">
                            ${gerarBotaoSolicitarColeta(coletor.id, coletor.nome_completo)}
                        </div>
                    </div>
                `;
            } else {
                conteudo.innerHTML = `<div style="text-align: center; padding: 40px; color: #e74c3c;"><i class="ri-error-warning-line" style="font-size: 48px; display: block; margin-bottom: 15px;"></i><p>${data.mensagem}</p></div>`;
            }
        })
        .catch(error => {
            conteudo.innerHTML = `<div style="text-align: center; padding: 40px; color: #e74c3c;"><i class="ri-error-warning-line" style="font-size: 48px; display: block; margin-bottom: 15px;"></i><p>Erro ao carregar perfil</p></div>`;
        });
}

// Inicializar event listeners do modal
document.addEventListener('DOMContentLoaded', function() {
    const modalClose = document.querySelector('.modal-perfil-close');
    const modal = document.getElementById('modalPerfilColetor');
    
    if (modalClose) {
        modalClose.addEventListener('click', () => {
            if (modal) {
                modal.classList.remove('show');
            }
        });
    }
    
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    }
});
