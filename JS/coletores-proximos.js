// Função para gerar estrelas com base na avaliação
function gerarEstrelas(avaliacao) {
  const mediaAvaliacao = parseFloat(avaliacao) || 0;
  let estrelas = "";

  for (let i = 1; i <= 5; i++) {
    if (i <= Math.floor(mediaAvaliacao)) {
      estrelas += '<span class="star filled">&#9733;</span>';
    } else if (i - 0.5 === mediaAvaliacao) {
      estrelas += '<span class="star half">&#9733;</span>';
    } else {
      estrelas += '<span class="star">&#9733;</span>';
    }
  }

  return estrelas;
}

// Função para calcular distância entre duas coordenadas (Haversine)
function calcularDistancia(lat1, lon1, lat2, lon2) {
  const R = 6371; // Raio da Terra em km
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

// Função para formatar data de criação
function calcularTempoAfiliacao(data_criacao) {
  const dataCriacao = new Date(data_criacao);
  const hoje = new Date();
  const diferenca = hoje - dataCriacao;
  const anos = Math.floor(diferenca / (1000 * 60 * 60 * 24 * 365));

  if (anos > 0) {
    return `Afiliado desde ${anos === 1 ? "um ano" : anos + " anos"}`;
  }

  const meses = Math.floor(diferenca / (1000 * 60 * 60 * 24 * 30));
  if (meses > 0) {
    return `Afiliado há ${meses} meses`;
  }

  return "Afiliado recentemente";
}

// Variável global para armazenar localização do usuário
let userCurrentLocation = null;

// Função para obter localização do usuário
function obterLocalizacaoUsuario() {
  return new Promise((resolve, reject) => {
    if (userCurrentLocation) {
      resolve(userCurrentLocation);
      return;
    }

    if (!navigator.geolocation) {
      reject(new Error("Geolocalização não suportada"));
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        userCurrentLocation = {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude
        };
        console.log("Localização do usuário obtida:", userCurrentLocation);
        resolve(userCurrentLocation);
      },
      (error) => {
        console.error("Erro ao obter localização:", error);
        reject(error);
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
      }
    );
  });
}

// Função para buscar coletores próximos com geolocalização
async function buscarColetoresPorGeolocation() {
  exibirCarregando();

  try {
    const location = await obterLocalizacaoUsuario();
    
    const response = await fetch(
      `api/get_coletores_proximos.php?latitude=${location.latitude}&longitude=${location.longitude}`
    );
    const text = await response.text();

    console.log("Resposta da API:", text);

    let data;
    try {
      data = JSON.parse(text);
    } catch (parseError) {
      console.error("Erro ao fazer parse do JSON:", text);
      exibirMensagemErro("Erro ao buscar coletores. Tente novamente.");
      return;
    }

    if (data.success && data.coletores.length > 0) {
      // Filtrar coletores pelo raio de atuação
      const coletoresFiltrados = filtrarColetoresPorRaio(data.coletores, location);
      
      if (coletoresFiltrados.length > 0) {
        exibirColetores(coletoresFiltrados);
      } else {
        exibirMensagemVazia();
      }
    } else if (data.success) {
      exibirMensagemVazia();
    } else {
      exibirMensagemErro(data.message || "Erro ao buscar coletores");
    }
  } catch (error) {
    console.error("Erro ao buscar coletores:", error);
    exibirMensagemErro("Erro ao obter sua localização. Tente inserir um endereço manualmente.");
  }
}

// Função para filtrar coletores pelo raio de atuação
function filtrarColetoresPorRaio(coletores, userLocation) {
  return coletores.filter((coletor) => {
    // Calcular distância real
    const distancia = calcularDistancia(
      userLocation.latitude,
      userLocation.longitude,
      parseFloat(coletor.latitude),
      parseFloat(coletor.longitude)
    );
    
    // Adicionar distância calculada ao objeto
    coletor.distancia = distancia;
    
    // Verificar se está dentro do raio de atuação
    const raioAtuacao = parseFloat(coletor.raio_atuacao) || 50;
    
    console.log(`Coletor ${coletor.nome}: distância=${distancia.toFixed(2)}km, raio=${raioAtuacao}km`);
    
    return distancia <= raioAtuacao;
  });
}

// Função para buscar coletores próximos por CEP
async function buscarColetoresProximos(cep) {
  try {
    exibirCarregando();
    const response = await fetch(
      `api/get_coletores_proximos.php?cep=${encodeURIComponent(cep)}`
    );
    const text = await response.text();

    console.log("Resposta da API (CEP):", text);

    let data;
    try {
      data = JSON.parse(text);
    } catch (parseError) {
      console.error("Erro ao fazer parse do JSON:", text);
      exibirMensagemErro("Erro ao buscar coletores. Tente novamente.");
      return;
    }

    if (data.success && data.coletores.length > 0) {
      // Se tem localização do CEP, filtrar por raio
      if (data.latitude && data.longitude) {
        const location = {
          latitude: parseFloat(data.latitude),
          longitude: parseFloat(data.longitude)
        };
        
        const coletoresFiltrados = filtrarColetoresPorRaio(data.coletores, location);
        
        if (coletoresFiltrados.length > 0) {
          exibirColetores(coletoresFiltrados);
        } else {
          exibirMensagemVazia();
        }
      } else {
        exibirColetores(data.coletores);
      }
    } else if (data.success) {
      exibirMensagemVazia();
    } else {
      exibirMensagemErro(data.message || "Erro ao buscar coletores");
    }
  } catch (error) {
    console.error("Erro ao buscar coletores:", error);
    exibirMensagemErro("Erro ao buscar coletores próximos");
  }
}

