<?php
      // Inclui o arquivo de conexão com o banco de dados
      include('../config/conexao.php');

      // Verifica se o parâmetro 'id' foi passado via GET
      if (!isset($_GET['id'])) {
          // Se não foi passado, redireciona para a página home.php
          header("Location: home.php");
          exit; // Encerra o script
      }

      // Obtém o valor do parâmetro 'id' e filtra como um inteiro
      $id = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);

      // Prepara e executa a consulta para selecionar o contato com base no 'id'
      $select = "SELECT * FROM Eventos WHERE EventoID=:id";
      try {
          $resultado = $conect->prepare($select);
          $resultado->bindParam(':id', $id, PDO::PARAM_INT);
          $resultado->execute();

          // Verifica se foi encontrado algum contato com o 'id' especificado
          $contar = $resultado->rowCount();
          if ($contar > 0) {
              // Se encontrado, obtém os dados do contato
              $show = $resultado->fetch(PDO::FETCH_OBJ);
              $idCont = $show->EventoID;
              $nome_eventos = $show->nome_eventos;
              $Descricao = $show->Descricao;
              $DataInicio = $show->DataInicio;
              $DataFim = $show->DataFim;
              $local_evento = $show->local_evento;
              $foto_evento = $show->foto_evento;

          } else {
              // Se nenhum contato foi encontrado, exibe uma mensagem de erro
              echo '<div class="alert alert-danger">Não há dados com o id informado!</div>';
          }
      } catch (PDOException $e) {
          // Em caso de erro na consulta PDO, exibe a mensagem de erro
          echo "<strong>ERRO DE SELECT NO PDO: </strong>" . $e->getMessage();
      }
      ?>
 
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Editar Evento</h1>
          </div>
          
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Editar Eventos</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label for="example">Nome Evento</label>
                    <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" required value="<?php echo htmlspecialchars($nome_eventos, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                  <div class="form-group">
                    <label for="descricao">Descricao</label>
                    <input type="text" class="form-control" name="Descricao" id="descricao" required value="<?php echo $Descricao; ?>">
                    </div>            
                  <div class="form-group">
                  <label>Data Inicio:</label>
                  <div class="input-group date" id="reservationdateStart" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#reservationdateStart" name="DataInicio" id="DataInicio" required value="<?php echo htmlspecialchars($DataInicio, ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="input-group-append" data-target="#reservationdateStart" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                    <label>Data Fim:</label>
                    <div class="input-group date" id="reservationdateEnd" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#reservationdateEnd" name="DataFim" id="DataFim" required value="<?php echo htmlspecialchars($DataFim, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="input-group-append" data-target="#reservationdateEnd" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                        </div>
                      </div>

                    <div class="form-group">
                    <label for="example">Local Evento</label>
                    <input type="text" class="form-control" name="local_evento" id="local_evento" required value="<?php echo $local_evento; ?>">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputFile">Foto do Evento</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto_evento" id="foto_evento">
                        <label class="custom-file-label" for="exampleInputFile">Arquivo de imagem</label>
                      </div>
                      
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="upEvento" class="btn btn-primary">Finalizar edição do evento</button>
                </div>
              </form>
              <?php
              // Verifica se o formulário foi submetido
              if (isset($_POST['upEvento'])) {
                  // Obtém os dados do formulário
                  $nome_eventos = $_POST['nome_eventos'];
                  $Descricao = $_POST['Descricao'];
                  $DataInicio = $_POST['DataInicio'];
                  $DataFim = $_POST['DataFim'];
                  $local_evento = $_POST['local_evento'];
                  $foto_evento = $_POST['foto_evento'];

                  // Verifica se foi feito upload de uma nova foto
                  if (!empty($_FILES['foto_evento']['name'])) {
                      // Define os formatos permitidos para a foto
                      $formatP = array("png", "jpg", "jpeg", "gif");
                      $extensao = pathinfo($_FILES['foto_evento']['name'], PATHINFO_EXTENSION);

                      // Verifica se a extensão do arquivo está entre os formatos permitidos
                      if (in_array($extensao, $formatP)) {
                          $pasta = "../img/eventos/";
                          $temporario = $_FILES['foto_evento']['tmp_name'];
                          $novoNome = uniqid() . ".{$extensao}";

                          // Move o arquivo temporário para a pasta de destino
                          if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                              // Se o upload foi bem-sucedido, verifica se há uma foto antiga para deletar
                              if ($foto && file_exists($pasta . $foto)) {
                                  unlink($pasta . $foto); // Deleta a foto antiga
                              }
                          } else {
                              $mensagem = "Erro, não foi possível fazer o upload do arquivo!";
                          }
                      } else {
                          echo "Formato inválido"; // Se o formato do arquivo não é permitido, exibe mensagem de erro
                      }
                  } else {
                      $novoNome = $foto_evento; // Se não foi feito upload de nova foto, mantém o nome da foto antiga
                  }

                  // Prepara e executa o comando SQL para atualizar os dados do contato
                  $update = "UPDATE Eventos SET nome_eventos=:nome_eventos, Descricao=:Descricao, DataInicio=:DataInicio, DataFim=:DataFim,local_evento=:local_evento,foto_evento=:foto_evento WHERE EventoID=:id";
                  try {
                      $result = $conect->prepare($update);
                      $result->bindParam(':id', $id, PDO::PARAM_STR);
                      $result->bindParam(':nome_eventos', $nome_eventos, PDO::PARAM_STR);
                      $result->bindParam(':Descricao', $Descricao, PDO::PARAM_STR);
                      $result->bindParam(':DataInicio', $DataInicio, PDO::PARAM_STR);
                      $result->bindParam(':DataFim', $DataFim, PDO::PARAM_STR);
                      $result->bindParam(':local_evento', $local_evento, PDO::PARAM_STR);
                      $result->bindParam(':foto_evento', $novoNome, PDO::PARAM_STR);
                      $result->execute();

                      // Verifica se a atualização foi bem-sucedida
                      $contar = $result->rowCount();
                      if ($contar > 0) {
                          // Se sim, exibe uma mensagem de sucesso e redireciona após 5 segundos
                          echo '<div class="container">
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-check"></i> Ok !!!</h5>
                                        Os dados foram atualizados com sucesso.
                                    </div>
                                </div>';
                          header("Refresh: 3, home.php");
                      } else {
                          // Se não, exibe uma mensagem de erro
                          echo '<div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> Erro !!!</h5>
                                    Não foi possível atualizar os dados.
                                </div>';
                      }
                  } catch (PDOException $e) {
                      // Em caso de erro PDO durante a atualização, exibe a mensagem de erro
                      echo "<strong>ERRO DE PDO= </strong>" . $e->getMessage();
                  }
              }
              ?>
              
            </div>
</div>
            
            <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Dados do Eventos</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0" style="text-align: center; margin-bottom: 98px">
              <?php
                  if ($show->foto_evento == 'foto_padra_event.png') {
                      echo '<img src="../img/evento_p/' . htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8') . '" style="width: 250px; border-radius: 100%;padding-top: 15px;">';
                  } else {
                      echo '<img src="../img/eventos/' . htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8') . '" style="width: 250px; border-radius: 100%;padding-top: 15px;">';
                  }
                  ?>


                <h1><?php echo $nome_eventos; ?></h1>
                <strong><?php echo $Descricao; ?></strong>
                <p><?php echo $DataInicio; ?></p>
                <p><?php echo $DataFim; ?></p>
                <strong><?php echo $local_evento; ?></strong>
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
  