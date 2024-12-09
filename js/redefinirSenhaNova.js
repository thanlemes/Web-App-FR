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

document.addEventListener("DOMContentLoaded", function() {
    const togglePasswordButtons = document.querySelectorAll(".show-password");

    togglePasswordButtons.forEach(button => {
        button.addEventListener("click", function() {
            // Seleciona o campo de senha e o ícone dentro do botão
            const passwordInput = button.previousElementSibling;
            const visibilityIcon = button.querySelector(".visibility-icon");

            // Alterna o tipo entre 'password' e 'text' para o campo correspondente
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                visibilityIcon.src = "./img/visibility.png"; // Ícone para mostrar a senha
                visibilityIcon.alt = "Ocultar senha";
            } else {
                passwordInput.type = "password";
                visibilityIcon.src = "./img/invisibility.png"; // Ícone para ocultar a senha
                visibilityIcon.alt = "Mostrar senha";
            }    
        });    
    });    
});    
