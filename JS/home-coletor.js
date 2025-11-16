// Gerenciamento do popup de notificações
document.addEventListener("DOMContentLoaded", function () {
  const notificationBtn = document.querySelector(".notification-btn");
  const notificationsPopup = document.querySelector(".notifications-popup");

  // Fechar popup quando clicar fora dele
  document.addEventListener("click", function (event) {
    const isClickInsidePopup = notificationsPopup.contains(event.target);
    const isClickOnButton = notificationBtn.contains(event.target);

    if (!isClickInsidePopup && !isClickOnButton) {
      notificationsPopup.classList.remove("show");
    }
  });

  // Toggle do popup ao clicar no botão
  notificationBtn.addEventListener("click", function (event) {
    event.stopPropagation();
    notificationsPopup.classList.toggle("show");
  });

  // Inicialização do mapa (substitua com sua chave API)
  function initMap() {
    const map = new google.maps.Map(document.getElementById("map"), {
      center: { lat: -23.55052, lng: -46.633308 }, // Coordenadas de São Paulo
      zoom: 12,
    });

    // Adicionar marcadores para cada coleta
    const collections = document.querySelectorAll(".collection-item");
    collections.forEach((collection) => {
      const location = collection.querySelector(".location").textContent;
      // Aqui você precisará converter o endereço em coordenadas usando o Geocoding
      // Este é apenas um exemplo
      const marker = new google.maps.Marker({
        position: { lat: -23.55052, lng: -46.633308 },
        map: map,
        title: location,
      });
    });
  }

  // Botões de ver no mapa
  document.querySelectorAll(".view-map-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const location =
        this.closest(".card-content").querySelector(
          ".location span"
        ).textContent;
      // Aqui você pode centralizar o mapa na localização específica
      // e abrir em tela cheia ou destacar o marcador correspondente
    });
  });

  // Inicializar o mapa quando a API do Google Maps estiver carregada
  if (window.google && window.google.maps) {
    initMap();
  }
});
