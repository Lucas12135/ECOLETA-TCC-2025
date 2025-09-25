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
var modal = document.getElementById('modal');
var closeBtn = document.querySelector('.close-btn');

// Função para abrir o modal
function openModal() {
    modal.classList.add('show');
}

// Função para fechar o modal
function closeModal() {
    modal.classList.remove('show');
}

// Evento de clique para abrir o modal
loginBtn.addEventListener('click', openModal);

// Evento de clique para fechar o modal (clicando no 'x')
closeBtn.addEventListener('click', closeModal);

// Evento de clique para fechar o modal (clicando fora do modal)
window.addEventListener('click', function(event) {
    if (event.target === modal) {
        closeModal();
    }
});