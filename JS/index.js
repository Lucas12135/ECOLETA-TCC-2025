// JS/index.js – versão original completa e comentada
// Requer:
// <script src="JS/index.js"></script>
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY&libraries=places,marker&v=beta&callback=initAutocomplete" async defer></script>

"use strict";

/* -----------------------
   Declarações globais
   ----------------------- */
let map;
let currentMarker = null;
let mapInitialized = false;
const MAP_ID = "f8d0dbf7ed48fd5de04be880"; // substitua se for outro

/* ---------- Helpers ---------- */

function distanceInKm(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function createPin(color, glyph = "📍") {
  // cria um PinElement (API beta)
  return new google.maps.marker.PinElement({
    background: color,
    borderColor: "#00000055",
    glyph,
    glyphColor: "white",
    scale: 1.2,
  });
}

function calcularTempoDeAfiliacao(dataCadastro) {
  if (!dataCadastro) return null;

  const criado = new Date(dataCadastro);
  const agora = new Date();

  // diferença em ms
  const diffMs = agora - criado;

  // anos aproximados
  const anos = diffMs / (1000 * 60 * 60 * 24 * 365);

  if (anos >= 1) {
    return `${Math.floor(anos)} ano${Math.floor(anos) > 1 ? "s" : ""}`;
  }

  // se for menos de 1 ano → mostrar meses
  const meses = diffMs / (1000 * 60 * 60 * 24 * 30.44);
  if (meses >= 1) {
    return `${Math.floor(meses)} mês${Math.floor(meses) > 1 ? "es" : ""}`;
  }

  // se for menos de 1 mês → mostrar dias
  const dias = diffMs / (1000 * 60 * 60 * 24);
  return `${Math.floor(dias)} dia${Math.floor(dias) > 1 ? "s" : ""}`;
}

function createInfoWindowHTML(coletor) {
  // HTML simples para a InfoWindow (pode ser estilizado)
  const defaultAvatar =
    "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23e0e0e0' width='100' height='100'/%3E%3Ccircle cx='50' cy='35' r='20' fill='%23999'/%3E%3Cellipse cx='50' cy='70' rx='30' ry='25' fill='%23999'/%3E%3C/svg%3E";
  const foto = coletor.foto_perfil
    ? `uploads/profile_photos/${coletor.foto_perfil}`
    : defaultAvatar;
  const tempo = coletor.created_at
    ? calcularTempoDeAfiliacao(coletor.created_at) + " de atuação"
    : "";
  const nome = coletor.nome_completo || coletor.nome || "Coletor";

  return `
    <div style="font-family: Inter, Arial; width: 260px; padding: 12px; border-radius: 10px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <img src="${foto}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:1px solid #eee" onerror="this.src='${defaultAvatar}'">
        <div style="flex:1">
          <div style="font-weight:700; font-size:15px; margin-bottom:4px;">${nome}</div>
          <div style="font-size:13px; color:#555;">${tempo}</div>
        </div>
      </div>
      <button onclick="abrirPerfilColetor(${coletor.id})"
        style="margin-top:12px; width:100%; padding:8px; border:none; background:#2ecc71; color:white; border-radius:8px; cursor:pointer; font-weight:700;">
        Ver Perfil
      </button>
    </div>
  `;
}

/* ---------- Inicialização do mapa ---------- */

function initMap() {
  if (mapInitialized) return;

  const defaultLocation = { lat: -15.8267, lng: -48.0516 };

  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 15,
    center: defaultLocation,
    mapId: MAP_ID,
  });

  mapInitialized = true;
  console.log("Mapa inicializado (initMap).");
}

/* ---------- Função que carrega coletores e adiciona markers ---------- */

