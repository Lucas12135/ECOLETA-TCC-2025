// Inicialização do VLibras
new window.VLibras.Widget('https://vlibras.gov.br/app');

// Variáveis globais
let map;
let marker;
let selectedColetorId = null;

// Coletores fictícios (em produção, seria carregado do banco de dados)
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
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('rua').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    
                    // Geocodificar o endereço
                    const endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade}, ${data.uf}`;
                    geocodeAddress(endereco);
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
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

// Gerenciar tipo de coleta
const tipoColetaInputs = document.querySelectorAll('input[name="tipo_coleta"]');
const coletorSelection = document.getElementById('coletor-selection');

tipoColetaInputs.forEach(input => {
    input.addEventListener('change', function() {
        if (this.value === 'especifico') {
            coletorSelection.style.display = 'block';
            carregarColetores();
        } else {
            coletorSelection.style.display = 'none';
            selectedColetorId = null;
        }
    });
});

// Carregar coletores disponíveis
async function carregarColetores() {
    const coletoresList = document.getElementById('coletores-list');
    const coletorSelect = document.getElementById('coletor');
    
    // Limpar lista e mostrar loading
    coletoresList.innerHTML = '<div class="loading"><i class="ri-loader-4-line"></i> Carregando coletores...</div>';
    coletorSelect.innerHTML = '<option value="">Carregando coletores...</option>';
    
    try {
        const response = await fetch('buscar_coletores.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            // Limpar e atualizar select
            coletorSelect.innerHTML = '<option value="">Selecione um coletor</option>';
            coletoresList.innerHTML = '';
            
            // Renderizar coletores
            data.coletores.forEach(coletor => {
                // Adicionar ao select
                const option = document.createElement('option');
                option.value = coletor.id;
                option.textContent = coletor.nome;
                coletorSelect.appendChild(option);
                
                // Criar e adicionar card
                const card = criarColetorCard(coletor);
                coletoresList.appendChild(card);
            });
        } else {
            coletoresList.innerHTML = '<div class="error">Erro ao carregar coletores</div>';
            console.error('Erro:', data.message);
        }
    } catch (error) {
        coletoresList.innerHTML = '<div class="error">Erro ao carregar coletores</div>';
        console.error('Erro:', error);
    }
}

// Criar card de coletor
function criarColetorCard(coletor) {
    const card = document.createElement('div');
    card.className = 'coletor-card';
    card.dataset.coletorId = coletor.id;
    
    const stars = '★'.repeat(Math.floor(coletor.rating)) + '☆'.repeat(5 - Math.floor(coletor.rating));
    
    card.innerHTML = `
        <div class="coletor-header">
            <div class="coletor-avatar">${coletor.avatar}</div>
            <div class="coletor-info">
                <h4>${coletor.nome}</h4>
                <div class="coletor-rating">
                    <span>${stars}</span>
                    <span>${coletor.rating}</span>
                </div>
            </div>
        </div>
        <div class="coletor-details">
            <div class="coletor-detail">
                <i class="ri-map-pin-line"></i>
                <span>${coletor.distancia} de distância</span>
            </div>
            <div class="coletor-detail">
                <i class="ri-checkbox-circle-line"></i>
                <span>${coletor.coletas} coletas realizadas</span>
            </div>
            <div class="coletor-detail">
                <i class="ri-truck-line"></i>
                <span>${coletor.veiculos.join(', ')}</span>
            </div>
            ${coletor.disponivel ? '<span class="coletor-badge"><i class="ri-check-line"></i> Disponível</span>' : ''}
        </div>
    `;
    
    // Adicionar evento de clique
    card.addEventListener('click', function() {
        selecionarColetor(coletor.id);
    });
    
    return card;
}

// Selecionar coletor
function selecionarColetor(coletorId) {
    // Remover seleção anterior
    document.querySelectorAll('.coletor-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Adicionar nova seleção
    const selectedCard = document.querySelector(`.coletor-card[data-coletor-id="${coletorId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        selectedColetorId = coletorId;
        
        // Atualizar select
        document.getElementById('coletor').value = coletorId;
    }
}

// Sincronizar select com cards
document.getElementById('coletor').addEventListener('change', function() {
    if (this.value) {
        selecionarColetor(parseInt(this.value));
    } else {
        document.querySelectorAll('.coletor-card').forEach(card => {
            card.classList.remove('selected');
        });
        selectedColetorId = null;
    }
});

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

// Validação do formulário antes do envio
document.querySelector('.collection-form').addEventListener('submit', function(e) {
    const tipoColeta = document.querySelector('input[name="tipo_coleta"]:checked').value;
    
    if (tipoColeta === 'especifico' && !selectedColetorId) {
        e.preventDefault();
        alert('Por favor, selecione um coletor para realizar a coleta.');
        return false;
    }
    
    // Adicionar coletor_id ao formulário se selecionado
    if (selectedColetorId) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'coletor_id';
        input.value = selectedColetorId;
        this.appendChild(input);
    }
    
    return true;
});

// Inicializar mapa quando a página carregar
window.addEventListener('load', initMap);