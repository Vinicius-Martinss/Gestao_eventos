<?php
// Função para iniciar a sessão se não estiver ativa
function startSessionIfNotStarted() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Iniciar a sessão
startSessionIfNotStarted();

// Inclui o arquivo de conexão com o banco de dados
include('../config/conexao.php');

// Obtém o ID do usuário logado da sessão
$usuario_id_logado = $_SESSION['id_user'] ?? null;

// Verifica se o parâmetro 'id' foi passado via GET
if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

// Obtém o valor do parâmetro 'id' e filtra como um inteiro
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Prepara e executa a consulta para selecionar o evento com base no 'id'
$select = "SELECT * FROM Eventos WHERE EventoID = :id";
try {
    $resultado = $conect->prepare($select);
    $resultado->bindParam(':id', $id, PDO::PARAM_INT);
    $resultado->execute();

    // Verifica se o evento foi encontrado
    if ($resultado->rowCount() > 0) {
        $show = $resultado->fetch(PDO::FETCH_OBJ);
        $nome_eventos = $show->nome_eventos;
        $Descricao = $show->Descricao;
        $idade_minima = $show->idade_minima;
        $valor_ingresso = $show->valor_ingresso;
        $DataInicio = $show->DataInicio;
        $DataFim = $show->DataFim;
        $local_evento = $show->local_evento;
        $foto_evento = $show->foto_evento;

        // Verifica se o usuário logado é o criador do evento
        $isOwner = ($show->id_user == $usuario_id_logado);
    } else {
        echo '<div class="alert alert-danger">Não há dados com o id informado!</div>';
        exit;
    }
} catch (PDOException $e) {
    echo "<strong>ERRO DE SELECT NO PDO: </strong>" . $e->getMessage();
}

// Inicializa a mensagem como vazia
$mensagem = '';

