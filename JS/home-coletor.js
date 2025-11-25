// Variáveis globais
let map;
let geocoder;
let markers = [];
let userLocation = null;
let routePolyline = null;
let loadingPanelId = null;
const GOOGLE_MAPS_API_KEY = "AIzaSyAe884hZ7UbSCJDuS4hkEWrR-ls0XVBe_U";

// Inicialização do mapa
function initMap() {
  // Inicializar geocoder
  geocoder = new google.maps.Geocoder();

  // Centro padrão: Campinas, SP
  let mapCenter = { lat: -22.9099384, lng: -47.0626332 };
  let initialZoom = 13;

  // Tentar obter localização atual para centrar o mapa
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        mapCenter = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };
        if (map) {
          map.setCenter(mapCenter);
          map.setZoom(14);
        }
      },
      () => {
        // Silenciosamente falha se geolocalização não estiver disponível
      },
      { timeout: 5000, maximumAge: 0 }
    );
  }

  // Criar mapa centralizado no ponto inicial
  map = new google.maps.Map(document.getElementById("map"), {
    center: mapCenter,
    zoom: initialZoom,
    mapTypeControl: true,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
      position: google.maps.ControlPosition.TOP_RIGHT,
    },
    streetViewControl: true,
    streetViewControlOptions: {
      position: google.maps.ControlPosition.RIGHT_BOTTOM,
    },
    fullscreenControl: true,
    fullscreenControlOptions: {
      position: google.maps.ControlPosition.RIGHT_TOP,
    },
    zoomControl: true,
    zoomControlOptions: {
      position: google.maps.ControlPosition.RIGHT_CENTER,
    },
    styles: [
      {
        featureType: "poi.business",
        stylers: [{ visibility: "off" }],
      },
      {
        featureType: "transit",
        elementType: "labels.icon",
        stylers: [{ visibility: "off" }],
      },
    ],
  });

  // Tentar obter localização do usuário
  getUserLocation();

  // Adicionar marcadores para as coletas do dia
  setTimeout(() => addCollectionMarkers(), 500);
}

// Obter localização do usuário
function getUserLocation() {
  if (navigator.geolocation) {
    const options = {
      enableHighAccuracy: true,
      timeout: 5000,
      maximumAge: 0,
    };

    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        // Adicionar marcador da localização do usuário
        new google.maps.Marker({
          position: userLocation,
          map: map,
          title: "Você está aqui",
          animation: google.maps.Animation.DROP,
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 12,
            fillColor: "#4285F4",
            fillOpacity: 1,
            strokeColor: "#ffffff",
            strokeWeight: 3,
          },
          zIndex: 1000,
        });

        // Adicionar círculo de precisão
        new google.maps.Circle({
          strokeColor: "#4285F4",
          strokeOpacity: 0.3,
          strokeWeight: 1,
          fillColor: "#4285F4",
          fillOpacity: 0.1,
          map: map,
          center: userLocation,
          radius: position.coords.accuracy,
        });

        // Centralizar mapa
        map.setCenter(userLocation);
        map.setZoom(14);
      },
      (error) => {
        console.log("Erro ao obter localização:", error);
        showNotification(
          "💡 Permita o acesso à localização para calcular rotas",
          "info"
        );
      },
      options
    );
  } else {
    showNotification("⚠️ Seu navegador não suporta geolocalização", "warning");
  }
}

// Adicionar marcadores para todas as coletas
function addCollectionMarkers() {
  const collections = document.querySelectorAll(".collection-item");

  collections.forEach((collection, index) => {
    const locationElement = collection.querySelector(".location");
    if (!locationElement) return;

    const address = locationElement.textContent.trim();
    const timeElement = collection.querySelector(".time");
    const quantityElement = collection.querySelector(".quantity");
    const statusElement = collection.querySelector(".status");

    const time = timeElement ? timeElement.textContent : "N/A";
    const quantity = quantityElement ? quantityElement.textContent : "N/A";
    const statusText = statusElement ? statusElement.textContent : "Agendada";

    // Geocodificar com delay para animação
    setTimeout(() => {
      geocodeAddress(address, time, quantity, statusText, index);
    }, index * 200);
  });
}

