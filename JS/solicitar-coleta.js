// Inicialização do VLibras
new window.VLibras.Widget('https://vlibras.gov.br/app');

// Variáveis globais
let map;
let marker;

// Inicialização do mapa
function initMap() {
    const defaultLocation = { lat: -24.0089, lng: -46.4130 }; // Praia Grande, SP
    
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 15,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false
    });

    marker = new google.maps.Marker({
        map: map,
        position: defaultLocation,
        draggable: true,
        animation: google.maps.Animation.DROP
    });

    // Atualizar coordenadas quando o marcador for movido
    marker.addListener('dragend', function() {
        const position = marker.getPosition();
        document.getElementById('latitude').value = position.lat();
        document.getElementById('longitude').value = position.lng();
    });
}

// Buscar endereço por CEP
document.getElementById('cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    
    if (cep.length === 8) {
        // Mostrar loading
        document.getElementById('rua').value = 'Buscando...';
        
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('rua').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                    
                    // Geocodificar o endereço
                    const endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade}, ${data.uf}`;
                    geocodeAddress(endereco);
                    
                    // Se estiver em modo de coleta específica, recarregar coletores
                    const tipoColetaEspecifico = document.querySelector('input[name="tipo_coleta"][value="especifico"]');
                    if (tipoColetaEspecifico && tipoColetaEspecifico.checked) {
                        setTimeout(() => {
                            carregarColetores();
                        }, 300);
                    }
                } else {
                    alert('CEP não encontrado!');
                    document.getElementById('rua').value = '';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
                document.getElementById('rua').value = '';
            });
    }
});

// Geocodificar endereço
function geocodeAddress(address) {
    const geocoder = new google.maps.Geocoder();
    
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK') {
            const location = results[0].geometry.location;
            map.setCenter(location);
            marker.setPosition(location);
            document.getElementById('latitude').value = location.lat();
            document.getElementById('longitude').value = location.lng();
        }
    });
}

// Validação de data mínima (não permitir datas passadas)
const dataInput = document.getElementById('data');
const hoje = new Date().toISOString().split('T')[0];
dataInput.setAttribute('min', hoje);

// Formatação de CEP
document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 5) {
        value = value.substring(0, 5) + '-' + value.substring(5, 8);
    }
    e.target.value = value;
});

// Mostrar/ocultar seleção de coletor
const tipoColetaInputs = document.querySelectorAll('input[name="tipo_coleta"]');
const coletorSelection = document.getElementById('coletor-selection');

tipoColetaInputs.forEach(input => {
    input.addEventListener('change', function() {
        if (this.value === 'especifico') {
            coletorSelection.style.display = 'block';
            carregarColetores();
        } else {
            coletorSelection.style.display = 'none';
            const coletorIdInput = document.getElementById('coletor_id');
            if (coletorIdInput) {
                coletorIdInput.value = '';
            }
        }
    });
});

// Carregar coletores baseado no CEP
function carregarColetores() {
    const cep = document.getElementById('cep').value.trim();
    
    if (!cep) {
        document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">Preencha o CEP primeiro para carregar os coletores disponíveis</p>';
        return;
    }
    
    // Validar formato do CEP
    const cepLimpo = cep.replace(/\D/g, '');
    if (cepLimpo.length < 5) {
        document.getElementById('coletores-list').innerHTML = '<p style="color: #d32f2f; text-align: center; padding: 20px;">CEP inválido. Por favor, corrija.</p>';
        return;
    }
    
    document.getElementById('coletores-list').innerHTML = '<div style="display: flex; justify-content: center; align-items: center; padding: 40px; color: #666;"><i class="ri-loader-4-line" style="animation: spin 1s linear infinite; font-size: 24px; margin-right: 10px;"></i>Carregando coletores disponíveis...</div>';
    
    // Fazer requisição ao servidor PHP
    fetch('../api/get_coletores_por_cep.php?cep=' + encodeURIComponent(cep))
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao conectar com o servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.coletores && data.coletores.length > 0) {
            exibirColetores(data.coletores);
        } else {
            document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center; padding: 20px;"><i class="ri-information-line"></i> Nenhum coletor disponível nesta região no momento.</p>';
        }
    })
    .catch(error => {
        console.error('Erro ao carregar coletores:', error);
        document.getElementById('coletores-list').innerHTML = '<p style="color: #d32f2f; text-align: center; padding: 20px;"><i class="ri-error-warning-line"></i> Erro ao carregar coletores. Tente novamente.</p>';
    });
}

// Exibir coletores em cards
function exibirColetores(coletores) {
    const container = document.getElementById('coletores-list');
    container.innerHTML = '';
    
    coletores.forEach(coletor => {
        const card = document.createElement('div');
        card.className = 'coletor-card';
        
        // Formatar avaliação
        const avaliacao = coletor.media_avaliacao ? parseFloat(coletor.media_avaliacao).toFixed(1) : 'Sem avaliações';
        const estrelas = coletor.media_avaliacao ? '★'.repeat(Math.floor(coletor.media_avaliacao)) : '';
        
        card.innerHTML = `
            <div class="coletor-card-header">
                <img src="${coletor.foto || '../img/avatar-default.png'}" alt="${coletor.nome}" class="coletor-foto" onerror="this.src='../img/avatar-default.png'">
                <div class="coletor-rating">
                    <i class="ri-star-fill"></i>
                    <span>${avaliacao}</span>
                </div>
            </div>
            <div class="coletor-card-body">
                <h3>${coletor.nome || 'Coletor'}</h3>
                <p class="coletor-city"><i class="ri-map-pin-line"></i> ${coletor.cidade || 'N/A'}, ${coletor.estado || 'N/A'}</p>
                <div class="coletor-stats">
                    <div class="stat">
                        <i class="ri-award-line"></i>
                        <span>${coletor.total_coletas || 0} ${coletor.total_coletas === 1 ? 'coleta' : 'coletas'}</span>
                    </div>
                </div>
                ${coletor.telefone || coletor.email ? `
                <div class="coletor-contact">
                    ${coletor.telefone ? `<a href="tel:${coletor.telefone}" title="Ligar para ${coletor.nome}"><i class="ri-phone-line"></i></a>` : ''}
                    ${coletor.email ? `<a href="mailto:${coletor.email}" title="Enviar email"><i class="ri-mail-line"></i></a>` : ''}
                </div>
                ` : ''}
            </div>
            <button type="button" class="btn-select-coletor" data-id="${coletor.id}" data-nome="${coletor.nome}">
                <i class="ri-check-line"></i> Selecionar
            </button>
        `;
        container.appendChild(card);
        
        // Adicionar evento de click ao botão
        card.querySelector('.btn-select-coletor').addEventListener('click', function(e) {
            e.preventDefault();
            selecionarColetor(this.dataset.id, this.dataset.nome);
        });
    });
}

// Selecionar coletor
function selecionarColetor(id, nome) {
    document.getElementById('coletor_id').value = id;
    
    // Destacar coletor selecionado e remover seleção anterior
    document.querySelectorAll('.coletor-card').forEach(card => {
        card.classList.remove('selected');
        const btn = card.querySelector('.btn-select-coletor');
        if (btn) {
            btn.innerHTML = '<i class="ri-check-line"></i> Selecionar';
        }
    });
    
    // Adicionar seleção ao card clicado
    const cardSelecionado = document.querySelector(`[data-id="${id}"]`).closest('.coletor-card');
    if (cardSelecionado) {
        cardSelecionado.classList.add('selected');
        const btnSelecionado = cardSelecionado.querySelector('.btn-select-coletor');
        if (btnSelecionado) {
            btnSelecionado.innerHTML = '<i class="ri-check-circle-fill"></i> Selecionado';
        }
    }
    
    // Remover mensagem antiga se existir
    const msgAntiga = document.querySelector('.success-message');
    if (msgAntiga) msgAntiga.remove();
    
    // Mostrar mensagem de confirmação
    const msgDiv = document.createElement('div');
    msgDiv.className = 'success-message';
    msgDiv.innerHTML = '<i class="ri-check-circle-line"></i> Coletor <strong>' + nome + '</strong> selecionado com sucesso!';
    document.getElementById('coletor-selection').insertBefore(msgDiv, document.getElementById('coletores-list'));
    
    // Auto-remover após 4 segundos
    setTimeout(() => {
        if (msgDiv.parentNode) {
            msgDiv.remove();
        }
    }, 4000);
}

// Recarregar coletores quando cidade/bairro mudarem
document.getElementById('cidade').addEventListener('change', function() {
    const tipoColetaEspecifico = document.querySelector('input[name="tipo_coleta"][value="especifico"]');
    if (tipoColetaEspecifico && tipoColetaEspecifico.checked) {
        carregarColetores();
    }
});

document.getElementById('bairro').addEventListener('change', function() {
    const tipoColetaEspecifico = document.querySelector('input[name="tipo_coleta"][value="especifico"]');
    if (tipoColetaEspecifico && tipoColetaEspecifico.checked) {
        carregarColetores();
    }
});

// Validação do formulário antes do envio
document.querySelector('.collection-form').addEventListener('submit', function(e) {
    const tipoColeta = document.querySelector('input[name="tipo_coleta"]:checked').value;
    
    if (tipoColeta === 'especifico') {
        const coletorId = document.getElementById('coletor_id').value;
        if (!coletorId) {
            e.preventDefault();
            alert('Por favor, selecione um coletor para realizar a coleta.');
            return false;
        }
    }
    
    // Formulário válido, permitir envio
    return true;
});

// Inicializar mapa quando a página carregar
window.addEventListener('load', initMap);