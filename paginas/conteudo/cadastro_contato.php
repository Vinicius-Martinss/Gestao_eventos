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
                    <label for="exampleInputEmail1">Nome do evento</label>
                    <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" required placeholder="Digite o nome do evento">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Descrição</label>
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
                    <label for="exampleInputFile">Foto do local</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto_evento" id="foto_evento">
                        <label class="custom-file-label" for="exampleInputFile">Arquivo de imagem</label>
                      </div>
                      
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="hidden" class="custom-file-input" name="id_user" id="id_user" value="<?php echo $id_user ?>">
                      </div>
                    </div>
                  </div>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" required>
                    <label class="form-check-label" for="exampleCheck1">Autorizo o cadastro do meu Evento</label>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="botao" class="btn btn-primary">Cadastrar Evento</button>
                </div>
              </form>
              <?php
// Inclui o arquivo de conexão com o banco de dados
include('../config/conexao.php');

// Função para converter o formato de data
function convertDateToMysqlFormat($date) {
    $dateTime = DateTime::createFromFormat('d/m/Y h:i A', $date);
    return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
}

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
                echo "Erro, não foi possível fazer o upload do arquivo!";
                $foto = 'foto_padra_event.png';
            }
        } else {
            // Se o formato da imagem não for permitido, exibe mensagem de erro e define o avatar padrão
            echo "Formato Inválido";
            $foto = 'foto_padra_event.png';
        }
    } else {
        // Se não houver imagem enviada, define o avatar padrão
        $foto = 'foto_padra_event.png';
    }

    // Prepara a consulta SQL para inserir os dados no banco de dados
    $cadastro = "INSERT INTO Eventos (nome_eventos, Descricao, DataInicio, DataFim,local_evento, id_user) VALUES (:nome_eventos, :Descricao, :DataInicio, :DataFim, :local_evento, :id_user)";

    try {
        // Prepara a consulta SQL com os parâmetros
        $result = $conect->prepare($cadastro);
        $result->bindParam(':nome_eventos', $nome_eventos, PDO::PARAM_STR);
        $result->bindParam(':Descricao', $Descricao, PDO::PARAM_STR);
        $result->bindParam(':DataInicio', $DataInicio, PDO::PARAM_STR);
        $result->bindParam(':DataFim', $DataFim, PDO::PARAM_STR);
        $result->bindParam(':local_evento', $local_evento, PDO::PARAM_STR);
        $result->bindParam(':id_user', $id_usuario, PDO::PARAM_INT);

        // Executa a consulta SQL
        $result->execute();

        // Verifica se a inserção foi bem-sucedida
        $contar = $result->rowCount();
        if ($contar > 0) {
            // Se a inserção for bem-sucedida, exibe mensagem de sucesso
            echo '<div class="container">
                    <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> OK!</h5>
                    Dados inseridos com sucesso !!!
                  </div>
                </div>';
            header("Refresh: 5; URL=home.php");
        } else {
            // Se a inserção falhar, exibe mensagem de erro
            echo '<div class="container">
                  <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas fa-check"></i> Erro!</h5>
                  Dados não inseridos !!!
                </div>
              </div>';
            header("Refresh: 5; URL=home.php");
        }
    } catch (PDOException $e) {
        // Exibe mensagem de erro se ocorrer um erro de PDO
        echo "<strong>ERRO DE PDO= </strong>" . $e->getMessage();   
    }
}
?>
    </div>
</div>  
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
    // Consulta SQL para selecionar os contatos do usuário atual
    $select = "SELECT * FROM Eventos WHERE id_user = :id_user ORDER BY EventoID DESC LIMIT 6";

    try{
      // Prepara a consulta SQL com o parâmetro :id_user
      $result = $conect->prepare($select);
      // Inicializa o contador de linhas
      $cont = 1;
      // Vincula o ID do usuário ao parâmetro :id_user
      $result->bindParam(':id_user', $id_user, PDO::PARAM_INT);
      // Executa a consulta SQL
      $result->execute();

      // Verifica se a consulta retornou algum resultado
      $contar = $result->rowCount();

      if ($contar > 0) {
        // Itera sobre cada linha de resultado da consulta
        while ($show = $result->FETCH(PDO::FETCH_OBJ)) {

      

                  ?>
                    <tr>
                      <td><?php echo $cont++; ?></td>
                      <td><?php echo $show->nome_eventos; ?></td>
                      <td><?php echo $show->Descricao; ?></td>
                      <td><?php echo $show->DataInicio; ?></td>
                      <td><?php echo $show->DataFim; ?></td>
                      <td><?php echo $show->local_evento; ?></td>
                      <td>
                      <div class="btn-group">
                        <a href="home.php?acao=editar&id=<?php echo $show->EventoID; ?>" class="btn btn-success" title="Editar Evento"><i class="fas fa-user-edit"></i></button>
                        <a href="conteudo/del-contato.php?idDel=<?php echo $show->EventoID; ?>" onclick="return confirm('Deseja remover o evento?')" class="btn btn-danger" title="Remover Evento"><i class="fas fa-user-times"></i></a>
                      </div>
                      </td>
                    </tr>
                  <?php
                    }
                  }else{
                    // Se a consulta não retornar resultados, exibe uma mensagem
                    echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>
                          <strong>Não há Eventos!</strong></div>';
                  }
                }catch(Exception $e){
                  // Exibe a mensagem de erro de PDO
                  echo '<strong>ERRO DE PDO= </strong>' . $e->getMessage();
                }
                  ?>                                       
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->