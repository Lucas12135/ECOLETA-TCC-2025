// Função para controle do menu móvel
function toggleMobileMenu() {
  const nav = document.querySelector(".sidebar-nav");
  nav.classList.toggle("active");

  const button = document.querySelector(".menu-mobile-button i");
  if (nav.classList.contains("active")) {
    button.classList.remove("ri-menu-line");
    button.classList.add("ri-close-line");
  } else {
    button.classList.remove("ri-close-line");
    button.classList.add("ri-menu-line");
  }
}

// Fecha o menu móvel quando clicar fora dele
document.addEventListener("click", function (event) {
  const nav = document.querySelector(".sidebar-nav");
  const button = document.querySelector(".menu-mobile-button");

  if (
    nav.classList.contains("active") &&
    !event.target.closest(".sidebar-nav") &&
    !event.target.closest(".menu-mobile-button")
  ) {
    nav.classList.remove("active");
    const icon = button.querySelector("i");
    icon.classList.remove("ri-close-line");
    icon.classList.add("ri-menu-line");
  }
});

// Fecha o menu móvel quando a tela for redimensionada para desktop
window.addEventListener("resize", function () {
  if (window.innerWidth > 576) {
    const nav = document.querySelector(".sidebar-nav");
    const button = document.querySelector(".menu-mobile-button i");
    nav.classList.remove("active");
    button.classList.remove("ri-close-line");
    button.classList.add("ri-menu-line");
  }
});
