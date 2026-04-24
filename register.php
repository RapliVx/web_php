<?php
session_start();
require 'koneksi.php';

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $pesan = '<p class="md-typescale-body-medium error">Username sudah terdaftar! Pilih yang lain.</p>';
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password_hashed')";
        if (mysqli_query($conn, $query)) {
            $pesan = '<p class="md-typescale-body-medium success">Akun berhasil dibuat! Silakan <a href="login.php">Login</a>.</p>';
        } else {
            $pesan = '<p class="md-typescale-body-medium error">Gagal membuat akun: ' . mysqli_error($conn) . '</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Mu-Lib</title>
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
            border-radius: 24px;
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
        .error { color: #B3261E; font-size: 14px; margin: 0; }
        .success { color: #146C2E; font-size: 14px; margin: 0; }
        a { color: #6750A4; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="login-card">
        <span class="material-symbols-outlined icon">person_add</span>
        <h1 class="md-typescale-display-small" style="margin:0; color:#1D1B20;">Buat Akun</h1>
        
        <?php echo $pesan; ?>

        <form action="" method="POST">
            <md-outlined-text-field label="Buat Username" name="username" required></md-outlined-text-field>
            <md-outlined-text-field label="Buat Password" name="password" type="password" required></md-outlined-text-field>
            <md-filled-button type="submit" style="margin-top: 8px;">Daftar Sekarang</md-filled-button>
        </form>
        <p class="md-typescale-body-small" style="margin:0;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

</body>
</html>