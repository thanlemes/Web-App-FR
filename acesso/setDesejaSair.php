<?php
// setDesejaSair.php
session_start();

// Recebe o valor de "desejaSair" do JavaScript
$dados = json_decode(file_get_contents('php://input'), true);

// Define a variável de sessão "desejaSair" com base no valor recebido
if (isset($dados['desejaSair']) && $dados['desejaSair'] === 'true') {
    $_SESSION['desejaSair'] = true;  // Define que o usuário clicou em "Sair"
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
