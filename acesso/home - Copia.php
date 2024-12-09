<?php
include "verificaSessao.php";
include "criptografia.php";
include "../conexao.php";

// Habilitar log de erros para depuração
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// // Capturar erros e convertê-los para JSON
// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
//     http_response_code(500);
//     echo json_encode([
//         'error' => "Erro: [$errno] $errstr - $errfile:$errline",
//     ]);
//     exit;
// });












// Consulta para pegar o último registro de rota do usuário
$query = "SELECT * FROM rota WHERE funcionario_id = ? ORDER BY CONCAT(data_inicial, ' ', hora_inicial) DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$result = $stmt->get_result();
$rota = $result->fetch_assoc();

if ($rota) {
    $veiculo_id = $rota['veiculo_id'];

    // Consulta para buscar os dados do veículo
    $queryVeiculo = "SELECT placa, tombamento, disponibilidade FROM veiculo WHERE id = ?";
    $stmtVeiculo = $conn->prepare($queryVeiculo);
    $stmtVeiculo->bind_param("i", $veiculo_id);
    $stmtVeiculo->execute();
    $resultVeiculo = $stmtVeiculo->get_result();
    $veiculoRota = $resultVeiculo->fetch_assoc();

    if ($veiculoRota && $veiculoRota['disponibilidade'] == 0) { // Veículo em uso
        // Verifica se a rota não foi finalizada
        if (empty($rota['km_final']) || empty($rota['data_final']) || empty($rota['hora_final'])) {
            $rotaAtiva = true;
            $veiculoPlaca = $veiculoRota['placa'];
            $veiculoTombamento = $veiculoRota['tombamento'];
            $exibirModalRota = true; // Exibir o modal de rota ativa

            $rota_id = $rota['id'];

            ?>

            <div id="rota" data-rota-id="<?= $rota_id ?>"></div>
            <div id="usuario" data-usuario-id="<?= $usuarioID ?>"></div>
            

            <?php

            echo "
            <script>
                (function sendRotaId() {
                    const rotaId = {$rota['id']};
                    const data = new URLSearchParams();
                    data.append('rota_id', rotaId);

                    fetch('buscaDestinos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: data.toString()
                    })
                    .then(response => response.json()) // Caso o servidor responda JSON
                    .then(result => {
                        console.log('Rota enviada com sucesso:', result);
                    })
                    .catch(error => {
                        console.error('Erro ao enviar rota_id:', error);
                    });
                })();
            </script>
            ";
        }
    }
}

if (isset($_POST['rota_id'])) {
    $rota_id = $_POST['rota_id'];

    ?>

    <div id="rota" data-rota-id="<?= $rota_id ?>"></div>
    <div id="usuario" data-usuario-id="<?= $usuarioID ?>"></div>
    

    <?php

    // Aqui você pode fazer a lógica para continuar o processo, como o registro de abastecimento, etc.
    // echo "Rota ID: " . $rota_id;
    // echo "<br>";
    // echo "Usuario ID: " . $usuarioID;
}

// Consulta para buscar veículos disponíveis no mesmo departamento do usuário
$query = "SELECT id, placa, tombamento, disponibilidade, departamento_id, rota_funcionario_id FROM veiculo WHERE departamento_id = '$cargoDepartamentoID'";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/acesso/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Página Inicial</title>
</head>

