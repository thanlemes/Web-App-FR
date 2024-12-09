<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Content-Type: application/json');
include "verificaSessao.php";
include "../conexao.php"; // Certifique-se de incluir a conexão com o banco

$usuarioID = $_SESSION['usuario_id'] ?? null;

if (!$usuarioID) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Inicializa a variável rotaAtiva como falsa
$rotaAtiva = false;

// Verifica se há uma rota ativa para o usuário
$query = "SELECT * FROM rota WHERE funcionario_id = ? AND (km_final IS NULL OR data_final IS NULL OR hora_final IS NULL) LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rotaAtiva = true;
}

// Retorna a resposta como JSON
echo json_encode(['rotaAtiva' => $rotaAtiva]);
exit;
