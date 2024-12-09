<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include "verificaSessao.php";
include "criptografia.php";

// Função de criptografia
function criptografar($texto, $chave) {
    $iv = openssl_random_pseudo_bytes(16);
    $texto_criptografado = openssl_encrypt($texto, 'aes-128-cbc', $chave, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $texto_criptografado);
}

// Função de descriptografia
function descriptografar($dados_criptografados_base64, $chave) {
    $dados_criptografados = base64_decode($dados_criptografados_base64);
    if (!$dados_criptografados) {
        return "Erro ao decodificar Base64.";
    }

    $iv = substr($dados_criptografados, 0, 16);  // IV de 16 bytes
    $dados_criptografados = substr($dados_criptografados, 16);  // Dados criptografados sem o IV

    $id = openssl_decrypt($dados_criptografados, 'aes-128-cbc', $chave, OPENSSL_RAW_DATA, $iv);
    if ($id === false) {
        return "Falha na descriptografia com a chave ou formato do IV.";
    }

    return $id;
}

// Exemplo de chave de criptografia de 16 bytes
$chave = '1234567890123456';  // Chave de 16 bytes

// Incluir a biblioteca FPDF para gerar o PDF
require_once('../libs/fpdf.php'); // Altere o caminho para a sua instalação do FPDF

// Se o ID criptografado for passado via GET
if (isset($_GET['rota_id'])) {
    // Recuperar o ID criptografado da URL (em Base64)
    $rota_id_base64 = $_GET['rota_id'];

    // Descriptografar o ID
    $id_recuperado = descriptografar($rota_id_base64, $chave);

    if ($id_recuperado && is_numeric($id_recuperado)) {
        // Conectar ao banco de dados
        include "../conexao.php";

        // Consulta para pegar os dados da rota, veículo e funcionário
        $sql = "SELECT r.protocolo, r.data_inicial, r.hora_inicial, r.km_inicial, r.km_final, r.data_final, r.hora_final, 
                       r.local_partida, r.destino, v.placa, f.nome 
                FROM rota r
                JOIN veiculo v ON r.veiculo_id = v.id
                JOIN funcionario f ON r.funcionario_id = f.id
                WHERE r.id = $id_recuperado";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Pegar os dados
            $row = $result->fetch_assoc();
            $protocolo = $row['protocolo'];
            $data_inicial = date('d/m/Y H:i', strtotime($row['data_inicial'] . ' ' . $row['hora_inicial']));
            $data_final = date('d/m/Y H:i', strtotime($row['data_final'] . ' ' . $row['hora_final']));
            $km_inicial = $row['km_inicial'];
            $km_final = $row['km_final'];
            $local_partida = $row['local_partida'];
            $destino = $row['destino'];
            $placa = $row['placa'];
            $funcionario_nome = $row['nome'];

            // Criar o PDF
            $pdf = new FPDF();
            $pdf->AddPage();

            // Título do relatório
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'Ficha de Registro de ' . $funcionario_nome . ' - Protocolo: ' . $protocolo), 0, 1);

             // Adicionar informações da rota ao PDF
            $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(10);  // Quebra de linha
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Placa: ' . $placa), 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Data de Início: ' . $data_inicial), 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Data Final: ' . $data_final), 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'KM Inicial: ' . $km_inicial), 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'KM Final: ' . $km_final), 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Local de Partida: ' . $local_partida), 0, 1);
            $pdf->Ln(10);

            // Usando MultiCell para o campo Destino, permitindo quebras de linha
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Destinos: '), 0, 1);
            $pdf->MultiCell(0, 10, iconv('UTF-8', 'ISO-8859-1', $destino), 0, 1);

            $pdf->Ln(10);

            // Gerar o PDF com nome de arquivo usando o nome do funcionário e o protocolo
            $nome_arquivo = 'Ficha_de_Registro_' . iconv('UTF-8', 'ISO-8859-1', $funcionario_nome) . '_' . $protocolo . '.pdf';
            $pdf->Output('D', $nome_arquivo);

        } else {
            echo "Nenhum dado encontrado para a rota.";
        }

        // Fechar a conexão com o banco
        $conn->close();
    } else {
        echo "Falha na descriptografia do ID.";
    }
} else {
    echo "ID da rota não especificado.";
}
?>