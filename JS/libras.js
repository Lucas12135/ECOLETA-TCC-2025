new window.VLibras.Widget('https://vlibras.gov.br/app');

function toggleAccessibility(event) {
  if (event) event.stopPropagation();

  const vlibrasButton = document.querySelector('div[vw-access-button]');
  if (vlibrasButton) {
    vlibrasButton.click();
  }
}
