<?php 
include ('../../config/conexao.php');

$message = ''; // Variável para armazenar a mensagem de erro

// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recupera o ID do usuário logado da sessão
$id_user_logado = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';

if (isset($_GET['idDel'])) {
    $id = $_GET['idDel'];

    try {
        // Verifica se o evento existe e obtém o ID do criador
        $select = "SELECT EventoID, id_user, foto_evento FROM Eventos WHERE EventoID = :id";
        $stmt = $conect->prepare($select);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            $foto = $event['foto_evento'];
            $id_criador = $event['id_user'];

            // Verifica se o usuário logado é o criador do evento
            if ($id_user_logado != $id_criador) {
                $message = '<div class="alert alert-danger alert-dismissible mt-3">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                              <h5><i class="icon fas fa-times"></i> Erro!</h5>
                              Você não tem permissão para excluir este evento.So e possivel deletar o Criador!!!
                            </div>';
            } else {
                // Verifica se o evento possui participantes
                $checkParticipants = "SELECT COUNT(*) AS total FROM Participantes WHERE EventoID = :id";
                $stmtCheck = $conect->prepare($checkParticipants);
                $stmtCheck->bindValue(':id', $id, PDO::PARAM_INT);
                $stmtCheck->execute();
                $participants = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($participants['total'] > 0) {
                    // Mensagem de erro se houver participantes
                    $message = '<div class="alert alert-danger alert-dismissible mt-3">
                                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                  <h5><i class="icon fas fa-times"></i> Erro!</h5>
                                  Não é possível deletar o evento, pois há participantes cadastrados!
                                </div>';
                } else {
                    // Remove a foto do evento se não houver participantes
                    if ($foto != 'avatar-padrao.png') {
                        $filepath = "../../img/eventos/" . $foto;
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                    }

                    // Deleta o evento
                    $delete = "DELETE FROM Eventos WHERE EventoID = :id";
                    $stmtDelete = $conect->prepare($delete);
                    $stmtDelete->bindValue(':id', $id, PDO::PARAM_INT);
                    $stmtDelete->execute();

                    if ($stmtDelete->rowCount() > 0) {
                        header("Location: ../home.php");
                        exit;
                    } else {
                        $message = '<div class="alert alert-danger alert-dismissible mt-3">
                                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                      <h5><i class="icon fas fa-times"></i> Erro!</h5>
                                      Não foi possível deletar o evento.
                                    </div>';
                    }
                }
            }
        } else {
            $message = '<div class="alert alert-danger alert-dismissible mt-3">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                          <h5><i class="icon fas fa-times"></i> Erro!</h5>
                          Evento não encontrado!
                        </div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger alert-dismissible mt-3">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-times"></i> Erro!</h5>
                      <strong>Erro de PDO:</strong> ' . htmlspecialchars($e->getMessage()) . '
                    </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Exclusão</title>
    <!-- Adicione os links do Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php
        if (!empty($message)) {
            echo $message;
        }
        ?>
        <a href="../home.php" class="btn btn-primary mt-3">Voltar para a Home</a>
    </div>
    <!-- Adicione os scripts do Bootstrap JS e jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
