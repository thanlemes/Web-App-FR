function toggleMenu() {
    const menu = document.getElementById('side-menu');
    const overlay = document.getElementById('overlay');
    menu.classList.toggle('visible');
    overlay.classList.toggle('visible');
}

document.addEventListener("DOMContentLoaded", () => {
    const dateInput = document.getElementById("data");
    const timeInput = document.getElementById("hora");

    const today = new Date();
    
    // Obter a data local (sem considerar o fuso horário UTC)
    const year = today.getFullYear();
    const month = (today.getMonth() + 1).toString().padStart(2, '0'); // Meses começam do 0, então somamos 1
    const day = today.getDate().toString().padStart(2, '0'); // Garante que o dia tenha dois dígitos

    const formattedDate = `${year}-${month}-${day}`; // Formato 'YYYY-MM-DD'
    
    // Obter a hora local
    const formattedTime = today.toTimeString().split(" ")[0].substring(0, 5); // Formato 'HH:MM'

    // Definir a data e hora atual nos campos de entrada
    dateInput.value = formattedDate;
    timeInput.value = formattedTime;

    // Tornar o campo de data apenas leitura (não permite edição, mas valor é enviado)
    dateInput.readOnly = true;

    // Desabilitar o campo de hora também, caso queira evitar edição (opcional)
    // timeInput.disabled = true; // Se quiser que a hora também não seja editável, use disabled
});


document.addEventListener("DOMContentLoaded", () => {
    const dadosVeiculos = document.querySelectorAll('.container-dados-veiculos, .dados-veiculos-box, .dados-veiculos, .dados-veiculos-box-detalhe, .dados-veiculos-detalhe, .input-wrapper');
    dadosVeiculos.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 300); // Atraso de 300ms entre cada elemento
    });
});

// Obtém o elemento de input
const kmInput = document.getElementById('km');

// Adiciona um ouvinte de evento para controlar a entrada
kmInput.addEventListener('input', function() {
    let value = kmInput.value;
    
    // Se o valor for maior que 6 caracteres, corta o valor
    if (value.length > 6) {
        kmInput.value = value.slice(0, 6);
    }
});

document.getElementById("formRota").addEventListener("submit", function() { //loader
    const loader = document.getElementById("loader");
    loader.classList.remove("hidden");
    loader.classList.add("show");
});


document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById("notification-popup");
    if (popup) {
        popup.classList.add("show"); // Exibe o popup
        setTimeout(() => {
            popup.classList.remove("show");
            popup.classList.add("hidden"); // Oculta o popup após 3 segundos
        }, 3000);
    }
});