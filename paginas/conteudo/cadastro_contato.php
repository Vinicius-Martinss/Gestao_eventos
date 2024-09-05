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
                    <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" required placeholder="Digite o nome de contato">
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
                    <label for="exampleInputEmail1">Local</label>
                    <input type="text" class="form-control" name="id_local" id="id_local" required placeholder="Digite o local do evento">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputFile">Foto do local</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto" id="foto">
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

// Verifica se o formulário foi submetido
if (isset($_POST['botao'])) {
    // Recupera os valores do formulário
    $nome_eventos = $_POST['nome_eventos'];
    $Descricao = $_POST['Descricao'];
    $DataInicio = $_POST['DataInicio'];
    $DataFim = $_POST['DataFim'];
    $id_local = $_POST['id_local'];
    $id_usuario = $_POST['id_user'];
    // Converte as datas para o formato 'YYYY-MM-DD' se necessário

    try {
  $DataInicio = date('Y-m-d', strtotime($DataInicio));
  $DataFim = date('Y-m-d', strtotime($DataFim));
} catch (Exception $e) {
  echo 'Erro na conversão de data: ' . $e->getMessage();
  exit;
}


    // Define os formatos de imagem permitidos
    $formatP = array("png", "jpg", "jpeg", "JPG", "gif");

    // Verifica se a imagem foi enviada e se é válida
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        // Verifica se o formato da imagem é permitido
        if (in_array($extensao, $formatP)) {
            // Define o diretório para upload da imagem
            $pasta = "../img/cont/";

            // Move o arquivo temporário para o diretório de upload
            $temporario = $_FILES['foto']['tmp_name'];
            $novoNome = uniqid() . ".$extensao";

            if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                // Se o upload for bem-sucedido, define o nome do arquivo como o nome da imagem
                $foto = $novoNome;
            } else {
                // Se o upload falhar, exibe mensagem de erro e define o avatar padrão
                echo "Erro, não foi possível fazer o upload do arquivo!";
                $foto = 'avatar_padrao.png';
            }
        } else {
            // Se o formato da imagem não for permitido, exibe mensagem de erro e define o avatar padrão
            echo "Formato Inválido";
            $foto = 'avatar_padrao.png';
        }
    } else {
        // Se não houver imagem enviada, define o avatar padrão
        $foto = 'avatar_padrao.png';
    }

    // Prepara a consulta SQL para inserir os dados no banco de dados
    $cadastro = "INSERT INTO Eventos (nome_eventos, Descricao, DataInicio, DataFim, id_local, id_user) VALUES (:nome_eventos, :Descricao, :DataInicio, :DataFim, :id_local, :id_user)";

    try {
        // Prepara a consulta SQL com os parâmetros
        $result = $conect->prepare($cadastro);
        $result->bindParam(':nome_eventos', $nome_eventos, PDO::PARAM_STR);
        $result->bindParam(':Descricao', $Descricao, PDO::PARAM_STR);
        $result->bindParam(':DataInicio', $DataInicio, PDO::PARAM_STR);
        $result->bindParam(':DataFim', $DataFim, PDO::PARAM_STR);
        $result->bindParam(':id_local', $id_local, PDO::PARAM_STR);
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

