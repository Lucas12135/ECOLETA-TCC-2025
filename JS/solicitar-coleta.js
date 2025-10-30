// Libras
document.addEventListener('DOMContentLoaded', function() {
    new window.VLibras.Widget('https://vlibras.gov.br/app');
});

function toggleAccessibility(event) {
    if (event) event.stopPropagation();
    const vlibrasButton = document.querySelector("div[vw-access-button]");
    if (vlibrasButton) {
        vlibrasButton.click();
    }
}

// Funções para o CEP
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = value;

            if (value.length === 8) {
                buscarCep(value);
            }
        });

        // Adicionar evento de blur para buscar CEP quando o campo perde o foco
        cepInput.addEventListener('blur', function(e) {
            const cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCep(cep);
            }
        });
    }
});

function limpa_formulario_cep() {
    document.getElementById("rua").value = "";
    document.getElementById("bairro").value = "";
    document.getElementById("cidade").value = "";
    
    // Habilita os campos para edição
    ["rua", "bairro", "cidade"].forEach(campo => {
        const elemento = document.getElementById(campo);
        elemento.readOnly = false;
        elemento.classList.remove("preenchido-cep");
    });
}

function preencheCampos(endereco) {
    if (!endereco) return;

    const campos = {
        rua: endereco.logradouro,
        bairro: endereco.bairro,
        cidade: endereco.localidade
    };

    // Preenche os campos e ajusta suas propriedades
    Object.entries(campos).forEach(([campo, valor]) => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.value = valor;
            if (valor) {
                elemento.readOnly = true;
                elemento.classList.add("preenchido-cep");
            } else {
                elemento.readOnly = false;
                elemento.classList.remove("preenchido-cep");
            }
        }
    });

    // Foca no campo número após preenchimento
    const numeroInput = document.getElementById("numero");
    if (numeroInput) numeroInput.focus();
}

function buscarCep(cep) {
    // Remove qualquer caracter não numérico
    cep = cep.replace(/\D/g, '');

    if (cep.length !== 8) {
        limpa_formulario_cep();
        return;
    }

    // Cria e mostra o indicador de carregamento
    const loadingIndicator = document.createElement("div");
    loadingIndicator.className = "loading-indicator";
    loadingIndicator.innerHTML = "Buscando CEP...";
    const addressContainer = document.querySelector(".address-container");
    if (addressContainer) {
        addressContainer.insertBefore(loadingIndicator, addressContainer.firstChild);
    }

    // Faz a requisição para a API do ViaCEP
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição do CEP');
            }
            return response.json();
        })
        .then(data => {
            if (data.erro) {
                throw new Error('CEP não encontrado');
            }
            preencheCampos(data);
        })
        .catch(error => {
            console.error('Erro:', error);
            limpa_formulario_cep();
            alert("CEP não encontrado ou erro na busca. Por favor, verifique o CEP informado.");
        })
        .finally(() => {
            // Remove o indicador de carregamento
            if (loadingIndicator && loadingIndicator.parentNode) {
                loadingIndicator.remove();
            }
        });
}

// Validação da Data
document.getElementById("data").addEventListener("input", function(e) {
    const selectedDate = new Date(e.target.value);
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 30); // Permite agendamento até 30 dias no futuro

    // Remove as horas para comparação apenas das datas
    today.setHours(0, 0, 0, 0);
    selectedDate.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        alert("Por favor, selecione uma data futura.");
        e.target.value = "";
    } else if (selectedDate > maxDate) {
        alert("Por favor, selecione uma data dentro dos próximos 30 dias.");
        e.target.value = "";
    }
});

// Validação do Volume
document.getElementById("volume").addEventListener("input", function(e) {
    const value = parseFloat(e.target.value);
    if (value < 1) {
        alert("O volume mínimo para coleta é de 1 litro.");
        e.target.value = "1";
    } else if (value > 100) {
        alert("Para volumes maiores que 100 litros, entre em contato com o suporte.");
        e.target.value = "100";
    }
});

// Validação do formulário completo
function validateForm() {
    const requiredFields = document.querySelectorAll("[required]");
    const submitBtn = document.querySelector("button[type='submit']");
    let allValid = true;

    requiredFields.forEach((field) => {
        if (!field.value.trim()) {
            allValid = false;
        }
    });

    submitBtn.disabled = !allValid;
    return allValid;
}

// Adiciona validação em tempo real para todos os campos
document.querySelectorAll("input, select, textarea").forEach((field) => {
    field.addEventListener("input", validateForm);
    field.addEventListener("change", validateForm);
});

// Manipulação do envio do formulário
document.querySelector(".collection-form").addEventListener("submit", function(e) {
    e.preventDefault();

    if (!validateForm()) {
        alert("Por favor, preencha todos os campos obrigatórios.");
        return;
    }

    // Aqui você pode adicionar a lógica para enviar os dados para o servidor
    const formData = new FormData(this);
    
    // Exemplo de como você pode processar os dados
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    // Log dos dados (para debug)
    console.log("Dados da solicitação:", data);

    // Aqui você pode adicionar a chamada AJAX para enviar os dados
    this.submit();
});
