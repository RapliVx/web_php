<script>
    (function() {
        const isDark = localStorage.getItem('mulib_dark_mode') === 'true';
        const theme = localStorage.getItem('mulib_theme');
        if (isDark) document.documentElement.classList.add('dark-mode');
        if (theme) document.documentElement.setAttribute('data-theme', theme);
    })();
</script>

<?php include 'style_tema.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />