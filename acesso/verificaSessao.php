<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_rgf'])) {
    // Se o usuário não estiver logado, redireciona para o login
    header('Location: ../index.php');
    exit;
}

// Conecta ao banco de dados
include "../conexao.php";


$rgf = mysqli_real_escape_string($conn, $_SESSION['usuario_rgf']);

$query = "
    SELECT
        funcionario.id AS funcionario_id,
        funcionario.rgf AS funcionario_rgf,
        funcionario.nome AS funcionario_nome,
        funcionario.email AS funcionario_email,
        funcionario.telefone AS funcionario_telefone,
        funcionario.registro_cnh AS funcionario_cnh,
        funcionario.categoria_cnh AS funcionario_categoria_cnh,
        funcionario.validade_cnh AS funcionario_validade_cnh,
        funcionario.status AS funcionario_status,
        cidade.nome AS cidade_nome,
        empresa.nome AS empresa_nome,
        secretaria.nome AS secretaria_nome,
        departamento.nome AS departamento_nome,
        cargo.nome AS cargo_nome
    FROM funcionario
    JOIN cargo_departamento ON funcionario.cargo_departamento_id = cargo_departamento.id
    JOIN cargo ON cargo_departamento.cargo_id = cargo.id
    JOIN departamento ON cargo_departamento.departamento_id = departamento.id
    JOIN secretaria ON departamento.secretaria_id = secretaria.id
    JOIN empresa ON secretaria.empresa_id = empresa.id
    JOIN cidade ON empresa.cidade_id = cidade.id
    WHERE funcionario.rgf = '$rgf'";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    $_SESSION['usuario_id'] = $user_data['funcionario_id'];
    $_SESSION['usuario_rgf'] = $user_data['funcionario_rgf'];
    $_SESSION['usuario_nome'] = $user_data['funcionario_nome'];
    $_SESSION['usuario_email'] = $user_data['funcionario_email'];
    $_SESSION['usuario_telefone'] = $user_data['funcionario_telefone'];
    $_SESSION['funcionario_cnh'] = $user_data['funcionario_cnh'];
    $_SESSION['funcionario_categoria_cnh'] = $user_data['funcionario_categoria_cnh'];
    $_SESSION['funcionario_validade_cnh'] = $user_data['funcionario_validade_cnh'];
    $_SESSION['funcionario_status'] = $user_data['funcionario_status'];
    $_SESSION['cidade_nome'] = $user_data['cidade_nome'];
    $_SESSION['empresa_nome'] = $user_data['empresa_nome'];
    $_SESSION['secretaria_nome'] = $user_data['secretaria_nome'];
    $_SESSION['departamento_nome'] = $user_data['departamento_nome'];
    $_SESSION['cargo_nome'] = $user_data['cargo_nome'];
} else {
    echo "<div style='font-size: 6rem;'>Usuário não encontrado.</div>";
    echo "<div style='font-size: 6rem;'><a href='logout.php'>clique para voltar</a></div>";
    exit;
}

// Verifica o status do usuário
if ($_SESSION['funcionario_status'] == 'inapto') {
    // Se o status do usuário for inapto, exibe uma mensagem de erro e desabilita o acesso
    echo "<h2>Condutor Inapto</h2>";
    echo "<p>Seu status de condutor está inapto. Entre em contato com a administração.</p>";
    echo "<div style='font-size: 6rem;'><a href='logout.php'>clique para voltar</a></div>";
    exit; // Impede o carregamento das demais funções da página
}

    
    $nome = $_SESSION['usuario_nome'] ?? 'Não especificado';
    $usuarioID = $_SESSION['usuario_id'] ?? 'Não especificado';
    $rgf = $_SESSION['usuario_rgf'] ?? 'Não especificado';
    $email = $_SESSION['usuario_email'] ?? 'Não especificado';
    $telefone = $_SESSION['usuario_telefone'] ?? 'Não especificado';
    $cnh = $_SESSION['funcionario_cnh'] ?? 'Não especificado';
    $categoria =$_SESSION['funcionario_categoria_cnh'] ?? 'Não especificado';
    $validade_cnh = $_SESSION['funcionario_validade_cnh'] ?? 'Não especificado';
    $status = $_SESSION['funcionario_status'] ?? 'Não especificado';
    $cidade = $_SESSION['cidade_nome'] ?? 'Não especificado';
    $empresa = $_SESSION['empresa_nome'] ?? 'Não especificado';
    $secretaria = $_SESSION['secretaria_nome'] ?? 'Não especificado';
    $departamento = $_SESSION['departamento_nome'] ?? 'Não especificado';
    $cargo = $_SESSION['cargo_nome'] ?? 'Não especificado';
    $cargoDepartamentoID = $_SESSION['cargo_departamento_id'];


    // Exibe todos os dados na página (exemplo de uso)
    // echo "<h1><strong>Nome:</strong>" . $nome . "</h1>";
    // echo "<p><strong>ID:</strong> " . $usuarioID . "</p>";
    // echo "<p><strong>RGF:</strong> " . $rgf . "</p>";
    // echo "<p><strong>Email:</strong> " . $email . "</p>";
    // echo "<p><strong>Telefone:</strong> " . $telefone . "</p>";
    // echo "<p><strong>CNH:</strong> " . $cnh . "</p>";
    // echo "<p><strong>Categoria CNH:</strong> " . $categoria . "</p>";
    // echo "<p><strong>Validade CNH:</strong> " . $validade_cnh . "</p>";
    // echo "<p><strong>Status:</strong> " . $status . "</p>";
    // echo "<p><strong>Cidade:</strong> " . $cidade . "</p>";
    // echo "<p><strong>Empresa:</strong> " . $empresa . "</p>";
    // echo "<p><strong>Secretaria:</strong> " . $secretaria . "</p>";
    // echo "<p><strong>Departamento:</strong> " . $departamento . "</p>";
    // echo "<p><strong>Cargo:</strong> " . $cargo . "</p>";
    // echo "<p><strong>ID de Cargo e Departamento:</strong> " . $cargoDepartamentoID . "</p>";
?>