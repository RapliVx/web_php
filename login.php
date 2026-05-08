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
    
    <script>
        // Set Theme sebelum load agar serasi dengan index.php
        (function() {
            const darkMode = localStorage.getItem('mulib_dark_mode');
            if (darkMode === 'true' || darkMode === null) { 
                document.documentElement.classList.add('dark-mode'); 
            }
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <style>
        :root {
            /* Material You (MD3) Light Theme */
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-container: #F3EDF7;
            --md-sys-color-on-surface: #1D1B20;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline-variant: #CAC4D0;
            --md-sys-color-error: #B3261E;
        }

        html.dark-mode {
            /* Material You (MD3) Dark Theme */
            --md-sys-color-primary: #D0BCFF;
            --md-sys-color-on-primary: #381E72;
            --md-sys-color-surface: #141218;
            --md-sys-color-surface-container: #211F26;
            --md-sys-color-on-surface: #E6E0E9;
            --md-sys-color-on-surface-variant: #CAC4D0;
            --md-sys-color-outline-variant: #49454F;
            --md-sys-color-error: #F2B8B5;
        }

        body {
            background-color: var(--md-sys-color-surface-container);
            color: var(--md-sys-color-on-surface);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .login-card {
            background-color: var(--md-sys-color-surface);
            padding: 40px;
            border-radius: 28px; /* Material 3 Card Radius */
            box-shadow: 0px 4px 16px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 360px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            text-align: center;
            box-sizing: border-box;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
        }
        .icon {
            font-size: 48px;
            color: var(--md-sys-color-primary);
        }
        .error {
            color: var(--md-sys-color-error);
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }
        
        /* Custom Native Input ala MD3 */
        .input-group { text-align: left; }
        .input-group label {
            display: block; font-size: 12px; font-weight: 500;
            color: var(--md-sys-color-on-surface-variant);
            margin-left: 16px; margin-bottom: 4px;
        }
        .md-input {
            width: 100%; box-sizing: border-box; padding: 16px;
            border-radius: 16px; border: 1px solid var(--md-sys-color-outline-variant);
            background: transparent; color: var(--md-sys-color-on-surface); font-size: 16px;
            transition: 0.2s;
        }
        .md-input:focus {
            border-color: var(--md-sys-color-primary); outline: none;
            border-width: 2px; padding: 15px; /* Kompensasi ketebalan border */
        }
        
        /* Custom Button */
        .btn-filled {
            background-color: var(--md-sys-color-primary);
            color: var(--md-sys-color-on-primary);
            padding: 16px; border: none; border-radius: 24px;
            font-size: 16px; font-weight: 500; cursor: pointer; transition: 0.2s;
            margin-top: 8px; font-family: inherit;
        }
        .btn-filled:hover { filter: brightness(1.1); transform: scale(1.02); }
        .btn-filled:active { transform: scale(0.98); }

        p a { color: var(--md-sys-color-primary); text-decoration: none; font-weight: 500; }
        p a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="login-card">
        <span class="material-symbols-outlined icon">library_music</span>
        <div>
            <h1 style="margin:0 0 8px 0; font-size: 28px; font-weight: 400;">Mu-Lib</h1>
            <p style="margin:0; font-size: 14px; color: var(--md-sys-color-on-surface-variant);">Login untuk memutar Hi-Res Audio</p>
        </div>

        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
            <p class="error">Username atau Password salah!</p>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" class="md-input" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" class="md-input" required>
            </div>
            <button type="submit" class="btn-filled">Masuk</button>
        </form>
        <p style="margin:0; font-size:14px; color:var(--md-sys-color-on-surface-variant);">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>

</body>
</html>