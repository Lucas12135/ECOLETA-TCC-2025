/**
 * Validação de Campos Vazios - login.php (CADASTRO_GERADOR)
 * Mostra mensagens de erro quando campos obrigatórios estão vazios
 */

document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  
  if (!form) return;

  // Campos do formulário
  const campos = {
    email: {
      input: document.getElementById('email'),
      erro: 'Digite um email válido.'
    },
    senha: {
      input: document.getElementById('senha'),
      erro: 'Digite uma senha.'
    },
    confirmar_senha: {
      input: document.getElementById('confirmar_senha'),
      erro: 'Por favor, confirme sua senha.'
    }
  };

  /**
   * Cria ou atualiza a mensagem de erro para um campo
   */
  function mostrarErro(nomeCampo, mensagem) {
    const campo = campos[nomeCampo];
    if (!campo || !campo.input) return;

    // Remove mensagem anterior se existir
    let erroDiv = campo.input.nextElementSibling;
    while (erroDiv && erroDiv.classList && erroDiv.classList.contains('input-error')) {
      const temp = erroDiv;
      erroDiv = erroDiv.nextElementSibling;
      temp.remove();
    }

    // Se o input está dentro de password-field, busca erros lá também
    const inputParent = campo.input.parentNode;
    if (inputParent.classList && inputParent.classList.contains('password-field')) {
      let pwFieldError = inputParent.nextElementSibling;
      while (pwFieldError && pwFieldError.classList && pwFieldError.classList.contains('input-error')) {
        const temp = pwFieldError;
        pwFieldError = pwFieldError.nextElementSibling;
        temp.remove();
      }
    }

    // Cria nova mensagem de erro
    const novoErro = document.createElement('div');
    novoErro.className = 'input-error';
    novoErro.textContent = '⚠️ ' + mensagem;

    // Insere após o input ou após o password-field
    if (inputParent.classList && inputParent.classList.contains('password-field')) {
      inputParent.parentNode.insertBefore(novoErro, inputParent.nextElementSibling);
    } else {
      campo.input.parentNode.insertBefore(novoErro, campo.input.nextElementSibling);
    }
  }

  /**
   * Remove a mensagem de erro para um campo
   */
  function removerErro(nomeCampo) {
    const campo = campos[nomeCampo];
    if (!campo || !campo.input) return;

    // Remove erros após o input
    let erroDiv = campo.input.nextElementSibling;
    while (erroDiv && erroDiv.classList && erroDiv.classList.contains('input-error')) {
      const temp = erroDiv;
      erroDiv = erroDiv.nextElementSibling;
      temp.remove();
    }

    // Se está em password-field, remove também após o container
    const inputParent = campo.input.parentNode;
    if (inputParent.classList && inputParent.classList.contains('password-field')) {
      let pwFieldError = inputParent.nextElementSibling;
      while (pwFieldError && pwFieldError.classList && pwFieldError.classList.contains('input-error')) {
        const temp = pwFieldError;
        pwFieldError = pwFieldError.nextElementSibling;
        temp.remove();
      }
    }
  }

  /**
   * Validar email
   */
  function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }

  /**
   * Validação ao enviar o formulário
   */
  form.addEventListener('submit', function(e) {
    let temErro = false;

    // Limpar todos os erros primeiro
    Object.keys(campos).forEach(nomeCampo => removerErro(nomeCampo));

    // Validar Email
    const email = campos.email.input.value.trim();
    if (email === '') {
      mostrarErro('email', 'Digite um email válido.');
      temErro = true;
    } else if (!validarEmail(email)) {
      mostrarErro('email', 'Digite um email válido.');
      temErro = true;
    }

    // Validar Senha
    const senha = campos.senha.input.value;
    if (senha === '') {
      mostrarErro('senha', 'Digite uma senha.');
      temErro = true;
    }

    // Validar Confirmação de Senha
    const confirmarSenha = campos.confirmar_senha.input.value;
    if (confirmarSenha === '') {
      mostrarErro('confirmar_senha', 'Por favor, confirme sua senha.');
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

    campo.input.addEventListener('input', function() {
      if (this.value.trim() !== '') {
        removerErro(nomeCampo);
      }
    });
  });
});

