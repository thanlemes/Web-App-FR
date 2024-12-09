<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "verificaSessao.php";
    
    $litros = $_POST['litros'];
    $km_atual = $_POST['km'];
    $rota_id = 1; // Substitua por um ID de rota real conforme necessário
    $comprovante = $_FILES['comprovante'];

    // Validar litros e km
    if (!preg_match('/^\d{1,3}(\.\d{1})?$/', $litros) || !preg_match('/^\d{1,6}$/', $km_atual)) {
        echo "Valores de litros ou km inválidos.";
        exit;
    }

    // Processar upload do arquivo de comprovante
    if ($comprovante && $comprovante['tmp_name']) {
        $targetDir = "uploads/comprovantes/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = uniqid() . "_" . basename($comprovante['name']);
        $targetFilePath = $targetDir . $filename;

        if (move_uploaded_file($comprovante['tmp_name'], $targetFilePath)) {
            // Inserir os dados no banco de dados
            $stmt = $conn->prepare("INSERT INTO abastecimento (rota_id, litros, km_atual, comprovante_abastecimento) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idss", $rota_id, $litros, $km_atual, $targetFilePath);

            if ($stmt->execute()) {
                echo "Abastecimento registrado com sucesso.";
            } else {
                echo "Erro ao registrar abastecimento.";
            }

            $stmt->close();
        } else {
            echo "Erro ao salvar o arquivo de comprovante.";
        }
    }
}

$conn->close();
?>