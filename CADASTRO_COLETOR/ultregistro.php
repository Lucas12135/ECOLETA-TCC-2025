<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal do Coletor - Últimos Passos</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../CSS/ultregistro.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    
  </style>
</head>
<body>
  <!-- ========== HEADER ========== -->
  <header>
    <div class="header-container">
      <div class="logo">
        <div class="logo-placeholder">
          <img src="../img/logo.png" alt="Logo Portal do Coletor">
        </div>
        <span class="logo-text">Portal do Coletor</span>
      </div>
    </div>
  </header>

  <!-- ========== CONTEÚDO PRINCIPAL ========== -->
  <main>
    <div class="form-container">
      <!-- Header do Formulário -->
      <div class="form-header">
        <a href="#" class="back-link" onclick="goBack(event)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z"/>
          </svg>
          Retornar
        </a>
        <h1 class="form-title">Últimos passos para virar coletor</h1>
      </div>

      <!-- Corpo do Formulário -->
      <div class="form-body">
        <div class="form-fields">
          <form id="finalRegistrationForm" novalidate>
            <div class="form-row full">
              <div class="form-group">
                <label for="address">Endereço Completo <span class="required">*</span></label>
                <input type="text" id="address" name="address" placeholder="Rua, número, complemento" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="city">Cidade <span class="required">*</span></label>
                <input type="text" id="cidade" name="city" placeholder="Sua cidade" required>
              </div>
              <div class="form-group">
                <label for="cep">CEP <span class="required">*</span></label>
                <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" required onblur="pesquisacep();">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="state">Estado <span class="required">*</span></label>
                <select id="estado" name="state" required>
                  <option value="">Selecione seu estado</option>
                  <option value="AC">AC</option>
                  <option value="AL">AL</option>
                  <option value="AP">AP</option>
                  <option value="AM">AM</option>
                  <option value="BA">BA</option>
                  <option value="CE">CE</option>
                  <option value="DF">DF</option>
                  <option value="ES">ES</option>
                  <option value="GO">GO</option>
                  <option value="MA">MA</option>
                  <option value="MT">MT</option>
                  <option value="MS">MS</option>
                  <option value="MG">MG</option>
                  <option value="PA">PA</option>
                  <option value="PB">PB</option>
                  <option value="PR">PR</option>
                  <option value="PE">PE</option>
                  <option value="PI">PI</option>
                  <option value="RJ">RJ</option>
                  <option value="RN">RN</option>
                  <option value="RS">RS</option>
                  <option value="RO">RO</option>
                  <option value="RR">RR</option>
                  <option value="SC">SC</option>
                  <option value="SP">SP</option>
                  <option value="SE">SE</option>
                  <option value="TO">TO</option>
                </select>
              </div>
              <div class="form-group">
                <label for="neighborhood">Bairro <span class="required">*</span></label>
                <input type="text" id="bairro" name="neighborhood" placeholder="Seu bairro" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="birthdate">Data de Nascimento <span class="required">*</span></label>
                <input type="date" id="birthdate" name="birthdate" required>
              </div>
              <div class="form-group">
                <label for="gender">Gênero</label>
                <select id="gender" name="gender">
                  <option value="">Prefiro não informar</option>
                  <option value="M">Masculino</option>
                  <option value="F">Feminino</option>
                  <option value="O">Outro</option>
                </select>
              </div>
            </div>
            <div class="form-row full">
              <div class="form-group">
                <label for="experience">Experiência com coleta (opcional)</label>
                <textarea id="experience" name="experience" placeholder="Conte-nos sobre sua experiência com coleta de materiais recicláveis..."></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="availability">Disponibilidade <span class="required">*</span></label>
                <select id="availability" name="availability" required>
                  <option value="">Selecione sua disponibilidade</option>
                  <option value="manha">Manhã</option>
                  <option value="tarde">Tarde</option>
                  <option value="noite">Noite</option>
                  <option value="integral">Período integral</option>
                  <option value="fins_de_semana">Fins de semana</option>
                </select>
              </div>
              <div class="form-group">
                <label for="transport">Meio de Transporte <span class="required">*</span></label>
                <select id="transport" name="transport" required>
                  <option value="">Selecione seu transporte</option>
                  <option value="bicicleta">Bicicleta</option>
                  <option value="motocicleta">Motocicleta</option>
                  <option value="carro">Carro</option>
                  <option value="carroca">Carroça</option>
                  <option value="a_pe">A pé</option>
                </select>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-submit" id="submitBtn" disabled>Finalizar Cadastro</button>
            </div>
          </form>
        </div>

        <!-- Seção de Foto -->
        <div class="photo-section">
          <div class="photo-upload" onclick="selectPhoto()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L19 5C18.8 4.8 18.5 4.8 18.3 5L16.9 6.4L17.6 7.1L21 9ZM19 17V19C19 20.1 18.1 21 17 21H5C3.9 21 3 20.1 3 19V17L8.5 12.5L11 15L14.5 11.5L19 17ZM17 3H5C3.9 3 3 3.9 3 5V15L6.8 11.2C7.2 10.8 7.8 10.8 8.2 11.2L12 15L15.8 11.2C16.2 10.8 16.8 10.8 17.2 11.2L19 13V5C19 3.9 18.1 3 17 3Z"/>
            </svg>
            <div class="photo-upload-text">
              Clique para adicionar<br>sua foto
            </div>
          </div>
          <div class="photo-requirements">
            • Formato: JPG ou PNG<br>
            • Tamanho máximo: 2MB<br>
            • Foto nítida do rosto<br>
            • Fundo claro (preferível)
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Botão de Acessibilidade -->
  <div class="accessibility-button" onclick="toggleAccessibility(event)" title="Ferramentas de Acessibilidade">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="25" height="25" fill="white">
      <title>accessibility</title>
      <g>
        <circle cx="24" cy="7" r="4"/>
        <path d="M40,13H8a2,2,0,0,0,0,4H19.9V27L15.1,42.4a2,2,0,0,0,1.3,2.5H17a2,2,0,0,0,1.9-1.4L23.8,28h.4l4.9,15.6A2,2,0,0,0,31,45h.6a2,2,0,0,0,1.3-2.5L28.1,27V17H40a2,2,0,0,0,0-4Z"/>
      </g>
    </svg>
  </div>

  <div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
      <div class="vw-plugin-top-wrapper"></div>
    </div>
  </div>
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script src="../JS/ultregistro.js"></script>
</body>
</html>