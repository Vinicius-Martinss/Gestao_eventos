  <?php
  session_start(); 

  // Redireciona para a home se o usuário já estiver logado
  if (isset($_SESSION['loginUser'])) {
      header("Location: paginas/home.php");
      exit;
  }

  ?>

  <!DOCTYPE html>
  <html lang="pt_br">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Eventos Pulse | Entrar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
.login-box {
  width: 400px; /* Ajuste a largura do card conforme necessário */
  margin-left: 50px; /* Espaçamento à esquerda */
  margin-top: 50px; /* Espaçamento no topo */
  position: absolute; /* Para permitir que você posicione com precisão */
  left: 1100px; /* Alinhamento à esquerda */
}
      
      .login-logo a {
        font-size: 30px;
        background: linear-gradient(45deg, #007bff, #00d4ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-decoration: none;
        font-weight: bold;
        font-family: 'Source Sans Pro', sans-serif;
      }

      .login-image {
    max-width: 1000px; /* ajuste a largura da imagem */
    height: auto; /* mantém a proporção da imagem */
    position: absolute; /* posição absoluta */
    left: 0; /* alinha à esquerda */
    bottom: 0; /* opcional: alinha à parte inferior da tela */
    margin: 20px; /* espaço ao redor da imagem */
}

    </style>
  </head>
  <body class="hold-transition login-page">
    
  <div class="login-box">
    <div class="login-logo">
      <a href="#"><b>Eventos</b> Pulse</a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Para acessar entre com E-mail e Senha</p>

        <form action="" method="post">
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Digite seu E-mail..." required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="senha" class="form-control" placeholder="Digite sua Senha..." required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12" style="margin-bottom: 5px">
              <button type="submit" name="login" class="btn btn-primary btn-block">Acessar Eventos Pulse</button>
            </div>
          </div>
        </form>
        <?php
        include_once('config/conexao.php');

        // Processar o formulário de login
        if (isset($_POST['login'])) {
            $login = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_DEFAULT);

            if ($login && $senha) {
                $select = "SELECT * FROM tb_user WHERE email_user = :emailLogin";
                try {
                    $resultLogin = $conect->prepare($select);
                    $resultLogin->bindParam(':emailLogin', $login, PDO::PARAM_STR);
                    $resultLogin->execute();

                    if ($resultLogin->rowCount() > 0) {
                        $user = $resultLogin->fetch(PDO::FETCH_ASSOC);
                        if (password_verify($senha, $user['senha_user'])) {
                            $_SESSION['loginUser'] = $login;
                            $_SESSION['id_user'] = $user['id_user'];
                            header("Location: paginas/home.php?acao=bemvindo");
                            exit;
                        } else {
                            echo '<div class="alert alert-danger">Senha incorreta, tente novamente.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">E-mail não encontrado, verifique seu login ou faça o cadastro.</div>';
                    }
                } catch (PDOException $e) {
                    error_log("ERRO DE LOGIN DO PDO: " . $e->getMessage());
                    echo '<div class="alert alert-danger">Ocorreu um erro ao tentar fazer login. Por favor, tente novamente mais tarde.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Todos os campos são obrigatórios.</div>';
            }
        }
        ?>

        <p style="text-align: center; padding-top: 25px">
          <a href="cad_user.php" class="text-center">Se ainda não tem cadastro clique aqui!</a>
        </p>
      </div>
    </div>
  </div>


  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
  
  </body>
  </html>


      <!-- /.login-card-body -->
    </div>
  </div>

  </div>
    <img src="img/home.svg" alt="Imagem de Home" class="login-image">
  </div>

  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>


  </body>
  </html>
