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
                        <input type="text" class="form-control" name="nome_eventos" id="nome_eventos" required placeholder="Digite seu nome">
                    </div>
                    <div class="form-group">
                        <label for=">exampleInputEmail1">Email</label>
                        <input type="text" class="form-control" name="email" id="email" required placeholder="Digite seu email  ">
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
                        <div class="input-group">
                        <div class="custom-file">
                            <input type="hidden" class="custom-file-input" name="id_user" id="id_user" value="<?php echo $id_user ?>">
                        </div>
                        </div>
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
    // Inclui o arquivo de conexão com o banco de dados
    include('../config/conexao.php');

    // Verifica se o formulário foi submetido
    if (isset($_POST['botao'])) {
        // Recupera os valores do formulário
        $nome_eventos = $_POST['nome_eventos'];
        $email = $_POST['email'];
        $id_usuario = $_POST['id_user'];



        // Prepara a consulta SQL para inserir os dados no banco de dados
        $cadastro = "INSERT INTO Eventos (nome_eventos, email , id_user) VALUES (:nome_eventos  ,:email,  :id_user)";

        try {
            // Prepara a consulta SQL com os parâmetros
            $result = $conect->prepare($cadastro);
            $result->bindParam(':nome_eventos', $nome_eventos, PDO::PARAM_STR);
            $result->bindParam(':email', $email, PDO::PARAM_STR);
            $result->bindParam(':id_user', $id_usuario, PDO::PARAM_INT);

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
                    <h5><i class="icon fas fa-check"></i> Erro!</h5>
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
    <div class="col-md-8">
                <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Seus Eventos Recentes</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                        <th style="width: 10px">#</th>
                        <th>Nome do evento</th>
                        <th>Descrição</th>
                        <th>Data inicio:</th>
                        <th>Data Fim:</th>
                        <th>Local</th>
                        <th style="width: 40px">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
        // Consulta SQL para selecionar os contatos do usuário atual
        $select = "SELECT * FROM Eventos WHERE id_user = :id_user ORDER BY EventoID DESC LIMIT 6";

        try{
        // Prepara a consulta SQL com o parâmetro :id_user
        $result = $conect->prepare($select);
        // Inicializa o contador de linhas
        $cont = 1;
        // Vincula o ID do usuário ao parâmetro :id_user
        $result->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        // Executa a consulta SQL
        $result->execute();

        // Verifica se a consulta retornou algum resultado
        $contar = $result->rowCount();

        if ($contar > 0) {
            // Itera sobre cada linha de resultado da consulta
            while ($show = $result->FETCH(PDO::FETCH_OBJ)) {

        

                    ?>
                        <tr>
                        <td><?php echo $cont++; ?></td>
                        <td><?php echo $show->nome_eventos; ?></td>
                        <td><?php echo $show->Descricao; ?></td>
                        <td><?php echo $show->DataInicio; ?></td>
                        <td><?php echo $show->DataFim; ?></td>
                        <td><?php echo $show->local_evento; ?></td>
                        <td>
                        <div class="btn-group">
                            <a href="home.php?acao=editar&id=<?php echo $show->EventoID; ?>" class="btn btn-success" title="Editar Evento"><i class="fas fa-user-edit"></i></button>
                            <a href="conteudo/del-contato.php?idDel=<?php echo $show->EventoID; ?>" onclick="return confirm('Deseja remover o evento?')" class="btn btn-danger" title="Remover Evento"><i class="fas fa-user-times"></i></a>
                        </div>
                        </td>
                        </tr>
                    <?php
                        }
                    }else{
                        // Se a consulta não retornar resultados, exibe uma mensagem
                        echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>Não há Eventos!</strong></div>';
                    }
                    }catch(Exception $e){
                    // Exibe a mensagem de erro de PDO
                    echo '<strong>ERRO DE PDO= </strong>' . $e->getMessage();
                    }
                    ?>                                       
                    </tbody>
                    </table>
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