// Geocodificar endereço e criar marcador
function geocodeAddress(address, time, quantity, statusText, index) {
  const fullAddress = `${address}, Campinas, São Paulo, Brasil`;

  geocoder.geocode({ address: fullAddress }, (results, status) => {
    if (status === "OK") {
      const location = results[0].geometry.location;
      const position = {
        lat: location.lat(),
        lng: location.lng(),
      };

      // Escolher cor do marcador
      let markerColor = "red";
      if (statusText.toLowerCase().includes("conclu")) markerColor = "green";
      else if (statusText.toLowerCase().includes("andamento"))
        markerColor = "yellow";

      const marker = new google.maps.Marker({
        position: position,
        map: map,
        title: address,
        animation: google.maps.Animation.DROP,
        label: {
          text: `${index + 1}`,
          color: "white",
          fontSize: "15px",
          fontWeight: "bold",
        },
        icon: {
          url: `http://maps.google.com/mapfiles/ms/icons/${markerColor}-dot.png`,
          scaledSize: new google.maps.Size(40, 40),
        },
      });

      // Info Window
      const infoWindow = new google.maps.InfoWindow({
        content: `
          <div style="padding: 18px; min-width: 240px; font-family: 'Poppins', sans-serif;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 2px solid #ffce46;">
              <div style="background: linear-gradient(135deg, #223e2a, #386043); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">
                ${index + 1}
              </div>
              <h4 style="margin: 0; color: #223e2a; font-size: 17px; font-weight: 600;">Coleta #${
                index + 1
              }</h4>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 18px;">🕐</span>
                <div>
                  <div style="font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">Horário</div>
                  <div style="color: #223e2a; font-weight: 600; font-size: 14px;">${time}</div>
                </div>
              </div>
              
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 18px;">🛢️</span>
                <div>
                  <div style="font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">Quantidade</div>
                  <div style="color: #223e2a; font-weight: 600; font-size: 14px;">${quantity}</div>
                </div>
              </div>
              
              <div style="display: flex; align-items: flex-start; gap: 8px;">
                <span style="font-size: 18px;">📍</span>
                <div style="flex: 1;">
                  <div style="font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">Endereço</div>
                  <div style="color: #4c4c4c; font-size: 13px; line-height: 1.4;">${address}</div>
                </div>
              </div>
            </div>
            
            <button onclick="showRouteToLocation(${position.lat}, ${
          position.lng
        }, '${address.replace(/'/g, "\\'")}', ${index + 1})" 
                    style="width: 100%; padding: 12px; background: linear-gradient(135deg, #4CAF50, #45a049); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(76, 175, 80, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(76, 175, 80, 0.3)'">
              🗺️ Calcular Rota
            </button>
          </div>
        `,
      });

      marker.addListener("click", () => {
        markers.forEach((m) => {
          if (m.infoWindow) m.infoWindow.close();
        });
        infoWindow.open(map, marker);
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => marker.setAnimation(null), 2000);
      });

      markers.push({ marker, position, address, time, quantity, infoWindow });
    } else {
      console.error("Geocode falhou:", address, status);
    }
  });
}

// Mostrar rota usando Routes API
function showRouteToLocation(lat, lng, address, markerNumber) {
  const destination = { lat: lat, lng: lng };

  if (!userLocation) {
    showNotification("🔍 Obtendo sua localização...", "info");

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
          calculateRouteWithRoutesAPI(
            userLocation,
            destination,
            address,
            markerNumber
          );
        },
        (error) => {
          showNotification("❌ Permita o acesso à localização", "error");
        }
      );
    } else {
      showNotification("⚠️ Geolocalização não disponível", "warning");
    }
  } else {
    calculateRouteWithRoutesAPI(
      userLocation,
      destination,
      address,
      markerNumber
    );
  }
}

// Obter meio de transporte do coletor
async function getMeioTransporte() {
  try {
    const response = await fetch("../api/get_meio_transporte.php");
    const data = await response.json();
    return data.meio_transporte || "carro";
  } catch (error) {
    console.error("Erro ao obter meio de transporte:", error);
    return "carro"; // default
  }
}

// Mapear meio de transporte para modo de viagem da API
function getTravelMode(meioTransporte) {
  const modos = {
    carro: "DRIVE",
    bicicleta: "BICYCLE",
    motocicleta: "TWO_WHEELER",
    carroca: "DRIVE",
    a_pe: "WALK",
  };
  return modos[meioTransporte] || "DRIVE";
}

// Calcular tempo estimado baseado no meio de transporte
function calcularTempoEstimado(distanceMeters, meioTransporte) {
  // Velocidades médias em km/h
  const velocidades = {
    carro: 40,
    motocicleta: 45,
    bicicleta: 15,
    carroca: 10,
    a_pe: 5,
  };

  const velocidade = velocidades[meioTransporte] || 40;
  const distanceKm = distanceMeters / 1000;
  const horas = distanceKm / velocidade;
  const minutos = Math.ceil(horas * 60);

  return minutos;
}

