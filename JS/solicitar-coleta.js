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
                    if (document.querySelector('input[name="tipo_coleta"][value="especifico"]').checked) {
                        // Aguardar um pouco para garantir que a cidade foi preenchida
                        setTimeout(() => {
                            const event = new Event('change');
                            document.getElementById('cidade').dispatchEvent(event);
                        }, 200);
                    }
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
    e.preventDefault();
    
    const tipoColeta = document.querySelector('input[name="tipo_coleta"]:checked').value;
    
    if (tipoColeta === 'especifico' && !document.getElementById('coletor_id').value) {
        alert('Por favor, selecione um coletor para realizar a coleta.');
        return false;
    }
    
    // Sincronizar valores dos campos ocultos
    document.getElementById('tipo_coleta_hidden').value = tipoColeta;
    document.getElementById('rua_hidden').value = document.getElementById('rua').value;
    document.getElementById('numero_hidden').value = document.getElementById('numero').value;
    document.getElementById('complemento_hidden').value = document.getElementById('complemento').value;
    document.getElementById('bairro_hidden').value = document.getElementById('bairro').value;
    document.getElementById('cidade_hidden').value = document.getElementById('cidade').value;
    
    // Enviar dados do formulário via AJAX
    const formData = new FormData(this);
    
    fetch('processar_solicitar_coleta.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensagem de sucesso
            const successDiv = document.createElement('div');
            successDiv.className = 'success-notification';
            successDiv.innerHTML = `
                <div class="notification-content">
                    <i class="ri-check-circle-line"></i>
                    <div>
                        <h3>Sucesso!</h3>
                        <p>${data.message}</p>
                        <p style="font-size: 0.9em; color: #666; margin-top: 5px;">
                            ID da solicitação: <strong>#${data.id_coleta}</strong>
                        </p>
                    </div>
                </div>
            `;
            document.body.appendChild(successDiv);
            
            // Remover notificação após 3 segundos e redirecionar
            setTimeout(() => {
                successDiv.remove();
                window.location.href = 'historico.php';
            }, 3000);
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro ao enviar solicitação:', error);
        alert('Erro ao enviar solicitação de coleta. Tente novamente.');
    });
    
    return false;
});

// Inicializar mapa quando a página carregar
window.addEventListener('load', initMap);