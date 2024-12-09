<?php
include "conexao.php";

// Variáveis de feedback
$mensagem = "";
$tipoMensagem = "";

// Verifica o token enviado no link
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se o token é válido
    $query = "SELECT * FROM funcionario WHERE reset_token = '$token'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $nomeUsuario = $user['nome']; // Obtém o nome do usuário

        // Processar a redefinição de senha
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $novaSenha = $_POST['nova-senha'];
            $confirmarSenha = $_POST['confirmar-senha'];

            if ($novaSenha == $confirmarSenha) {
                // Atualiza a senha no banco de dados
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE funcionario SET senha = '$novaSenhaHash', reset_token = NULL WHERE reset_token = '$token'";
                $conn->query($updateQuery);

                $mensagem = "Senha redefinida com sucesso!<br>Redirecionando...";
                $tipoMensagem = "success";

                // Redireciona para index.php após 3 segundos
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 3000); // 3000ms para 3 segundos
                    </script>";
            } else {
                $mensagem = "As senhas não coincidem.<br>Tente novamente.";
                $tipoMensagem = "error";
            }
        }
    } else {
        $mensagem = "Token inválido ou expirado.";
        $tipoMensagem = "error";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000); // 3000ms para 3 segundos
            </script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/redefinirSenhaNova.css">
    <title>Redefinir Senha</title>
</head>

<body id="redefinir-senha">
    <div class="container-redefinir">

        <!-- Exibição da mensagem de feedback -->
        <?php if ($mensagem): ?>
            <div id="notification-popup" class="notification-popup <?= $tipoMensagem ?>">
                <p><?= $mensagem ?></p>
                <div class="countdown-bar"></div>
            </div>
        <?php endif; ?>

        
        <form action="" method="POST">
            <!-- Saudação personalizada -->
            <?php if (isset($nomeUsuario)): ?>
                <h3>Olá, <?= htmlspecialchars($nomeUsuario) ?>!<br>Crie sua nova senha.</h3>
            <?php endif; ?>
            <div class="redefinir-info">
                <label for="nova-senha">Nova Senha</label>
                <div class="password-input">
                    <input type="password" name="nova-senha" id="nova-senha" required>
                    <button type="button" class="show-password">
                        <img src="./img/invisibility.png" alt="Mostrar senha" class="visibility-icon">
                    </button>
                </div>
            </div>
            <div class="redefinir-info">
                <label for="confirmar-senha">Confirmar Senha</label>
                <div class="password-input">
                    <input type="password" name="confirmar-senha" id="confirmar-senha" required>
                    <button type="button" class="show-password">
                        <img src="./img/invisibility.png" alt="Mostrar senha" class="visibility-icon">
                    </button>
                </div>
            </div>
            <div class="redefinir-info">
                <button type="submit" id="submit" name="submit">Redefinir Senha</button>
            </div>
        </form>
    </div>
    <script src="./js/redefinirSenhaNova.js"></script>
</body>

</html>