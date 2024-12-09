<?php
    include "verificaSessao.php";
    include "criptografia.php";

    // Habilitar log de erros para depuração
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    $km = $_POST['km'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $placa = $_POST['placa'];
    $tombamento = $_POST['tombamento'];
    $protocolo = $_POST['protocolo'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/acesso/rotaFinalizada.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Rota Finalizada</title>
</head>

<body id="rota-finalizada">
    <div class="nav-condutor-rota-finalizada">
        <h1>ROTA FINALIZADA!</h1>
    </div>
    <div class="container-dados-veiculos">
        <div class="border-container">
            <div class="dados-veiculos-box">
                <div class="dados-veiculos">
                    <img src="../img/icone_veiculo_cinza.png" alt="">
                </div>
                <div class="dados-veiculos">
                    <h1>Protocolo</h1>
                    <p><?= htmlspecialchars($protocolo) ?></p>
                </div>
            </div>
            <div class="dados-veiculos-box">
                <div class="dados-veiculos">
                    <h1>Placa</h1>
                    <p><?= htmlspecialchars($placa) ?></p>
                </div>
                <div class="dados-veiculos">
                    <h1>Tombamento</h1>
                    <p><?= htmlspecialchars($tombamento) ?></p>
                </div>
            </div>
            <div class="dados-veiculos-box">
                <div class="dados-veiculos">
                    <h1>Data</h1>
                    <p><?= date('d/m/y', strtotime($data)) ?></p>
                </div>
                <div class="dados-veiculos">
                    <h1>Hora</h1>
                    <p><?= htmlspecialchars($hora) ?></p>
                </div>
            </div>
            <div class="guias-acesso">
            
            <?php
                $sql = "SELECT rota.*, veiculo.placa 
        FROM rota 
        JOIN veiculo ON rota.veiculo_id = veiculo.id 
        WHERE rota.funcionario_id = ? 
        ORDER BY rota.id DESC 
        LIMIT 1 OFFSET ?";

        // Obter parâmetros

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

        // Validar os parâmetros
        if ($usuarioID <= 0 || $offset < 0) {
            echo json_encode(['error' => 'Parâmetros inválidos.']);
            
        }

        // Depuração
        // echo "Offset: " . $offset . ", UsuarioID: " . $usuarioID;
        // exit;

        $stmt = $conn->prepare($sql);

        // Preparar a consulta
        if (!$stmt) {
            echo json_encode(['error' => "Erro ao preparar consulta SQL: " . $conn->error]);
            exit;
        }

        // Associar os parâmetros corretamente (ii para dois inteiros)
        if (!$stmt->bind_param("ii", $usuarioID, $offset)) {
            echo json_encode(['error' => "Erro ao associar parâmetros: " . $stmt->error]);
            exit;
        }

        // Executar a consulta
        if (!$stmt->execute()) {
            echo json_encode(['error' => "Erro ao executar consulta: " . $stmt->error]);
            exit;
        }

        $result = $stmt->get_result();


        if ($stmt) {
            // $stmt->bind_param("i", $usuarioID);
            // $stmt->execute();
            // $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($rota = $result->fetch_assoc()) {
                    $chave = '1234567890123456';
                    $id = $rota['id'];

                    // Criptografar o ID da rota
                    $rota_id_criptografado = criptografar_id($id, $chave);
                    ?>


                <div class="gerar-fr">
                    <form action="gerarFR.php" method="GET">
                        <button type="submit">
                            <div class="gerar-row">
                                <p>Gerar FR</p>
                                <img src="../img/icone_pdf_white.png" alt="Gerar FR">
                            </div>
                            <input type="hidden" name="rota_id" value="<?= $rota_id_criptografado ?>">
                        </button>
                    </form>
                </div>

                <?php
                }
            } else {
                echo "<p style='color: #6C757D; text-align: center; padding: 15px 10px; font-weight: bold;'>Nenhuma rota encontrada para este usuário.</p>";
            }
            
            $stmt->close();
        } else {
            echo "<p>Erro na consulta: " . $conn->error . "</p>";
        }
        ?>


                <a href="./home.php">Página Inicial</a>
            </div>
        </div>
    </div>
    <script src="../js/finalizarRota.js"></script>
</body>

</html>