// Calcular rota usando Routes API
async function calculateRouteWithRoutesAPI(
  origin,
  destination,
  address,
  markerNumber
) {
  showLoadingPanel("🗺️ Calculando melhor rota...");

  let loadingCompleted = false;

  // Timeout de segurança para esconder o loading após 5 segundos
  const timeoutId = setTimeout(() => {
    if (!loadingCompleted) {
      loadingCompleted = true;
      hideLoadingPanel();
      showNotification(
        "⏱️ A requisição demorou muito. Tente novamente.",
        "warning"
      );
    }
  }, 5000);

  try {
    // Obter meio de transporte do banco com timeout
    let meioTransporte = "carro";
    try {
      meioTransporte = await Promise.race([
        getMeioTransporte(),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error("Timeout")), 5000)
        ),
      ]);
    } catch (e) {
      console.warn("Erro ao obter meio de transporte, usando padrão:", e);
      meioTransporte = "carro";
    }

    const travelMode = getTravelMode(meioTransporte);

    // Configurar preferência de roteamento baseado no modo
    const requestBody = {
      origin: {
        location: {
          latLng: {
            latitude: origin.lat,
            longitude: origin.lng,
          },
        },
      },
      destination: {
        location: {
          latLng: {
            latitude: destination.lat,
            longitude: destination.lng,
          },
        },
      },
      travelMode: travelMode,
      computeAlternativeRoutes: false,
      languageCode: "pt-BR",
      units: "METRIC",
    };

    // TRAFFIC_AWARE só funciona com DRIVE
    if (travelMode === "DRIVE") {
      requestBody.routingPreference = "TRAFFIC_AWARE";
    }

    const response = await fetch(
      "https://routes.googleapis.com/directions/v2:computeRoutes",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Goog-Api-Key": GOOGLE_MAPS_API_KEY,
          "X-Goog-FieldMask":
            "routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline",
        },
        body: JSON.stringify(requestBody),
      }
    );

    clearTimeout(timeoutId);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (!loadingCompleted) {
      loadingCompleted = true;
      hideLoadingPanel(); // IMPORTANTE: Esconder loading ANTES de mostrar resultado
    }

    if (data.routes && data.routes.length > 0) {
      const route = data.routes[0];
      displayRoute(
        route,
        origin,
        destination,
        address,
        markerNumber,
        meioTransporte
      );
      showNotification("✅ Rota calculada com sucesso!", "success");
    } else {
      throw new Error("Nenhuma rota encontrada");
    }
  } catch (error) {
    clearTimeout(timeoutId);
    if (!loadingCompleted) {
      loadingCompleted = true;
      hideLoadingPanel(); // IMPORTANTE: Esconder loading em caso de erro também
    }
    console.error("Erro ao calcular rota:", error);
    showNotification(
      "❌ Erro ao calcular rota. Abrindo Google Maps...",
      "warning"
    );
    // Fallback: abrir diretamente no Google Maps
    setTimeout(() => {
      openInGoogleMaps(destination.lat, destination.lng);
    }, 1500);
  }
}

// Exibir rota no mapa
function displayRoute(
  route,
  origin,
  destination,
  address,
  markerNumber,
  meioTransporte
) {
  // Limpar rota anterior
  if (routePolyline) {
    routePolyline.setMap(null);
  }

  // Decodificar polyline
  const path = google.maps.geometry.encoding.decodePath(
    route.polyline.encodedPolyline
  );

  // Criar polyline
  routePolyline = new google.maps.Polyline({
    path: path,
    geodesic: true,
    strokeColor: "#4CAF50",
    strokeOpacity: 0.8,
    strokeWeight: 6,
    map: map,
  });

  // Ajustar bounds
  const bounds = new google.maps.LatLngBounds();
  path.forEach((point) => bounds.extend(point));
  map.fitBounds(bounds, {
    padding: { top: 100, right: 100, bottom: 100, left: 100 },
  });

  // Calcular distância e duração baseado no meio de transporte
  const distanceKm = (route.distanceMeters / 1000).toFixed(1);
  const durationMin = calcularTempoEstimado(
    route.distanceMeters,
    meioTransporte
  );

  // Ícones por meio de transporte
  const icones = {
    carro: "🚗",
    motocicleta: "🏍️",
    bicicleta: "🚴",
    carroca: "🛺",
    a_pe: "🚶",
  };

  const icone = icones[meioTransporte] || "🚗";

  // Mostrar painel de informações
  showRouteInfo(
    {
      distance: `${distanceKm} km`,
      duration: `${durationMin} min`,
      distanceMeters: route.distanceMeters,
      meioTransporte: meioTransporte,
      icone: icone,
    },
    address,
    destination,
    markerNumber
  );
}

