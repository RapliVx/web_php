<?php
session_start();
require 'koneksi.php'; // Wajib ditambahkan untuk akses database

if (!isset($_SESSION['login'])) { 
    header("Location: login.php"); 
    exit; 
}

$username = $_SESSION['username'];

// Ambil data user + detail menggunakan LEFT JOIN
$query_user = mysqli_query($conn, "SELECT users.id, users.username, users.role, 
    user_detail.nama_lengkap, user_detail.email, user_detail.no_telp, user_detail.alamat 
    FROM users 
    LEFT JOIN user_detail ON users.id = user_detail.user_id 
    WHERE users.username = '$username'");
$data_user = mysqli_fetch_assoc($query_user);
$user_id = $data_user['id'];

// Sinkronkan role dengan session
$_SESSION['role'] = $data_user['role'];
$isAdmin = ($data_user['role'] === 'admin');

// Proses jika tombol "Simpan Profil" ditekan
$pesan_sukses = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_telp = mysqli_real_escape_string($conn, trim($_POST['no_telp']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    
    // Cek apakah data user_detail sudah ada sebelumnya
    $cek_detail = mysqli_query($conn, "SELECT * FROM user_detail WHERE user_id = '$user_id'");
    
    if (mysqli_num_rows($cek_detail) > 0) {
        // Jika sudah ada, lakukan UPDATE
        $query_save = "UPDATE user_detail SET nama_lengkap='$nama', email='$email', no_telp='$no_telp', alamat='$alamat' WHERE user_id='$user_id'";
    } else {
        // Jika belum ada (user baru), lakukan INSERT
        $query_save = "INSERT INTO user_detail (user_id, nama_lengkap, email, no_telp, alamat) VALUES ('$user_id', '$nama', '$email', '$no_telp', '$alamat')";
    }

    if (mysqli_query($conn, $query_save)) {
        // Perbarui array data_user agar langsung tampil tanpa perlu query ulang
        $data_user['nama_lengkap'] = $nama;
        $data_user['email'] = $email;
        $data_user['no_telp'] = $no_telp;
        $data_user['alamat'] = $alamat;
        $pesan_sukses = "Profil berhasil disimpan!";
    }
}
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
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-on-primary-container: #21005D;
            
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-container: #F3EDF7;
            --md-sys-color-surface-container-high: #ECE6F0;
            --md-sys-color-on-surface: #1D1B20;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline-variant: #CAC4D0;
            
            --md-sys-color-error: #B3261E;
            --md-sys-color-on-error: #FFFFFF;
            --md-sys-color-error-container: #F9DEDC;
            --md-sys-color-on-error-container: #410E0B;
            --md-sys-color-success: #146C2E;
        }
        
        html.dark-mode {
            /* Material You (MD3) Dark Theme */
            --md-sys-color-primary: #D0BCFF;
            --md-sys-color-on-primary: #381E72;
            --md-sys-color-primary-container: #4F378B;
            --md-sys-color-on-primary-container: #EADDFF;
            
            --md-sys-color-surface: #141218;
            --md-sys-color-surface-container: #211F26;
            --md-sys-color-surface-container-high: #2B2930;
            --md-sys-color-on-surface: #E6E0E9;
            --md-sys-color-on-surface-variant: #CAC4D0;
            --md-sys-color-outline-variant: #49454F;
            
            --md-sys-color-error: #F2B8B5;
            --md-sys-color-on-error: #601410;
            --md-sys-color-error-container: #8C1D18;
            --md-sys-color-on-error-container: #F9DEDC;
            --md-sys-color-success: #6DD58C;
        }

        body {
            background-color: var(--md-sys-color-surface-container); 
            color: var(--md-sys-color-on-surface);
            margin: 0; padding: 16px; font-family: 'Roboto', sans-serif;
            transition: background-color 0.3s, color 0.3s; 
            display: flex; justify-content: center;
            min-height: 100vh; box-sizing: border-box;
        }

        .settings-card {
            background-color: var(--md-sys-color-surface); 
            padding: 24px; border-radius: 32px; 
            width: 100%; max-width: 480px; 
            box-shadow: 0px 4px 16px rgba(0,0,0,0.05);
            align-self: flex-start; margin-top: 20px;
        }

        header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
        .icon-btn {
            background: transparent; border: none; color: var(--md-sys-color-on-surface-variant);
            width: 48px; height: 48px; border-radius: 50%; cursor: pointer;
            display: flex; justify-content: center; align-items: center; transition: 0.2s;
            text-decoration: none;
        }
        .icon-btn:hover { background-color: var(--md-sys-color-surface-container-high); }

        .tabs { display: flex; border-bottom: 1px solid var(--md-sys-color-surface-container-high); margin-bottom: 16px; }
        .tab-btn {
            flex: 1; background: transparent; border: none; padding: 16px 0;
            color: var(--md-sys-color-on-surface-variant); font-size: 14px; font-weight: 500;
            cursor: pointer; position: relative; transition: 0.2s;
            display: flex; justify-content: center; align-items: center; gap: 8px; font-family: inherit;
        }
        .tab-btn:hover { background-color: rgba(103, 80, 164, 0.08); }
        .tab-btn.active { color: var(--md-sys-color-primary); }
        .tab-btn.active::after {
            content: ''; position: absolute; bottom: 0; left: 20%; right: 20%;
            height: 3px; background-color: var(--md-sys-color-primary);
            border-top-left-radius: 3px; border-top-right-radius: 3px;
        }

        .panel { display: none; padding-top: 8px; animation: slideUp 0.3s ease; }
        .panel.active { display: block; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .setting-group {
            background-color: var(--md-sys-color-surface-container-high);
            border-radius: 24px; padding: 8px 20px; margin-bottom: 16px;
        }

        .setting-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--md-sys-color-outline-variant); }
        .setting-item:last-child { border-bottom: none; }

        .item-info { display: flex; align-items: center; gap: 16px; }
        .item-icon { color: var(--md-sys-color-primary); font-size: 24px; }
        .item-text h3 { margin: 0; font-size: 16px; font-weight: 500; }
        .item-text p { margin: 4px 0 0; font-size: 14px; color: var(--md-sys-color-on-surface-variant); }

        .switch { position: relative; display: inline-block; width: 52px; height: 32px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: var(--md-sys-color-surface-container-high); transition: .3s;
            border-radius: 32px; border: 2px solid var(--md-sys-color-outline-variant);
        }
        .slider:before {
            position: absolute; content: ""; height: 16px; width: 16px; left: 6px; bottom: 6px;
            background-color: var(--md-sys-color-outline-variant); transition: .3s; border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--md-sys-color-primary); border-color: var(--md-sys-color-primary); }
        input:checked + .slider:before {
            transform: translateX(20px); background-color: var(--md-sys-color-on-primary);
            height: 24px; width: 24px; left: 2px; bottom: 2px;
        }

        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; margin-top: 6px; }
        .role-admin { background-color: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container); }
        .role-user { background-color: var(--md-sys-color-surface-container); color: var(--md-sys-color-on-surface-variant); border: 1px solid var(--md-sys-color-outline-variant); }

        /* Form Inputs MD3 */
        .input-group { text-align: left; margin-bottom: 16px; }
        .input-group label {
            display: block; font-size: 12px; font-weight: 500;
            color: var(--md-sys-color-on-surface-variant);
            margin-left: 16px; margin-bottom: 4px;
        }
        .md-input {
            width: 100%; box-sizing: border-box; padding: 14px 16px;
            border-radius: 16px; border: 1px solid var(--md-sys-color-outline-variant);
            background: transparent; color: var(--md-sys-color-on-surface); font-size: 15px;
            transition: 0.2s; font-family: inherit;
        }
        .md-input:focus {
            border-color: var(--md-sys-color-primary); outline: none;
            border-width: 2px; padding: 13px 15px; /* Kompensasi border agar tidak lompat */
        }
        .md-input::placeholder { color: var(--md-sys-color-outline-variant); }
        
        textarea.md-input { resize: vertical; min-height: 80px; }

        .btn-save {
            background-color: var(--md-sys-color-primary-container);
            color: var(--md-sys-color-on-primary-container);
            padding: 12px 24px; border: none; border-radius: 20px;
            font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s;
            font-family: inherit; margin-top: 4px; width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;
        }
        .btn-save:hover { background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); }

        .btn-logout {
            width: 100%; background-color: var(--md-sys-color-error); color: var(--md-sys-color-on-error);
            padding: 16px; border: none; border-radius: 24px; font-size: 14px; font-weight: 500;
            cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 24px;
            transition: 0.2s; font-family: inherit;
        }
        .btn-logout:hover { background-color: var(--md-sys-color-on-error-container); }

        .admin-link-card {
            background-color: var(--md-sys-color-surface-container-high);
            border-radius: 24px; margin-bottom: 16px; border: 1px solid var(--md-sys-color-primary);
            transition: background-color 0.2s;
        }
        .admin-link-card:hover { background-color: var(--md-sys-color-primary-container); }
        .admin-link { display: block; text-decoration: none; color: inherit; padding: 8px 20px; }

        .alert-success { background-color: rgba(20, 108, 46, 0.1); color: var(--md-sys-color-success); padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 500; margin-bottom: 16px; text-align: center; }
        
        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px); display: none; justify-content: center; align-items: center; z-index: 100; }
        .modal { background: var(--md-sys-color-surface-container-high); padding: 24px; border-radius: 28px; width: 320px; box-shadow: 0 12px 24px rgba(0,0,0,0.2); color: var(--md-sys-color-on-surface); }
        .modal h3 { margin: 0 0 16px; font-size: 24px; font-weight: 400; }
        .modal p { margin: 0 0 24px; font-size: 14px; color: var(--md-sys-color-on-surface-variant); line-height: 20px;}
        .modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .btn { padding: 10px 24px; border: none; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: 0.2s; font-family: inherit; }
        .btn-text { background: transparent; color: var(--md-sys-color-primary); }
        .btn-text:hover { background: var(--md-sys-color-surface-container); }
        .btn-filled { background: var(--md-sys-color-error); color: var(--md-sys-color-on-error); }
        .btn-filled:hover { opacity: 0.9; }
    </style>
