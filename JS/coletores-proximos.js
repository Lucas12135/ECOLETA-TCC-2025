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

// Função para buscar coletores próximos com geolocalização
async function buscarColetoresPorGeolocation() {
  if (!navigator.geolocation) {
    alert("Seu navegador não suporta geolocalização");
    return;
  }

  // Mostrar mensagem de carregamento
  exibirCarregando();

  navigator.geolocation.getCurrentPosition(
    async function (position) {
      const latitude = position.coords.latitude;
      const longitude = position.coords.longitude;

      try {
        const response = await fetch(
          `api/get_coletores_proximos.php?latitude=${latitude}&longitude=${longitude}`
        );
        const text = await response.text();

        // Debug: verificar se é JSON válido
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
          exibirColetores(data.coletores);
        } else if (data.success) {
          exibirMensagemVazia();
        } else {
          exibirMensagemErro(data.message || "Erro ao buscar coletores");
        }
      } catch (error) {
        console.error("Erro ao buscar coletores:", error);
        exibirMensagemErro("Erro ao buscar coletores próximos");
      }
    },
    function (error) {
      console.error("Erro de geolocalização:", error);
      exibirMensagemErro(
        "Erro ao obter sua localização. Tente inserir um CEP manualmente."
      );
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0,
    }
  );
}

// Função para buscar coletores próximos por CEP
async function buscarColetoresProximos(cep) {
  try {
    exibirCarregando();
    const response = await fetch(
      `api/get_coletores_proximos.php?cep=${encodeURIComponent(cep)}`
    );
    const text = await response.text();

    // Debug: verificar se é JSON válido
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
      exibirColetores(data.coletores);
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

    console.log("Coletor nome:", coletor.nome); // Debug

    const card = document.createElement("div");
    card.className = "coletor-card";
    card.innerHTML = `
      <div class="card-content" style="color: var(--cor-texto-primaria);">
        <div class="avatar-placeholder">
          &#x1F464;
        </div>
        
        <h2 class="coletor-nome">${escapeHtml(coletor.nome)}</h2>
        <p class="coletor-info">${tempoAfiliacao}</p>
        
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
          Distância: ${distancia} km | Raio: ${coletor.raio_atuacao} km
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
      <p style="font-size: 18px; color: #666;">Nenhum coletor disponível na região</p>
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
  // Redirecionar para página de perfil ou abrir modal
  window.location.href = `PAGINAS_COLETOR/perfil.php?id=${id_coletor}`;
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
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        console.log("Localização obtida automaticamente:", position.coords);
        // Auto-buscar coletores próximos
        buscarColetoresPorGeolocation();
      },
      function (error) {
        console.log("Geolocalização não disponível:", error);
      }
    );
  }
});
