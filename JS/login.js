// Libras
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

  function toggleLibras(event) {
    if (event) event.stopPropagation();

    const vlibrasButton = document.querySelector('div[vw-access-button]');
    if (vlibrasButton) {
      vlibrasButton.click();
    }
  }

  // Torna disponível globalmente
  window.toggleLibras = toggleLibras;

  // Menu Hamburguer
  function toggleMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIcon = document.getElementById('menuIcon');
    const menuOverlay = document.getElementById('menuOverlay');
    
    mobileMenu.classList.toggle('active');
    menuIcon.classList.toggle('active');
    menuOverlay.classList.toggle('active');
  }

  document.addEventListener('click', function(event) {
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIcon = document.getElementById('menuIcon');
    const menuOverlay = document.getElementById('menuOverlay');
    const headerContainer = document.querySelector('.header-container');
    const accessibilityButton = document.querySelector('.accessibility-button');
    const accessibilityPanel = document.querySelector('.accessibility-panel');
    const vlibrasButton = document.querySelector('div[vw-access-button]');
    const vlibrasContainer = document.querySelector('.vw-plugin-wrapper');
    const librasButton = document.querySelector('.libras-button');

    const clickInsideHeader = headerContainer && headerContainer.contains(event.target);
    const clickInAccessibility = accessibilityButton && accessibilityButton.contains(event.target);
    const clickInPanel = accessibilityPanel && accessibilityPanel.contains(event.target);
    const clickInVLibrasButton = vlibrasButton && vlibrasButton.contains(event.target);
    const clickInVLibrasContainer = vlibrasContainer && vlibrasContainer.contains(event.target);
    const clickInLibrasButton = librasButton && librasButton.contains(event.target);

    if (
      !clickInsideHeader &&
      !clickInAccessibility &&
      !clickInPanel &&
      !clickInVLibrasButton &&
      !clickInVLibrasContainer &&
      !clickInLibrasButton &&
      mobileMenu.classList.contains('active')
    ) {
      mobileMenu.classList.remove('active');
      menuIcon.classList.remove('active');
      menuOverlay.classList.remove('active');
    }
  });

  document.querySelectorAll('.mobile-menu-item').forEach(item => {
    item.addEventListener('click', function() {
      document.getElementById('mobileMenu').classList.remove('active');
      document.getElementById('menuIcon').classList.remove('active');
      document.getElementById('menuOverlay').classList.remove('active');
    });
  });
