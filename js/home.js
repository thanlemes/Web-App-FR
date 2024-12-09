// MENU LATERAL
function toggleMenu() {
    const menu = document.getElementById('side-menu');
    const overlay = document.getElementById('overlay');
    menu.classList.toggle('visible');
    overlay.classList.toggle('visible');
}

function logout() {
    alert('Saindo da conta...');
    toggleMenu();
}

// MODAL DE ITINERÁRIO
function abrirModal() {
    document.getElementById('modal').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('destino').value = ''; // Limpa o campo
    document.getElementById('endereco').value = ''; // Limpar o textarea
}

function adicionarDestino() {
    const endereco = document.getElementById('endereco').value;
    const rotaId1 = document.getElementById('rota').getAttribute('data-rota-id');

    // Verifique os dados que estão sendo enviados
    console.log("Destino recebido:", endereco);
    console.log("Rota ID enviado:", rotaId1);

    if (!endereco.trim()) {
        alert('Por favor, insira um destino!');
        return;
    }

    fetch('../acesso/adicionarItinerario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'rota_id': rotaId1,
            'endereco': endereco,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mensagem de sucesso
            alert('Destino adicionado com sucesso!');

            // Limpar e fechar o modal
            fecharModal();

            // Atualizar a lista de destinos na tela
            atualizarDestinos(data.destino);
            window.location.reload(); // Recarrega a página
        } else {
            alert('Erro ao adicionar destino: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro ao enviar dados:', error);
        alert('Ocorreu um erro ao adicionar o destino.');
    });
}

// Modificação para pegar corretamente o rotaId1 do DOM
document.addEventListener('DOMContentLoaded', function () {
    const rotaId1Element = document.getElementById('rota');
    if (rotaId1Element) {
        const rotaId1 = rotaId1Element.getAttribute('data-rota-id');
        if (rotaId1) {
            carregarDestinos(rotaId1);
        }
    }
});

function atualizarDestinos(destinos) {
    const destinosList = document.getElementById('destinos-list');
    destinosList.innerHTML = ''; // Limpa a lista de destinos antigos
    
    // Verifica se destinos não está vazio ou nulo
    if (!destinos || destinos.trim() === '') {
        alert('Nenhum destino encontrado.');
        return;
    }
    
    const destinosArray = destinos.split('; '); // Divide os destinos

    destinosArray.forEach((destino, index) => {
        const destinoItem = criarDestinoElemento(destino, index);
        destinosList.appendChild(destinoItem);
    });
}

function criarDestinoElemento(destino, id) {
    const destinoItem = document.createElement('div');
    destinoItem.className = 'destino-item';

    const novoDestino = document.createElement('input');
    novoDestino.type = 'text';
    novoDestino.value = destino;
    novoDestino.className = 'input-invisivel';
    novoDestino.readOnly = true;

    const lixeira = document.createElement('img');
    lixeira.src = '../img/icone_lixeira.png';
    lixeira.alt = 'Ícone de lixeira';
    lixeira.onclick = function () {
        removerItinerario(id, destinoItem); // Passa o ID e o elemento
    };

    destinoItem.appendChild(novoDestino);
    destinoItem.appendChild(lixeira);
    return destinoItem;
}

function removerItinerario(id, destinoItem) {
    const rotaId1 = document.getElementById('rota').getAttribute('data-rota-id');
    
    if (confirm("Tem certeza que deseja remover este destino?")) {
        fetch('../acesso/removerItinerario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'rota_id': rotaId1,
                'destino': destinoItem.querySelector('input').value // Destino a ser removido
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Se a remoção for bem-sucedida, removemos o item da lista
                destinoItem.remove();
                alert('Destino removido com sucesso!');
            } else {
                alert('Erro ao remover destino: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao tentar remover o destino.');
        });
    }
}


function carregarDestinos(rotaId1) {
    fetch('../acesso/buscaDestinos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ 'rota_id': rotaId1 })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Resposta de buscaDestinos.php:", data);
        if (data.success) {
            atualizarDestinos(data.destino);
        } else {
            alert('Erro ao carregar destinos: ' + data.message);
        }
    })
    .catch(error => console.error('Erro:', error));
}



// Funções do Modal de Abastecimento
function abrirModalAbastecimento() {
    document.getElementById('modal-abastecimento').style.display = 'flex'; // Exibe o modal
}

// Função para fechar o modal de abastecimento
function fecharModalAbastecimento() {
    document.getElementById('modal-abastecimento').style.display = 'none'; // Esconde o modal
}

function limparCamposModal() {
    document.getElementById('litros').value = '';
    document.getElementById('km').value = '';
    document.getElementById('comprovante').value = ''; // Limpa o campo de arquivo
    document.getElementById('comprovante-label').innerHTML = 'Selecionar Comprovante';
}

function handleFileSelect() {
    const fileInput = document.getElementById('comprovante');
    const fileLabel = document.getElementById('comprovante-label');

    fileLabel.innerHTML = fileInput.files && fileInput.files[0]
        ? `Comprovante: ${fileInput.files[0].name}`
        : 'Selecionar Comprovante';
}

