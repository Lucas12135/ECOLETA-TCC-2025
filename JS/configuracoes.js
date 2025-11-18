document.addEventListener("DOMContentLoaded", () => {
  // Carregar configurações salvas
  loadSavedSettings();

  // Adicionar listeners para os formulários
  setupFormListeners();

  // Adicionar listeners para os toggles
  setupToggleListeners();

  // Adicionar listeners para os selects
  setupSelectListeners();
});

// Funções para os Modais
function showChangePasswordModal() {
  const modal = document.getElementById("changePasswordModal");
  modal.style.display = "block";
}

function showDeleteAccountModal() {
  const modal = document.getElementById("deleteAccountModal");
  modal.style.display = "block";
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.style.display = "none";
}

// Fechar modal quando clicar fora dele
window.onclick = function (event) {
  if (event.target.classList.contains("modal")) {
    event.target.style.display = "none";
  }
};

// Setup dos Formulários
function setupFormListeners() {
  // Formulário de alteração de senha
  const changePasswordForm = document.getElementById("changePasswordForm");
  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", handleChangePassword);
  }

  // Formulário de exclusão de conta
  const deleteAccountForm = document.getElementById("deleteAccountForm");
  if (deleteAccountForm) {
    deleteAccountForm.addEventListener("submit", handleDeleteAccount);
  }
}

// Setup dos Toggles
function setupToggleListeners() {
  const toggleInputs = document.querySelectorAll(
    '.toggle-switch input[type="checkbox"]'
  );
  toggleInputs.forEach((toggle) => {
    toggle.addEventListener("change", handleToggleChange);
  });
}

// Setup dos Selects
function setupSelectListeners() {
  const selects = document.querySelectorAll(".setting-select");
  selects.forEach((select) => {
    select.addEventListener("change", handleSelectChange);
  });
}

// Handlers
async function handleChangePassword(event) {
  event.preventDefault();

  const currentPassword = document.getElementById("currentPassword").value;
  const newPassword = document.getElementById("newPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  // Validação básica
  if (newPassword !== confirmPassword) {
    showNotification("As senhas não coincidem", "error");
    return;
  }

  try {
    const response = await fetch("../BANCO/change_password.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        currentPassword,
        newPassword,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("Senha alterada com sucesso!", "success");
      closeModal("changePasswordModal");
      document.getElementById("changePasswordForm").reset();
    } else {
      showNotification(data.message || "Erro ao alterar a senha", "error");
    }
  } catch (error) {
    showNotification("Erro ao processar a solicitação", "error");
  }
}

async function handleDeleteAccount(event) {
  event.preventDefault();

  const password = document.getElementById("deleteConfirmPassword").value;

  const confirmed = confirm(
    "Tem certeza que deseja excluir sua conta? Esta ação é irreversível."
  );

  if (!confirmed) return;

  try {
    const response = await fetch("../BANCO/delete_account.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ password }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("Conta excluída com sucesso", "success");
      setTimeout(() => {
        window.location.href = "../index.php";
      }, 2000);
    } else {
      showNotification(data.message || "Erro ao excluir a conta", "error");
    }
  } catch (error) {
    showNotification("Erro ao processar a solicitação", "error");
  }
}

async function handleToggleChange(event) {
  const setting = event.target.id;
  const value = event.target.checked;

  try {
    const response = await fetch("../BANCO/update_settings.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        setting,
        value,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("Configuração atualizada", "success");
    } else {
      showNotification("Erro ao atualizar configuração", "error");
      // Reverter o toggle se houver erro
      event.target.checked = !value;
    }
  } catch (error) {
    showNotification("Erro ao salvar configuração", "error");
    // Reverter o toggle se houver erro
    event.target.checked = !value;
  }
}

async function handleSelectChange(event) {
  const setting = event.target.id;
  const value = event.target.value;

  try {
    const response = await fetch("../BANCO/update_settings.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        setting,
        value,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showNotification("Preferência atualizada", "success");
    } else {
      showNotification("Erro ao atualizar preferência", "error");
    }
  } catch (error) {
    showNotification("Erro ao salvar preferência", "error");
  }
}

// Carregar configurações salvas
async function loadSavedSettings() {
  try {
    const response = await fetch("../BANCO/get_settings.php");
    const settings = await response.json();

    if (settings.success) {
      // Atualizar toggles
      Object.keys(settings.data).forEach((key) => {
        const element = document.getElementById(key);
        if (element && element.type === "checkbox") {
          element.checked = settings.data[key];
        } else if (element && element.tagName === "SELECT") {
          element.value = settings.data[key];
        }
      });
    }
  } catch (error) {
    console.error("Erro ao carregar configurações:", error);
  }
}

// Função de notificação
function showNotification(message, type = "info") {
  // Verificar se o elemento de notificação já existe
  let notification = document.querySelector(".notification");
  if (!notification) {
    // Criar elemento de notificação
    notification = document.createElement("div");
    notification.className = "notification";
    document.body.appendChild(notification);
  }

  // Adicionar classe de tipo e mensagem
  notification.className = `notification ${type}`;
  notification.textContent = message;

  // Mostrar notificação
  notification.style.display = "block";
  notification.style.opacity = "1";

  // Ocultar após 3 segundos
  setTimeout(() => {
    notification.style.opacity = "0";
    setTimeout(() => {
      notification.style.display = "none";
    }, 300);
  }, 3000);
}

// Estilização da notificação via JavaScript
const style = document.createElement("style");
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1001;
        transition: opacity 0.3s ease;
        display: none;
    }

    .notification.success {
        background-color: var(--success);
    }

    .notification.error {
        background-color: var(--danger);
    }

    .notification.info {
        background-color: var(--primary);
    }
