new window.VLibras.Widget('https://vlibras.gov.br/app');

function toggleAccessibility(event) {
  if (event) event.stopPropagation();

  const vlibrasButton = document.querySelector('div[vw-access-button]');
  if (vlibrasButton) {
    vlibrasButton.click();
  }
}

// Seleciona os elementos do DOM
var loginBtn = document.getElementById('open-login-modal-btn');
var searchBtn = document.querySelector('.search-button');
var modal = document.getElementById('modal');
var closeBtn = document.querySelector('.close-btn');
var searchInput = document.querySelector('.search-input-container input');
var modalInput = document.querySelector('.modal-input-container input');

// Inicializa o autocomplete do Google Places
function initAutocomplete() {
    const options = {
        types: ['address'],
        componentRestrictions: { country: 'BR' }
    };

    const searchAutocomplete = new google.maps.places.Autocomplete(searchInput, options);
    const modalAutocomplete = new google.maps.places.Autocomplete(modalInput, options);

    // Transfere o valor da busca para o modal
    searchBtn.addEventListener('click', function() {
        modalInput.value = searchInput.value;
        modal.classList.add('show');
        modal.style.display = 'block';
    });
}

// Função para abrir o modal
function openModal() {
    const myModal = new bootstrap.Modal(modal);
    myModal.show();
}

// Função para fechar o modal
function closeModal() {
    const myModal = bootstrap.Modal.getInstance(modal);
    if (myModal) {
        myModal.hide();
    }
}

// Evento de clique para abrir o modal com o botão de login
loginBtn.addEventListener('click', openModal);

// Evento de clique para abrir o modal com o botão de busca
searchBtn.addEventListener('click', function() {
    modalInput.value = searchInput.value;
    openModal();
});

// Evento de clique para fechar o modal (clicando no 'x')
closeBtn.addEventListener('click', closeModal);