<body id="pagina-inicial">
    <div class="nav-info">
        <div class="nav-icone">
            <img src="../img/icone_condutor.png" alt="">
        </div>
        <div class="nav-dados">
            <h2><?= htmlspecialchars($nome) ?></h2>
            <p>Secretaria: <span><?= htmlspecialchars($secretaria) ?></span></p>
            <p>Departamento: <span><?= htmlspecialchars($departamento) ?></span></p>
        </div>
        <div class="nav-menu">
            <div id="menu-icon" onclick="toggleMenu()">☰</div>
            <div id="overlay" class="hidden" onclick="toggleMenu()"></div>
            <div id="side-menu" class="hidden">
                <ul>
                    <li onclick="toggleMenu()"><img src="../img/icone_X_cinza.png" alt=""> Fechar</li>
                    <a href="./meusDados.php">
                        <li><img src="../img/icone_dados_cinza.png" alt="">Meus Dados</li>
                    </a>
                    <a href="./suporte.php">
                        <li><img src="../img/icone_suporte_cinza.png" alt="">Suporte</li>
                    </a>
                    <a href="#" onclick="confirmLogout(event)">
                        <li><img src="../img/icone_sair_cinza.png" alt="Sair da Conta">Sair da Conta</li>
                    </a>
                </ul>

                <!-- Modal de Logout -->
                <div id="modalLogout" class="modal" style="display: none;">
                    <div class="modal-content">
                        <p>Você realmente deseja <span class="txt-sair">SAIR</span> da sua conta?</p>
                        <div class="buttons">
                            <button onclick="setExit(true); checkActiveRoute()">Sair</button>
                            <button onclick="closeModal('modalLogout')">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="veiculos-lista">

        <!-- Modal de Rota Ativa -->
        <?php if ($exibirModalRota && $rotaAtiva): ?>
            <!-- <div id="modal2-overlay" class="modal2-overlay"></div> -->
            

        <?php endif; ?>
        
                
            <script>

                // Exibe o modal de logout
                function confirmLogout(event) {
                    event.preventDefault(); // Evita o link de ser seguido
                    document.getElementById("modalLogout").style.display = "block"; // Mostra o modal
                }

                // Fecha o modal especificado
                function closeModal(modalId) {
                    document.getElementById(modalId).style.display = "none"; // Esconde o modal
                }

                function closeModal(modalId2) {
                    document.getElementById(modalId2).style.display = "none"; // Esconde o modal
                }

                let desejaSair = false;

                // Função que altera a variável desejaSair para true
                function setExit(value) {
                    desejaSair = value;
                    console.log("Usuário deseja sair:", desejaSair);
                }

                function checkActiveRoute() {
                fetch('./setDesejaSair.php', {
                    method: 'POST',
                    body: JSON.stringify({ desejaSair: 'true' }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetch('./verificarRotaAtiva.php')
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Erro HTTP: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    const modalRota = document.querySelector("#modalRotaAtiva");
                                    const modalLogout = document.querySelector("#modalLogout");

                                    if (data.rotaAtiva) {
                                        if (modalRota) {
                                            modalRota.style.display = "block";
                                            if (modalLogout) {
                                                modalLogout.style.display = "none";
                                            }
                                            console.log("Modal de rota ativa exibido");
                                        } else {
                                            console.error('Modal de rota ativa não encontrado no DOM. Tentando recarregar...');
                                            setTimeout(() => {
                                                const retryModal = document.querySelector("#modalRotaAtiva");
                                                if (retryModal) {
                                                    retryModal.style.display = "block";
                                                    if (modalLogout) {
                                                        modalLogout.style.display = "none";
                                                    }
                                                    console.log("Modal de rota ativa exibido após atraso.");
                                                } else {
                                                    alert('Modal de rota ativa ainda não encontrado.');
                                                }
                                            }, 500); // Tenta buscar o modal após 500ms
                                        }
                                    } else {
                                        window.location.href = 'logout.php';
                                    }
                                })
                                .catch(error => {
                                    console.error('Erro ao verificar rota ativa:', error);
                                    alert('Houve um problema ao verificar sua rota ativa. Tente novamente.');
                                });
                        } else {
                            console.error("Falha ao definir o estado de Sair no servidor.");
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao enviar o estado de "Sair":', error);
                    });
            }

              
                function rotaAtivaClose() {
                    document.getElementById("modalRotaAtiva").style.display = "none";
                    
                }

            </script>


        <?php
            if ($result->num_rows == 0) {
                echo "Nenhum veículo encontrado para o departamento.";
            }

            $rotaEmAndamento = false; // Variável para controlar se o usuário já está em rota

            while($veiculo = $result->fetch_assoc()):
                // Verifica se o veículo está em rota e se foi iniciado pelo usuário atual
                if ($veiculo['disponibilidade'] == 0 && $veiculo['rota_funcionario_id'] == $usuarioID) {
                     // Define que o usuário já está em rota
                    break; // Interrompe o loop, exibindo apenas o veículo em rota
                }
            endwhile;

            if (!$rotaEmAndamento) {
                // Lista os veículos disponíveis normalmente, pois o usuário não está em rota
                $result->data_seek(0); // Retorna ao início do conjunto de resultados

                ?>

                <div class="veiculos-titulo">
                    <div class="veiculos-titulo-before"></div>
                    <h2>Selecione o Veículo</h2>
                </div>

                <?php
                while($veiculo = $result->fetch_assoc()):
                    if ($veiculo['disponibilidade'] == 1) {
                        // Configurações de veículo disponível
                        $classeDisponibilidade = "disp-veiculos-selecionar";
                        $imagemVeiculo = "../img/icone_veiculo_selecionar.png";
                        $textoDisponibilidade = "Disponível";
                        $iconeDisponibilidade = "../img/icone_seta_preta.png";
                        $disabled = "";
                        $mostrarRota = false; // Não exibe a parte de rota
                        $statusVeiculo = "disp";
                        $corTexto = "color: #2A2F34;";
                        $corBorda = "disp";
                        $bloco = "disp";
                        $dados = "disp";
                    } elseif ($veiculo['disponibilidade'] == 0) {
                        // Configurações de veículo Indisponível
                        $classeDisponibilidade = "indisp-veiculos-selecionar";
                        $imagemVeiculo = "../img/icone_veiculo_cinza.png";
                        $textoDisponibilidade = "Indisponível";
                        $iconeDisponibilidade = "../img/icone_cadeado_cinza.png";
                        $disabled = "disabled";
                        $mostrarRota = false; // Não exibe a parte de rota
                        $statusVeiculo = "indisp";
                        $corTexto = "color: #FFFFFF;";
                        $corBorda = "indisp";
                        $bloco = "indisp";
                        $dados = "indisp";
                    }
            ?>

    
    <?php endwhile; 
        }
    ?>
    <?php if ($mostrarRota): 

    $mensagem = $_POST['mensagem'] ?? '';
    $tipoMensagem = $_POST['tipoMensagem'] ?? '';

    ?>

    <!-- Se o veículo está em rota, exibe o formulário de finalizar rota -->
    
        

    <?php endif; ?>
    
    <?php if (!$mostrarRota): ?>
    <hr style="margin-top: 50px; border: 1px solid #6C757D;">

    <div class="veiculos-titulo">
        <div class="veiculos-historico-before"></div>
        <h2>Histórico de Uso</h2>
    </div>

