<?php

$localhost = 'localhost';
$usuario = 'root';
$senha = '';
$db = 'fr';
$conn = new mysqli($localhost, $usuario, $senha, $db);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
} else {
    // echo "conectado";
}