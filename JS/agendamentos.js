// Variável global para armazenar os mapas inicializados
const maps = {};

// Variável global para armazenar o callback de confirmação
let confirmCallback = null;

// Função para geocodificar endereço
function geocodeAddress(address, callback) {
    const geocoder = new google.maps.Geocoder();
    
    console.log('Geocodificando:', address);
    
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK') {
            console.log('Geocodificação bem-sucedida:', results[0]);
            callback(results[0].geometry.location);
        } else {
            console.error('Erro ao geocodificar endereço:', status, address);
            callback(null);
        }
    });
}

// Função para inicializar mapa de um agendamento
function initMapForSchedule(mapContainer, endereco) {
    const mapId = mapContainer.id;
    
    console.log('Inicializando mapa:', mapId, endereco);
    
    if (maps[mapId]) {
        console.log('Mapa já foi inicializado');
        return;
    }

    // Geocodificar o endereço
    geocodeAddress(endereco, function(location) {
        if (!location) {
            console.log('Usando localização padrão');
            // Localização padrão se não conseguir geocodificar
            location = { lat: -23.550520, lng: -46.633308 };
        }

        console.log('Criando mapa com localização:', location);

        const map = new google.maps.Map(mapContainer, {
            center: location,
            zoom: 15,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false
        });

        new google.maps.Marker({
            position: location,
            map: map,
            title: 'Local da Coleta'
        });

        maps[mapId] = map;
        console.log('Mapa inicializado com sucesso');
    });
}

// Função para mostrar modal de confirmação
function mostrarConfirmacao(titulo, mensagem, callback) {
    document.getElementById('modalConfirmacaoTitulo').textContent = titulo;
    document.getElementById('modalConfirmacaoMensagem').textContent = mensagem;
    confirmCallback = callback;
    document.getElementById('modalConfirmacao').classList.add('show');
}

