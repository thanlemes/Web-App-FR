document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById("senha-rgf");
    const togglePasswordButton = document.querySelector(".show-password");
    const visibilityIcon = document.querySelector(".visibility-icon");

    togglePasswordButton.addEventListener("click", function() {
        // Alterna o tipo entre 'password' e 'text'
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