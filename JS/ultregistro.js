// Libras
    new window.VLibras.Widget('https://vlibras.gov.br/app');

    function toggleAccessibility(event) {
      if (event) event.stopPropagation();
      const vlibrasButton = document.querySelector('div[vw-access-button]');
      if (vlibrasButton) {
        vlibrasButton.click();
      }
    }

    // Função para voltar à tela anterior
    function goBack(event) {
      event.preventDefault();
      window.history.back();
    }

    // Função para selecionar foto (placeholder)
    function selectPhoto() {
      alert('Funcionalidade de upload de foto será implementada em breve!');
      // Aqui seria implementada a lógica de upload de foto
    }

    // Máscara para CEP
      document.getElementById('cep').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = value.replace(/(\d{5})(\d)/, '$1-$2');
      e.target.value = value;
    });

    function limpa_formulário_cep() {
    //Limpa valores do formulário de cep.
    document.getElementById('inRua').value=("");
    document.getElementById('inBairro').value=("");
    document.getElementById('inCidade').value=("");
    document.getElementById('inUF').value=("");
    document.getElementById('inComplemento').value=("");
}

function meu_callback(conteudo) {
if (!("erro" in conteudo)) {
  //Atualiza os campos com os valores.
  document.getElementById('estado').value = conteudo.uf;
  document.getElementById('bairro').value = conteudo.bairro;
  document.getElementById('cidade').value = conteudo.localidade;
} //end if.
else {
    //CEP não Encontrado.
    limpa_formulário_cep();
    alert("CEP não encontrado.");
}
}

function pesquisacep() {
//Nova variável "cep" somente com dígitos.
var inCEP = document.getElementById("cep").value;
var cep = inCEP.replace(/\D/g, '');

//Verifica se campo cep possui valor informado.
if (cep != "") {

    //Expressão regular para validar o CEP.
    var validacep = /^[0-9]{8}$/;

    //Valida o formato do CEP.
    if(validacep.test(cep)) {
        
        //Cria um elemento javascript.
        var script = document.createElement('script');

        //Sincroniza com o callback.
        script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';

        //Insere script no documento e carrega o conteúdo.
        document.body.appendChild(script);

    } //end if.
    else {
        //cep é inválido.
        limpa_formulário_cep();
    }
} //end if.
else {
    //cep sem valor, limpa formulário.
    alert("Digite um CEP.");
    limpa_formulário_cep();
}

}

    // Validação em tempo real dos campos obrigatórios
    function validateForm() {
      const requiredFields = document.querySelectorAll('[required]');
      const submitBtn = document.getElementById('submitBtn');
      let allValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          allValid = false;
        }
      });
      
      submitBtn.disabled = !allValid;
    }

    // Adicionar listeners para validação em tempo real
    document.querySelectorAll('input, select, textarea').forEach(field => {
      field.addEventListener('input', function() {
        this.style.borderColor = '#ddd';
        validateForm();
      });
      field.addEventListener('change', validateForm);
    });

    // Validação e envio do formulário
    document.getElementById('finalRegistrationForm').addEventListener('submit', function(e) {
      
      
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.style.borderColor = '#e74c3c';
          e.preventDefault();
          isValid = false;
        } else {
          field.style.borderColor = '#ddd';
        }
      });
      
      if (isValid) {
        console.log('Dados do formulário final:', new FormData(this));
        // Aqui você redirecionaria para a página de sucesso ou dashboard
      }
    });
    validateForm();