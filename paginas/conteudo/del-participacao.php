<?php
session_start();
include('../../config/conexao.php');

// Verifica se o usuário está autenticado
$email = $_SESSION['loginUser'] ?? ''; // Mudou para 'loginUser'

if (empty($email)) {
    $_SESSION['message'] = "Usuário não autenticado.";
    header("Location: ../home.php");
    exit();
}

// Recupera o ID do evento a ser removido
$idDel = isset($_GET['idDel']) ? intval($_GET['idDel']) : 0;

if ($idDel > 0) {
    $delParticipacao = "DELETE FROM Participantes WHERE EventoID = :EventoID AND email = :email";
    try {
        $result = $conect->prepare($delParticipacao);
        $result->bindParam(':EventoID', $idDel, PDO::PARAM_INT);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->execute();

        if ($result->rowCount() > 0) {
            $_SESSION['message'] = "Participação removida com sucesso.";
        } else {
            $_SESSION['message'] = "Erro ao remover participação ou participação não encontrada.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro: " . htmlspecialchars($e->getMessage());
    }
} else {
    $_SESSION['message'] = "ID de evento inválido.";
}

header("Location: ../home.php");
exit();
?>
