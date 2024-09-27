<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Editar Perfil</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-6">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Editar Perfil</h3>
                            <?php
                            // Verificar se a sessão já foi iniciada
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }

                            // Inclui o arquivo de conexão com o banco de dados
                            include('../config/conexao.php');

                            // Recupera o ID do usuário (supondo que ele esteja na sessão)
                            $id_user = $_SESSION['id_user'] ?? '';

                            // Recupera os dados do usuário
                            $query = "SELECT email_user, senha_user, data_nascimento, foto_user FROM tb_user WHERE id_user=:id";
                            $stmt = $conect->prepare($query);
                            $stmt->bindParam(':id', $id_user, PDO::PARAM_STR);
                            $stmt->execute();
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($row) {
                                $email_antigo = $row['email_user'];
                                $senha_antiga = $row['senha_user'];
                                $data_nascimento = isset($row['data_nascimento']) ? $row['data_nascimento'] : 'Não definido';
                                $foto_user = $row['foto_user'];

                                // Cálculo da idade com base na data de nascimento
                                $dataNascimento = new DateTime($data_nascimento);
                                $hoje = new DateTime();
                                $idade = $hoje->diff($dataNascimento)->y;
                            } else {
                                echo 'Nenhum usuário encontrado com o ID fornecido.';
                            }
                            ?>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" name="nome" id="nome" required value="<?php echo htmlspecialchars($nome_user); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="idade">Idade</label>
                                    <input type="text" class="form-control" name="idade_user" id="idade_user" value="<?php echo htmlspecialchars($idade); ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="email">Endereço de E-mail</label>
                                    <input type="email" class="form-control" name="email" id="email" required value="<?php echo htmlspecialchars($email_user); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="senha">Senha</label>
                                    <input type="password" class="form-control" name="senha" id="senha" value="" placeholder="**************************">
                                </div>

                                <div class="form-group">
                                    <label for="foto">Avatar do usuário</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="foto" id="foto">
                                            <label class="custom-file-label" for="foto">Escolher arquivo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" name="upPerfil" class="btn btn-primary">Alterar dados do usuário</button>
                            </div>
                        </form>

                        <?php
                        // Verifica se o formulário foi enviado
                        if (isset($_POST['upPerfil'])) {
                            // Recebe os dados do formulário
                            $nome = $_POST['nome'];
                            $email = $_POST['email'];
                            $senha_nova = $_POST['senha'];

                            // Verificar se existe imagem para fazer o upload
                            if (!empty($_FILES['foto']['name'])) {
                                $formatP = array("png", "jpg", "jpeg", "gif");
                                $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                                if (in_array($extensao, $formatP)) {
                                    $pasta = "../img/user/";
                                    $temporario = $_FILES['foto']['tmp_name'];
                                    $novoNome = uniqid() . ".{$extensao}";

                                    // Excluir a imagem antiga se ela existir
                                    if (file_exists($pasta . $foto_user) && $foto_user != 'avatar-padrao.png') {
                                        unlink($pasta . $foto_user);
                                    }

                                    // Move o novo arquivo para o diretório de upload
                                    if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                                        $foto_user = $novoNome; // Atualiza o nome da foto para o novo nome
                                    } else {
                                        $mensagem = "Erro, não foi possível fazer o upload do arquivo!";
                                    }
                                } else {
                                    $mensagem = "Formato inválido";
                                }
                            } else {
                                $foto_user = $foto_user; // Mantém a foto antiga se nenhuma nova for enviada
                            }

                            // Verificar se a senha foi alterada
                            $senha = !empty($senha_nova) ? password_hash($senha_nova, PASSWORD_DEFAULT) : $senha_antiga;

                            // Atualizar o banco de dados
                            $update = "UPDATE tb_user SET foto_user=:foto, nome_user=:nome, email_user=:email, senha_user=:senha WHERE id_user=:id";
                            try {
                                $result = $conect->prepare($update);
                                $result->bindParam(':id', $id_user, PDO::PARAM_STR);
                                $result->bindParam(':foto', $foto_user, PDO::PARAM_STR);
                                $result->bindParam(':nome', $nome, PDO::PARAM_STR);
                                $result->bindParam(':email', $email, PDO::PARAM_STR);
                                $result->bindParam(':senha', $senha, PDO::PARAM_STR);
                                $result->execute();

                               $contar = $result->rowCount();
                                if ($contar > 0) {
                                    echo '<div class="container">
                                            <div class="alert alert-success alert-dismissible">
                                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                              <h5><i class="icon fas fa-check"></i> Ok !!!</h5>
                                              Perfil atualizado com sucesso.
                                            </div>
                                          </div>';

                                    // Verificar se tanto o email quanto a senha foram alterados
                                    if ($email !== $email_antigo || $senha !== $senha_antiga) {
                                        header("Location: ?sair");
                                        exit();
                                    } else {
                                        header("Refresh: 3; home.php?acao=perfil");
                                        exit();
                                    }
                                } else {
                                    echo '<div class="alert alert-danger alert-dismissible">
                                            <button  type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h5><i class="icon fas fa-times"></i> Erro !!!</h5>
                                            Perfil não foi atualizado.
                                          </div>';
                                }
                            } catch (PDOException $e) {
                                echo "<strong>ERRO DE PDO= </strong>" . $e->getMessage();
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- right column -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Usuário</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0" style="text-align: center; margin-bottom: 98px">
                            <?php
                            // Verifica se a variável $foto_user não está vazia e se não é 'avatar-padrao.png'
                            if (!empty($foto_user) && $foto_user !== 'avatar-padrao.png') {
                                echo '<img src="../img/user/' . htmlspecialchars($foto_user) . '" alt="' . htmlspecialchars($foto_user) . '" title="' . htmlspecialchars($foto_user) . '" style="width: 250px; border-radius: 100%; padding-top:15px">';
                            } else {
                                // Exibe a imagem do avatar padrão
                                echo '<img src="../img/avatar_p/avatar-padrao.png" alt="avatar padrão" title="avatar padrão" style="width: 250px; border-radius: 100%; padding-top:15px">';
                            }
                            ?>
                            <h1><?php echo htmlspecialchars($nome_user); ?></h1>
                            <strong><?php echo htmlspecialchars($email_user); ?></strong>
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
