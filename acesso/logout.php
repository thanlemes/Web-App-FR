<?php
    session_start();  // Inicia a sessão
    session_unset(); // Remove todas as variáveis de sessão
    session_destroy(); // Destrói todas as variáveis da sessão
    header('Location: ../index.php'); // Redireciona para a página de login
    exit;
