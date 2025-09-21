// Libras
  new window.VLibras.Widget('https://vlibras.gov.br/app');

  function toggleAccessibility(event) {
    if (event) event.stopPropagation();

    const vlibrasButton = document.querySelector('div[vw-access-button]');
    if (vlibrasButton) {
      vlibrasButton.click();
    }
  }

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
    const vlibrasButton = document.querySelector('div[vw-access-button]');
    const vlibrasContainer = document.querySelector('.vw-plugin-wrapper');

    const clickInsideHeader = headerContainer && headerContainer.contains(event.target);
    const clickInAccessibility = accessibilityButton && accessibilityButton.contains(event.target);
    const clickInVLibrasButton = vlibrasButton && vlibrasButton.contains(event.target);
    const clickInVLibrasContainer = vlibrasContainer && vlibrasContainer.contains(event.target);

    if (
      !clickInsideHeader &&
      !clickInAccessibility &&
      !clickInVLibrasButton &&
      !clickInVLibrasContainer &&
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

  function validarEmail(event) {
    event.preventDefault();
    var email = document.getElementById("email").value;
    var usuario = email.substr(0, email.indexOf("@"));
    var dominio = email.substr(email.indexOf("@") + 1, email.length);

    if ((usuario.length >= 1) &&
      (dominio.length >= 3) &&
      (usuario.search("@") == -1) &&
      (dominio.search("@") == -1) &&
      (usuario.search(" ") == -1) &&
      (dominio.search(" ") == -1) &&
      (dominio.search(".") != -1) &&
      (dominio.indexOf(".") >= 1) &&
      (dominio.lastIndexOf(".") < dominio.length - 1)) {
      window.location.href = "../CADASTRO_COLETOR/registro.php";
    } else {
      document.getElementById("email").focus();
    }
    return false;
  }