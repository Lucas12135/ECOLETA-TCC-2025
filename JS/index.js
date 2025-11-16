document.addEventListener("DOMContentLoaded", () => {
  new window.VLibras.Widget("https://vlibras.gov.br/app");

  let map;
  let currentMarker;
  let mapInitialized = false;

  function toggleAccessibility(event) {
    if (event) event.stopPropagation();
    const vlibrasButton = document.querySelector("div[vw-access-button]");
    if (vlibrasButton) vlibrasButton.click();
  }

  var loginBtn = document.getElementById("open-login-modal-btn");
  var searchBtn = document.querySelector(".search-button");
  var modal = document.getElementById("modal");
  var closeBtn = document.querySelector(".close-btn");
  var searchInput = document.querySelector(".search-input-container input");
  var modalInput = document.querySelector("#modalLocationInput");

  // Inicializa o mapa
  function initMap() {
    if (mapInitialized) return;

    const defaultLocation = { lat: -15.8267, lng: -48.0516 }; // Centro do Brasil (Brasília)
    map = new google.maps.Map(document.getElementById("map"), {
      zoom: 15,
      center: defaultLocation,
    });
    mapInitialized = true;
    console.log("Mapa inicializado");
  }

  // Atualiza o mapa com a localização do endereço
  function updateMapLocation(address) {
    if (!address || address.trim() === "") return;

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function (results, status) {
      if (status === "OK") {
        const location = results[0].geometry.location;
        map.setCenter(location);
        map.setZoom(15);

        // Remove o marcador anterior se existir
        if (currentMarker) {
          currentMarker.setMap(null);
        }

        // Adiciona novo marcador
        currentMarker = new google.maps.Marker({
          map: map,
          position: location,
          title: address,
        });
        console.log("Localização atualizada para: " + address);
      }
    });
  }

  // Inicializa o autocomplete do Google Places (AGORA GLOBAL)
  window.initAutocomplete = function () {
    const options = {
      types: ["address"],
      componentRestrictions: { country: "BR" },
    };

    const searchAutocomplete = new google.maps.places.Autocomplete(
      searchInput,
      options
    );
    const modalAutocomplete = new google.maps.places.Autocomplete(
      modalInput,
      options
    );

    // Quando o usuário clica no botão Buscar
    searchBtn.addEventListener("click", function () {
      const address = searchInput.value;
      modalInput.value = address;

      // Abre o modal
      const myModal = new bootstrap.Modal(modal);
      myModal.show();

      // Inicializa o mapa quando o modal é aberto
      setTimeout(function () {
        if (!mapInitialized) {
          initMap();
        }
        // Atualiza a localização do mapa com o endereço
        updateMapLocation(address);
      }, 300);
    });

    // Quando o endereço do modal é alterado
    modalInput.addEventListener("change", function () {
      updateMapLocation(this.value);
    });
  };

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

  // Evento de clique para abrir o modal pelo botão de login
  if (loginBtn) {
    loginBtn.addEventListener("click", openModal);
  }

  // Evento de clique para fechar o modal
  if (closeBtn) {
    closeBtn.addEventListener("click", closeModal);
  }
});
