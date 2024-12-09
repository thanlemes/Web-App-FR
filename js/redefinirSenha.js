document.getElementById("form-redefinir").addEventListener("submit", function() { //loader
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