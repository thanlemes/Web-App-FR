<?php
include '../conexao.php';
include 'verificaSessao.php';

// Verifique se o ID do usuário está disponível e se a rota está sendo passada corretamente
if ($usuarioID && isset($_POST['rota_id'])) {
    $rota_id = $_POST['rota_id'];
    $litros = $_POST['litros'];
    $km_atual = $_POST['km'];

    // Upload do comprovante (se houver)
    if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] === UPLOAD_ERR_OK) {
        $comprovante_path = 'uploads/comprovantes' . basename($_FILES['comprovante']['name']);
        if (!move_uploaded_file($_FILES['comprovante']['tmp_name'], $comprovante_path)) {
            echo json_encode(['success' => false, 'message' => 'Erro ao mover o arquivo de comprovante']);
            exit;
        }
    } else {
        $comprovante_path = '';  // Caso não tenha arquivo
    }


    // Verifica se o rota_id é válido (caso não tenha sido passado, não insere no banco)
    if (empty($rota_id)) {
        echo json_encode(['success' => false, 'message' => 'Rota não especificada']);
        exit;
    }

    // Prepara a query para inserir o abastecimento no banco
    $stmt = $conn->prepare("INSERT INTO abastecimento (rota_id, litros, km_atual, comprovante_abastecimento) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iids', $rota_id, $litros, $km_atual, $comprovante_path);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $conn->error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Usuário ou rota não encontrados']);
}
?>
