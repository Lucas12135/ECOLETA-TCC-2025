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
      e.preventDefault();
      
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.style.borderColor = '#e74c3c';
          isValid = false;
        } else {
          field.style.borderColor = '#ddd';
        }
      });
      
      if (isValid) {
        alert('Cadastro finalizado com sucesso! Bem-vindo ao Portal do Coletor!');
        console.log('Dados do formulário final:', new FormData(this));
        // Aqui você redirecionaria para a página de sucesso ou dashboard
      }
    });
    validateForm();