// Verifica se o formulário foi submetido
if (isset($_POST['upEvento'])) {
    // Obtém os dados do formulário
    $nome_eventos = $_POST['nome_eventos'];
    $Descricao = $_POST['Descricao'];
    $idade_minima = $_POST['idade_minima'];
    $valor_ingresso = $_POST['valor_ingresso'];
    $DataInicio = $_POST['DataInicio'];
    $DataFim = $_POST['DataFim'];
    $local_evento = $_POST['local_evento'];
    $foto_evento_atual = $_POST['foto_evento_atual'];

    // Verifica se foi feito upload de uma nova foto
    if (!empty($_FILES['foto_evento']['name'])) {
        $formatP = array("png", "jpg", "jpeg", "gif");
        $extensao = pathinfo($_FILES['foto_evento']['name'], PATHINFO_EXTENSION);

        if (in_array($extensao, $formatP)) {
            $pasta = "../img/eventos/";
            $temporario = $_FILES['foto_evento']['tmp_name'];
            $novoNome = uniqid() . ".{$extensao}";

            if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                $foto_evento = $novoNome;
            } else {
                $mensagem = "<div class='alert alert-danger'>Falha ao enviar a foto.</div>";
                $foto_evento = $foto_evento_atual; // Mantém a foto atual em caso de erro
            }
        } else {
            $mensagem = "<div class='alert alert-danger'>Formato de foto não suportado.</div>";
            $foto_evento = $foto_evento_atual; // Mantém a foto atual em caso de erro
        }
    } else {
        // Se não há nova foto, mantém a foto atual
        $foto_evento = $foto_evento_atual;
    }

    // Prepara e executa a consulta de atualização
    $update = "UPDATE Eventos SET nome_eventos = :nome_eventos, Descricao = :Descricao, idade_minima = :idade_minima, valor_ingresso = :valor_ingresso, DataInicio = :DataInicio, DataFim = :DataFim, local_evento = :local_evento, foto_evento = :foto_evento WHERE EventoID = :id";
    try {
        $resultado = $conect->prepare($update);
        $resultado->bindParam(':nome_eventos', $nome_eventos);
        $resultado->bindParam(':Descricao', $Descricao);
        $resultado->bindParam(':idade_minima', $idade_minima);
        $resultado->bindParam(':valor_ingresso', $valor_ingresso);
        $resultado->bindParam(':DataInicio', $DataInicio);
        $resultado->bindParam(':DataFim', $DataFim);
        $resultado->bindParam(':local_evento', $local_evento);
        $resultado->bindParam(':foto_evento', $foto_evento);
        $resultado->bindParam(':id', $id);
        $resultado->execute();

        // Verifica se a atualização foi bem-sucedida
        if ($resultado->rowCount() > 0) {
            $mensagem = '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Ok !!!</h5>
                            Dados atualizados com sucesso.
                        </div>';
        } else {
            $mensagem = '<div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-times"></i> Erro !!!</h5>
                            Nenhuma mudança detectada nos dados.
                        </div>';
        }
    } catch (PDOException $e) {
        $mensagem = "<strong>ERRO DE PDO: </strong>" . $e->getMessage();
    }
}
?>

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
                <!-- Coluna do formulário de edição -->
                <div class="col-md-<?php echo $isOwner ? '6' : '12'; ?>">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Editar Evento</h3>
                        </div>
                        <?php if ($isOwner): ?>
                            <!-- Formulário de edição do evento -->
                            <form role="form" action="" method="post" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="nome_eventos">Nome Evento</label>
                                        <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" required value="<?php echo htmlspecialchars($nome_eventos, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="descricao">Descrição</label>
                                        <input type="text" class="form-control" name="Descricao" id="descricao" required value="<?php echo htmlspecialchars($Descricao, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="idade_minima">Idade Mínima</label>
                                        <input type="number" class="form-control" name="idade_minima" id="idade_minima" required value="<?php echo htmlspecialchars($idade_minima, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="valor_ingresso">Valor do Ingresso</label>
                                        <input type="number" class="form-control" name="valor_ingresso" id="valor_ingresso" required value="<?php echo htmlspecialchars($valor_ingresso, ENT_QUOTES, 'UTF-8'); ?>" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="DataInicio">Data Início</label>
                                        <input type="date" class="form-control" name="DataInicio" id="DataInicio" required value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($DataInicio)), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="DataFim">Data Fim</label>
                                        <input type="date" class="form-control" name="DataFim" id="DataFim" required value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($DataFim)), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="local_evento">Local</label>
                                        <input type="text" class="form-control" name="local_evento" id="local_evento" required value="<?php echo htmlspecialchars($local_evento, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="foto_evento">Foto do evento</label>
                                        <input type="file" class="form-control-file" name="foto_evento" id="foto_evento">
                                        <input type="hidden" name="foto_evento_atual" value="<?php echo htmlspecialchars($foto_evento, ENT_QUOTES, 'UTF-8'); ?>">
                                        <small class="form-text text-muted">Deixe em branco se não deseja alterar a foto.</small>
                                    </div>
                                    <!-- Mensagem de confirmação ou erro será exibida aqui -->
                                    <?php echo $mensagem; ?>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" name="upEvento" class="btn btn-primary">Finalizar edição do evento</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- Mensagem de aviso se o usuário não for o criador -->
                            <div class="alert alert-warning">Você não tem permissão para editar este evento.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($isOwner): ?>
                    <div class="col-md-6">
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Evento</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Nome do Evento:</label>
                                    <p><?php echo htmlspecialchars($nome_eventos, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Descrição:</label>
                                    <p><?php echo htmlspecialchars($Descricao, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Idade Mínima:</label>
                                    <p><?php echo htmlspecialchars($idade_minima, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Valor do Ingresso:</label>
                                    <p>R$ <?php echo number_format($valor_ingresso, 2, ',', '.'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Data Início:</label>
                                    <p><?php echo htmlspecialchars($DataInicio, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Data Fim:</label>
                                    <p><?php echo htmlspecialchars($DataFim, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Local do Evento:</label>
                                    <p><?php echo htmlspecialchars($local_evento, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Foto do Evento:</label>
                                    <?php if ($foto_evento): ?>
                                        <img src="../img/eventos/<?php echo htmlspecialchars($foto_evento, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto do Evento" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                    <?php else: ?>
                                        <p>Sem foto</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

           