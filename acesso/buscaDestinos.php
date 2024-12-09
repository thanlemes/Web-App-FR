<?php
include "verificaSessao.php";
include "../conexao.php";

header('Content-Type: application/json');

// Verifique se o parâmetro foi enviado
if (!isset($_POST['rota_id'])) {
    echo json_encode(['success' => false, 'message' => 'Rota ID não fornecido']);
    exit;
}

$rota_id = intval($_POST['rota_id']); // Converte para inteiro de forma segura

// Prepare e execute a consulta
$stmt = $conn->prepare("SELECT destino FROM rota WHERE id = ?");
$stmt->bind_param('i', $rota_id);
$stmt->execute();
$result = $stmt->get_result();
$rota = $result->fetch_assoc();

if ($rota) {
    // Rota encontrada
    $destinos[] = $rota['destino'];
    echo json_encode(['success' => true, 'destino' => implode('; ', $destinos)]);
} else {
    // Rota não encontrada
    echo json_encode(['success' => false, 'message' => 'Rota não encontrada']);
}
?>
