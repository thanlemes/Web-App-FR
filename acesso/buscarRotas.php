<?php
// Exibir erros para debug
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Cabeçalho para retorno em JSON
header('Content-Type: application/json');

// Incluir o arquivo com a função de criptografia
include 'criptografia.php';

// Conectar ao banco
include '../conexao.php';

// Obter parâmetros de entrada
$usuarioID = isset($_GET['usuarioID']) ? intval($_GET['usuarioID']) : 0;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Validar os parâmetros
if ($usuarioID <= 0 || $offset < 0) {
    echo json_encode(['error' => 'Parâmetros inválidos.']);
    exit;
}

try {
    // Consulta SQL para pegar um registro por vez (LIMIT 1)
    $sql = "SELECT rota.*, veiculo.placa 
            FROM rota 
            JOIN veiculo ON rota.veiculo_id = veiculo.id 
            WHERE rota.id != 0
            AND rota.funcionario_id = ? 
            ORDER BY rota.id DESC 
            LIMIT 1 OFFSET ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }

    $stmt->bind_param("ii", $usuarioID, $offset); // Certifique-se de que ambos os parâmetros estão definidos como inteiros
    $stmt->execute();
    $result = $stmt->get_result();

    $rotas = [];
    $chave = '1234567890123456'; // Chave de criptografia

    while ($row = $result->fetch_assoc()) {
        // Adicionar campo criptografado
        $row['id_criptografado'] = criptografar_id($row['id'], $chave);
        $rotas[] = $row;
    }

    // Verificar se existem mais registros após o offset atual
    $sqlCount = "SELECT COUNT(*) FROM rota WHERE funcionario_id = ?";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param("i", $usuarioID);
    $stmtCount->execute();
    $stmtCount->bind_result($totalRegistros);
    $stmtCount->fetch();

    // Verifica se há mais registros para carregar
    $temMaisRegistros = ($totalRegistros > ($offset + 1));

    // Retornar resposta JSON
    echo json_encode([
        'rotas' => $rotas,
        'temMaisRegistros' => $temMaisRegistros,
    ]);
} catch (Exception $e) {
    // Retornar erro como JSON
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}
?>
