<?php
    $mensagem = "";
    $tipoMensagem = "";

   // Verifica se o formulário foi enviado para iniciar a rota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_id']) && $_POST['form_id'] === 'formRota') {

       include "../conexao.php";  // Verifique se a sessão está configurada corretamente
       $usuarioID = $_POST['usuario_id'];

       $veiculo_id = isset($_POST['veiculo_id']) ? $_POST['veiculo_id'] : '';
       $placa = isset($_POST['placa']) ? $_POST['placa'] : '';
       $tombamento = isset($_POST['tombamento']) ? $_POST['tombamento'] : '';
       $marca = isset($_POST['marca']) ? $_POST['marca'] : '';
       $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
       $especie = isset($_POST['especie']) ? $_POST['especie'] : '';
       $km_inicial = isset($_POST['km_inicial']) ? $_POST['km_inicial'] : '';
   
       // Recebe os dados do formulário
       $data_inicial = isset($_POST['data']) ? $_POST['data'] : null;
       $hora_inicial = isset($_POST['hora']) ? $_POST['hora'] : null;
       $destino = isset($_POST['destino']) ? $_POST['destino'] : null;
   
       if (!$data_inicial || !$hora_inicial || !$destino) {
           echo "Dados incompletos para iniciar a rota.";
           exit;
       }
   
        // Gera um número de protocolo único
    function gerarProtocoloUnico($conn) {
        do {
            // Gera um número aleatório de 6 dígitos
            $protocolo = rand(100000, 999999);
    
            // Verifica no banco de dados se o protocolo já existe
            $query = "SELECT COUNT(*) FROM rota WHERE protocolo = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $protocolo);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
    
        } while ($count > 0); // Repete enquanto o protocolo já existir
    
        return $protocolo;
    }
    
    $protocolo = gerarProtocoloUnico($conn);

    // Insere a rota no banco de dados com o número de protocolo
    $rotaQuery = "
        INSERT INTO rota (veiculo_id, funcionario_id, data_inicial, hora_inicial, km_inicial, local_partida, destino, protocolo) 
        VALUES ('$veiculo_id', '$usuarioID', '$data_inicial', '$hora_inicial', '$km_inicial', 'Partida não especificada', '$destino', '$protocolo')
    ";
   
       if ($conn->query($rotaQuery) === TRUE) {
           // Agora que a rota foi inserida, buscamos o ID da rota recém-criada
           $selectRotaQuery = "
               SELECT id 
               FROM rota 
               WHERE veiculo_id = '$veiculo_id' AND funcionario_id = '$usuarioID' 
               ORDER BY id DESC LIMIT 1
           ";
   
           $result = $conn->query($selectRotaQuery);
   
           if ($result && $result->num_rows > 0) {
                // Obtém o ID da rota gerada
                $rota = $result->fetch_assoc();
                $rota_id = $rota['id'];
   
                // Atualiza o status do veículo para "indisponível"
                $updateVeiculoQuery = "UPDATE veiculo SET disponibilidade = 0, rota_funcionario_id = '$usuarioID' WHERE id = '$veiculo_id'";
                $conn->query($updateVeiculoQuery);

                

                $mensagem = "Sua rota foi iniciada com sucesso!<br><br>Redirecionando...";
                $tipoMensagem = "success"; // Ou "error", dependendo do caso
                
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'home.php'; // Redirecionamento
                        }, 3000); // 3 segundos
                    </script>";
   
               // Redireciona para `home.php` passando o ID da rota
                echo '<form id="formRota" method="POST" action="home.php">
                    <input type="hidden" name="rota_id" value="' . $rota_id . '">
                    <input type="hidden" name="veiculo_id" value="' . $veiculo_id . '">
                    <input type="hidden" name="placa" value="' . $placa . '">
                    <input type="hidden" name="tombamento" value="' . $tombamento . '">
                    <input type="hidden" name="marca" value="' . $marca . '">
                    <input type="hidden" name="modelo" value="' . $modelo . '">
                    <input type="hidden" name="especie" value="' . $especie . '">
                    <input type="hidden" name="km_inicial" value="' . $km_inicial . '">
                    <input type="hidden" name="data_inicial" value="' . $data_inicial . '">
                    <input type="hidden" name="hora_inicial" value="' . $hora_inicial . '">
                    <input type="hidden" name="destino" value="' . $destino . '">

                    <script>
                        setTimeout(function() {
                            document.getElementById("formRota").submit();
                        }, 3000); // 3000 milissegundos = 3 segundos
                    </script>
                </form>';


                } else {
                    $mensagem = "Erro ao buscar o ID da rota recém-criada.";
                    $tipoMensagem = "error";
                }
            } else {
            $mensagem = "Erro ao registrar a rota:<br>" . $conn->error;
            $tipoMensagem = "error";
        }
        $conn->close();
    }
   ?>
   

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/acesso/veiculoSelecionado.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Veículo Selecionado</title>
</head>
<body id="veiculoSelecionado">

    <!-- HTML para o loader -->
    <div id="loader" class="loader hidden">
        <div class="spinner"></div>
        <p>Aguarde...</p>
    </div>
    
    <?php if ($mensagem): ?>
        <div id="notification-popup" class="notification-popup <?= $tipoMensagem ?>">
            <p><?= $mensagem ?></p>
            <div class="countdown-bar"></div>
        </div>
    <?php endif; ?>

    <?php
    include "verificaSessao.php";

    // Verificar se o ID do veículo foi passado pelo formulário
    if (isset($_POST['veiculo_id']) && !empty($_POST['veiculo_id'])) {
        $veiculo_id = $_POST['veiculo_id'];
        // echo "ID do veículo selecionado: " . $veiculo_id;
    } else {
        echo "Nenhum veículo selecionado.";
        exit;
    }

    // Consulta para obter dados do veículo, incluindo km_inicial
    $veiculoQuery = "
        SELECT id, placa, tombamento, marca, modelo, especie, km
        FROM veiculo 
        WHERE id = '$veiculo_id'
    ";

    $veiculoResult = $conn->query($veiculoQuery);

    if ($veiculoResult && $veiculoResult->num_rows > 0) {
        $veiculoData = $veiculoResult->fetch_assoc();
        $placa = $veiculoData['placa'];
        $tombamento = $veiculoData['tombamento'];
        $marca = $veiculoData['marca'];
        $modelo = $veiculoData['modelo'];
        $especie = $veiculoData['especie'];
        $km_inicial = $veiculoData['km'];
    } else {
        // Caso não encontre o veículo
        echo "Erro: Veículo não encontrado.";
        exit;
    }
    ?>
    <div class="nav-veiculo-selecionado-box">
        <div class="seta-voltar">
            <a href="../acesso/home.php"><img src="../img/icone_select_cinza.png" alt=""></a>
        </div>
        <div class="nav-veiculo-selecionado">
            <img src="../img/icone_carro_selecionado_blue.png" alt="">
        </div>
        <div class="nav-menu">
            <div id="menu-icon" onclick="toggleMenu()">☰</div>
            <div id="overlay" class="hidden" onclick="toggleMenu()"></div> <!-- Sombra -->
            <div id="side-menu" class="hidden">
                <ul>
                    <li onclick="toggleMenu()"><img src="../img/icone_X_cinza.png" alt=""> Fechar</li>
                    <a href="./meusDados.php">
                        <li><img src="../img/icone_dados_cinza.png" alt="">Meus Dados</li>
                    </a>
                    <a href="./suporte.php">
                        <li><img src="../img/icone_suporte_cinza.png" alt="">Suporte</li>
                    </a>
                    <div id="modalLogout" class="modal2">
                        <div class="modal2-content">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <p>Tem certeza de que deseja sair da conta?</p>
                            <button onclick="window.location.href='logout.php'">Sim, Sair</button>
                            <button onclick="closeModal()">Cancelar</button>
                        </div>
                    </div>

                    <script>
                        function confirmLogout(event) {
                            event.preventDefault();  // Evita o link de ser seguido
                            document.getElementById("modalLogout").style.display = "block";  // Exibe o modal
                        }

                        function closeModal() {
                            document.getElementById("modalLogout").style.display = "none";  // Fecha o modal
                        }
                    </script>
                </ul>
            </div>
        </div>
    </div>
    <div class="container-dados-veiculos">
        <div class="dados-veiculos-box">
            <div class="dados-veiculos">
                <h1>Placa</h1>
                <p><?php echo $placa; ?></p>
            </div>
            <div class="dados-veiculos">
                <h1>Tombamento</h1>
                <p><?php echo $tombamento; ?></p>
            </div>
        </div>
        <div class="dados-veiculos-box-detalhe">
            <div class="dados-veiculos-detalhe">
                <h2>Marca</h2>
                <p><?php echo $marca; ?></p>
            </div>
            <div class="dados-veiculos-detalhe">
                <span></span>
                <h2>Modelo</h2>
                <p><?php echo $modelo; ?></p>
            </div>
            <div class="dados-veiculos-detalhe">
                <span></span>
                <span></span>
                <h2>Espécie</h2>
                <p><?php echo $especie; ?></p>
            </div>
        </div>

        <div class="dados-partida">
            <form id="formRota" action="" method="POST">
                <input type="hidden" name="veiculo_id" value="<?= $veiculo_id ?>">
                <input type="hidden" name="placa" value="<?= $placa ?>">
                <input type="hidden" name="tombamento" value="<?= $tombamento ?>">
                <input type="hidden" name="marca" value="<?= $marca ?>">
                <input type="hidden" name="modelo" value="<?= $modelo ?>">
                <input type="hidden" name="especie" value="<?= $especie ?>">
                <input type="hidden" name="km_inicial" value="<?= $km_inicial ?>">
                <input type="hidden" name="form_id" value="formRota">
                <input type="hidden" name="usuario_id" value="<?= $usuarioID ?>">

                <h3>INSIRA OS DADOS (PARTIDA)</h3>
                <div class="input-container">
                <div class="input-wrapper">
                    <label for="km">KM</label>
                    <input type="number" id="km" name="km" min="1" required maxlength="6" inputmode="numeric">
                </div>

                <div class="input-wrapper">
                    <label for="data">DATA</label>
                    <input type="date" id="data" name="data" required>
                </div>

                    <div class="input-wrapper">
                        <label for="hora">HORA</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>
                    <div class="input-wrapper">
                        <label for="destino">DESTINO</label>
                        <input type="text" id="destino" name="destino" required>
                    </div>
                    <button type="submit" id="submit">Iniciar Rota</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/veiculoSelecionado.js"></script>
</body>
</html>