async function loadColetores(userLat, userLng) {
  if (!map) {
    console.warn("Mapa não inicializado. Chamando initMap()...");
    initMap();
  }

  let response;
  try {
    response = await fetch("api/get_coletores.php");
  } catch (e) {
    console.error("Falha ao buscar coletores:", e);
    return [];
  }

  let coletores;
  try {
    coletores = await response.json();
    console.log("Coletores recebidos:", coletores);
  } catch (e) {
    console.error("Resposta inválida de get_coletores.php:", e);
    return [];
  }

  if (!Array.isArray(coletores)) {
    console.error("Formato inesperado de coletores:", coletores);
    return [];
  }

  const geocoder = new google.maps.Geocoder();

  // opcional: limpar markers antigos do mapa (exceto currentMarker)
  // Se quiser manter referências e removê-los depois, implemente um array global de markers.

  for (const col of coletores) {
    // pula sem endereço
    if (!col.endereco_completo || col.endereco_completo.trim() === "") {
      col.lat = null;
      col.lng = null;
      continue;
    }

    // geocodifica cada endereço (respeitando quotas com delay)
    await new Promise((resolve) => {
      geocoder.geocode(
        { address: col.endereco_completo },
        function (results, status) {
          if (status === "OK" && results[0]) {
            const loc = results[0].geometry.location;
            col.lat = loc.lat();
            col.lng = loc.lng();

            // PIN VERDE
            try {
              const pinGreen = createPin("#2ecc71", "♻️");

              const marker = new google.maps.marker.AdvancedMarkerElement({
                map,
                position: { lat: col.lat, lng: col.lng },
                content: pinGreen.element,
                title: col.nome_completo || "Coletor " + (col.id || ""),
              });

              // InfoWindow padrão (usa google.maps.InfoWindow)
              const info = new google.maps.InfoWindow({
                content: createInfoWindowHTML(col),
                maxWidth: 300,
              });

              marker.addListener("click", () => {
                info.open({ map, anchor: marker });
              });
            } catch (err) {
              // se AdvancedMarkerElement não estiver disponível por algum motivo, cai para Marker normal
              console.warn(
                "Falha ao criar AdvancedMarker, usando Marker padrão:",
                err
              );
              new google.maps.Marker({
                map,
                position: { lat: col.lat, lng: col.lng },
                title: col.nome_completo || "Coletor " + (col.id || ""),
              });
            }
          } else {
            col.lat = null;
            col.lng = null;
          }

          // pequeno delay para evitar spikes no geocoder
          setTimeout(resolve, 150);
        }
      );
    });
  }

  // calcular distâncias
  coletores.forEach((c) => {
    if (c.lat && c.lng) {
      c.dist = distanceInKm(
        userLat,
        userLng,
        parseFloat(c.lat),
        parseFloat(c.lng)
      );
    } else {
      c.dist = Infinity;
    }
  });

  coletores.sort((a, b) => a.dist - b.dist);

  const proximos = coletores.slice(0, 5).filter((c) => isFinite(c.dist));
  console.log("Coletores mais próximos:", proximos);

  return proximos;
}

/* ---------- Atualiza posição do usuário e carrega coletores ---------- */

function updateMapLocation(address) {
  if (!address || address.trim() === "") return;

  if (!mapInitialized) initMap();

  const geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: address }, function (results, status) {
    if (status === "OK" && results[0]) {
      const location = results[0].geometry.location;

      map.setCenter(location);
      map.setZoom(15);

      // remove marker anterior do usuário
      if (currentMarker && typeof currentMarker.setMap === "function") {
        currentMarker.setMap(null);
        currentMarker = null;
      }

      // cria pin amarelo do usuário
      try {
        const pinUser = createPin("#f1c40f", "📌");
        currentMarker = new google.maps.marker.AdvancedMarkerElement({
          map,
          position: location,
          content: pinUser.element,
          title: "Sua localização",
        });
      } catch (err) {
        // fallback para Marker padrão se AdvancedMarker falhar
        console.warn(
          "AdvancedMarker falhou ao criar marker do usuário, usando Marker padrão:",
          err
        );
        currentMarker = new google.maps.Marker({
          map,
          position: location,
          title: "Sua localização",
        });
      }

      // carrega coletores próximos baseado na localização encontrada
      loadColetores(location.lat(), location.lng());
    } else {
      console.warn("Geocode falhou para:", address, "status:", status);
    }
  });
}

/* ===========================
   initAutocomplete (GLOBAL)
   O Maps chama isso via callback=initAutocomplete
   =========================== */
