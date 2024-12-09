<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ou o caminho para o autoload do Composer

include "conexao.php";

// Variáveis de feedback
$mensagem = "";
$tipoMensagem = "";

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rgf = $_POST['rdf-rgf'];
    $email = $_POST['email-rgf'];

    // Consulta para verificar se o RGF e o email correspondem
    $query = "SELECT * FROM funcionario WHERE rgf = '$rgf' AND email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Gerar um token único para o link de redefinição
        $token = bin2hex(random_bytes(50)); // Gera um token de 50 bytes

        // Armazenar o token no banco de dados, junto com o RGF (para validar mais tarde)
        $updateQuery = "UPDATE funcionario SET reset_token = '$token' WHERE rgf = '$rgf'";
        $conn->query($updateQuery);

        // Criar o link de redefinição
        $link = "https://www.codejam.com.br/fr/redefinirSenhaNova.php?token=" . $token;
        // $link = "localhost/fr/redefinirSenhaNova.php?token=" . $token;

        // Configurar o PHPMailer para enviar e-mail via Gmail
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8'; // Define a codificação do e-mail para UTF-8
            $mail->AddCustomHeader('Content-Type', 'text/html; charset=UTF-8');
            $mail->Host = 'smtp.gmail.com'; // Servidor SMTP do Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'thanrocha99@gmail.com'; // Seu e-mail do Gmail
            $mail->Password = 'hkeq nolw ujio iosa'; // Sua senha ou senha de aplicativo
            // $mail->Password = 'vjed kbxu ojss gpgf'; // chave projetoti.ads@gmail.com
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ],
            ];

            // Destinatário e remetente
            $mail->setFrom('thanrocha99@gmail.com', 'Viatur');
            $mail->addAddress($email); // E-mail do usuário

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Redefinir sua senha';
            // Saudar o usuário com o nome
            $nomeUsuario = $user['nome'];  // Supondo que o campo 'nome' existe no banco
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #49B9ED;'>Olá, $nomeUsuario</h2>
                <p>Você solicitou a redefinição de sua senha. Clique no botão abaixo para prosseguir:</p>
                <a href='$link' style='background-color: #49B9ED; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>Redefinir Senha</a>
                <p style='margin-top: 20px; color: #777;'>Se você não solicitou essa alteração, ignore este e-mail.</p>
                <p>Atenciosamente,<br>Equipe Viatur</p>
            </div>";

            // Enviar o e-mail
            $mail->send();

            $mensagem = "Email enviado com sucesso!<br>Verifique sua caixa de entrada.";
            $tipoMensagem = "success";
            echo "<script>
                setTimeout(function() {
                    window.location.href = './index.php'; // Redirecionamento
                }, 3000); // 3 segundos
              </script>";
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar o email. Erro: " . $mail->ErrorInfo;
            $tipoMensagem = "error";
        }
    } else {
        // Caso não encontre RGF ou email
        $mensagem = "Dados não encontrados.<br>Verifique o RGF e o email.";
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
    <link rel="stylesheet" href="./css/redefinirSenha.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Redefinir Senha</title>
</head>

<body id="redefinir-senha">
    <div class="container-redefinir">
        
        <!-- HTML para o loader -->
        <div id="loader" class="loader hidden">
            <div class="spinner"></div>
            <p>Aguarde...</p>
        </div>
        
        <h3>REDEFINIR SENHA</h3>
        <!-- Exibição da mensagem de feedback com popup -->
        <?php if ($mensagem): ?>
            <div id="notification-popup" class="notification-popup <?= $tipoMensagem ?>">
                <p><?= $mensagem ?></p>
                <div class="countdown-bar"></div>
            </div>
        <?php endif; ?>
            
            <form id="form-redefinir" action="" method="POST">
                <div class="redefinir-info">
                    <label for="rdf-rgf">RGF</label>
                <input type="text" name="rdf-rgf" id="rdf-rgf" placeholder="Insira seu RGF" pattern="\d+" title="Por favor, insira apenas números." inputmode="numeric" maxlength="6"
                    value="<?= isset($_POST['rdf-rgf']) ? htmlspecialchars($_POST['rdf-rgf']) : '' ?>">
            </div>
            <div class="redefinir-info">
                <label for="email-rgf">E-mail</label>
                <input type="email" name="email-rgf" id="email-rgf" placeholder="Insira seu e-mail" required>
            </div>
            <div class="redefinir-info">
                <button type="submit" id="submit">REDEFINIR SENHA</button>
                <a href="./index.php">ACESSAR A CONTA</a>
            </div>
        </form>
    </div>

    <script src="./js/redefinirSenha.js"></script>
</body>

</html>