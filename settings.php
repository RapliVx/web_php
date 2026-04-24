<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Mu-Lib</title>
    
    <script>
        (function() {
            const darkMode = localStorage.getItem('mulib_dark_mode');
            if (darkMode === 'true') document.documentElement.classList.add('dark-mode');
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script type="importmap"> { "imports": { "@material/web/": "https://esm.run/@material/web/" } } </script>
    <script type="module">
        import '@material/web/all.js';
        import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';
        document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
    </script>

    <style>
        :root {
            --primary: #6750A4;
            --bg: #FEF7FF;
            --surface: #F3EDF7;
            --surface-high: #ECE6F0;
            --text: #1D1B20;
            --text-v: #49454F;
        }
        
        html.dark-mode {
            --bg: #141218; 
            --surface: #1D1B20; 
            --surface-high: #2B2930; 
            --text: #E6E0E9; 
            --text-v: #CAC4D0;
            --primary: #D0BCFF;
        }

        body {
            background-color: var(--bg); color: var(--text);
            margin: 0; padding: 16px; font-family: 'Roboto', sans-serif;
            transition: 0.3s; display: flex; justify-content: center;
            min-height: 100vh;
        }

        .settings-card {
            background-color: var(--surface); padding: 24px; border-radius: 36px;
            width: 100%; max-width: 480px; box-shadow: 0px 4px 20px rgba(0,0,0,0.08);
            align-self: flex-start; margin-top: 20px;
        }

        header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        
        md-tabs { --md-sys-color-primary: var(--primary); margin-bottom: 8px; }

        .panel { display: none; padding-top: 20px; animation: slideUp 0.4s cubic-bezier(0, 1, 0.5, 1); }
        .panel.active { display: block; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .setting-group {
            background-color: var(--surface-high);
            border-radius: 28px;
            padding: 12px 20px;
            margin-bottom: 20px;
        }

        .setting-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 0;
        }

        .item-info { display: flex; align-items: center; gap: 16px; }
        .item-icon { color: var(--primary); font-size: 24px; }
        .item-text h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .item-text p { margin: 2px 0 0; font-size: 13px; color: var(--text-v); }

        .logout-btn {
            --md-filled-button-container-color: #B3261E;
            width: 100%; margin-top: 10px;
            --md-filled-button-container-shape: 20px;
        }
    </style>
</head>
<body>

    <div class="settings-card">
        <header>
            <a href="index.php" style="color:inherit;"><md-icon-button><span class="material-symbols-outlined">arrow_back</span></md-icon-button></a>
            <h1 class="md-typescale-headline-small" style="margin:0; font-weight:700;">Pengaturan</h1>
        </header>

        <md-tabs id="mainTabs">
            <md-tab active><md-icon slot="icon">palette</md-icon>Tampilan</md-tab>
            <md-tab><md-icon slot="icon">account_circle</md-icon>Akun</md-tab>
        </md-tabs>

        <div id="panel0" class="panel active">
            <div class="setting-group">
                <div class="setting-item">
                    <div class="item-info">
                        <span class="material-symbols-outlined item-icon">dark_mode</span>
                        <div class="item-text">
                            <h3>Mode Gelap</h3>
                            <p>Tampilan yang nyaman saat malam</p>
                        </div>
                    </div>
                    <md-switch id="darkToggle"></md-switch>
                </div>
            </div>
        </div>

        <div id="panel1" class="panel">
            <div class="setting-group">
                <div class="setting-item">
                    <div class="item-info">
                        <span class="material-symbols-outlined item-icon">alternate_email</span>
                        <div class="item-text">
                            <h3>ID Pengguna</h3>
                            <p>Akun yang sedang aktif</p>
                        </div>
                    </div>
                    <b style="color:var(--primary); font-size: 18px;">@<?php echo $_SESSION['username']; ?></b>
                </div>
            </div>
            
            <md-filled-button id="btnLogout" class="logout-btn">
                <md-icon slot="icon">logout</md-icon>
                Keluar Akun
            </md-filled-button>
        </div>
    </div>

    <md-dialog id="logoutDiag">
        <div slot="headline">Keluar dari Mu-Lib?</div>
        <div slot="content">Kamu harus login kembali untuk mengakses daftar putar pribadimu.</div>
        <div slot="actions">
            <md-text-button id="closeDiag">Batal</md-text-button>
            <a href="logout.php" style="text-decoration:none;">
                <md-filled-button style="--md-sys-color-primary:#B3261E;">Keluar</md-filled-button>
            </a>
        </div>
    </md-dialog>

    <script>
        const tabs = document.getElementById('mainTabs');
        tabs.addEventListener('change', () => {
            document.getElementById('panel0').classList.toggle('active', tabs.activeTabIndex === 0);
            document.getElementById('panel1').classList.toggle('active', tabs.activeTabIndex === 1);
        });

        const darkToggle = document.getElementById('darkToggle');
        if (localStorage.getItem('mulib_dark_mode') === 'true') darkToggle.selected = true;
        
        darkToggle.addEventListener('change', () => {
            const isDark = darkToggle.selected;
            document.documentElement.classList.toggle('dark-mode', isDark);
            localStorage.setItem('mulib_dark_mode', isDark);
        });

        const diag = document.getElementById('logoutDiag');
        document.getElementById('btnLogout').onclick = () => diag.show();
        document.getElementById('closeDiag').onclick = () => diag.close();
    </script>
</body>
</html>