// Exibir informações da rota
function showRouteInfo(route, address, destination, markerNumber) {
  const existingPanel = document.getElementById("route-info-panel");
  if (existingPanel) {
    existingPanel.remove();
  }

  // Nomes amigáveis dos meios de transporte
  const nomesTransporte = {
    carro: "Carro",
    motocicleta: "Motocicleta",
    bicicleta: "Bicicleta",
    carroca: "Carroça",
    a_pe: "A pé",
  };

  const nomeTransporte = nomesTransporte[route.meioTransporte] || "Carro";

  const routeInfoPanel = document.createElement("div");
  routeInfoPanel.id = "route-info-panel";

  routeInfoPanel.innerHTML = `
    <div style="position: relative;">
      <button onclick="closeRouteInfo()" class="route-close-btn">×</button>
      
      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 2px solid #ffce46;">
        <div style="background: linear-gradient(135deg, #223e2a, #386043); color: white; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; box-shadow: 0 3px 10px rgba(34, 62, 42, 0.3);">
          ${markerNumber}
        </div>
        <div>
          <h4 style="margin: 0; color: #223e2a; font-size: 1.15rem; font-weight: 700;">Rota Calculada</h4>
          <p style="margin: 2px 0 0 0; color: #666; font-size: 0.8rem;">${route.icone} Via ${nomeTransporte}</p>
        </div>
      </div>
      
      <div style="background: #f8f4e7; padding: 14px; border-radius: 10px; margin-bottom: 14px;">
        <p style="margin: 0; color: #4c4c4c; font-size: 0.85rem; display: flex; align-items: flex-start; gap: 8px;">
          <span style="font-size: 16px;">📍</span>
          <span style="flex: 1; line-height: 1.4;"><strong style="color: #223e2a;">Destino:</strong><br>${address}</span>
        </p>
      </div>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
        <div style="background: white; padding: 12px; border-radius: 8px; border: 2px solid #e0e0e0; text-align: center;">
          <div style="font-size: 20px; margin-bottom: 4px;">📏</div>
          <div style="font-size: 1.3rem; font-weight: 700; color: #223e2a; margin-bottom: 2px;">${route.distance}</div>
          <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">Distância</div>
        </div>
        
        <div style="background: white; padding: 12px; border-radius: 8px; border: 2px solid #e0e0e0; text-align: center;">
          <div style="font-size: 20px; margin-bottom: 4px;">⏱️</div>
          <div style="font-size: 1.3rem; font-weight: 700; color: #223e2a; margin-bottom: 2px;">${route.duration}</div>
          <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">Tempo Estimado</div>
        </div>
      </div>
      
      <button onclick="openInGoogleMaps(${destination.lat}, ${destination.lng})" class="open-maps-btn">
        <span style="font-size: 20px;">🚗</span>
        <span>Iniciar Navegação no Maps</span>
      </button>
      
      <button onclick="shareRoute('${address}', '${route.distance}', '${route.duration}')" 
              style="margin-top: 8px; width: 100%; padding: 10px; background: white; color: #223e2a; border: 2px solid #223e2a; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s;">
        <span style="font-size: 18px;">📤</span>
        <span>Compartilhar Rota</span>
      </button>
    </div>
  `;

  document.body.appendChild(routeInfoPanel);
}

// Compartilhar rota
function shareRoute(address, distance, duration) {
  const text = `📍 Rota para coleta:\n${address}\n\n📏 Distância: ${distance}\n⏱️ Tempo: ${duration}`;

  if (navigator.share) {
    navigator
      .share({
        title: "Rota de Coleta - Ecoleta",
        text: text,
      })
      .catch(() => {});
  } else {
    navigator.clipboard.writeText(text).then(() => {
      showNotification("✅ Informações copiadas!", "success");
    });
  }
}

