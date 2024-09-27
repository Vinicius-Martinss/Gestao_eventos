<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/conexao.php');

// Recupera o id_user da sessão
$id_user = $_SESSION['id_user'] ?? '';

// Verifica se o id_user foi recuperado
if (empty($id_user)) {
    die("Usuário não autenticado.");
}

$query = "SELECT email_user, senha_user, data_nascimento, foto_user FROM tb_user WHERE id_user=:id";
$stmt = $conect->prepare($query);
$stmt->bindParam(':id', $id_user, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $email = $row['email_user'];
    $data_nascimento = $row['data_nascimento'] ?? 'Não definido';
} else {
    die("Usuário não encontrado.");
}

function calcularIdade($data_nascimento) {
    $data_nascimento = new DateTime($data_nascimento);
    $hoje = new DateTime();
    return $hoje->diff($data_nascimento)->y;
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cadastro de Participantes</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Cadastre-se para Participar</h3>
                        </div>
                        <form role="form" action="" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" required placeholder="Digite seu nome">
                                </div>

                                <div class="form-group">
                                    <label for="sexo">Sexo</label>
                                    <select class="form-control" name="sexo" id="sexo" required>
                                        <option value="" disabled selected>Selecione seu sexo</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Feminino</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="eventos_registrados">Selecione um Evento</label>
                                    <select class="form-control" id="eventos_registrados" name="eventos_registrados" required>
                                        <option value="">Selecione um Evento</option>
                                        <?php
                                        $queryEventos = "SELECT EventoID, nome_eventos, idade_minima, valor_ingresso FROM Eventos ORDER BY nome_eventos ASC";
                                        try {
                                            $resultEventos = $conect->prepare($queryEventos);
                                            $resultEventos->execute();
                                            
                                            while ($evento = $resultEventos->FETCH(PDO::FETCH_OBJ)) {
                                                echo '<option value="' . $evento->EventoID . '" data-idade-minima="' . $evento->idade_minima . '" data-valor-ingresso="' . $evento->valor_ingresso . '">' . $evento->nome_eventos . '</option>';
                                            }
                                        } catch (PDOException $e) {
                                            echo '<option value="">Erro ao buscar eventos</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="idade_minima">Idade Mínima</label>
                                    <input type="text" class="form-control" id="idade_minima" name="idade_minima" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="valor_ingresso">Valor do Ingresso</label>
                                    <input type="text" class="form-control" id="valor_ingresso" name="valor_ingresso" readonly>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" required>
                                    <label class="form-check-label" for="exampleCheck1">Autorizo participar do Evento</label>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" name="botao" class="btn btn-primary">Participar do Evento</button>
                            </div>
                        </form>
                        
                        <?php
                        if (isset($_POST['botao'])) {
                            $nome = $_POST['nome'];
                            $sexo = $_POST['sexo'];
                            $EventoID = $_POST['eventos_registrados'];

                            // Verifica se o usuário já está registrado para o evento selecionado
                            $checkQuery = "SELECT * FROM Participantes WHERE email = :email AND EventoID = :EventoID";
                            $checkStmt = $conect->prepare($checkQuery);
                            $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
                            $checkStmt->bindParam(':EventoID', $EventoID, PDO::PARAM_INT);
                            $checkStmt->execute();
                            $isRegistered = $checkStmt->rowCount() > 0;

                            if ($isRegistered) {
                                echo '<div class="alert alert-danger">Você já está registrado neste evento.</div>';
                            } else {
                                // Calcular a idade do usuário
                                $idade_usuario = calcularIdade($data_nascimento);
                                $idade_minima = $_POST['idade_minima'];

                                // Verifica se a idade do usuário é suficiente para participar do evento
                                if ($idade_usuario < $idade_minima) {
                                    echo '<div class="alert alert-danger">Você não possui a idade mínima de ' . $idade_minima . ' anos para participar deste evento.</div>';
                                } else {
                                    // Prepara a consulta SQL para inserir os dados no banco de dados
                                    $cadastro = "INSERT INTO Participantes (nome, idade, sexo, email, EventoID) VALUES (:nome, :idade, :sexo, :email, :EventoID)";

                                    try {
                                        $result = $conect->prepare($cadastro);
                                        $result->bindParam(':nome', $nome, PDO::PARAM_STR);
                                        $result->bindParam(':idade', $idade_usuario, PDO::PARAM_INT);
                                        $result->bindParam(':sexo', $sexo, PDO::PARAM_STR);
                                        $result->bindParam(':email', $email, PDO::PARAM_STR);
                                        $result->bindParam(':EventoID', $EventoID, PDO::PARAM_INT);
                                        
                                        $result->execute();

                                        if ($result->rowCount() > 0) {
                                            echo '<div class="alert alert-success">Dados inseridos com sucesso!</div>';
                                            header("Refresh: 3;");
                                        } else {
                                            echo '<div class="alert alert-danger">Dados não inseridos!</div>';
                                            header("Refresh: 5;");
                                        }
                                    } catch (PDOException $e) {
                                        echo "<strong>ERRO DE PDO= </strong>" . htmlspecialchars($e->getMessage());   
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Eventos que Você Está Participando</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome do Evento</th>
                                        <th>Descrição</th>
                                        <th>Data Início</th>
                                        <th>Data Fim</th>
                                        <th>Local</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $queryEventosParticipando = "
                                    SELECT e.EventoID, e.nome_eventos, e.Descricao, e.DataInicio, e.DataFim, e.local_evento, e.idade_minima
                                    FROM Eventos e
                                    INNER JOIN Participantes p ON e.EventoID = p.EventoID
                                    WHERE p.email = :email
                                    ORDER BY e.DataInicio ASC
                                ";

                                    try {
                                        $resultEventosParticipando = $conect->prepare($queryEventosParticipando);
                                        $resultEventosParticipando->bindParam(':email', $email, PDO::PARAM_STR);
                                        $resultEventosParticipando->execute();

                                        if ($resultEventosParticipando->rowCount() > 0) {
                                            while ($evento = $resultEventosParticipando->FETCH(PDO::FETCH_OBJ)) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($evento->nome_eventos) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->Descricao) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->DataInicio) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->DataFim) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->local_evento) . '</td>';
                                                echo '<td>
                                                        <a href="conteudo/del-participacao.php?idDel=' . $evento->EventoID . '" onclick="return confirm(\'Deseja remover sua participação?\')" class="btn btn-danger" title="Remover Participação">
                                                            <i class="fas fa-user-times"></i>
                                                        </a>
                                                    </td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="6">Você não está participando de nenhum evento.</td></tr>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<tr><td colspan="6"><strong>ERRO DE PDO: </strong>' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('eventos_registrados').addEventListener('change', function () {
    var selectedOption = this.options[this.selectedIndex];
    var idadeMinima = selectedOption.getAttribute('data-idade-minima');
    var valorIngresso = selectedOption.getAttribute('data-valor-ingresso');

    document.getElementById('idade_minima').value = idadeMinima ? idadeMinima : '';
    document.getElementById('valor_ingresso').value = valorIngresso ? valorIngresso : '';
});
</script>