`;
document.head.appendChild(style);

document.querySelector(".logout-btn").addEventListener("click", function () {
  window.location.href = "../logout.php";
});

// Função para aplicar máscara de CEP (00000-000)
function maskCEP(value) {
  value = value.replace(/\D/g, "");
  if (value.length > 5) {
    value = value.slice(0, 5) + "-" + value.slice(5, 8);
  }
  return value;
}

// Função para buscar endereço por CEP
async function buscarEnderecoPorCEP(cep) {
  const cepLimpo = cep.replace(/\D/g, "");
  if (cepLimpo.length !== 8) {
    alert("CEP inválido. Use o formato: 00000-000");
    return;
  }

  try {
    const response = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
    const data = await response.json();

    if (data.erro) {
      alert("CEP não encontrado");
      return;
    }

    // Preenchendo os campos com os dados do CEP
    document.getElementById("street").value = data.logradouro || "";
    document.getElementById("neighborhood").value = data.bairro || "";
    document.getElementById("city").value = data.localidade || "";
    document.getElementById("state").value = data.uf || "";
  } catch (error) {
    console.error("Erro ao buscar CEP:", error);
    alert("Erro ao buscar CEP. Verifique sua conexão.");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // Status toggle
  const statusOptions = document.querySelectorAll(".status-option");
  statusOptions.forEach((option) => {
    option.addEventListener("click", () => {
      statusOptions.forEach((opt) => opt.classList.remove("active"));
      option.classList.add("active");
    });
  });

  // Radius slider
  const radiusSlider = document.getElementById("radius-slider");
  const radiusValue = document.getElementById("radius-value");
  radiusSlider.addEventListener("input", () => {
    radiusValue.textContent = radiusSlider.value;
  });

  // Day schedule toggles
  const dayToggles = document.querySelectorAll(".day-toggle");
  dayToggles.forEach((toggle) => {
    toggle.addEventListener("change", (e) => {
      const timeInputs = e.target.parentElement.querySelector(".time-inputs");
      timeInputs.style.opacity = e.target.checked ? "1" : "0.5";
      const inputs = timeInputs.querySelectorAll("input");
      inputs.forEach((input) => (input.disabled = !e.target.checked));
    });
  });

  // Notificações
  const notificationBtn = document.querySelector(".notification-btn");
  const notificationsPopup = document.querySelector(".notifications-popup");

  document.addEventListener("click", function (event) {
    const isClickInsidePopup = notificationsPopup.contains(event.target);
    const isClickOnButton = notificationBtn.contains(event.target);

    if (!isClickInsidePopup && !isClickOnButton) {
      notificationsPopup.classList.remove("show");
    }
  });

  notificationBtn.addEventListener("click", function (event) {
    event.stopPropagation();
    notificationsPopup.classList.toggle("show");
  });

  // CEP - Máscara e Busca
  const cepInput = document.getElementById("cep");
  if (cepInput) {
    cepInput.addEventListener("input", function (e) {
      e.target.value = maskCEP(e.target.value);
    });

    cepInput.addEventListener("blur", function () {
      if (this.value.length === 9) {
        // Considera o hífen (00000-000)
        buscarEnderecoPorCEP(this.value);
      }
    });
  }

  // Transporte - Seleção
  const transportSelect = document.getElementById("transport");
  if (transportSelect) {
    transportSelect.addEventListener("change", function () {
      console.log("Meio de transporte selecionado:", this.value);
    });
  }
});
