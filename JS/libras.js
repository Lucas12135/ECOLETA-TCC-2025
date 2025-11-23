new window.VLibras.Widget('https://vlibras.gov.br/app');

// Mostra o botão de libras quando o painel está pronto
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    const librasButton = document.querySelector('.libras-button');
    if (librasButton) {
      librasButton.classList.add('show');
    }
  }, 500);
});

function toggleAccessibility(event) {
  if (event) event.stopPropagation();

  const target = event.currentTarget;
  
  // Se clicou no botão de libras
  if (target.classList.contains('libras-button')) {
    const vlibrasButton = document.querySelector('div[vw-access-button]');
    if (vlibrasButton) {
      vlibrasButton.click();
    }
  }
}

// Torna disponível globalmente
window.toggleAccessibility = toggleAccessibility;