// Adicionar Abastecimento
function adicionarAbastecimento() {
    const litros = document.getElementById('litros').value;
    const km = document.getElementById('km').value;
    const comprovante = document.getElementById('comprovante').files[0];
    const rotaId3 = document.getElementById('rota').getAttribute('data-rota-id'); // Obter ID da rota

    // Verifica se todos os campos estão preenchidos
    if (litros.trim() && km.trim() && comprovante) {
        const formData = new FormData();
        formData.append('litros', litros);
        formData.append('km', km);
        formData.append('comprovante', comprovante);
        formData.append('rota_id', rotaId3); // Adiciona o ID da rota ao FormData

        // Envia os dados para o servidor
        fetch('../acesso/adicionarAbastecimento.php', {
            method: 'POST',
            body: formData // Envia os dados como FormData
        })
        .then(response => response.json())
        .then(data => {
            // Verifica se a resposta contém erro
            if (data.success) {
                alert('Abastecimento adicionado com sucesso!');
                fecharModalAbastecimento(); // Fecha o modal
                carregarAbastecimentos(); // Atualiza a lista de abastecimentos
                window.location.reload(); // Recarrega a página
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao adicionar abastecimento: ' + error);
        });
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}


// Carregar Abastecimentos
function carregarAbastecimentos(rotaId2) {
    // console.log('Rota ID:', rotaId2);  // Verifique se o valor de rotaId2 está correto
    fetch('../acesso/buscarAbastecimento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ 'rota_id': rotaId2 })  // Passando a rota_id para o PHP
    })
    .then(response => response.json())
    .then(data => {
        // console.log('Resposta do servidor:', data);
        
        if (data.error) {
            // console.log(data.error); // Exibe o erro no console
            // document.getElementById('lista-abastecimentos').innerHTML = 'Nenhum abastecimento encontrado.';
        } else {
            exibirAbastecimentos(data);  // Exibe os dados de abastecimento se encontrados
        }
    })
    .catch(error => {
        console.error('Erro ao carregar abastecimentos:', error);
    });
}

// Função para exibir os abastecimentos na tela
function exibirAbastecimentos(abastecimentos) {
    const listaAbastecimentos = document.getElementById('lista-abastecimentos');
    listaAbastecimentos.innerHTML = ''; // Limpa qualquer conteúdo anterior

    abastecimentos.forEach(abastecimento => {
        const abastecimentoItem = document.createElement('div');
        abastecimentoItem.classList.add('abastecimento-item');
        
        // Cria os elementos para exibir litros, KM e comprovante
        const infoLitros = document.createElement('p');
        infoLitros.innerHTML = `Litros: <br><span class="azul" style="font-size: 15px;">${abastecimento.litros}</span>`;

        const infoKm = document.createElement('p');
        infoKm.innerHTML = `KM: <br><span class="azul" style="font-size: 15px;">${abastecimento.km_atual}</span>`;

        const infoComprovante = document.createElement('p');
        infoComprovante.innerHTML = `Comprovante: <a class="azul" style="text-decoration: none;" href="${abastecimento.comprovante_abastecimento}" target="_blank"><br>Ver anexo</a>`;

        
        // Adiciona os itens à lista de abastecimentos
        abastecimentoItem.appendChild(infoLitros);
        abastecimentoItem.appendChild(infoKm);
        abastecimentoItem.appendChild(infoComprovante);
        
        listaAbastecimentos.appendChild(abastecimentoItem);
    });
}

function mostrarMensagemErro(mensagem) {
    let errorMessageDiv = document.createElement('div');
    errorMessageDiv.classList.add('error-message');
    errorMessageDiv.innerText = mensagem;
    document.body.appendChild(errorMessageDiv); // Exibe a mensagem de erro na tela
}

// Chamando a função para carregar os Destinos e Abastecimentos

carregarAbastecimentos();

// Inicialização
document.addEventListener('DOMContentLoaded', function () {
    const rotaId2Element = document.getElementById('rota');

    if (rotaId2Element) {
        const rotaId2 = rotaId2Element.getAttribute('data-rota-id');
        // console.log("Rota ID capturado:", rotaId2);
        carregarAbastecimentos(rotaId2);
    } else {
        console.error("Elemento #rota não encontrado no DOM.");
    }
});

// LIMITADORES DE INPUT
document.addEventListener("DOMContentLoaded", () => {
    const litrosInput = document.getElementById('litros');
    const kmInput = document.getElementById('km');

    function limitInput(input, maxLength) {
        input.addEventListener('input', function () {
            let value = input.value;
            if (input.step && value.includes('.')) {
                const parts = value.split('.');
                if (parts[0].length > maxLength) {
                    input.value = parts[0].slice(0, maxLength) + '.' + parts[1];
                } else if (value.length > maxLength + 2) {
                    input.value = value.slice(0, maxLength + 2);
                }
            } else {
                if (value.length > maxLength) {
                    input.value = value.slice(0, maxLength);
                }
            }
        });
    }

    limitInput(litrosInput, 3); // Limita a 3 dígitos para Litros
    limitInput(kmInput, 6); // Limita a 6 dígitos para KM
});
