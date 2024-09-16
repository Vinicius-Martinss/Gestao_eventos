<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../config/conexao.php');

// Recupera o email da sessão
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Recupera o ID do evento a ser removido
$idDel = isset($_GET['idDel']) ? intval($_GET['idDel']) : 0;

if ($idDel > 0) {
    // Prepara a consulta SQL para remover a participação
    $delParticipacao = "DELETE FROM Participantes WHERE EventoID = :EventoID AND email = :email";

    try {
        // Prepara a consulta SQL com os parâmetros
        $result = $conect->prepare($delParticipacao);
        $result->bindParam(':EventoID', $idDel, PDO::PARAM_INT);
        $result->bindParam(':email', $email, PDO::PARAM_STR);

        // Executa a consulta SQL
        $result->execute();

        // Verifica se a exclusão foi bem-sucedida
        if ($result->rowCount() > 0) {
            // Se a exclusão for bem-sucedida, redireciona com uma mensagem de sucesso
            header("Location: ../home.php?message=Participação removida com sucesso.");
        } else {
            // Se a exclusão falhar, redireciona com uma mensagem de erro
            header("Location: ../home.php?message=Erro ao remover participação.");
        }
    } catch (PDOException $e) {
        // Redireciona com uma mensagem de erro em caso de exceção
        header("Location: ../home.php?message=Erro: " . htmlspecialchars($e->getMessage()));
    }
} else {
    // Redireciona com uma mensagem de erro se o ID não for válido
    header("Location: ../home.php?message=ID de evento inválido.");
}
exit();
?>
