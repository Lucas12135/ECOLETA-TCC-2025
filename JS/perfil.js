// Funções para manipulação da foto de perfil
function updateProfilePhoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Verifica o tamanho do arquivo (máximo 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert("A imagem deve ter no máximo 2MB");
            input.value = "";
            return;
        }

        // Verifica o tipo do arquivo
        if (!file.type.match("image/jpeg") && !file.type.match("image/png")) {
            alert("Por favor, selecione apenas arquivos JPG ou PNG");
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("profilePhoto").src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Máscara para telefone
document.getElementById("telefone").addEventListener("input", function(e) {
    let value = e.target.value.replace(/\D/g, "");
    if (value.length > 11) value = value.slice(0, 11);
    
    // Formata o número conforme vai digitando
    if (value.length > 0) {
        // Formato: (XX) XXXXX-XXXX
        value = value.replace(/^(\d{2})(\d)/g, "($1) $2");
        value = value.replace(/(\d{5})(\d)/, "$1-$2");
    }
    
    e.target.value = value;
});

// Funções para o CEP
document.getElementById("cep").addEventListener("input", function(e) {
    let value = e.target.value.replace(/\D/g, "");
    if (value.length > 8) value = value.slice(0, 8);
    value = value.replace(/^(\d{5})(\d)/, "$1-$2");
    e.target.value = value;

    if (value.length === 8) {
        buscarCep(value);
    }
});

function limpa_formulario_cep() {
    document.getElementById("rua").value = "";
    document.getElementById("bairro").value = "";
    document.getElementById("cidade").value = "";
}

function preencheCampos(endereco) {
    document.getElementById("rua").value = endereco.logradouro;
    document.getElementById("bairro").value = endereco.bairro;
    document.getElementById("cidade").value = endereco.localidade;
}

function buscarCep(cep) {
    cep = cep.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                throw new Error('CEP não encontrado');
            }
            preencheCampos(data);
        })
        .catch(error => {
            console.error('Erro:', error);
            limpa_formulario_cep();
            alert("CEP não encontrado ou erro na busca.");
        });
}

// Função para salvar as alterações do perfil
function saveProfile() {
    // Coleta os dados dos formulários
    const personalData = new FormData(document.getElementById('personalInfoForm'));
    const addressData = new FormData(document.getElementById('addressForm'));
    
    const data = {
        personal: Object.fromEntries(personalData),
        address: Object.fromEntries(addressData)
    };

    // Aqui você deve implementar a lógica para enviar os dados para o servidor
    console.log('Dados a serem salvos:', data);
    
    // Exemplo de requisição AJAX (você deve adaptar para seu backend)
    fetch('atualizar-perfil.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        alert('Perfil atualizado com sucesso!');
    })
    .catch(error => {
        console.error('Erro ao salvar:', error);
        alert('Erro ao atualizar o perfil. Tente novamente.');
    });
}

// Função para resetar os formulários
function resetForms() {
    document.getElementById('personalInfoForm').reset();
    document.getElementById('addressForm').reset();
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Carrega os dados do perfil ao iniciar (você deve implementar a lógica do backend)
    fetch('carregar-perfil.php')
        .then(response => response.json())
        .then(data => {
            // Preenche os formulários com os dados recebidos
            Object.keys(data.personal).forEach(key => {
                const input = document.getElementById(key);
                if (input) input.value = data.personal[key];
            });

            Object.keys(data.address).forEach(key => {
                const input = document.getElementById(key);
                if (input) input.value = data.address[key];
            });
        })
        .catch(error => console.error('Erro ao carregar dados:', error));
});