<div class="veiculos-lista" style="margin-bottom: 70px;">
    <div id="historicoVeiculos">
        <?php
        include "../conexao.php";

        $sql = "SELECT rota.*, veiculo.placa 
        FROM rota 
        JOIN veiculo ON rota.veiculo_id = veiculo.id 
        WHERE rota.funcionario_id = ? 
        ORDER BY rota.id DESC 
        LIMIT 1 OFFSET ?";

        // Obter parâmetros

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

        // Validar os parâmetros
        if ($usuarioID <= 0 || $offset < 0) {
            echo json_encode(['error' => 'Parâmetros inválidos.']);
            
        }

        // Depuração
        // echo "Offset: " . $offset . ", UsuarioID: " . $usuarioID;
        // exit;

        $stmt = $conn->prepare($sql);

        // Preparar a consulta
        if (!$stmt) {
            echo json_encode(['error' => "Erro ao preparar consulta SQL: " . $conn->error]);
            exit;
        }

        // Associar os parâmetros corretamente (ii para dois inteiros)
        if (!$stmt->bind_param("ii", $usuarioID, $offset)) {
            echo json_encode(['error' => "Erro ao associar parâmetros: " . $stmt->error]);
            exit;
        }

        // Executar a consulta
        if (!$stmt->execute()) {
            echo json_encode(['error' => "Erro ao executar consulta: " . $stmt->error]);
            exit;
        }

        $result = $stmt->get_result();


        if ($stmt) {

            if ($result->num_rows > 0) {
                while ($rota = $result->fetch_assoc()) {
                    $chave = '1234567890123456';
                    $id = $rota['id'];

                    // Criptografar o ID da rota
                    $rota_id_criptografado = criptografar_id($id, $chave);
                    ?>
                
                
                    

                    <?php
                }
            } else {
                echo "<div class='veiculos-container-historico'>
                        <div class='historico-container-before'></div>
                        <div class='historico-veiculos-selecionar'>
                            <div class='historico-veiculos-bloco'>
                                <p style='color: #6C757D; text-align: center; padding: 15px 10px; font-weight: bold;'>
                                    Nenhuma rota encontrada para este usuário.
                                </p>
                            </div>
                        </div>
                      </div>";
            }
            
            $stmt->close();
        } else {
            echo "<p>Erro na consulta: " . $conn->error . "</p>";
        }
        ?>
    </div>
    <?php
              
    ?>
    <!-- Botão Ver Mais -->
    
        
    <?php endif; ?>





