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

// Carregar coletores baseado na localização
function carregarColetores() {
    const cidade = document.getElementById('cidade').value.trim();
    const bairro = document.getElementById('bairro').value.trim();
    
    if (!cidade) {
        document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">Preencha o CEP primeiro para carregar os coletores disponíveis</p>';
        return;
    }
    
    document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">Carregando coletores...</p>';
    
    // Fazer requisição ao servidor PHP
    fetch('../api/get_coletores_por_localizacao.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'cidade=' + encodeURIComponent(cidade) + '&bairro=' + encodeURIComponent(bairro)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.coletores && data.coletores.length > 0) {
            exibirColetores(data.coletores);
        } else {
            document.getElementById('coletores-list').innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">Nenhum coletor disponível nesta região</p>';
        }
    })
    .catch(error => {
        console.error('Erro ao carregar coletores:', error);
        document.getElementById('coletores-list').innerHTML = '<p style="color: #d32f2f; text-align: center; padding: 20px;">Erro ao carregar coletores. Tente novamente.</p>';
    });
}

// Exibir coletores em cards
function exibirColetores(coletores) {
    const container = document.getElementById('coletores-list');
    container.innerHTML = '';
    
    coletores.forEach(coletor => {
        const card = document.createElement('div');
        card.className = 'coletor-card';
        card.innerHTML = `
            <div class="coletor-card-header">
                <img src="${coletor.foto_perfil ? '../uploads/profile_photos/' + coletor.foto_perfil : '../img/default-avatar.png'}" 
                     alt="${coletor.nome_completo}" class="coletor-foto">
            </div>
            <div class="coletor-card-body">
                <h3>${coletor.nome_completo}</h3>
                <p class="coletor-localizacao"><i class="ri-map-pin-line"></i> ${coletor.localizacao}</p>
                <div class="coletor-info">
                    <div class="rating">
                        <i class="ri-star-fill"></i>
                        <span>${coletor.avaliacao_media} (${coletor.total_avaliacoes})</span>
                    </div>
                    ${coletor.meio_transporte ? '<div><i class="ri-e-bike-2-line"></i> ' + coletor.meio_transporte + '</div>' : ''}
                </div>
                ${coletor.experiencia ? '<p class="coletor-experiencia"><i class="ri-award-line"></i> ' + coletor.experiencia + ' anos</p>' : ''}
            </div>
            <button type="button" class="btn-select-coletor" data-id="${coletor.id}" data-nome="${coletor.nome_completo}">
                Selecionar
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
    
    // Destacar coletor selecionado
    document.querySelectorAll('.coletor-card').forEach(card => {
        card.classList.remove('selected');
        if (card.querySelector('[data-id="' + id + '"]')) {
            card.classList.add('selected');
        }
    });
    
    // Remover mensagem antiga se existir
    const msgAntiga = document.querySelector('.success-message');
    if (msgAntiga) msgAntiga.remove();
    
    // Mostrar mensagem
    const msgDiv = document.createElement('div');
    msgDiv.className = 'success-message';
    msgDiv.innerHTML = '<i class="ri-check-circle-line"></i> Coletor ' + nome + ' selecionado!';
    document.getElementById('coletor-selection').insertBefore(msgDiv, document.getElementById('coletores-list'));
    
    setTimeout(() => msgDiv.remove(), 3000);
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