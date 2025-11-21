/**
 * Validação de Campos Vazios - registro.php
 * Mostra mensagens de erro quando campos obrigatórios estão vazios
 */

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registrationForm');
  
  if (!form) return;

  // Determinar qual é o campo dinâmico (cpf ou cnpj)
  const cpfField = document.getElementById('cpf');
  const cnpjField = document.getElementById('cnpj');
  const campoDocumento = cpfField ? 'cpf' : cnpjField ? 'cnpj' : null;

  // Campos do formulário
  const campos = {
    nome: {
      input: document.getElementById('fullName'),
      erro: 'Por favor, digite o seu nome completo.'
    },
    documento: {
      input: cpfField || cnpjField,
      erro: campoDocumento === 'cnpj' ? 'Informe o CNPJ.' : 'Informe o CPF.'
    },
    celular: {
      input: document.getElementById('phone'),
      erro: 'Informe um telefone.'
    },
    dataConsent: {
      input: document.getElementById('dataConsent'),
      erro: 'Você precisa concordar com o uso dos dados para continuar o cadastro.'
    }
  };

  /**
   * Cria ou atualiza a mensagem de erro para um campo
   */
  function mostrarErro(nomeCampo, mensagem) {
    const campo = campos[nomeCampo];
    if (!campo || !campo.input) return;

    // Para checkbox (dataConsent), trata diferente
    if (nomeCampo === 'dataConsent') {
      const checkboxLabel = campo.input.closest('label');
      if (!checkboxLabel) return;

      // Remove erros anteriores
      let erroDiv = checkboxLabel.nextElementSibling;
      while (erroDiv && erroDiv.classList && erroDiv.classList.contains('error-message')) {
        const temp = erroDiv;
        erroDiv = erroDiv.nextElementSibling;
        temp.remove();
      }

      // Cria novo erro
      const novoErro = document.createElement('div');
      novoErro.className = 'error-message';
      novoErro.textContent = '⚠️ ' + mensagem;

      // Insere após o label do checkbox
      checkboxLabel.parentNode.insertBefore(novoErro, checkboxLabel.nextElementSibling);
    } else {
      // Para inputs normais
      const formGroup = campo.input.closest('.form-group');
      if (!formGroup) return;

      // Remove TODOS os erros anteriores
      let erroDiv = campo.input.nextElementSibling;
      while (erroDiv && erroDiv.classList && erroDiv.classList.contains('error-message')) {
        const temp = erroDiv;
        erroDiv = erroDiv.nextElementSibling;
        temp.remove();
      }

      // Cria novo erro
      const novoErro = document.createElement('div');
      novoErro.className = 'error-message';
      novoErro.textContent = '⚠️ ' + mensagem;

      // Insere após o input
      campo.input.parentNode.insertBefore(novoErro, campo.input.nextElementSibling);
    }
  }

  /**
   * Remove a mensagem de erro para um campo
   */
  function removerErro(nomeCampo) {
    const campo = campos[nomeCampo];
    if (!campo || !campo.input) return;

    if (nomeCampo === 'dataConsent') {
      // Para checkbox, busca o label e remove erros após ele
      const checkboxLabel = campo.input.closest('label');
      if (!checkboxLabel) return;

      let erroDiv = checkboxLabel.nextElementSibling;
      while (erroDiv && erroDiv.classList && erroDiv.classList.contains('error-message')) {
        const temp = erroDiv;
        erroDiv = erroDiv.nextElementSibling;
        temp.remove();
      }
    } else {
      // Para inputs normais
      let erroDiv = campo.input.nextElementSibling;
      while (erroDiv && erroDiv.classList && erroDiv.classList.contains('error-message')) {
        const temp = erroDiv;
        erroDiv = erroDiv.nextElementSibling;
        temp.remove();
      }
    }
  }

  /**
   * Validação ao enviar o formulário
   */
  form.addEventListener('submit', function(e) {
    let temErro = false;

    // Limpar todos os erros primeiro
    Object.keys(campos).forEach(nomeCampo => removerErro(nomeCampo));

    // Validar Nome
    if (campos.nome.input.value.trim() === '') {
      mostrarErro('nome', campos.nome.erro);
      temErro = true;
    }

    // Validar Documento (CPF ou CNPJ)
    if (campos.documento.input.value.trim() === '') {
      mostrarErro('documento', campos.documento.erro);
      temErro = true;
    }

    // Validar Telefone
    if (campos.celular.input.value.trim() === '') {
      mostrarErro('celular', campos.celular.erro);
      temErro = true;
    }

    // Validar Consentimento
    if (!campos.dataConsent.input.checked) {
      mostrarErro('dataConsent', campos.dataConsent.erro);
      temErro = true;
    }

    // Se houver erro, previne o envio
    if (temErro) {
      e.preventDefault();
    }
  });

  /**
   * Remover erro ao digitar/interagir
   */
  Object.keys(campos).forEach(nomeCampo => {
    const campo = campos[nomeCampo];
    if (!campo.input) return;

    if (nomeCampo === 'dataConsent') {
      campo.input.addEventListener('change', function() {
        if (this.checked) {
          removerErro(nomeCampo);
        }
      });
    } else {
      campo.input.addEventListener('input', function() {
        if (this.value.trim() !== '') {
          removerErro(nomeCampo);
        }
      });
    }
  });
});