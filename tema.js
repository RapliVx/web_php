// File: tema.js
(function() {
    const darkMode = localStorage.getItem('mulib_dark_mode');
    const theme = localStorage.getItem('mulib_theme');
    
    if (darkMode === 'true') {
        document.documentElement.classList.add('dark-mode');
    }
    if (theme) {
        document.documentElement.setAttribute('data-theme', theme);
    }
})();