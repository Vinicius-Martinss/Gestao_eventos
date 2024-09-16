<?php
// Inclui o arquivo de conexão com o banco de dados
include('../config/conexao.php');
session_start(); // Inicia a sessão

// Obtém o ID do usuário logado da sessão
$usuario_id_logado = $_SESSION['id_user'] ?? null; // Obtém o ID do usuário logado ou define como null se não estiver setado

// Verifica se o parâmetro 'id' foi passado via GET
if (!isset($_GET['id'])) {
    // Se não foi passado, redireciona para a página home.php
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
    $contar = $resultado->rowCount();
    if ($contar > 0) {
        // Se encontrado, obtém os dados do evento
        $show = $resultado->fetch(PDO::FETCH_OBJ);
        $idCont = $show->EventoID;
        $nome_eventos = $show->nome_eventos;
        $Descricao = $show->Descricao;
        $DataInicio = $show->DataInicio;
        $DataFim = $show->DataFim;
        $local_evento = $show->local_evento;
        $foto_evento = $show->foto_evento;
        
        // Verifica se o usuário logado é o criador do evento
        $isOwner = ($show->id_user == $usuario_id_logado);

    } else {
        // Se o evento não foi encontrado, exibe uma mensagem de erro
        echo '<div class="alert alert-danger">Não há dados com o id informado!</div>';
        exit;
    }
} catch (PDOException $e) {
    // Em caso de erro na consulta PDO, exibe a mensagem de erro
    echo "<strong>ERRO DE SELECT NO PDO: </strong>" . $e->getMessage();
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
                                        <label for="DataInicio">Data Início</label>
                                        <input type="text" class="form-control" name="DataInicio" id="DataInicio" required value="<?php echo htmlspecialchars($DataInicio, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="DataFim">Data Fim</label>
                                        <input type="text" class="form-control" name="DataFim" id="DataFim" required value="<?php echo htmlspecialchars($DataFim, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="local_evento">Local</label>
                                        <input type="text" class="form-control" name="local_evento" id="local_evento" required value="<?php echo htmlspecialchars($local_evento, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="foto_evento">Foto do evento</label>
                                        <input type="file" class="form-control-file" name="foto_evento" id="foto_evento">
                                        <small class="form-text text-muted">Deixe em branco se não deseja alterar a foto.</small>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" name="upEvento" class="btn btn-primary">Finalizar edição do evento</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- Mensagem de aviso se o usuário não for o criador -->
                            <div class="alert alert-warning">Você não tem permissão para editar este evento, pois você não é o criador.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Coluna de exibição dos dados do evento -->
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
