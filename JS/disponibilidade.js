// Controle de disponibilidade
document.querySelectorAll('.dia-horario input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const horarios = this.closest('.dia-horario').querySelector('.horarios');
        const inputs = horarios.querySelectorAll('input[type="time"]');
        
        inputs.forEach(input => {
            input.disabled = !this.checked;
            if (this.checked) {
                input.required = true;
            } else {
                input.required = false;
                input.value = '';
            }
        });
    });
});

// Validação do formulário
document.getElementById('finalRegistrationForm').addEventListener('submit', function(e) {
    let valid = true;
    const diasSelecionados = document.querySelectorAll('.dia-horario input[type="checkbox"]:checked');
    
    if (diasSelecionados.length === 0) {
        alert('Selecione pelo menos um dia de disponibilidade');
        e.preventDefault();
        return;
    }

    diasSelecionados.forEach(dia => {
        const horarios = dia.closest('.dia-horario').querySelector('.horarios');
        const inicio = horarios.querySelector('input[name$="[inicio]"]');
        const fim = horarios.querySelector('input[name$="[fim]"]');
        
        if (!inicio.value || !fim.value) {
            valid = false;
        } else if (inicio.value >= fim.value) {
            alert('O horário de início deve ser menor que o horário de fim');
            valid = false;
        }
    });

    if (!valid) {
        alert('Preencha todos os horários dos dias selecionados corretamente');
        e.preventDefault();
    }
});

// Validação do CEP
function limpa_formulário_cep() {
    document.getElementById('endereco').value = "";
    document.getElementById('bairro').value = "";
    document.getElementById('cidade').value = "";
    document.getElementById('estado').value = "";
}

function meu_callback(conteudo) {
    if (!("erro" in conteudo)) {
        document.getElementById('endereco').value = conteudo.logradouro;
        document.getElementById('bairro').value = conteudo.bairro;
        document.getElementById('cidade').value = conteudo.localidade;
        document.getElementById('estado').value = conteudo.uf;
    } else {
        limpa_formulário_cep();
        alert("CEP não encontrado.");
    }
}

function pesquisacep(valor) {
    var cep = valor.replace(/\D/g, '');

    if (cep != "") {
        var validacep = /^[0-9]{8}$/;

        if(validacep.test(cep)) {
            document.getElementById('endereco').value = "...";
            document.getElementById('bairro').value = "...";
            document.getElementById('cidade').value = "...";
            document.getElementById('estado').value = "...";

            var script = document.createElement('script');
            script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
            document.body.appendChild(script);
        } else {
            limpa_formulário_cep();
            alert("Formato de CEP inválido.");
        }
    } else {
        limpa_formulário_cep();
    }
};