<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Eventos Pulse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .about {
            background: #e2e2e2;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .card-img-top {
            height: 200px; /* Altura fixa para as imagens dos eventos */
            object-fit: cover; /* Para manter a proporção */
        }
        .slider-img {
            height: 400px; /* Altura fixa para as imagens do slider */
            object-fit: cover; /* Para manter a proporção */
        }
        .tips {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
    </style>
</head>
<body>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bem-vindo ao Eventos Pulse</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div id="eventCarousel" class="carousel slide mb-4" data-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Exemplo de consulta para pegar imagens de eventos
                    $select = "SELECT * FROM Eventos ORDER BY DataInicio DESC LIMIT 5"; // Pega os 5 eventos mais recentes
                    try {
                        $result = $conect->prepare($select);
                        $result->execute();

                        if ($result->rowCount() > 0) {
                            $first = true; // Controla a classe 'active'
                            while ($show = $result->FETCH(PDO::FETCH_OBJ)) {
                                $fotoPath = ($show->foto_evento == 'padrao.jpeg') 
                                    ? '../img/evento_p/' . $show->foto_evento 
                                    : '../img/eventos/' . $show->foto_evento;
                                ?>
                                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                                    <img src="<?php echo $fotoPath; ?>" class="d-block w-100 slider-img" alt="<?php echo htmlspecialchars($show->nome_eventos, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <?php
                                $first = false;
                            }
                        } else {
                            echo '<div class="col-12"><p>Nenhuma imagem de evento encontrada.</p></div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="col-12"><p class="alert alert-danger">Erro: ' . $e->getMessage() . '</p></div>';
                    }
                    ?>
                </div>
                <a class="carousel-control-prev" href="#eventCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Anterior</span>
                </a>
                <a class="carousel-control-next" href="#eventCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Próximo</span>
                </a>
            </div>

            <div class="about">
                <h2>Sobre Nós</h2>
                <p>Seja bem-vindo ao Eventos Pulse, onde a conexão entre pessoas e experiências se transforma em momentos inesquecíveis. Oferecemos uma plataforma diversificada que reúne eventos de todos os tipos — desde shows vibrantes, oficinas criativas até conferências impactantes. Nossa missão é promover a cultura e a interação social, proporcionando experiências que enriquecem a vida. Junte-se à nossa comunidade e descubra o que há de melhor no mundo dos eventos!</p>
            </div>

            <h2>Eventos do Nosso Sistema</h2>
            <div class="row">
                <?php
                $select = "SELECT * FROM Eventos ORDER BY DataInicio DESC LIMIT 6"; // Pega os 6 eventos mais recentes
                try {
                    $result = $conect->prepare($select);
                    $result->execute();

                    if ($result->rowCount() > 0) {
                        while ($show = $result->FETCH(PDO::FETCH_OBJ)) {
                            $fotoPath = ($show->foto_evento == 'padrao.jpeg') 
                                ? '../img/evento_p/' . $show->foto_evento 
                                : '../img/eventos/' . $show->foto_evento;
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <img src="<?php echo $fotoPath; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($show->nome_eventos, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="card-header">
                                        <h3 class="card-title"><?php echo htmlspecialchars($show->nome_eventos, ENT_QUOTES, 'UTF-8'); ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Data:</strong> <?php echo htmlspecialchars($show->DataInicio, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p><strong>Local:</strong> <?php echo htmlspecialchars($show->local_evento, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p><strong>Descrição:</strong> <?php echo htmlspecialchars($show->Descricao, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <a href="home.php?acao=participante" class="btn btn-primary">Cadastre-se</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12"><p>Nenhum evento recente encontrado.</p></div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="col-12"><p class="alert alert-danger">Erro: ' . $e->getMessage() . '</p></div>';
                }
                ?>
            </div>

            <h2>Dicas para Participar de Eventos</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="tips">
                        <h4>1. Chegue Cedo</h4>
                        <p>Chegar com antecedência ajuda você a evitar filas e garante um bom lugar!</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="tips">
                        <h4>2. Verifique a Programação</h4>
                        <p>Fique atento à programação do evento para não perder nenhuma atividade importante.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="tips">
                        <h4>3. Interaja com Outros Participantes</h4>
                        <p>Eventos são ótimas oportunidades para fazer networking e conhecer pessoas novas.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="tips">
                        <h4>4. Use Conforto</h4>
                        <p>Escolha roupas e calçados confortáveis, especialmente para eventos que duram várias horas.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="tips">
                        <h4>5. Aproveite as Redes Sociais</h4>
                        <p>Use as redes sociais para compartilhar sua experiência e interagir com outros participantes!</p>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