// Função para exibir carregando
function exibirCarregando() {
  const container = document.querySelector(".coletores-container");

  if (!container) return;

  container.innerHTML = `
    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
      <p style="font-size: 18px; color: #666;">Buscando coletores próximos...</p>
      <div style="margin-top: 20px;">
        <div style="border: 4px solid #f3f3f3; border-top: 4px solid #4CAF50; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
      </div>
    </div>
    <style>
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    </style>
  `;
}

// Função para exibir os coletores na página
function exibirColetores(coletores) {
  const container = document.querySelector(".coletores-container");

  if (!container) return;

  // Limpar cards antigos
  container.innerHTML = "";

  coletores.forEach((coletor) => {
    const avaliacaoMedia = parseFloat(coletor.avaliacao_media) || 0;
    const totalAvaliacoes = coletor.total_avaliacoes || 0;
    const estrelas = gerarEstrelas(avaliacaoMedia);
    const tempoAfiliacao = calcularTempoAfiliacao(coletor.data_criacao);
    const distancia = coletor.distancia ? coletor.distancia.toFixed(1) : "N/A";

    // Definir foto de perfil com fallback para avatar padrão em SVG
    const defaultAvatar =
      "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23e0e0e0' width='100' height='100'/%3E%3Ccircle cx='50' cy='35' r='20' fill='%23999'/%3E%3Cellipse cx='50' cy='70' rx='30' ry='25' fill='%23999'/%3E%3C/svg%3E";
    const foto = coletor.foto_perfil
      ? `uploads/profile_photos/${coletor.foto_perfil}`
      : defaultAvatar;

    console.log("Coletor nome:", coletor.nome);
    console.log("Foto perfil:", coletor.foto_perfil);

    const card = document.createElement("div");
    card.className = "coletor-card";
    card.setAttribute("data-id", coletor.id);
    card.innerHTML = `
      <div class="card-content" style="color: var(--cor-texto-primaria);">
        <div class="avatar-placeholder" style="display: flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background: #f0f0f0; margin: 0 auto 10px;">
          <img src="${foto}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='${defaultAvatar}'">
        </div>
        
        <h2 class="coletor-nome">${escapeHtml(coletor.nome)}</h2>
        <p class="coletor-info">${tempoAfiliacao}</p>
        
        <p class="coletor-distancia" style="color: #2ecc71; font-weight: 600; margin: 8px 0;">
          📍 ${distancia} km de você
        </p>
        
        <div class="star-rating">
          ${estrelas}
          <span class="rating-number">(${avaliacaoMedia.toFixed(
            1
          )}) • ${totalAvaliacoes} avaliações</span>
        </div>

        <p class="coletor-endereco">
          ${escapeHtml(coletor.bairro)}, ${escapeHtml(
      coletor.cidade
    )} - ${escapeHtml(coletor.estado)}
        </p>

        <p class="coletor-raio">
          Raio de atuação: ${coletor.raio_atuacao} km
        </p>

        <button class="coletor-btn" onclick="verPerfilColetor(${coletor.id})">
          Ver perfil
        </button>
      </div>
    `;

    container.appendChild(card);
  });
}

// Função para exibir mensagem de vazio
function exibirMensagemVazia() {
  const container = document.querySelector(".coletores-container");

  if (!container) return;

  container.innerHTML = `
    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
      <p style="font-size: 18px; color: #666;">Nenhum coletor disponível na sua região</p>
      <p style="font-size: 14px; color: #999; margin-top: 10px;">Os coletores mostrados atendem apenas dentro do seu raio de atuação</p>
    </div>
  `;
}

// Função para exibir mensagem de erro
function exibirMensagemErro(mensagem) {
  const container = document.querySelector(".coletores-container");

  if (!container) return;

  container.innerHTML = `
    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
      <p style="font-size: 18px; color: #d32f2f;">${mensagem}</p>
    </div>
  `;
}

// Função para escapar HTML
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// Função para ver perfil do coletor
function verPerfilColetor(id_coletor) {
  // Abrir modal do perfil do coletor
  abrirPerfilColetor(id_coletor);
}

// Event listener para o botão de busca
document.addEventListener("DOMContentLoaded", function () {
  const searchButton = document.querySelector(".search-button");
  const searchInput = document.querySelector(".search-input-container input");

  if (searchButton) {
    searchButton.addEventListener("click", function () {
      const cep = searchInput.value.trim();

      if (!cep) {
        // Se não tiver CEP, tentar geolocalização
        buscarColetoresPorGeolocation();
      } else {
        // Se tiver CEP, buscar por CEP
        buscarColetoresProximos(cep);
      }
    });
  }

  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        searchButton.click();
      }
    });
  }

  // Tentar carregar coletores automaticamente usando geolocalização ao carregar a página
  console.log("Iniciando busca automática de coletores...");
  buscarColetoresPorGeolocation();
});