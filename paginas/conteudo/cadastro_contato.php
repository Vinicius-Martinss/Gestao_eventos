<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/conexao.php');

// Recupera o email e o ID do usuário da sessão
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$criador_id = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';

// Recupera o ID do usuário da sessão
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';

// Função para converter o formato de data
function convertDateToMysqlFormat($date) {
    $dateTime = DateTime::createFromFormat('d/m/Y h:i A', $date);
    return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
}

// Variável para armazenar a mensagem
$message = '';

// Verifica se o formulário foi submetido
if (isset($_POST['botao'])) {
    // Recupera os valores do formulário
    $nome_eventos = $_POST['nome_eventos'];
    $Descricao = $_POST['Descricao'];
    $DataInicio = convertDateToMysqlFormat($_POST['DataInicio']);
    $DataFim = convertDateToMysqlFormat($_POST['DataFim']);
    $local_evento = isset($_POST['local_evento']) ? $_POST['local_evento'] : null; // Permitir valor nulo
    $id_usuario = $_POST['id_user'];

    // Define os formatos de imagem permitidos
    $formatP = array("png", "jpg", "jpeg", "JPG", "gif");

    // Verifica se a imagem foi enviada e se é válida
    if (isset($_FILES['foto_evento']) && $_FILES['foto_evento']['error'] == UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['foto_evento']['name'], PATHINFO_EXTENSION);

        // Verifica se o formato da imagem é permitido
        if (in_array($extensao, $formatP)) {
            // Define o diretório para upload da imagem
            $pasta = "../img/eventos/";

            // Move o arquivo temporário para o diretório de upload
            $temporario = $_FILES['foto_evento']['tmp_name'];
            $novoNome = uniqid() . ".$extensao";

            if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                // Se o upload for bem-sucedido, define o nome do arquivo como o nome da imagem
                $foto = $novoNome;
            } else {
                // Se o upload falhar, exibe mensagem de erro e define o avatar padrão
                $message = '<div class="alert alert-danger alert-dismissible mt-3">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                              <h5><i class="icon fas fa-times"></i> Erro!</h5>
                              Erro, não foi possível fazer o upload do arquivo!
                            </div>';
                $foto = 'padrao.jpeg';
            }
        } else {
            // Se o formato da imagem não for permitido, exibe mensagem de erro e define o avatar padrão
            $message = '<div class="alert alert-danger alert-dismissible mt-3">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                          <h5><i class="icon fas fa-times"></i> Erro!</h5>
                          Formato inválido!
                        </div>';
            $foto = 'padrao.jpeg';
        }
    } else {
        // Se não houver imagem enviada, define o avatar padrão
        $foto = 'padrao.jpeg';
    }

    // Prepara a consulta SQL para inserir os dados no banco de dados
    $cadastro = "INSERT INTO Eventos (nome_eventos, Descricao, DataInicio, DataFim, local_evento, id_user, foto_evento) VALUES (:nome_eventos, :Descricao, :DataInicio, :DataFim, :local_evento, :id_user, :foto_evento)";

    try {
        // Prepara a consulta SQL com os parâmetros
        $result = $conect->prepare($cadastro);
        $result->bindParam(':nome_eventos', $nome_eventos, PDO::PARAM_STR);
        $result->bindParam(':Descricao', $Descricao, PDO::PARAM_STR);
        $result->bindParam(':DataInicio', $DataInicio, PDO::PARAM_STR);
        $result->bindParam(':DataFim', $DataFim, PDO::PARAM_STR);
        $result->bindParam(':local_evento', $local_evento, PDO::PARAM_STR);
        $result->bindParam(':id_user', $id_usuario, PDO::PARAM_INT);
        $result->bindParam(':foto_evento', $foto, PDO::PARAM_STR);

        // Executa a consulta SQL
        $result->execute();

        // Verifica se a inserção foi bem-sucedida
        if ($result->rowCount() > 0) {
            // Se a inserção for bem-sucedida, define a mensagem de sucesso
            $message = '<div class="alert alert-success alert-dismissible mt-3">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                          <h5><i class="icon fas fa-check"></i> OK!</h5>
                          Dados inseridos com sucesso !!!
                        </div>';
            header("Refresh: 5; URL=home.php");
        } else {
            // Se a inserção falhar, define a mensagem de erro
            $message = '<div class="alert alert-danger alert-dismissible mt-3">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                          <h5><i class="icon fas fa-times"></i> Erro!</h5>
                          Dados não inseridos !!!
                        </div>';
            header("Refresh: 5; URL=home.php");
        }
    } catch (PDOException $e) {
        // Define a mensagem de erro se ocorrer um erro de PDO
        $message = "<div class='alert alert-danger alert-dismissible mt-3'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
                      <strong>ERRO DE PDO: </strong>" . htmlspecialchars($e->getMessage()) . "
                    </div>";
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Cadastro de Eventos</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-4">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Cadastrar Evento</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label for="nome_eventos">Nome do evento</label>
                    <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" required placeholder="Digite o nome do evento">
                  </div>
                  <div class="form-group">
                    <label for="Descricao">Descrição</label>
                    <input type="text" class="form-control" name="Descricao" id="Descricao" required placeholder="Digite uma descrição breve">
                  </div>
                  <!-- Date -->
                  <div class="form-group">
                    <label>Data Inicio:</label>
                    <div class="input-group date" id="reservationdateStart" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#reservationdateStart" name="DataInicio" id="DataInicio"/>
                        <div class="input-group-append" data-target="#reservationdateStart" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                  </div>

                  <div class="form-group">
                      <label>Data Fim:</label>
                      <div class="input-group date" id="reservationdateEnd" data-target-input="nearest">
                          <input type="text" class="form-control datetimepicker-input" data-target="#reservationdateEnd" name="DataFim" id="DataFim"/>
                          <div class="input-group-append" data-target="#reservationdateEnd" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                      </div>
                  </div>

                  <div class="form-group">
                    <label for="local_evento">Local</label>
                    <input type="text" class="form-control" name="local_evento" id="local_evento" required placeholder="Digite o local do evento">
                  </div>
                  
                  <div class="form-group">
                    <label for="foto_evento">Foto do local</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto_evento" id="foto_evento">
                        <label class="custom-file-label" for="foto_evento">Arquivo de imagem</label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <input type="hidden" name="id_user" id="id_user" value="<?php echo htmlspecialchars($id_user); ?>">
                    </div>
                  </div>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" required>
                    <label class="form-check-label" for="exampleCheck1">Autorizo o cadastro do meu Evento</label>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="botao">Cadastrar</button>
                  
                  <!-- Exibe a mensagem logo abaixo do botão de cadastro -->
                  <?php
                  if (!empty($message)) {
                      echo $message;
                  }
                  ?>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (left) -->

          <!-- right column -->
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Seus Eventos Recentes</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Nome do evento</th>
                      <th>Descrição</th>
                      <th>Data inicio:</th>
                      <th>Data Fim:</th>
                      <th>Local</th>
                      <th style="width: 40px">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
    // Consulta SQL para selecionar os eventos do usuário atual
    $select = "SELECT * FROM Eventos WHERE id_user = :id_user ORDER BY EventoID DESC LIMIT 6";

    try{
      // Prepara a consulta SQL com o parâmetro :id_user
      $result = $conect->prepare($select);
      // Vincula o ID do usuário ao parâmetro :id_user
      $result->bindParam(':id_user', $id_user, PDO::PARAM_INT);
      // Executa a consulta SQL
      $result->execute();

      // Verifica se a consulta retornou algum resultado
      if ($result->rowCount() > 0) {
        // Itera sobre cada linha de resultado da consulta
        while ($show = $result->FETCH(PDO::FETCH_OBJ)) {
                  ?>
                    <tr>
                      <td><?php echo $show->EventoID; ?></td>
                      <td><?php echo htmlspecialchars($show->nome_eventos); ?></td>
                      <td><?php echo htmlspecialchars($show->Descricao); ?></td>
                      <td><?php echo htmlspecialchars($show->DataInicio); ?></td>
                      <td><?php echo htmlspecialchars($show->DataFim); ?></td>
                      <td><?php echo htmlspecialchars($show->local_evento); ?></td>
                      <td>
                        <div class="btn-group">
                          <a href="home.php?acao=editar&id=<?php echo $show->EventoID; ?>" class="btn btn-success" title="Editar Evento"><i class="fas fa-user-edit"></i></a>
                          <a href="conteudo/del-contato.php?idDel=<?php echo $show->EventoID; ?>" onclick="return confirm('Deseja remover o evento?')" class="btn btn-danger" title="Remover Evento"><i class="fas fa-user-times"></i></a>
                        </div>
                      </td>
                    </tr>
                  <?php
                    }
                  } else {
                    // Se a consulta não retornar resultados, exibe uma mensagem
                    echo '<tr><td colspan="7" class="text-center"><div class="alert alert-danger">Não há Eventos!</div></td></tr>';
                  }
                } catch (Exception $e) {
                  // Exibe a mensagem de erro de PDO
                  echo '<tr><td colspan="7" class="text-center"><div class="alert alert-danger"><strong>ERRO DE PDO: </strong>' . htmlspecialchars($e->getMessage()) . '</div></td></tr>';
                }
                  ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
