<?php

include '../conexao.php';
include 'verificaSessao.php';


header('Content-Type: application/json'); 

if (isset($_POST['rota_id'])) {

$veiculo_id = isset($_POST['veiculo_id']) ? $_POST['veiculo_id'] : '';
$placa = isset($_POST['placa']) ? $_POST['placa'] : '';
$tombamento = isset($_POST['tombamento']) ? $_POST['tombamento'] : '';
$marca = isset($_POST['marca']) ? $_POST['marca'] : '';
$modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
$especie = isset($_POST['especie']) ? $_POST['especie'] : '';
$km_inicial = isset($_POST['km_inicial']) ? $_POST['km_inicial'] : '';


$data_inicial = isset($_POST['data']) ? $_POST['data'] : null;
$hora_inicial = isset($_POST['hora']) ? $_POST['hora'] : null;
$destino = isset($_POST['destino']) ? $_POST['destino'] : null;

$selectRotaQuery = "
               SELECT id 
               FROM rota 
               WHERE veiculo_id = '$veiculo_id' AND funcionario_id = '$usuarioID' 
               ORDER BY id DESC LIMIT 1
           ";
   
           $result1 = $conn->query($selectRotaQuery);
   
           if ($result1 && $result1->num_rows > 0) {
        
               $rota = $result1->fetch_assoc();
               $rota_id = $rota['id'];
   
          
               echo '<form id="formRota" method="POST" action="home.php">
                       <input type="hidden" name="rota_id" value="' . $rota_id . '">
                       <script>document.getElementById("formRota").submit();</script>
                   </form>';
           }


           try {
            if (!isset($_POST['rota_id'])) {
                throw new Exception("Parâmetros ausentes: rota_id");
            }
        
            $rota_id = intval($_POST['rota_id']);
        
            error_log("rota_id: $rota_id");
        
            $stmt = $conn->prepare("SELECT litros, km_atual, comprovante_abastecimento FROM abastecimento WHERE rota_id = ?");
            $stmt->bind_param("i", $rota_id);
            $stmt->execute();
            $result2 = $stmt->get_result();
        
            if ($result2 && $result2->num_rows > 0) {
          
                $abastecimentos = [];
                while ($row = $result2->fetch_assoc()) {
                    $abastecimentos[] = $row;
                }
            
                
                echo json_encode($abastecimentos);
            } else {
                
                echo json_encode(['error' => 'Nenhum abastecimento encontrado.']);
            }
        
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        
}


if (!empty($output)) {
    echo json_encode(['error' => $output]); 
    exit;
}
?>
