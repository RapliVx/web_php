<?php
session_start();
require 'koneksi.php';

// Keamanan Lapis Ganda
if (!isset($_SESSION['login']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Logika Hapus User
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $query_cek = mysqli_query($conn, "SELECT username FROM users WHERE id = $id_hapus");
    $data_hapus = mysqli_fetch_assoc($query_cek);
    
    if ($data_hapus['username'] !== $_SESSION['username']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id_hapus");
        header("Location: admin_users.php?pesan=dihapus");
        exit;
    } else {
        $pesan_error = "Anda tidak bisa menghapus akun Anda sendiri!";
    }
}

// AMBIL DATA GABUNGAN: users + user_detail
$query = mysqli_query($conn, "SELECT users.id, users.username, users.role, users.created_at, 
    user_detail.nama_lengkap, user_detail.email, user_detail.no_telp, user_detail.alamat 
    FROM users 
    LEFT JOIN user_detail ON users.id = user_detail.user_id 
    ORDER BY users.role ASC, users.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin Dashboard</title>
    
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
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-container: #F3EDF7;
            --md-sys-color-surface-container-high: #ECE6F0;
            --md-sys-color-on-surface: #1D1B20;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline-variant: #CAC4D0;
            --md-sys-color-error: #B3261E;
            --md-sys-color-error-container: #F9DEDC;
            --md-sys-color-success: #146C2E;
        }
        
        html.dark-mode {
            --md-sys-color-primary: #D0BCFF;
            --md-sys-color-on-primary: #381E72;
            --md-sys-color-surface: #141218;
            --md-sys-color-surface-container: #211F26;
            --md-sys-color-surface-container-high: #2B2930;
            --md-sys-color-on-surface: #E6E0E9;
            --md-sys-color-on-surface-variant: #CAC4D0;
            --md-sys-color-outline-variant: #49454F;
            --md-sys-color-error: #F2B8B5;
            --md-sys-color-error-container: #8C1D18;
            --md-sys-color-success: #6DD58C;
        }

        body {
            background-color: var(--md-sys-color-surface-container); 
            color: var(--md-sys-color-on-surface);
            margin: 0; padding: 24px; font-family: 'Roboto', sans-serif;
            display: flex; justify-content: center; min-height: 100vh;
            transition: 0.3s;
        }

        .dashboard-card {
            background-color: var(--md-sys-color-surface); 
            padding: 32px; border-radius: 28px;
            width: 100%; max-width: 1000px;
            box-shadow: 0px 4px 16px rgba(0,0,0,0.05);
            align-self: flex-start;
        }

        header { display: flex; align-items: center; gap: 16px; margin-bottom: 32px; }
        .icon-btn {
            background: transparent; border: none; color: var(--md-sys-color-on-surface-variant);
            width: 48px; height: 48px; border-radius: 50%; cursor: pointer;
            display: flex; justify-content: center; align-items: center; text-decoration: none;
        }
        .icon-btn:hover { background-color: var(--md-sys-color-surface-container-high); }

        .alert { padding: 12px 16px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; }
        .alert-success { background: rgba(20, 108, 46, 0.1); color: var(--md-sys-color-success); }
        .alert-error { background: var(--md-sys-color-error-container); color: var(--md-sys-color-on-error-container); }

        .table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid var(--md-sys-color-outline-variant); }
        table { width: 100%; border-collapse: collapse; text-align: left; background: var(--md-sys-color-surface); }
        
        th { 
            background-color: var(--md-sys-color-surface-container-high);
            padding: 16px; font-size: 13px; font-weight: 700; color: var(--md-sys-color-on-surface-variant);
            border-bottom: 1px solid var(--md-sys-color-outline-variant); text-transform: uppercase;
        }

        td { padding: 16px; font-size: 14px; border-bottom: 1px solid var(--md-sys-color-outline-variant); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: var(--md-sys-color-surface-container); }

        .user-cell { display: flex; align-items: center; gap: 12px; }
        .mini-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: var(--md-sys-color-primary); color: white;
            display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;
            flex-shrink: 0;
        }
        .mini-avatar.user { background: var(--md-sys-color-outline-variant); }

        .contact-info { font-size: 12px; color: var(--md-sys-color-on-surface-variant); line-height: 1.8; }
        .contact-icon { font-size: 14px; vertical-align: middle; margin-right: 6px; color: var(--md-sys-color-primary); }

        .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-admin { background: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container); }
        .badge-user { background: var(--md-sys-color-surface-container-high); color: var(--md-sys-color-on-surface-variant); }

        .btn-delete {
            color: var(--md-sys-color-error); background: transparent; border: none;
            cursor: pointer; width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; transition: 0.2s;
        }
        .btn-delete:hover { background: var(--md-sys-color-error-container); }
        .btn-delete:disabled { opacity: 0.3; cursor: not-allowed; }
    </style>
</head>
<body>

    <div class="dashboard-card">
        <header>
            <a href="settings.php" class="icon-btn"><span class="material-symbols-outlined">arrow_back</span></a>
            <div>
                <h1 style="margin:0; font-size: 24px; font-weight: 500;">Manajemen Pengguna</h1>
                <p style="margin:4px 0 0; font-size:14px; color:var(--md-sys-color-on-surface-variant);">Basis data relasi akun Mu-Lib</p>
            </div>
        </header>

        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'dihapus'): ?>
            <div class="alert alert-success">Akun telah dihapus secara permanen.</div>
        <?php endif; ?>
        <?php if(isset($pesan_error)): ?>
            <div class="alert alert-error"><?php echo $pesan_error; ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Identitas Pengguna</th>
                        <th>Info Kontak & Alamat</th>
                        <th>Terdaftar</th>
                        <th>Peran</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($query)): ?>
                        <?php 
                            $isAdminUser = ($user['role'] === 'admin'); 
                            $isMe = ($user['username'] === $_SESSION['username']);
                            
                            $tgl_daftar = !empty($user['created_at']) ? date('d M Y, H:i', strtotime($user['created_at'])) : '-';
                        ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="mini-avatar <?php echo $isAdminUser ? '' : 'user'; ?>">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700;">@<?php echo htmlspecialchars($user['username']); ?></div>
                                        <div style="font-size: 12px; color: var(--md-sys-color-on-surface-variant);">
                                            <?php echo !empty($user['nama_lengkap']) ? htmlspecialchars($user['nama_lengkap']) : '<i style="opacity:0.6;">Nama belum diisi</i>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <?php if(!empty($user['email'])): ?>
                                        <div><span class="material-symbols-outlined contact-icon">mail</span><?php echo htmlspecialchars($user['email']); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($user['no_telp'])): ?>
                                        <div><span class="material-symbols-outlined contact-icon">call</span><?php echo htmlspecialchars($user['no_telp']); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($user['alamat'])): ?>
                                        <div><span class="material-symbols-outlined contact-icon">home</span><?php echo htmlspecialchars($user['alamat']); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if(empty($user['email']) && empty($user['no_telp']) && empty($user['alamat'])): ?>
                                        <i style="opacity:0.5;">Belum melengkapi data profil</i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: var(--md-sys-color-on-surface-variant);">
                                    <?php echo $tgl_daftar; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo $isAdminUser ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo $user['role']; ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="?hapus=<?php echo $user['id']; ?>" 
                                   onclick="return confirm('Hapus permanen akun @<?php echo htmlspecialchars($user['username']); ?>?');"
                                   style="<?php echo $isMe ? 'pointer-events:none;' : ''; ?>">
                                    <button class="btn-delete" <?php echo $isMe ? 'disabled' : ''; ?> title="Hapus Akun">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                    </button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>