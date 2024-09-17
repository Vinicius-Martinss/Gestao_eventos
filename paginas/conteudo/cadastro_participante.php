<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/conexao.php');

// Recupera o email da sessão
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$query = "SELECT email_user, senha_user, data_nascimento, foto_user FROM tb_user WHERE id_user=:id";
$stmt = $conect->prepare($query);
$stmt->bindParam(':id', $id_user, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $email_antigo = $row['email_user'];
    $senha_antiga = $row['senha_user'];
    $data_nascimento = isset($row['data_nascimento']) ? $row['data_nascimento'] : 'Não definido'; // Verifica se idade_user está definida
    $foto_user = $row['foto_user'];
}
function calcularIdade($data_nascimento) {
    $data_nascimento = new DateTime($data_nascimento);
    $hoje = new DateTime();
    $idade = $hoje->diff($data_nascimento)->y;
    return $idade;
}
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
                            <h3 class="card-title">Cadastre-se para Participar</h3>
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
                                        // Consulta SQL para obter todos os eventos
                                        $queryEventos = "SELECT EventoID, nome_eventos, idade_minima, valor_ingresso FROM Eventos ORDER BY nome_eventos ASC";
                                        
                                        try {
                                            // Prepara e executa a consulta
                                            $resultEventos = $conect->prepare($queryEventos);
                                            $resultEventos->execute();
                                            
                                            // Verifica se há eventos registrados
                                            $contarEventos = $resultEventos->rowCount();
                                            if ($contarEventos > 0) {
                                                // Loop para exibir cada evento como uma opção no <select>
                                                while ($evento = $resultEventos->FETCH(PDO::FETCH_OBJ)) {
                                                    echo '<option value="' . $evento->EventoID . '" data-idade-minima="' . $evento->idade_minima . '" data-valor-ingresso="' . $evento->valor_ingresso . '">' . $evento->nome_eventos . '</option>';
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

                                <!-- Campo de Idade Mínima -->
                                <div class="form-group">
                                    <label for="idade_minima">Idade Mínima</label>
                                    <input type="text" class="form-control" id="idade_minima" name="idade_minima" readonly>
                                </div>

                                <!-- Campo de Valor do Ingresso -->
                                <div class="form-group">
                                    <label for="valor_ingresso">Valor do Ingresso</label>
                                    <input type="text" class="form-control" id="valor_ingresso" name="valor_ingresso" readonly>
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
                        if (isset($_POST['botao'])) {
                            // Recupera os valores do formulário
                            $nome = $_POST['nome'];
                            $sexo = $_POST['sexo'];
                            $email = $_POST['email'];
                            $EventoID = $_POST['eventos_registrados'];
                            $valor_ingresso = $_POST['valor_ingresso'];

                            // Calcular a idade do usuário
                            $idade_usuario = calcularIdade($data_nascimento);

                            // Obter a idade mínima do evento selecionado
                            $queryIdadeMinima = "SELECT idade_minima FROM Eventos WHERE EventoID = :EventoID";
                            try {
                                $resultIdadeMinima = $conect->prepare($queryIdadeMinima);
                                $resultIdadeMinima->bindParam(':EventoID', $EventoID, PDO::PARAM_INT);
                                $resultIdadeMinima->execute();
                                
                                $evento = $resultIdadeMinima->fetch(PDO::FETCH_OBJ);
                                $idade_minima = $evento ? $evento->idade_minima : 0;
                            } catch (PDOException $e) {
                                echo "<strong>ERRO AO OBTER IDADE MÍNIMA: </strong>" . htmlspecialchars($e->getMessage());
                                exit;
                            }

                            // Verifica se a idade do usuário é suficiente para participar do evento
                            if ($idade_usuario < $idade_minima) {
                                echo '<div class="container">
                                        <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h5><i class="icon fas fa-times"></i> Erro!</h5>
                                            Você não possui a idade mínima de ' . $idade_minima . ' anos para participar deste evento.
                                        </div>
                                    </div>';
                            } else {
                                // Prepara a consulta SQL para inserir os dados no banco de dados
                                $cadastro = "INSERT INTO Participantes (nome, idade, sexo, email, EventoID, valor_ingresso) VALUES (:nome, :idade, :sexo, :email, :EventoID, :valor_ingresso)";

                                try {
                                    // Prepara a consulta SQL com os parâmetros
                                    $result = $conect->prepare($cadastro);
                                    $result->bindParam(':nome', $nome, PDO::PARAM_STR);
                                    $result->bindParam(':idade', $idade_usuario, PDO::PARAM_INT);
                                    $result->bindParam(':sexo', $sexo, PDO::PARAM_STR);
                                    $result->bindParam(':email', $email, PDO::PARAM_STR);
                                    $result->bindParam(':EventoID', $EventoID, PDO::PARAM_INT);
                                    $result->bindParam(':valor_ingresso', $valor_ingresso, PDO::PARAM_STR);

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
                                    echo "<strong>ERRO DE PDO= </strong>" . htmlspecialchars($e->getMessage());   
                                }
                            }
                        }
                        ?>
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->

                <!-- right column -->
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
                                    <th>Ação</th> <!-- Nova coluna para o botão de remoção -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Consulta SQL para obter eventos nos quais o usuário está participando
                                $queryEventosParticipando = "
                                SELECT e.EventoID, e.nome_eventos, e.Descricao, e.DataInicio, e.DataFim, e.local_evento, e.idade_minima
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
                                            echo '<td>
                                                    <a href="conteudo/del-participacao.php?idDel=' . $evento->EventoID . '" onclick="return confirm(\'Deseja remover sua participação?\')" class="btn btn-danger" title="Remover Participação">
                                                        <i class="fas fa-user-times"></i>
                                                    </a>
                                                </td>'; // Botão de remoção
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
                    <!-- /.card-body -->

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

<script>
document.getElementById('eventos_registrados').addEventListener('change', function () {
    var selectedOption = this.options[this.selectedIndex];
    var idadeMinima = selectedOption.getAttribute('data-idade-minima');
    var valorIngresso = selectedOption.getAttribute('data-valor-ingresso');

    document.getElementById('idade_minima').value = idadeMinima ? idadeMinima : '';
    document.getElementById('valor_ingresso').value = valorIngresso ? valorIngresso : '';
});
</script>
