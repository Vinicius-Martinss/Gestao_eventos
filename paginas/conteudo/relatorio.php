<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Lista de Eventos</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Eventos</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align:center">#</th>
                                <th style="text-align:center">Foto do evento</th>
                                <th>Nome Evento</th>
                                <th style="text-align:left">Descrição</th>
                                <th>Data início:</th>
                                <th>Data Fim:</th>
                                <th>Local</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta SQL para selecionar os eventos
                            $select = "SELECT * FROM Eventos ORDER BY EventoID DESC";
                            try {
                                // Prepara a consulta SQL
                                $result = $conect->prepare($select);
                                $result->execute();
                                $cont = 1; // Inicializa o contador de linhas

                                // Verifica se a consulta retornou algum resultado
                                if ($result->rowCount() > 0) {
                                    // Itera sobre cada linha de resultado
                                    while ($show = $result->FETCH(PDO::FETCH_OBJ)) {
                                        $fotoPath = ($show->foto_evento == 'foto_padra_event.png') 
                                            ? '../img/evento_p/' . $show->foto_evento 
                                            : '../img/eventos/' . $show->foto_evento;
                                        ?>
                                        <tr>
                                            <td><?php echo $cont++; ?></td>
                                            <td>
                                                <img src="<?php echo $fotoPath; ?>" alt="<?php echo htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8'); ?>" style="width: 40px; border-radius: 100%;">
                                            </td>
                                            <td><?php echo htmlspecialchars($show->nome_eventos, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($show->Descricao, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($show->DataInicio, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($show->DataFim, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($show->local_evento, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="home.php?acao=editar&id=<?php echo $show->EventoID; ?>" class="btn btn-success" title="Editar Evento"><i class="fas fa-user-edit"></i></a>
                                                    <a href="conteudo/del-contato.php?idDel=<?php echo $show->EventoID; ?>" onclick="return confirm('Deseja remover o evento?')" class="btn btn-danger" title="Remover Evento"><i class="fas fa-user-times"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    // Se a consulta não retornar resultados, exibe uma mensagem
                                    echo '<tr><td colspan="8" class="text-center ">Não há eventos!</td></tr>';
                                }
                            } catch (Exception $e) {
                                // Exibe a mensagem de erro de PDO
                                echo '<tr><td colspan="8" class="text-center alert alert-danger"><strong>ERRO DE PDO= </strong>' . $e->getMessage() . '</td></tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="width: 5%; text-align:center">#</th>
                                <th style="text-align:center">Foto</th>
                                <th>Nome</th>
                                <th style="text-align:left">Descrição</th>
                                <th>Data início:</th>
                                <th>Data Fim:</th>
                                <th>Local</th>
                                <th>Ações</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
