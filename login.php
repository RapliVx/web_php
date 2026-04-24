<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mu-Lib</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script type="importmap">
        {
            "imports": {
                "@material/web/": "https://esm.run/@material/web/"
            }
        }
    </script>
    <script type="module">
        import '@material/web/all.js';
        import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';
        document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
    </script>
    <style>
        body {
            background-color: #F3EDF7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
        }
        .login-card {
            background-color: #FEF7FF;
            padding: 40px;
            border-radius: 24px; /* Material 3 Card Radius */
            box-shadow: 0px 4px 8px 3px rgba(0,0,0,0.15), 0px 1px 3px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 360px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
        }
        .icon {
            font-size: 48px;
            color: #6750A4;
        }
        .error {
            color: #B3261E;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <span class="material-symbols-outlined icon">library_music</span>
        <h1 class="md-typescale-display-small" style="margin:0; color:#1D1B20;">Mu-Lib</h1>
        <p class="md-typescale-body-large" style="margin:0; color:#49454F;">Login untuk memutar Hi-Res Audio</p>

        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
            <p class="md-typescale-body-medium error">Username atau Password salah!</p>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <md-outlined-text-field label="Username" name="username" required></md-outlined-text-field>
            <md-outlined-text-field label="Password" name="password" type="password" required></md-outlined-text-field>
            <md-filled-button type="submit" style="margin-top: 8px;">Masuk</md-filled-button>
        </form>
    </div>

</body>
</html>