// Fechar painel de rota
function closeRouteInfo() {
  const panel = document.getElementById("route-info-panel");
  if (panel) {
    panel.style.opacity = "0";
    panel.style.transform = "translateX(100%)";
    panel.style.transition = "all 0.3s";
    setTimeout(() => panel.remove(), 300);
  }

  if (routePolyline) {
    routePolyline.setMap(null);
  }

  if (userLocation) {
    map.setCenter(userLocation);
    map.setZoom(14);
  }
}

// Abrir Google Maps
function openInGoogleMaps(lat, lng) {
  const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`;
  window.open(url, "_blank");
  showNotification("🚗 Abrindo Google Maps...", "success");
}

// Loading panel
function showLoadingPanel(message) {
  // Remove painel anterior se existir
  const existingPanel = document.getElementById("loading-panel");
  if (existingPanel) {
    existingPanel.remove();
  }

  const loadingPanel = document.createElement("div");
  loadingPanel.id = "loading-panel";
  loadingPanelId = loadingPanel.id;
  loadingPanel.style.cssText = `
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 35px 40px;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    z-index: 2000;
    text-align: center;
    border: 3px solid #ffce46;
  `;
  loadingPanel.innerHTML = `
    <div style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #ffce46; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 18px;"></div>
    <p style="color: #223e2a; font-weight: 600; margin: 0; font-size: 1.05rem;">${message}</p>
  `;
  document.body.appendChild(loadingPanel);
}

function hideLoadingPanel() {
  const loadingPanel = document.getElementById("loading-panel");
  if (loadingPanel) {
    loadingPanel.style.opacity = "0";
    loadingPanel.style.transition = "opacity 0.3s";
    setTimeout(() => {
      if (loadingPanel && loadingPanel.parentNode) {
        loadingPanel.remove();
      }
    }, 300);
  }
}

// Sistema de notificações
function showNotification(message, type = "info") {
  const colors = {
    success: "#4CAF50",
    error: "#f44336",
    warning: "#ff9800",
    info: "#2196F3",
  };

  const notification = document.createElement("div");
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${colors[type]};
    color: white;
    padding: 16px 24px;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    z-index: 3000;
    font-weight: 500;
    font-size: 0.95rem;
    animation: slideInRight 0.3s ease-out;
    max-width: 320px;
  `;
  notification.textContent = message;
  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.opacity = "0";
    notification.style.transform = "translateX(100%)";
    notification.style.transition = "all 0.3s";
    setTimeout(() => notification.remove(), 300);
  }, 3500);
}

// Event listeners
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(() => {
    document
      .querySelectorAll(".next-collection .view-map-btn")
      .forEach((button) => {
        button.addEventListener("click", function (e) {
          e.preventDefault();
          const locationElement =
            this.closest(".card-content").querySelector(".location span");
          if (locationElement) {
            const address = locationElement.textContent.trim();
            geocodeAndShowRoute(address);
          }
        });
      });

    document
      .querySelectorAll(".collection-item .view-map-btn")
      .forEach((button, index) => {
        button.addEventListener("click", function (e) {
          e.preventDefault();
          const locationElement =
            this.closest(".collection-item").querySelector(".location");
          if (locationElement) {
            const address = locationElement.textContent.trim();
            geocodeAndShowRoute(address, index + 1);
          }

          setTimeout(() => {
            document.getElementById("map").scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
          }, 300);
        });
      });
  }, 1000);
});

// Geocodificar e mostrar rota
function geocodeAndShowRoute(address, markerNumber = 1) {
  if (!geocoder) {
    showNotification("⏳ Aguarde o mapa carregar...", "info");
    setTimeout(() => geocodeAndShowRoute(address, markerNumber), 1000);
    return;
  }

  const fullAddress = `${address}, Campinas, São Paulo, Brasil`;

  showLoadingPanel("🔍 Localizando endereço...");

  geocoder.geocode({ address: fullAddress }, (results, status) => {
    hideLoadingPanel();

    if (status === "OK") {
      const location = results[0].geometry.location;
      const position = {
        lat: location.lat(),
        lng: location.lng(),
      };
      showRouteToLocation(position.lat, position.lng, address, markerNumber);
    } else {
      showNotification("❌ Endereço não encontrado", "error");
    }
  });
}

// Inicializar
window.initMap = initMap;

function toggleMobileMenu() {
  const sidebar = document.querySelector(".sidebar");
  if (sidebar) {
    sidebar.classList.toggle("mobile-active");
  }
}

// Animações CSS
const style = document.createElement("style");
style.textContent = `
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
`;
document.head.appendChild(style);