</head>
<body>

    <div class="settings-card">
        <header>
            <a href="index.php" class="icon-btn"><span class="material-symbols-outlined">arrow_back</span></a>
            <h1 style="margin:0; font-size: 22px; font-weight: 500; color: var(--md-sys-color-primary);">Pengaturan</h1>
        </header>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab(0)" id="tab0"><span class="material-symbols-outlined">palette</span> Tampilan</button>
            <button class="tab-btn" onclick="switchTab(1)" id="tab1"><span class="material-symbols-outlined">account_circle</span> Profil & Akun</button>
        </div>

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
                    <label class="switch">
                        <input type="checkbox" id="darkToggle">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div id="panel1" class="panel">
            
            <?php if(!empty($pesan_sukses)): ?>
                <div class="alert-success"><?php echo $pesan_sukses; ?></div>
            <?php endif; ?>

            <div class="setting-group">
                <div class="setting-item">
                    <div class="item-info">
                        <span class="material-symbols-outlined item-icon">alternate_email</span>
                        <div class="item-text">
                            <h3>ID Pengguna</h3>
                            <b style="font-size: 16px; color: var(--md-sys-color-primary);">@<?php echo htmlspecialchars($data_user['username']); ?></b>
                        </div>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="item-info">
                        <span class="material-symbols-outlined item-icon">
                            <?php echo $isAdmin ? 'admin_panel_settings' : 'person'; ?>
                        </span>
                        <div class="item-text">
                            <h3>Tingkat Akses</h3>
                            <p><?php echo $isAdmin ? 'Hak akses penuh ke pengaturan sistem.' : 'Akses pemutaran library standar.'; ?></p>
                            <span class="role-badge <?php echo $isAdmin ? 'role-admin' : 'role-user'; ?>">
                                <?php echo $isAdmin ? 'ADMINISTRATOR' : 'PENGGUNA STANDAR'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="">
                <div class="setting-group" style="padding: 16px 20px;">
                    <h3 style="margin: 0 0 16px 0; font-size: 14px; color: var(--md-sys-color-primary); display: flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 18px;">contact_page</span> Detail Profil Lengkap
                    </h3>
                    
                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="md-input" value="<?php echo htmlspecialchars($data_user['nama_lengkap'] ?? ''); ?>" placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="input-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" class="md-input" value="<?php echo htmlspecialchars($data_user['email'] ?? ''); ?>" placeholder="Masukkan alamat email">
                    </div>
                    
                    <div class="input-group">
                        <label>Nomor Telepon</label>
                        <input type="tel" name="no_telp" class="md-input" value="<?php echo htmlspecialchars($data_user['no_telp'] ?? ''); ?>" placeholder="Masukkan nomor telepon aktif">
                    </div>

                    <div class="input-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="md-input" placeholder="Masukkan alamat tempat tinggal saat ini"><?php echo htmlspecialchars($data_user['alamat'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-save">
                        <span class="material-symbols-outlined" style="font-size: 20px;">save</span> Simpan Perubahan
                    </button>
                </div>
            </form>

            <?php if ($isAdmin): ?>
            <div style="margin-top: 24px; margin-bottom: 8px;">
                <p style="font-size: 12px; font-weight: 700; color: var(--md-sys-color-primary); margin-left: 16px; letter-spacing: 1px;">ADMIN TOOLS</p>
            </div>
            <div class="admin-link-card">
                <a href="admin_users.php" class="admin-link">
                    <div class="setting-item" style="border-bottom: none; padding: 8px 0;">
                        <div class="item-info">
                            <span class="material-symbols-outlined item-icon">group</span>
                            <div class="item-text">
                                <h3>Manajemen Pengguna</h3>
                                <p>Lihat dan kelola semua relasi akun</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined" style="color: var(--md-sys-color-primary);">chevron_right</span>
                    </div>
                </a>
            </div>
            <?php endif; ?>
            
            <button id="btnOpenLogout" class="btn-logout" type="button">
                <span class="material-symbols-outlined">logout</span> Keluar Sesi
            </button>
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal">
            <h3>Keluar dari Mu-Lib?</h3>
            <p>Kamu harus login kembali untuk mengakses daftar putar pribadimu.</p>
            <div class="modal-actions">
                <button class="btn btn-text" onclick="document.getElementById('logoutModal').style.display='none'">Batal</button>
                <button class="btn btn-filled" onclick="window.location.href='logout.php'">Keluar</button>
            </div>
        </div>
    </div>

    <script>
        // Logika Tab
        function switchTab(index) {
            document.getElementById('panel0').classList.toggle('active', index === 0);
            document.getElementById('panel1').classList.toggle('active', index === 1);
            document.getElementById('tab0').classList.toggle('active', index === 0);
            document.getElementById('tab1').classList.toggle('active', index === 1);
            
            // Simpan posisi tab aktif ke session storage agar tidak reset saat halaman di-refresh
            sessionStorage.setItem('mulib_active_tab', index);
        }

        // Kembalikan tab terakhir yang dibuka setelah form disubmit
        const savedTab = sessionStorage.getItem('mulib_active_tab');
        if (savedTab !== null) {
            switchTab(parseInt(savedTab));
        }

        // Logika Dark Mode
        const darkToggle = document.getElementById('darkToggle');
        if (localStorage.getItem('mulib_dark_mode') === 'true') {
            darkToggle.checked = true;
        }
        
        darkToggle.addEventListener('change', () => {
            const isDark = darkToggle.checked;
            document.documentElement.classList.toggle('dark-mode', isDark);
            localStorage.setItem('mulib_dark_mode', isDark);
        });

        // Logika Dialog Keluar
        const modal = document.getElementById('logoutModal');
        document.getElementById('btnOpenLogout').onclick = () => modal.style.display = 'flex';
        
        window.onclick = function(event) {
            if (event.target == modal) { modal.style.display = "none"; }
        }
    </script>
</body>
</html>