<script>
    document.addEventListener('DOMContentLoaded', function() {
    const usuario_ID = <?= isset($_SESSION['usuario_id']) ? json_encode($_SESSION['usuario_id']) : 'null'; ?>;

    if (usuario_ID === null) {
        console.error("Erro: ID do usuário não encontrado na sessão.");
        return;
    }

    // console.log('ID do usuário:', usuario_ID); // Debug para verificar o ID no console

    let offset = 1; // Começando com o offset 1
    const historicoContainer = document.getElementById('historicoVeiculos');
    const verMaisButton = document.getElementById('verMais');
    const verMaisMessageContainer = document.getElementById('verMaisMessage'); // Novo container para a mensagem

    function carregarRotas() {
        fetch(`../acesso/buscarRotas.php?usuarioID=${usuario_ID}&offset=${offset}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta da requisição: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Erro do servidor:', data.error);
                    return;
                }

                if (Array.isArray(data.rotas) && data.rotas.length > 0) {
                    // Criar o contêiner principal para os veículos
                    const veiculosContainer = document.createElement('div');
                    veiculosContainer.classList.add('veiculos-container-historico');
                    
                    // Criar o contêiner "before" dentro do veiculosContainer
                    const antesContainer = document.createElement('div');
                    antesContainer.classList.add('historico-container-before');
                    veiculosContainer.appendChild(antesContainer);
                    
                    // Criar o contêiner de seleção de veículos
                    const historicoSelecao = document.createElement('div');
                    historicoSelecao.classList.add('historico-veiculos-selecionar');
                    veiculosContainer.appendChild(historicoSelecao);

                    function formatarData(data) {
                        const dataObj = new Date(data);
                        const dia = String(dataObj.getDate()).padStart(2, '0');
                        const mes = String(dataObj.getMonth() + 1).padStart(2, '0'); // Meses começam em 0
                        const ano = dataObj.getFullYear();

                        return `${dia}/${mes}/${ano}`;
                    }

                    // Adicionar os registros dentro de historico-veiculos-selecionar
                    data.rotas.forEach(rota => {
                        const blocoDiv = document.createElement('div');
                        blocoDiv.classList.add('historico-veiculos-bloco');

                        blocoDiv.innerHTML = `
                            <div class="historico-veiculos-dados">
                                <img src="../img/icone_historico_veiculo.png" alt="Histórico">
                            </div>
                            <div class="historico-veiculos-dados historico-borda-direita">
                                <h3>Placa</h3>
                                <p>${rota.placa}</p>
                            </div>
                            <div class="historico-veiculos-dados historico-borda-direita">
                                <h3>Data</h3>
                                <p>${formatarData(rota.data_inicial)}</p>
                            </div>
                            <div class="historico-veiculos-dados">
                                <form action="gerarFR.php" method="GET">
                                    <button type="submit">
                                        <div class="historico-fr">
                                            <img src="../img/icone_pdf_white.png" alt="Gerar FR">
                                            <p>Gerar FR</p>
                                            <input type="hidden" name="rota_id" value="${rota.id_criptografado}">
                                        </div>
                                    </button>
                                </form>
                            </div>
                        `;

                        historicoSelecao.appendChild(blocoDiv);
                    });

                    // Adicionar o veiculosContainer ao histórico
                    historicoContainer.appendChild(veiculosContainer);

                    offset += 1; // Incrementar o offset para o próximo clique

                    if (!data.temMaisRegistros) {
                        // Adicionar a mensagem fora do botão "Ver Mais"
                        verMaisMessageContainer.innerHTML = "<p style='color: #6C757D; background-color: #2A2F34; text-align: center; padding: 15px 10px; font-weight: bold;'>Todos os registros foram exibidos.</p>";
                        verMaisButton.style.display = 'none'; // Ocultar o botão após exibir todos os registros
                    }
                } else {
                    verMaisButton.style.display = 'none'; // Ocultar o botão após exibir todos os registros
                    verMaisMessageContainer.innerHTML = "<p style='color: #6C757D; background-color: #2A2F34; text-align: center; padding: 15px 10px; font-weight: bold;'>Todos os registros foram exibidos.</p>";
                }
            })
            .catch(error => console.error('Erro ao carregar rotas:', error));
    }

    verMaisButton.addEventListener('click', carregarRotas);
});
</script>
    <script src="../js/home.js"></script>
    </body>
</html>