// Função para mostrar modal de resultado (sucesso/erro)
function mostrarResultado(tipo, titulo, mensagem, recarregar = false) {
    document.getElementById('modalResultadoTitulo').textContent = titulo;
    
    let html = `<div class="modal-resultado-${tipo}">
        <i class="ri-${tipo === 'sucesso' ? 'check-circle' : 'error-warning'}-fill"></i>
        <div>${mensagem}</div>
    </div>`;
    
    document.getElementById('modalResultadoConteudo').innerHTML = html;
    
    const modal = document.getElementById('modalResultado');
    modal.classList.add('show');
    
    // Limpar listeners anteriores
    const btnFechar = document.getElementById('btnFecharResultado');
    const novoBtn = btnFechar.cloneNode(true);
    btnFechar.parentNode.replaceChild(novoBtn, btnFechar);
    
    novoBtn.addEventListener('click', () => {
        modal.classList.remove('show');
        if (recarregar) {
            setTimeout(() => location.reload(), 300);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado');
    
    // Configurar fechamento de modais
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modalId = btn.getAttribute('data-modal') || 'modalConcluir';
            document.getElementById(modalId).classList.remove('show');
        });
    });

    // Fechar modal ao clicar fora
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    });

    // Gerenciar botão de cancelamento modal concluir
    document.getElementById('btnCancelarModal').addEventListener('click', () => {
        document.getElementById('modalConcluir').classList.remove('show');
    });

    // Gerenciar botão de cancelamento confirmação
    document.getElementById('btnCancelarConfirmacao').addEventListener('click', () => {
        document.getElementById('modalConfirmacao').classList.remove('show');
        confirmCallback = null;
    });

    // Gerenciar botão de confirmação
    document.getElementById('btnConfirmarAcao').addEventListener('click', () => {
        document.getElementById('modalConfirmacao').classList.remove('show');
        if (confirmCallback) {
            confirmCallback();
            confirmCallback = null;
        }
    });
    
    // Gerenciar expansão dos itens de agendamento
    const agendamentoItems = document.querySelectorAll('.agendamento-item');
    console.log('Agendamentos encontrados:', agendamentoItems.length);

    agendamentoItems.forEach((item, index) => {
        const header = item.querySelector('.agendamento-header');
        const mapContainer = item.querySelector('.map-container');

        console.log(`Agendamento ${index}:`, {
            temHeader: !!header,
            temMapa: !!mapContainer,
            idMapa: mapContainer?.id
        });

        if (header && mapContainer) {
            header.addEventListener('click', (e) => {
                console.log('Header clicado');
                if (!e.target.closest('button')) {
                    item.classList.toggle('expanded');
                    console.log('Expandido:', item.classList.contains('expanded'));

                    if (item.classList.contains('expanded')) {
                        // Extrair dados de endereço dos detail-value
                        const detailGroups = item.querySelectorAll('.detail-group');
                        let rua = '';
                        let numero = '';
                        let bairro = '';
                        let cidade = '';
                        let estado = '';
                        let cep = '';
                        
                        detailGroups.forEach(group => {
                            const label = group.querySelector('.detail-label')?.textContent?.trim() || '';
                            const value = group.querySelector('.detail-value')?.textContent?.trim() || '';
                            
                            console.log(`Label: "${label}", Value: "${value}"`);
                            
                            if (label === 'Endereço') {
                                // Formato: "Rua X, Número Y - Bairro Z"
                                const parts = value.split(' - ');
                                if (parts.length >= 2) {
                                    bairro = parts[1].trim();
                                    const ruaParte = parts[0].split(', ');
                                    if (ruaParte.length >= 2) {
                                        rua = ruaParte[0].trim();
                                        numero = ruaParte[1].trim();
                                    }
                                }
                            } else if (label === 'Cidade/UF') {
                                const cidadeUF = value.split(' - ');
                                cidade = cidadeUF[0].trim();
                                estado = cidadeUF[1].trim();
                            } else if (label === 'CEP') {
                                cep = value;
                            }
                        });

                        // Montar endereço completo
                        const endereco = `${rua}, ${numero}, ${bairro}, ${cidade}, ${estado}`;
                        
                        console.log('Dados extraídos:', { rua, numero, bairro, cidade, estado, cep });
                        console.log('Endereço montado:', endereco);

                        // Inicializar mapa
                        if (rua && numero && cidade && estado && !maps[mapContainer.id]) {
                            console.log('Chamando inicialização do mapa');
                            initMapForSchedule(mapContainer, endereco);
                        } else {
                            console.log('Validação falhou:', {
                                rua: !!rua,
                                numero: !!numero,
                                cidade: !!cidade,
                                estado: !!estado,
                                jaExiste: maps[mapContainer.id]
                            });
                        }
                    }
                }
            });
        }
    });

    // Gerenciar botões de ação
    document.querySelectorAll('.btn-aceitar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const coletaId = btn.getAttribute('data-coleta-id');
            
            mostrarConfirmacao('Aceitar Coleta', 'Você deseja aceitar esta solicitação de coleta?', () => {
                fetch('../api/aceitar_coleta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_coleta: coletaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultado('sucesso', 'Sucesso', 'Coleta aceita com sucesso!', true);
                    } else {
                        mostrarResultado('erro', 'Erro', 'Erro ao aceitar coleta: ' + (data.message || 'Tente novamente'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarResultado('erro', 'Erro', 'Erro ao processar a solicitação');
                });
            });
        });
    });

    document.querySelectorAll('.btn-concluir').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const coletaId = btn.getAttribute('data-coleta-id');
            const quantidade = btn.getAttribute('data-quantidade');
            
            // Abrir modal
            const modal = document.getElementById('modalConcluir');
            document.getElementById('coleta_id_input').value = '#' + coletaId;
            document.getElementById('hidden_coleta_id').value = coletaId;
            document.getElementById('quantidade_coletada').value = '';
            document.getElementById('quantidade_coletada').placeholder = 'Solicitado: ' + quantidade + ' litros';
            document.getElementById('observacoes_coleta').value = '';
            
            modal.classList.add('show');
        });
    });

    // Validar quantidade em tempo real
    document.getElementById('quantidade_coletada').addEventListener('input', function() {
        const quantidadeInput = this;
        const placeholder = quantidadeInput.placeholder;
        
        // Extrair a quantidade esperada do placeholder
        const match = placeholder.match(/(\d+(?:[.,]\d+)?)/);
        if (match) {
            const quantidadeEsperada = parseFloat(match[1].replace(',', '.'));
            const quantidadeMaxima = quantidadeEsperada * 1.2; // 20% de tolerância
            const quantidadeDigitada = parseFloat(quantidadeInput.value);
            
            if (quantidadeDigitada > quantidadeMaxima) {
                quantidadeInput.value = quantidadeMaxima.toFixed(1);
                this.style.borderColor = '#ff9800';
                this.style.boxShadow = '0 0 5px rgba(255, 152, 0, 0.3)';
            } else if (quantidadeDigitada < 0) {
                quantidadeInput.value = 0;
            } else {
                this.style.borderColor = '#ddd';
                this.style.boxShadow = 'none';
            }
        }
    });

    // Confirmar conclusão
    document.getElementById('btnConfirmarConclusao').addEventListener('click', () => {
        const coletaId = document.getElementById('hidden_coleta_id').value;
        const quantidadeColetada = document.getElementById('quantidade_coletada').value;
        const observacoes = document.getElementById('observacoes_coleta').value;
        const placeholder = document.getElementById('quantidade_coletada').placeholder;
        
        if (!quantidadeColetada) {
            mostrarResultado('erro', 'Erro', 'Por favor, informe a quantidade de óleo coletada');
            return;
        }
        
        // Validar se a quantidade não excede o máximo permitido
        const match = placeholder.match(/(\d+(?:[.,]\d+)?)/);
        if (match) {
            const quantidadeEsperada = parseFloat(match[1].replace(',', '.'));
            const quantidadeMaxima = quantidadeEsperada * 1.2; // 20% de tolerância
            const quantidadeDigitada = parseFloat(quantidadeColetada);
            
            if (quantidadeDigitada > quantidadeMaxima) {
                mostrarResultado('erro', 'Quantidade Inválida', `O máximo permitido é ${quantidadeMaxima.toFixed(1)} litros (20% acima do esperado de ${quantidadeEsperada} litros).`);
                return;
            }
        }
        
        fetch('../api/concluir_coleta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_coleta: coletaId,
                quantidade_coletada: quantidadeColetada,
                observacoes: observacoes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalConcluir').classList.remove('show');
                mostrarResultado('sucesso', 'Sucesso', 'Coleta concluída com sucesso!', true);
            } else {
                mostrarResultado('erro', 'Erro', 'Erro ao concluir coleta: ' + (data.message || 'Tente novamente'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarResultado('erro', 'Erro', 'Erro ao processar a solicitação');
        });
    });

    document.querySelectorAll('.btn-recusar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const coletaId = btn.getAttribute('data-coleta-id');
            
            mostrarConfirmacao('Recusar Coleta', 'Você deseja recusar esta solicitação de coleta?', () => {
                fetch('../api/recusar_coleta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_coleta: coletaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultado('sucesso', 'Sucesso', 'Coleta recusada', true);
                    } else {
                        mostrarResultado('erro', 'Erro', 'Erro ao recusar coleta: ' + (data.message || 'Tente novamente'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarResultado('erro', 'Erro', 'Erro ao processar a solicitação');
                });
            });
        });
    });

    document.querySelectorAll('.btn-cancelar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const coletaId = btn.getAttribute('data-coleta-id');
            
            mostrarConfirmacao('Cancelar Agendamento', 'Tem certeza que deseja cancelar este agendamento?', () => {
                fetch('../api/cancelar_coleta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_coleta: coletaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarResultado('sucesso', 'Sucesso', 'Agendamento cancelado', true);
                    } else {
                        mostrarResultado('erro', 'Erro', 'Erro ao cancelar agendamento: ' + (data.message || 'Tente novamente'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarResultado('erro', 'Erro', 'Erro ao processar a solicitação');
                });
            });
        });
    });

    document.querySelectorAll('.btn-ver-mapa').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const item = e.target.closest('.agendamento-item');
            item.classList.add('expanded');
            
            // Inicializar mapa imediatamente
            const mapContainer = item.querySelector('.map-container');
            if (mapContainer && !maps[mapContainer.id]) {
                // Extrair dados de endereço
                const detailGroups = item.querySelectorAll('.detail-group');
                let rua = '';
                let numero = '';
                let bairro = '';
                let cidade = '';
                let estado = '';
                
                detailGroups.forEach(group => {
                    const label = group.querySelector('.detail-label')?.textContent?.trim() || '';
                    const value = group.querySelector('.detail-value')?.textContent?.trim() || '';
                    
                    if (label === 'Endereço') {
                        const parts = value.split(' - ');
                        if (parts.length >= 2) {
                            bairro = parts[1].trim();
                            const ruaParte = parts[0].split(', ');
                            if (ruaParte.length >= 2) {
                                rua = ruaParte[0].trim();
                                numero = ruaParte[1].trim();
                            }
                        }
                    } else if (label === 'Cidade/UF') {
                        const cidadeUF = value.split(' - ');
                        cidade = cidadeUF[0].trim();
                        estado = cidadeUF[1].trim();
                    }
                });
                
                if (rua && numero && cidade && estado) {
                    const endereco = `${rua}, ${numero}, ${bairro}, ${cidade}, ${estado}`;
                    initMapForSchedule(mapContainer, endereco);
                }
            }
        });
    });

    // Gerenciar notificações
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationsPopup = document.querySelector('.notifications-popup');

    if (notificationBtn && notificationsPopup) {
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
    }
});
