<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Lista de Eventos</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Eventos</h3>
                </div>
                <div class="card-body" style="display: flex; flex-direction: column; gap: 10px;">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align:center; max-width: 50px;">#</th>
                                <th style="text-align:center; max-width: 80px;">Foto do Evento</th>
                                <th style="max-width: 150px;">Nome Evento</th>
                                <th style="text-align:left; max-width: 300px;">Descrição</th>
                                <th style="max-width: 120px;">Data início:</th>
                                <th style="max-width: 120px;">Data Fim:</th>
                                <th style="max-width: 150px;">Local</th>
                                <th style="max-width: 100px;">Idade Mínima</th>
                                <th style="max-width: 100px;">Valor Ingresso</th>
                                <th style="max-width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Obtém o ID do usuário logado da sessão
                            $usuario_id_logado = $_SESSION['id_user'] ?? null;

                            // Consulta SQL para selecionar os eventos
                            $select = "SELECT * FROM Eventos ORDER BY EventoID DESC";
                            try {
                                $result = $conect->prepare($select);
                                $result->execute();
                                $cont = 1; // Inicializa o contador de linhas

                                if ($result->rowCount() > 0) {
                                    while ($show = $result->FETCH(PDO::FETCH_OBJ)) {
                                        $fotoPath = ($show->foto_evento == 'padrao.jpeg') 
                                            ? '../img/evento_p/' . $show->foto_evento 
                                            : '../img/eventos/' . $show->foto_evento;

                                        // Verifica se o usuário logado é o criador do evento
                                        $isOwner = ($show->id_user == $usuario_id_logado);
                                        ?>
                                        <tr>
                                            <td style="text-align:center;">
                                                <div class="box" style="max-width: 50px; text-align: center;"><?php echo $cont++; ?></div>
                                            </td>
                                            <td style="text-align:center;">
                                                <div class="box" style="max-width: 80px; text-align: center;">
                                                    <img src="<?php echo $fotoPath; ?>" alt="<?php echo htmlspecialchars($show->foto_evento, ENT_QUOTES, 'UTF-8'); ?>" style="width: 40px; border-radius: 100%;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 150px;">
                                                    <?php echo htmlspecialchars($show->nome_eventos, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($show->Descricao, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 120px;">
                                                    <?php echo htmlspecialchars($show->DataInicio, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 120px;">
                                                    <?php echo htmlspecialchars($show->DataFim, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 150px;">
                                                    <?php echo htmlspecialchars($show->local_evento, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 100px;">
                                                    <?php echo htmlspecialchars($show->idade_minima, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="box" style="max-width: 100px;">
                                                    <?php echo htmlspecialchars($show->valor_ingresso, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </td>
                                            <td style="text-align:center; white-space: nowrap;">
                                                <?php if ($isOwner): ?>
                                                    <div class="btn-group no-flex" style="display: flex; justify-content: center; gap: 5px;">
                                                        <a href="home.php?acao=editar&id=<?php echo $show->EventoID; ?>" class="btn btn-success btn-sm" title="Editar Evento">
                                                            <i class="fas fa-user-edit"></i>
                                                        </a>
                                                        <a href="conteudo/del-evento.php?idDel=<?php echo $show->EventoID; ?>" onclick="return confirm('Deseja remover o evento?')" class="btn btn-danger btn-sm" title="Remover Evento">
                                                            <i class="fas fa-user-times"></i>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <span>N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="10" class="text-center alert ">Não há eventos!</td></tr>';
                                }
                            } catch (Exception $e) {
                                echo '<tr><td colspan="10" class="text-center alert alert-danger"><strong>ERRO DE PDO= </strong>' . $e->getMessage() . '</td></tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="width: 5%; text-align:center">#</th>
                                <th style="text-align:center">Foto do Evento</th>
                                <th>Nome do Evento</th>
                                <th style="text-align:left">Descrição</th>
                                <th>Data início:</th>
                                <th>Data Fim:</th>
                                <th>Local</th>
                                <th>Idade Mínima</th>
                                <th>Valor Ingresso</th>
                                <th>Ações</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Define o layout da tabela */
    table#example {
        border-collapse: separate;
        border-spacing: 0 10px; /* Espaçamento entre as linhas */
        width: 100%;
    }

    /* Define que o cabeçalho e as células não têm bordas e o fundo é transparente */
    table#example th, 
    table#example td {
        padding: 0; /* Remove o padding interno */
        border: none; /* Remove bordas visíveis */
        background-color: transparent; /* Fundo transparente */
    }

    /* Estilo para os boxes de cada coluna */
    .box {
        padding: 8px; /* Espaço interno nas caixas */
        border: 1px solid transparent; /* Borda transparente */
        background-color: white; /* Fundo branco para as caixas */
        word-wrap: break-word; /* Permite quebra de palavras longas */
        overflow-wrap: break-word; /* Garante quebra de palavras longas */
        white-space: normal; /* Permite quebra de linha */
        overflow: hidden; /* Esconde qualquer texto que ultrapasse */
        text-overflow: clip; /* Remove reticências */
    }

    /* Define que o flex não afeta botões */
    .no-flex {
        display: inline-flex;
    }
</style>

<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>