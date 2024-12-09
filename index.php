<?php
session_start();

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario_rgf'])) {
    header('Location: ./acesso/home.php'); // Caso já esteja logado, redireciona para home
    exit();
}

include "conexao.php";

// Variável para mensagem de feedback
$mensagem = "";
$tipoMensagem = "";

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rgf = $_POST['rgf'];
    $senha = $_POST['senha-rgf'];

    // Previne injeção de SQL
    $rgf = mysqli_real_escape_string($conn, $rgf);
    $senha = mysqli_real_escape_string($conn, $senha);

    // Consulta para verificar o login, incluindo as informações das tabelas relacionadas
    $query = "
        SELECT 
            f.id, f.rgf, f.nome, f.email, f.telefone, f.endereco, f.registro_cnh, 
            f.categoria_cnh, f.validade_cnh, f.imagem_cnh, f.data_admissao, 
            f.status, f.senha, c.nome AS cidade_nome, s.nome AS secretaria_nome, 
            d.nome AS departamento_nome, cd.id AS cargo_departamento_id
        FROM 
            funcionario f
        LEFT JOIN 
            cargo_departamento cd ON f.cargo_departamento_id = cd.id
        LEFT JOIN 
            departamento d ON cd.departamento_id = d.id
        LEFT JOIN 
            secretaria s ON d.secretaria_id = s.id
        LEFT JOIN 
            empresa e ON s.empresa_id = e.id
        LEFT JOIN 
            cidade c ON e.cidade_id = c.id
        WHERE 
            f.rgf = '$rgf'
    ";
    
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $dados = $result->fetch_assoc();

        // Verifica se a senha inserida corresponde ao hash no banco de dados
        if (password_verify($senha, $dados['senha'])) {
            // Salva todos os dados do usuário na sessão
            $_SESSION['usuario_id'] = $dados['id'];
            $_SESSION['usuario_nome'] = $dados['nome'];
            $_SESSION['usuario_rgf'] = $dados['rgf'];
            $_SESSION['usuario_email'] = $dados['email'];
            $_SESSION['usuario_telefone'] = $dados['telefone'];
            $_SESSION['cnh'] = $dados['registro_cnh'];
            $_SESSION['validade_cnh'] = $dados['validade_cnh'];
            $_SESSION['categoria'] = $dados['categoria_cnh'];
            $_SESSION['status'] = $dados['status'];
            $_SESSION['cidade_nome'] = $dados['cidade_nome'];
            $_SESSION['secretaria_nome'] = $dados['secretaria_nome'];
            $_SESSION['departamento_nome'] = $dados['departamento_nome'];
            $_SESSION['cargo_departamento_id'] = $dados['cargo_departamento_id'];

            // Verificar se o usuário está em uma rota ativa
            $queryRotaAtiva = "
                SELECT r.veiculo_id, v.placa, r.id AS rota_id
                FROM rota r
                INNER JOIN veiculo v ON r.veiculo_id = v.id
                WHERE r.funcionario_id = ? 
                  AND r.km_final IS NULL 
                  AND r.data_final IS NULL 
                  AND r.hora_final IS NULL
                LIMIT 1
            ";
            $stmtRota = $conn->prepare($queryRotaAtiva);
            $stmtRota->bind_param("i", $dados['id']);
            $stmtRota->execute();
            $stmtRota->bind_result($veiculo_id, $placa, $rota_id);
            $rotaAtiva = $stmtRota->fetch();
            $stmtRota->close();

            if ($rotaAtiva) {
                // Salvar informações da rota ativa na sessão
                $_SESSION['rota_ativa'] = [
                    'rota_id' => $rota_id,
                    'veiculo_id' => $veiculo_id,
                    'placa' => $placa,
                ];
            }

            // Mensagem de sucesso e redirecionamento
            $mensagem = "Login bem-sucedido!<br>Redirecionando...";
            $tipoMensagem = "success";

            $_SESSION['mostrarModalRota'] = true;

            echo "<script>
                setTimeout(function() {
                    window.location.href = './acesso/home.php'; // Redireciona após o login
                }, 3000); // 3 segundos
              </script>";
        } else {
            // Senha incorreta
            $mensagem = "Senha incorreta. Verifique sua senha.";
            $tipoMensagem = "error";
        }
    } else {
        // RGF não encontrado
        $mensagem = "RGF não encontrado.";
        $tipoMensagem = "error";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    <title>FR</title>
</head>
<body id="home">
    <!-- Popup de Notificação -->
    <?php if ($mensagem): ?>
        <div id="notification-popup" class="notification-popup <?= $tipoMensagem ?>">
            <p><?= $mensagem ?></p>
            <div class="countdown-bar"></div>
        </div>
    <?php endif; ?>

    <div class="container-login">
        <form action="" method="POST">
            <h3>LOGIN</h3>
            <div class="login-info">
                <label for="rgf">RGF</label>
                <input type="text" name="rgf" id="rgf" placeholder="Entre com seu RGF"
                    pattern="\d+" title="Por favor, insira apenas números." inputmode="numeric" maxlength="6"
                    value="<?= isset($_POST['rgf']) ? htmlspecialchars($_POST['rgf']) : '' ?>">
            </div>
            <div class="login-info">
                <label for="senha-rgf">Senha</label>
                <div class="password-input">
                    <input type="password" name="senha-rgf" id="senha-rgf" placeholder="Entre com sua senha">
                    <button type="button" class="show-password">
                        <img src="./img/invisibility.png" alt="Mostrar senha" class="visibility-icon">
                    </button>
                </div>
            </div>
            <div class="login-info">
                <a href="./redefinirSenha.php">Esqueceu sua senha? <u>Clique aqui</u></a>
                <button type="submit" id="submit" name="submit">ENTRAR</button>
            </div>
        </form>
    </div>
    <script src="./js/index.js"></script>
</body>
</html>
