<?php

if (isset($_POST['submit'])) {
    include "verificaSessao.php";

    // Dados necessários
    $km = $_POST['km'] ?? 0;
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $protocolo = $_POST['protocolo'] ?? 0;

    if ($km > 0 && !empty($data) && !empty($hora) && $usuarioID) {
        $conn->begin_transaction();

        try {
            // Obter o registro mais recente na tabela rota para o usuário atual
            $queryGetRota = "
                SELECT id, veiculo_id, data_inicial 
                FROM rota 
                WHERE funcionario_id = $usuarioID 
                  AND (km_final IS NULL OR data_final IS NULL OR hora_final IS NULL)
                ORDER BY CONCAT(data_inicial, ' ', hora_inicial) DESC 
                LIMIT 1";
            $resultRota = $conn->query($queryGetRota);

            if ($resultRota->num_rows > 0) {
                $rotaData = $resultRota->fetch_assoc();
                $rotaID = $rotaData['id'];
                $veiculoID = $rotaData['veiculo_id'];
                $dataInicial = $rotaData['data_inicial'];
            } else {
                throw new Exception("Rota ou veículo não encontrados.");
            }

            // Obter os dados do veículo
            $queryGetVeiculo = "
                SELECT placa, tombamento 
                FROM veiculo 
                WHERE id = $veiculoID AND rota_funcionario_id = $usuarioID";
            $resultVeiculo = $conn->query($queryGetVeiculo);

            if ($resultVeiculo->num_rows > 0) {
                $veiculoData = $resultVeiculo->fetch_assoc();
                $placa = $veiculoData['placa'];
                $tombamento = $veiculoData['tombamento'];
            } else {
                throw new Exception("Veículo não encontrado ou dados incompletos.");
            }


            // Atualizar os dados do veículo
            $queryUpdateVeiculo = "
                UPDATE veiculo 
                SET km = $km, disponibilidade = 1, rota_funcionario_id = NULL 
                WHERE id = $veiculoID";
            if (!$conn->query($queryUpdateVeiculo)) {
                throw new Exception("Erro ao atualizar o veículo.");
            }

            // Atualizar os dados da rota
            $queryUpdateRota = "
                UPDATE rota 
                SET km_final = $km, data_final = '$data', hora_final = '$hora' 
                WHERE id = $rotaID";
            if (!$conn->query($queryUpdateRota)) {
                throw new Exception("Erro ao atualizar a rota.");
            }

            // Confirmar a transação
            $conn->commit();

            // Formulário para redirecionamento
            ?>
            <form id="finalizarRotaForm" action="rotaFinalizada.php" method="POST">
                <input type="hidden" name="km" value="<?= htmlspecialchars($km) ?>">
                <input type="hidden" name="data" value="<?= htmlspecialchars($data) ?>">
                <input type="hidden" name="hora" value="<?= htmlspecialchars($hora) ?>">
                <input type="hidden" name="placa" value="<?= htmlspecialchars($placa) ?>">
                <input type="hidden" name="tombamento" value="<?= htmlspecialchars($tombamento) ?>">
                <input type="hidden" name="protocolo" value="<?= htmlspecialchars($protocolo); ?>">
            </form>

            <script>
                document.getElementById("finalizarRotaForm").submit();
            </script>
            <?php
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo "Erro: " . $e->getMessage();
        }
    } else {
        echo "Todos os campos são obrigatórios.";
    }

    $conn->close();
}
?>
               
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/acesso/finalizarRota.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Finalizar Rota</title>
</head>

<body id="finalizar-rota">
    <?php
        include "verificaSessao.php";

            // Verificar se o ID do veículo foi passado pelo formulário
        if (isset($_POST['veiculo_id']) && !empty($_POST['veiculo_id'])) {
            $veiculo_id = $_POST['veiculo_id'];
            $placa = $_POST['placa'];
            $tombamento = $_POST['tombamento'];
            // echo "ID do veículo selecionado: " . htmlspecialchars($veiculo_id);

            // Consultar o banco de dados para pegar o protocolo mais recente baseado no veiculo_id e funcionario_id
            $queryProtocolo = "
                SELECT protocolo 
                FROM rota 
                WHERE veiculo_id = '$veiculo_id' 
                AND funcionario_id = '$usuarioID' 
                ORDER BY id DESC LIMIT 1
            ";
            $resultProtocolo = $conn->query($queryProtocolo);

            // Verificar se a consulta retornou um resultado
            if ($resultProtocolo && $resultProtocolo->num_rows > 0) {
                $protocoloData = $resultProtocolo->fetch_assoc();
                $protocolo = $protocoloData['protocolo'];
            } else {
                // Caso não encontre protocolo
                $protocolo = 0;
            }
        } else {
            echo "Nenhum veículo selecionado.";
            exit;
        }
    ?>
    <div class="seta-voltar">
        <a href="../acesso/home.php"><img src="../img/icone_select_cinza.png" alt=""></a>
    </div>
    <div class="nav-condutor-finalizar-rota">
        <h1>FINALIZAR ROTA</h1>
    </div>
    <div class="container-dados-veiculos">
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

        <div class="dados-partida">
            <form action="" method="POST">
                <input type="hidden" name="veiculo_id" value="<?= htmlspecialchars($veiculo_id); ?>">
                <input type="hidden" name="placa" value="<?= htmlspecialchars($placa); ?>">
                <input type="hidden" name="tombamento" value="<?= htmlspecialchars($tombamento); ?>">
                <input type="hidden" name="protocolo" value="<?= htmlspecialchars($protocolo); ?>">
                <h3>INSIRA OS DADOS (RETORNO)</h3>
                <div class="input-container">
                    <div class="input-wrapper">
                        <input type="number" name="km" id="km" required maxlength="6" inputmode="numeric">
                        <label for="km">KM</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="date" name="data" id="data" required>
                        <label for="data">DATA</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="time" name="hora" id="hora" required>
                        <label for="hora">HORA</label>
                    </div>
                    <button type="submit" name="submit" id="submit">Finalizar</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/finalizarRota.js"></script>
</body>
</html>
