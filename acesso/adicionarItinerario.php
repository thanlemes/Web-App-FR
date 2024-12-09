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
    // Verifique se o rota_id e endereco (destino) estão presentes na requisição
    if (isset($_POST['rota_id']) && isset($_POST['endereco'])) {
        include '../conexao.php';
        $rota_id = $_POST['rota_id'];
        $endereco = $_POST['endereco'];

        // Escapar dados para evitar SQL injection
        $rota_id = mysqli_real_escape_string($conn, $rota_id);
        $endereco = mysqli_real_escape_string($conn, $endereco);

        // Verifique se o destino já existe
        $query_check = "SELECT destino FROM rota WHERE id = '$rota_id'";
        $result_check = mysqli_query($conn, $query_check);
        $row = mysqli_fetch_assoc($result_check);
        $destinos_atuais = $row['destino'];

        if (strpos($destinos_atuais, $endereco) === false) {
            // Se o destino não estiver presente, adicione
            $query = "UPDATE rota SET destino = CONCAT(destino, '; ', '$endereco') WHERE id = '$rota_id'";

            if (mysqli_query($conn, $query)) {
                echo json_encode(['success' => true, 'destino' => $destinos_atuais . '; ' . $endereco]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao adicionar destino.']);
                error_log("Erro no SQL: " . mysqli_error($conn));  // Log do erro SQL
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Destino já adicionado.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}
?>
