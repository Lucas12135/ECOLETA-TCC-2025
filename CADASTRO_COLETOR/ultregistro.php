<?php
session_start();

$tipo = $_SESSION['cadastro']['tipo'] ?? null;
if ($tipo === "pessoa_juridica") {
  $datanasc = false;
  $genero = false;
  $desc_img = "
        • Formato: JPG ou PNG<br>
        • Tamanho máximo: 2MB<br>
        • Imagem nítida
  ";
} elseif ($tipo === "pessoa_fisica") {
  $datanasc = true;
  $genero = true;
  $desc_img = "
        • Formato: JPG ou PNG<br>
        • Tamanho máximo: 2MB<br>
        • Foto nítida do rosto<br>
        • Fundo claro (preferível)
  ";
} else {
  header('Location: login.php');
  exit;
}

if (!empty($_POST)) {
  // Salva os dados da última etapa na sessão
  $endereco = $_POST['endereco'];
  $partes = explode(',', $endereco);
  $rua = trim($partes[1] ?? '');
  $numero = trim($partes[0] ?? '');
  $complemento = trim($partes[2] ?? '');
  $_SESSION['cadastro']['rua'] = $rua;
  $_SESSION['cadastro']['numero'] = $numero;
  $_SESSION['cadastro']['complemento'] = $complemento;
  $_SESSION['cadastro']['cidade'] = $_POST['cidade'];
  $_SESSION['cadastro']['cep'] = $_POST['cep'];
  $_SESSION['cadastro']['estado'] = $_POST['estado'];
  $_SESSION['cadastro']['bairro'] = $_POST['bairro'];
  $_SESSION['cadastro']['data_nasc'] = $_POST['data_nasc'];
  $_SESSION['cadastro']['genero'] = $_POST['genero'];
  $_SESSION['cadastro']['experiencia'] = $_POST['experiencia'];
  $_SESSION['cadastro']['disponibilidade'] = $_POST['disponibilidade'];
  $_SESSION['cadastro']['transporte'] = $_POST['transporte'];

  include_once('../BANCO/conexao.php');

  $dados = $_SESSION['cadastro'];

  try {
    if ($conn) {
      // 1. Insere endereço
      $stmt_endereco = $conn->prepare("INSERT INTO enderecos (
                rua, numero, complemento, cidade, cep, estado, bairro
                ) VALUES (
                  :rua, :numero, :complemento, :cidade, :cep, :estado, :bairro
                )");
      $stmt_endereco->bindParam(':rua', $dados['rua']);
      $stmt_endereco->bindParam(':numero', $dados['numero']);
      $stmt_endereco->bindParam(':complemento', $dados['complemento']);
      $stmt_endereco->bindParam(':cidade', $dados['cidade']);
      $stmt_endereco->bindParam(':cep', $dados['cep']);
      $stmt_endereco->bindParam(':estado', $dados['estado']);
      $stmt_endereco->bindParam(':bairro', $dados['bairro']);
      $stmt_endereco->execute();
      $id_endereco = $conn->lastInsertId();

      // 2. Insere coletor
      $stmt = $conn->prepare("INSERT INTO coletores (
                  email, senha, tipo_coletor, nome_completo, cpf_cnpj, telefone, 
                  data_nasc, genero, id_endereco, meio_transporte,
                  status, raio_atuacao
                ) VALUES (
                  :email, :senha, :tipo_coletor, :nome_completo, :cpf_cnpj, :telefone,
                  :data_nasc, :genero, :id_endereco, :meio_transporte,
                  'pendente', 5
                )");
      
      $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
      
      $stmt->bindParam(':email', $dados['email']);
      $stmt->bindParam(':senha', $senha_hash);
      $tipo_coletor = ($dados['tipo'] === 'pessoa_fisica') ? 'pessoa_fisica' : 'pessoa_juridica';
      $stmt->bindParam(':tipo_coletor', $tipo_coletor);
      $stmt->bindParam(':nome_completo', $dados['nome']);
      $stmt->bindParam(':cpf_cnpj', $dados['cpf_cnpj']);
      $stmt->bindParam(':telefone', $dados['celular']);
      $stmt->bindParam(':data_nasc', $dados['data_nasc']);
      $stmt->bindParam(':genero', $dados['genero']);
      $stmt->bindParam(':meio_transporte', $dados['transporte']);
      $stmt->bindParam(':id_endereco', $id_endereco, PDO::PARAM_INT);
      
      if ($stmt->execute()) {
        $id_coletor = $conn->lastInsertId();
        
        // 3. Insere disponibilidade
        if (isset($dados['disponibilidade']) && is_array($dados['disponibilidade'])) {
          $stmt_disponibilidade = $conn->prepare("INSERT INTO disponibilidade_coletores 
            (id_coletor, dia_semana, hora_inicio, hora_fim) 
            VALUES (:id_coletor, :dia_semana, :hora_inicio, :hora_fim)");

          foreach ($dados['disponibilidade'] as $disponibilidade) {
            $stmt_disponibilidade->bindParam(':id_coletor', $id_coletor);
            $stmt_disponibilidade->bindParam(':dia_semana', $disponibilidade['dia']);
            $stmt_disponibilidade->bindParam(':hora_inicio', $disponibilidade['inicio']);
            $stmt_disponibilidade->bindParam(':hora_fim', $disponibilidade['fim']);
            $stmt_disponibilidade->execute();
          }
        }
        
        // 4. Processa upload de foto
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
          $uploadDir = '../uploads/profile_photos/';
          if (!file_exists($uploadDir)) {
              mkdir($uploadDir, 0777, true);
          }
          
          $fileInfo = pathinfo($_FILES['photo']['name']);
          $extension = strtolower($fileInfo['extension']);
          
          if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $newFileName = $id_coletor . '_' . uniqid() . '.' . $extension;
            $uploadFile = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
              $stmt_foto = $conn->prepare("UPDATE coletores SET foto_perfil = :foto WHERE id = :id");
              $stmt_foto->bindParam(':foto', $newFileName);
              $stmt_foto->bindParam(':id', $id_coletor);
              $stmt_foto->execute();
            }
          }
        }
        
        // ============================================
        // NOVO CÓDIGO: Configura a sessão do usuário
        // ============================================
        $_SESSION['id_usuario'] = $id_coletor;
        $_SESSION['tipo_usuario'] = 'coletor';
        $_SESSION['nome_usuario'] = $dados['nome'];
        $_SESSION['email'] = $dados['email'];
        $_SESSION['telefone'] = $dados['celular'];
        $_SESSION['cadastro_completo'] = true;
        
        // Limpar dados temporários do cadastro
        unset($_SESSION['cadastro']);
        // ============================================
        
        header("Location: final.php");
        exit;
      }
    }
  } catch (PDOException $e) {
    echo "<div style='color:red'>Erro ao cadastrar: " . $e->getMessage() . "</div>";
  }
  $conn = null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de cadastro - Coletor</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link rel="stylesheet" href="../CSS/ultregistro.css">
  <link rel="stylesheet" href="../CSS/global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <!-- ========== HEADER ========== -->
  <header>
    <div class="header-container">
      <div class="logo">
        <div class="logo-placeholder">
          <img src="../img/logo.png" alt="Logo Portal de cadastro - Coletor">
        </div>
        <span class="logo-text">Portal de cadastro - Coletor</span>
      </div>
    </div>
  </header>

  <!-- ========== CONTEÚDO PRINCIPAL ========== -->
  <main>
    <div class="form-container">
      <div class="form-header">
        <a href="#" class="back-link" onclick="goBack(event)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z" />
          </svg>
          Retornar
        </a>
        <h1 class="form-title">Últimos passos para virar coletor</h1>
      </div>

      
      
      <form action="#" method="POST" class="form" id="finalRegistrationForm" enctype="multipart/form-data">
        <div class="form-body">
          <div class="form-fields">
            <div class="form-row full">
              <div class="form-group">
                <label for="endereco">Endereço Completo <span class="required">*</span></label>
                <input type="text" id="endereco" name="endereco" placeholder="Rua, número, complemento" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="cidade">Cidade <span class="required">*</span></label>
                <input type="text" id="cidade" name="cidade" placeholder="Sua cidade" required>
              </div>
              <div class="form-group">
                <label for="cep">CEP <span class="required">*</span></label>
                <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" required onblur="pesquisacep();">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="estado">Estado <span class="required">*</span></label>
                <select id="estado" name="estado" required>
                  <option value="" disabled selected>Selecione seu estado</option>
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
                <label for="bairro">Bairro <span class="required">*</span></label>
                <input type="text" id="bairro" name="bairro" placeholder="Seu bairro" required>
              </div>
            </div>
            <?php if ($datanasc && $genero) echo "
            <div class='form-row'>
              <div class='form-group'>
                <label for='data_nasc'>Data de Nascimento <span class='required'>*</span></label>
                <input type='date' id='birthdate' name='data_nasc' required>
              </div>
              <div class='form-group'>
                <label for='genero'>Gênero</label>
                <select id='gender' name='genero'>
                  <option value=''>Prefiro não informar</option>
                  <option value='M'>Masculino</option>
                  <option value='F'>Feminino</option>
                  <option value='O'>Outro</option>
                </select>
              </div>
            </div>
            ";
            ?>
            <div class="form-row full">
              <div class="form-group">
                <label for="experiencia">Experiência com coleta (opcional)</label>
                <textarea id="experience" name="experiencia" placeholder="Conte-nos sobre sua experiência com coleta de materiais recicláveis..."></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="disponibilidade">Disponibilidade <span class="required">*</span></label>
                <select id="availability" name="disponibilidade" required>
                  <option value="" disabled selected>Selecione sua disponibilidade</option>
                  <option value="manha">Manhã</option>
                  <option value="tarde">Tarde</option>
                  <option value="noite">Noite</option>
                  <option value="integral">Período integral</option>
                  <option value="fins_de_semana">Fins de semana</option>
                </select>
              </div>
              <div class="form-group">
                <label for="transporte">Meio de Transporte <span class="required">*</span></label>
                <select id="transport" name="transporte" required>
                  <option value="" disabled selected>Selecione seu transporte</option>
                  <option value="bicicleta">Bicicleta</option>
                  <option value="motocicleta">Motocicleta</option>
                  <option value="carro">Carro</option>
                  <option value="carroca">Carroça</option>
                  <option value="a_pe">A pé</option>
                </select>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="btn btn-submit" id="submitBtn">Finalizar Cadastro</button>
            </div>
          </div>

          <!-- Seção de Foto ao Lado -->
          <div class="photo-section">
            <div class="photo-upload" onclick="selectPhoto()" id="photoUploadArea">
              <input type="file"
                id="photoInput"
                name="photo"
                accept="image/jpeg,image/png"
                style="display: none;"
                onchange="previewPhoto(this)">
              <img id="photoPreview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
              <div id="uploadIcon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48">
                  <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L19 5C18.8 4.8 18.5 4.8 18.3 5L16.9 6.4L17.6 7.1L21 9ZM19 17V19C19 20.1 18.1 21 17 21H5C3.9 21 3 20.1 3 19V17L8.5 12.5L11 15L14.5 11.5L19 17ZM17 3H5C3.9 3 3 3.9 3 5V15L6.8 11.2C7.2 10.8 7.8 10.8 8.2 11.2L12 15L15.8 11.2C16.2 10.8 16.8 10.8 17.2 11.2L19 13V5C19 3.9 18.1 3 17 3Z" fill="#223e2a"/>
                </svg>
                <div class="photo-upload-text">Adicionar foto de perfil</div>
              </div>
            </div>
            <div class="photo-requirements">
              <?= $desc_img ?>
            </div>
          </div>
        </div>
      </form>
    </div>
  </main>

  <div class="accessibility-button" onclick="toggleAccessibility(event)" title="Ferramentas de Acessibilidade">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="25" height="25" fill="white">
      <title>accessibility</title>
      <g>
        <circle cx="24" cy="7" r="4" />
        <path d="M40,13H8a2,2,0,0,0,0,4H19.9V27L15.1,42.4a2,2,0,0,0,1.3,2.5H17a2,2,0,0,0,1.9-1.4L23.8,28h.4l4.9,15.6A2,2,0,0,0,31,45h.6a2,2,0,0,0,1.3-2.5L28.1,27V17H40a2,2,0,0,0,0-4Z" />
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
  <script src="../JS/libras.js"></script>
  <script src="../JS/ultregistro.js"></script>
</body>

</html>