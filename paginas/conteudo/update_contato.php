<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/conexao.php');

// Recupera o ID do usuário da sessão
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';

// Recupera o ID do evento da URL
$evento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($evento_id > 0) {
    // Consulta SQL para selecionar o evento com base no ID e verificar o criador
    $select = "SELECT * FROM Eventos WHERE EventoID = :evento_id AND id_user = :id_user";
    
    try {
        $result = $conect->prepare($select);
        $result->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
        $result->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $result->execute();

        if ($result->rowCount() === 1) {
            $evento = $result->fetch(PDO::FETCH_ASSOC);
        } else {
            echo '<div class="container">
                    <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-times"></i> Erro!</h5>
                    Você não tem permissão para editar este evento!
                  </div>
                </div>';
            header("Refresh: 5; URL=home.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "<div class='container'>
                <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
                <strong>ERRO DE PDO: </strong>" . htmlspecialchars($e->getMessage()) . "
                </div>
              </div>";
        exit;
    }
} else {
    echo '<div class="container">
            <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-times"></i> Erro!</h5>
            Evento inválido!
          </div>
        </div>';
    header("Refresh: 5; URL=home.php");
    exit;
}
?>

<!-- HTML para o formulário de edição -->
<div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Editar Evento</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Atualizar Evento</h3>
              </div>
              <form role="form" action="editar-evento.php" method="post" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label for="nome_eventos">Nome do evento</label>
                    <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" value="<?php echo htmlspecialchars($evento['nome_eventos']); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="Descricao">Descrição</label>
                    <input type="text" class="form-control" name="Descricao" id="Descricao" value="<?php echo htmlspecialchars($evento['Descricao']); ?>" required>
                  </div>
                  <div class="form-group">
                    <label>Data Inicio:</label>
                    <div class="input-group date" id="reservationdateStart" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#reservationdateStart" name="DataInicio" id="DataInicio" value="<?php echo htmlspecialchars($evento['DataInicio']); ?>" required/>
                        <div class="input-group-append" data-target="#reservationdateStart" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                      <label>Data Fim:</label>
                      <div class="input-group date" id="reservationdateEnd" data-target-input="nearest">
                          <input type="text" class="form-control datetimepicker-input" data-target="#reservationdateEnd" name="DataFim" id="DataFim" value="<?php echo htmlspecialchars($evento['DataFim']); ?>" required/>
                          <div class="input-group-append" data-target="#reservationdateEnd" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                      </div>
                  </div>
                  <div class="form-group">
                    <label for="local_evento">Local</label>
                    <input type="text" class="form-control" name="local_evento" id="local_evento" value="<?php echo htmlspecialchars($evento['local_evento']); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="foto_evento">Foto do local</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="foto_evento" id="foto_evento">
                        <label class="custom-file-label" for="foto_evento">Arquivo de imagem</label>
                      </div>
                    </div>
                    <img src="../img/eventos/<?php echo htmlspecialchars($evento['foto_evento']); ?>" alt="Foto do Evento" class="img-thumbnail mt-2" style="max-width: 200px;">
                  </div>
                  
                  <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
                  <input type="hidden" name="evento_id" value="<?php echo $evento_id; ?>">

                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" required>
                    <label class="form-check-label" for="exampleCheck1">Autorizo a atualização do meu Evento</label>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" name="atualizar" class="btn btn-primary">Atualizar Evento</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>

<?php
// Processa a atualização se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $nome_eventos = $_POST['nome_eventos'];
    $Descricao = $_POST['Descricao'];
    $DataInicio = convertDateToMysqlFormat($_POST['DataInicio']);
    $DataFim = convertDateToMysqlFormat($_POST['DataFim']);
    $local_evento = $_POST['local_evento'];
    $evento_id = $_POST['evento_id'];

    if (isset($_FILES['foto_evento']) && $_FILES['foto_evento']['error'] == UPLOAD_ERR_OK) {
        $formatP = array("png", "jpg", "jpeg", "JPG", "gif");
        $extensao = pathinfo($_FILES['foto_evento']['name'], PATHINFO_EXTENSION);

        if (in_array($extensao, $formatP)) {
            $pasta = "../img/eventos/";
            $temporario = $_FILES['foto_evento']['tmp_name'];
            $novoNome = uniqid() . ".$extensao";

            if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                $foto = $novoNome;
            } else {
                echo "Erro, não foi possível fazer o upload do arquivo!";
                $foto = 'foto_padra_event.png';
            }
        } else {
            echo "Formato Inválido";
            $foto = 'foto_padra_event.png';
        }
    } else {
        // Se não houver nova foto, mantém a foto atual (não altera)
        $foto = isset($_POST['foto_evento_atual']) ? $_POST['foto_evento_atual'] : 'foto_padra_event.png';
    }

    try {
        // Atualiza o evento no banco de dados
        $update = "UPDATE Eventos SET nome_eventos = :nome_eventos, Descricao = :Descricao, DataInicio = :DataInicio, DataFim = :DataFim, local_evento = :local_evento, foto_evento = :foto_evento WHERE EventoID = :evento_id AND id_user = :id_user";

        $stmt = $conect->prepare($update);
        $stmt->bindParam(':nome_eventos', $nome_eventos);
        $stmt->bindParam(':Descricao', $Descricao);
        $stmt->bindParam(':DataInicio', $DataInicio);
        $stmt->bindParam(':DataFim', $DataFim);
        $stmt->bindParam(':local_evento', $local_evento);
        $stmt->bindParam(':foto_evento', $foto);
        $stmt->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo '<div class="container">
                    <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Sucesso!</h5>
                    Evento atualizado com sucesso!
                  </div>
                </div>';
            header("Refresh: 2; URL=home.php");
        } else {
            echo '<div class="container">
                    <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-times"></i> Erro!</h5>
                    Falha ao atualizar o evento.
                  </div>
                </div>';
        }
    } catch (PDOException $e) {
        echo "<div class='container'>
                <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
                <strong>ERRO DE PDO: </strong>" . htmlspecialchars($e->getMessage()) . "
                </div>
              </div>";
    }
}

// Função para converter data para o formato MySQL
function convertDateToMysqlFormat($date) {
    $dateObj = DateTime::createFromFormat('d/m/Y', $date);
    return $dateObj ? $dateObj->format('Y-m-d') : null;
}
?>
