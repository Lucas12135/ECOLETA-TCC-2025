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
    function goBack() {
      // Aqui você pode implementar a navegação de volta
      // Por exemplo, usando window.history.back() ou redirecionando para a página anterior
      window.history.back();
    }

    // Máscara para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = value.replace(/(\d{3})(\d)/, '$1.$2');
      value = value.replace(/(\d{3})(\d)/, '$1.$2');
      value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      e.target.value = value;
    });

    // Máscara para Telefone
    document.getElementById('phone').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
      } else {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
      }
      e.target.value = value;
    });

    // Validação de CPF
    function isValidCPF(cpf) {
      cpf = cpf.replace(/[^\d]+/g, '');
      
      if (cpf.length !== 11 || /^(.)\1{10}$/.test(cpf)) {
        return false;
      }
      
      let sum = 0;
      for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
      }
      
      let remainder = 11 - (sum % 11);
      if (remainder === 10 || remainder === 11) remainder = 0;
      if (remainder !== parseInt(cpf.charAt(9))) return false;
      
      sum = 0;
      for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
      }
      
      remainder = 11 - (sum % 11);
      if (remainder === 10 || remainder === 11) remainder = 0;
      return remainder === parseInt(cpf.charAt(10));
    }

    // Validação de telefone
    function isValidPhone(phone) {
  const dddsValidos = [
    11,12,13,14,15,16,17,18,19,21,22,24,27,28,31,32,33,34,35,37,38,41,42,43,44,45,46,
    47,48,49,51,53,54,55,61,62,64,63,65,66,67,68,69,71,73,74,75,77,79,81,82,83,84,85,
    86,87,88,89,91,92,93,94,95,96,97,98,99
  ];
  phone = phone.replace(/\D/g, '');
  if (phone.length < 10 || phone.length > 11) return false;
  const ddd = parseInt(phone.substring(0,2));
  if (!dddsValidos.includes(ddd)) return false;
  if (phone.length === 11 && phone[2] !== '9') return false; // Celular deve começar com 9
  return true;
}

    // Validação do formulário
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
      
      
      const fullName = document.getElementById('fullName');
      const cpf = document.getElementById('cpf');
      const phone = document.getElementById('phone');
      const dataConsent = document.getElementById('dataConsent');
      
      let isValid = true;
      
      // Validar nome completo
      if (fullName.value.trim().length < 3 || fullName.value.trim().split(' ').length < 2) {
        fullName.closest('.form-group').classList.add('error');
        isValid = false;
        e.preventDefault();
      } else {
        fullName.closest('.form-group').classList.remove('error');
      }
      
      // Validar CPF
      if (!isValidCPF(cpf.value)) {
        cpf.closest('.form-group').classList.add('error');
        isValid = false;
        e.preventDefault();
      } else {
        cpf.closest('.form-group').classList.remove('error');
      }
      
      // Validar telefone
      if (!isValidPhone(phone.value)) {
        phone.closest('.form-group').classList.add('error');
        isValid = false;
        e.preventDefault();
      } else {
        phone.closest('.form-group').classList.remove('error');
      }
      
      // Validar consentimento
      if (!dataConsent.checked) {
        alert('Por favor, concorde com o fornecimento dos dados para continuar.');
        isValid = false;
      }
      
      if (isValid) {
        // Aqui você pode prosseguir para a próxima etapa ou enviar os dados
        console.log('Dados do formulário:', {
          fullName: fullName.value,
          cpf: cpf.value,
          phone: phone.value,
          dataConsent: dataConsent.checked
        });
      }
    });

    // Remover erros ao digitar
    document.querySelectorAll('input').forEach(input => {
      input.addEventListener('input', function() {
        this.closest('.form-group').classList.remove('error');
      });
    });