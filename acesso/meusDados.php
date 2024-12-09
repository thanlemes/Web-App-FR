<?php
    include "verificaSessao.php";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/acesso/meusDados.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Meus Dados</title>
</head>

<body id="meus-dados">
    <div class="seta-voltar">
        <a href="../acesso/home.php"><img src="../img/icone_select_cinza.png" alt=""></a>
    </div>
    <div class="nav-condutor-dados">
        <img src="../img/icone_dados_condutor_cinza.png" alt="">
        <h1>Condutor</h1>
    </div>
    <div class="container-condutor-dados">
        <div class="condutor-dados">
            <h2>NOME</h2>
            <p><?php echo $nome ?? 'Nome não encontrado'; ?></p>
        </div>
        <div class="condutor-dados">
            <h2>RGF</h2>
            <p><?php echo $rgf ?? 'RGF não encontrado'; ?></p>
        </div>
        <div class="condutor-dados">
            <h2>CNH</h2>
            <p><?php echo $cnh ?? 'CNH não encontrada'; ?></p>
        </div>
        <div class="condutor-dados">
            <h2>CATEGORIA</h2>
            <p><?php echo $categoria ?? 'Categoria não encontrada'; ?></p>
        </div>
        <div class="condutor-dados">
            <h2>VALIDADE</h2>
            <p><?php echo $validade_cnh ?? 'Validade não encontrada'; ?></p>
        </div>
    </div>

    <div class="nav-condutor-dados-lotacao">
        <h1>Lotação</h1>
    </div>
    <div class="container-condutor-dados">
        <div class="condutor-dados">
            <h2>SECRETARIA</h2>
            <p>SMMU</p>
        </div>
        <div class="condutor-dados">
            <h2>DEPTO</h2>
            <p>DID</p>
        </div>
        <div class="condutor-dados">
            <h2>CARGO</h2>
            <p>Motorista</p>
        </div>
    </div>

    <div class="nav-condutor-dados-status">
        <h1>Status</h1>
    </div>
    <div class="status-condutor">
        <h3 class="condutor-apto">APTO</h3>
        <p class="condutor-autorizado">Condutor Autorizado!</p>
    </div>
</body>

</html>
