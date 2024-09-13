<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/conexao.php');

// Recupera o email da sessão
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cadastro de Participantes</h1>
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
                            <h3 class="card-title">Cadastrar Participante</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" required placeholder="Digite seu nome">
                                </div>
                                <div class="form-group">
                                    <label for="idade">Idade</label>
                                    <input type="text" class="form-control" name="idade" id="idade" required placeholder="Digite sua idade">
                                </div>
                                <div class="form-group">
                                    <label for="sexo">Sexo</label>
                                    <input type="text" class="form-control" name="sexo" id="sexo" required placeholder="Digite seu sexo">
                                </div>
                                <div class="form-group">
                                    <label for="eventos_registrados">Selecione um Evento</label>
                                    <select class="form-control" id="eventos_registrados" name="eventos_registrados">
                                        <option value="">Selecione um Evento</option>
                                        <?php
                                        // Consulta SQL para obter todos os eventos
                                        $queryEventos = "SELECT EventoID, nome_eventos FROM Eventos ORDER BY nome_eventos ASC";
                                        
                                        try {
                                            // Prepara e executa a consulta
                                            $resultEventos = $conect->prepare($queryEventos);
                                            $resultEventos->execute();
                                            
                                            // Verifica se há eventos registrados
                                            $contarEventos = $resultEventos->rowCount();
                                            if ($contarEventos > 0) {
                                                // Loop para exibir cada evento como uma opção no <select>
                                                while ($evento = $resultEventos->FETCH(PDO::FETCH_OBJ)) {
                                                    echo '<option value="' . $evento->EventoID . '">' . $evento->nome_eventos . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">Nenhum evento encontrado</option>';
                                            }
                                        } catch (PDOException $e) {
                                            echo '<option value="">Erro ao buscar eventos</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" required>
                                    <label class="form-check-label" for="exampleCheck1">Autorizo participar do Evento</label>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" name="botao" class="btn btn-primary">Participar do Evento</button>
                            </div>
                        </form>
                        <?php
                        // Verifica se o formulário foi submetido
                        if (isset($_POST['botao'])) {
                            // Recupera os valores do formulário
                            $nome = $_POST['nome'];
                            $idade = $_POST['idade'];
                            $sexo = $_POST['sexo'];
                            $email = $_POST['email'];
                            $EventoID = $_POST['eventos_registrados'];

                            // Prepara a consulta SQL para inserir os dados no banco de dados
                            $cadastro = "INSERT INTO Participantes (nome, idade, sexo, email, EventoID) VALUES (:nome, :idade, :sexo, :email, :EventoID)";

                            try {
                                // Prepara a consulta SQL com os parâmetros
                                $result = $conect->prepare($cadastro);
                                $result->bindParam(':nome', $nome, PDO::PARAM_STR);
                                $result->bindParam(':idade', $idade, PDO::PARAM_STR);
                                $result->bindParam(':sexo', $sexo, PDO::PARAM_STR);
                                $result->bindParam(':email', $email, PDO::PARAM_STR);
                                $result->bindParam(':EventoID', $EventoID, PDO::PARAM_INT);

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
                                        <h5><i class="icon fas fa-times"></i> Erro!</h5>
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
                
                <!-- Seção para Listagem de Eventos que o Usuário Está Participando -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Eventos que Você Está Participando</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome do Evento</th>
                                        <th>Descrição</th>
                                        <th>Data Início</th>
                                        <th>Data Fim</th>
                                        <th>Local</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta SQL para obter eventos nos quais o usuário está participando
                                    $queryEventosParticipando = "
                                        SELECT e.nome_eventos, e.Descricao, e.DataInicio, e.DataFim, e.local_evento
                                        FROM Eventos e
                                        INNER JOIN Participantes p ON e.EventoID = p.EventoID
                                        WHERE p.email = :email
                                        ORDER BY e.DataInicio ASC
                                    ";

                                    try {
                                        // Prepara a consulta SQL com o parâmetro :email do participante
                                        $resultEventosParticipando = $conect->prepare($queryEventosParticipando);
                                        $resultEventosParticipando->bindParam(':email', $email, PDO::PARAM_STR);
                                        $resultEventosParticipando->execute();

                                        // Verifica se há eventos nos quais o usuário está participando
                                        $contarEventosParticipando = $resultEventosParticipando->rowCount();
                                        if ($contarEventosParticipando > 0) {
                                            // Loop para exibir cada evento como uma linha na tabela
                                            while ($evento = $resultEventosParticipando->FETCH(PDO::FETCH_OBJ)) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($evento->nome_eventos) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->Descricao) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->DataInicio) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->DataFim) . '</td>';
                                                echo '<td>' . htmlspecialchars($evento->local_evento) . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5">Você não está participando de nenhum evento.</td></tr>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<tr><td colspan="5"><strong>ERRO DE PDO: </strong>' . htmlspecialchars($e->getMessage()) . '</td></tr>';
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