window.initAutocomplete = function initAutocompleteCallback() {
  // Se DOM ainda não estiver pronto, espera e re-executa
  if (!document.querySelector(".search-input-container input")) {
    document.addEventListener(
      "DOMContentLoaded",
      () => {
        window.initAutocomplete && window.initAutocomplete();
      },
      { once: true }
    );
    return;
  }

  // garante que o mapa exista (cria, caso contrário)
  if (!mapInitialized) initMap();

  const searchInput = document.querySelector(".search-input-container input");
  const modalInput = document.querySelector("#modalLocationInput");
  const searchBtn = document.querySelector(".search-button");
  const modal = document.getElementById("modal");

  const options = {
    types: ["address"],
    componentRestrictions: { country: "BR" },
  };

  // Autocomplete (Places) - ok usar por enquanto
  const searchAutocomplete = new google.maps.places.Autocomplete(
    searchInput,
    options
  );
  const modalAutocomplete = new google.maps.places.Autocomplete(
    modalInput,
    options
  );

  // Quando escolher uma sugestão no input principal, atualiza o modalInput (não abre modal automaticamente)
  searchAutocomplete.addListener("place_changed", () => {
    const place = searchAutocomplete.getPlace();
    if (place && place.formatted_address) {
      modalInput.value = place.formatted_address;
    } else if (searchInput.value) {
      modalInput.value = searchInput.value;
    }
  });

  // mesma lógica para o modal
  modalAutocomplete.addListener("place_changed", () => {
    const place = modalAutocomplete.getPlace();
    if (place && place.formatted_address) {
      // atualiza mapa imediatamente quando a pessoa escolher no modal
      updateMapLocation(place.formatted_address);
    }
  });

  // botão buscar: abre modal e centraliza no endereço escrito
  if (searchBtn && modal) {
    searchBtn.addEventListener("click", function () {
      // atualiza modal input para manter sincronizado
      modalInput.value = searchInput.value;

      // abre modal usando bootstrap (já carregado no seu HTML)
      try {
        const myModal = new bootstrap.Modal(modal);
        myModal.show();
      } catch (e) {
        console.warn(
          "Bootstrap Modal não abriu (verifique se o bootstrap está carregado):",
          e
        );
      }

      // inicializa mapa no modal e atualiza
      setTimeout(() => {
        if (!mapInitialized) initMap();
        updateMapLocation(searchInput.value);
      }, 300);
    });
  }

  console.log("initAutocomplete executado.");
};

/* ===========================
   DOMContentLoaded (inicializações que dependem do DOM)
   =========================== */
document.addEventListener("DOMContentLoaded", () => {
  // widget de acessibilidade
  try {
    new window.VLibras.Widget("https://vlibras.gov.br/app");
  } catch (_) {}

  // Mostra o botão de libras quando o painel está pronto
  setTimeout(() => {
    const librasButton = document.querySelector(".libras-button");
    if (librasButton) {
      librasButton.classList.add("show");
    }
  }, 500);

  function toggleAccessibility(event) {
    if (event) event.stopPropagation();

    const target = event.currentTarget;

    // Se clicou no botão de libras
    if (target.classList.contains("libras-button")) {
      const vlibrasButton = document.querySelector("div[vw-access-button]");
      if (vlibrasButton) {
        vlibrasButton.click();
      }
    }
  }

  // Attach toggleAccessibility ao escopo global
  window.toggleAccessibility = toggleAccessibility;

  // elementos (alguns já acessados em initAutocomplete; aqui garantimos)
  const loginBtn = document.getElementById("open-login-modal-btn");
  const closeBtn = document.querySelector(".close-btn");
  const modal = document.getElementById("modal");

  // eventos simples do modal (se existir botão de login)
  if (loginBtn) {
    loginBtn.addEventListener("click", () => {
      if (!mapInitialized) initMap();
      const myModal = new bootstrap.Modal(modal);
      myModal.show();
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      const myModal = bootstrap.Modal.getInstance(modal);
      if (myModal) myModal.hide();
    });
  }

  // se o Maps já carregou antes do DOM, initAutocomplete pode ter sido chamado;
  // se não, nada mais precisa ser feito aqui.
});