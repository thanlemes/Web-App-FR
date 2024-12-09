<?php
include 'verificaSessao.php';
include '../conexao.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
//     http_response_code(500);
//     echo json_encode([
//         'error' => "Erro: [$errno] $errstr - $errfile:$errline",
//     ]);
//     exit;
// });

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifique se o rota_id e destino estão presentes na requisição
    if (isset($_POST['rota_id']) && isset($_POST['destino'])) {
        include '../conexao.php';
        $rota_id = $_POST['rota_id'];
        $destino = $_POST['destino'];

        // Escapar dados para evitar SQL injection
        $rota_id = mysqli_real_escape_string($conn, $rota_id);
        $destino = mysqli_real_escape_string($conn, $destino);

        // Buscar o destino atual
        $query_check = "SELECT destino FROM rota WHERE id = '$rota_id'";
        $result_check = mysqli_query($conn, $query_check);
        $row = mysqli_fetch_assoc($result_check);
        $destinos_atuais = $row['destino'];

        // Remover o destino da string
        $novo_destino = str_replace('; ' . $destino, '', $destinos_atuais);
        $novo_destino = str_replace($destino . '; ', '', $novo_destino); // Caso o destino esteja no começo ou no meio

        // Atualizar a lista de destinos no banco
        $query_update = "UPDATE rota SET destino = '$novo_destino' WHERE id = '$rota_id'";

        if (mysqli_query($conn, $query_update)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar a lista de destinos.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
?>
