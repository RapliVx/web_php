<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// --- LOGIKA BACA FOLDER AUDIO OTOMATIS ---
$audio_dir = 'audio/';
$tracks = [];

if (is_dir($audio_dir)) {
    $files = scandir($audio_dir);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext === 'mp3' || $ext === 'flac') {
            $tracks[] = [
                'filename' => $file,
                'path' => $audio_dir . $file,
                'title' => pathinfo($file, PATHINFO_FILENAME),
                'ext' => strtoupper($ext)
            ];
        }
    }
}
$tracks_json = json_encode($tracks);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mu-Lib - Music Player</title>

    <script>
        // Set Theme sebelum load
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
            /* Material You (MD3) Light Theme - Baseline Purple */
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-on-primary-container: #21005D;
            
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-container-low: #F7F2FA;
            --md-sys-color-surface-container: #F3EDF7;
            --md-sys-color-surface-container-high: #ECE6F0;
            --md-sys-color-on-surface: #1D1B20;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline-variant: #CAC4D0;
            
            --md-sys-color-error: #B3261E;
            --md-sys-color-error-container: #F9DEDC;
            --md-sys-color-on-error-container: #410E0B;
        }

        html.dark-mode {
            /* Material You (MD3) Dark Theme */
            --md-sys-color-primary: #D0BCFF;
            --md-sys-color-on-primary: #381E72;
            --md-sys-color-primary-container: #4F378B;
            --md-sys-color-on-primary-container: #EADDFF;
            
            --md-sys-color-surface: #141218;
            --md-sys-color-surface-container-low: #1D1B20;
            --md-sys-color-surface-container: #211F26;
            --md-sys-color-surface-container-high: #2B2930;
            --md-sys-color-on-surface: #E6E0E9;
            --md-sys-color-on-surface-variant: #CAC4D0;
            --md-sys-color-outline-variant: #49454F;
            
            --md-sys-color-error: #F2B8B5;
            --md-sys-color-error-container: #8C1D18;
            --md-sys-color-on-error-container: #F9DEDC;
        }

        body {
            background-color: var(--md-sys-color-surface);
            color: var(--md-sys-color-on-surface);
            margin: 0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        /* --- LAYOUT SPLIT PANE --- */
        .sidebar {
            width: 360px;
            background-color: var(--md-sys-color-surface-container-low);
            display: flex;
            flex-direction: column;
            padding: 24px;
            box-sizing: border-box;
            z-index: 10;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--md-sys-color-surface);
            border-top-left-radius: 32px; /* Kesan 'Card' khas Material You */
            border-bottom-left-radius: 32px;
            margin: 16px 16px 16px 0;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            border: 1px solid var(--md-sys-color-surface-container-high);
        }

        /* --- HEADER (MAIN CONTENT) --- */
        .top-bar {
            height: 72px;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h1 {
            font-size: 22px;
            font-weight: 500;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--md-sys-color-primary);
        }

        .header-actions { display: flex; gap: 8px; }

        /* Icon Button M3 */
        .icon-btn {
            background: transparent;
            border: none;
            color: var(--md-sys-color-on-surface-variant);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.2s;
        }
        .icon-btn:hover { background-color: var(--md-sys-color-surface-container-high); }
        .icon-btn.danger { color: var(--md-sys-color-error); }
        .icon-btn.danger:hover { background-color: var(--md-sys-color-error-container); }

        /* --- PLAYER SIDEBAR --- */
        .album-art-container {
            width: 100%;
            aspect-ratio: 1;
            background: linear-gradient(135deg, var(--md-sys-color-primary), var(--md-sys-color-primary-container));
            border-radius: 32px; /* Radius besar ala M3 */
            margin-bottom: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--md-sys-color-surface);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .album-art-container.playing { transform: scale(1.03); }

        .track-info { margin-bottom: 32px; }
        .track-info h2 { margin: 0 0 4px; font-size: 24px; font-weight: 500; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; }
        .track-info p { margin: 0; font-size: 14px; color: var(--md-sys-color-on-surface-variant); }

        .badge {
            display: inline-block;
            background-color: var(--md-sys-color-surface-container-high);
            color: var(--md-sys-color-on-surface);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        /* Material You Slider */
        .progress-container { margin-bottom: 24px; }
        .progress-bar {
            width: 100%;
            -webkit-appearance: none;
            background: transparent;
            margin: 0;
            height: 24px;
        }
        .progress-bar:focus { outline: none; }
        .progress-bar::-webkit-slider-runnable-track {
            width: 100%; height: 6px; cursor: pointer;
            background: var(--md-sys-color-surface-container-high);
            border-radius: 3px;
        }
        .progress-bar::-webkit-slider-thumb {
            height: 16px; width: 8px; border-radius: 4px; /* Bentuk pill/kapsul khas Android */
            background: var(--md-sys-color-primary); cursor: pointer;
            -webkit-appearance: none; margin-top: -5px;
        }
        .time-info {
            display: flex; justify-content: space-between;
            font-size: 12px; color: var(--md-sys-color-on-surface-variant); margin-top: 8px; font-weight: 500;
        }

        .controls { display: flex; justify-content: space-between; align-items: center; padding: 0 16px; }
        
        /* Floating Action Button (FAB) Style */
        .play-btn {
            background-color: var(--md-sys-color-primary-container); 
            color: var(--md-sys-color-on-primary-container);
            width: 72px; height: 72px; border-radius: 20px; /* FAB Rounded Square M3 */
            border: none; cursor: pointer;
            display: flex; justify-content: center; align-items: center;
            transition: 0.2s;
        }
        .play-btn:hover { background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary); }
        .play-btn:active { transform: scale(0.9); }
        .play-btn span { font-size: 36px; }

        /* --- PLAYLIST AREA --- */
        .playlist-content {
            flex-grow: 1; padding: 0 32px 32px 32px; overflow-y: auto;
        }

        .track-item {
            display: flex; align-items: center; padding: 12px 16px;
            border-radius: 16px; cursor: pointer; transition: background-color 0.2s;
            margin-bottom: 8px;
        }
        .track-item:hover { background-color: var(--md-sys-color-surface-container); }
        
        /* Active State M3 */
        .track-item.active {
            background-color: var(--md-sys-color-primary-container);
            color: var(--md-sys-color-on-primary-container);
        }
        .track-item.active .track-meta { color: var(--md-sys-color-on-primary-container); opacity: 0.8; }
        
        .track-icon {
            width: 48px; height: 48px; border-radius: 12px; 
            background: var(--md-sys-color-surface-container-high);
            display: flex; justify-content: center; align-items: center; margin-right: 16px;
            color: var(--md-sys-color-on-surface-variant);
        }
        .track-item.active .track-icon { 
            background: transparent; 
            color: var(--md-sys-color-on-primary-container); 
        }

        .track-details { flex-grow: 1; }
        .track-name { font-size: 16px; font-weight: 500; margin: 0; }
        .track-meta { font-size: 14px; color: var(--md-sys-color-on-surface-variant); margin: 4px 0 0; }

        /* Dialog/Modal Styling M3 */
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);
            display: none; justify-content: center; align-items: center; z-index: 100;
        }
        .modal {
            background: var(--md-sys-color-surface-container-high); 
            padding: 24px; border-radius: 28px; /* Dialog M3 radius */
            width: 320px; box-shadow: 0 12px 24px rgba(0,0,0,0.2);
            color: var(--md-sys-color-on-surface);
        }
        .modal h3 { margin: 0 0 16px; font-size: 24px; font-weight: 400; }
        .modal p { margin: 0 0 24px; font-size: 14px; color: var(--md-sys-color-on-surface-variant); line-height: 20px;}
        .modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .btn {
            padding: 10px 24px; border: none; border-radius: 20px; cursor: pointer;
            font-size: 14px; font-weight: 500; transition: 0.2s; font-family: inherit;
        }
        .btn-text { background: transparent; color: var(--md-sys-color-primary); }
        .btn-text:hover { background: var(--md-sys-color-surface-container); }
        .btn-filled { background: var(--md-sys-color-error); color: var(--md-sys-color-on-primary); }
        .btn-filled:hover { opacity: 0.9; }

        /* Responsive */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; flex-shrink: 0; padding: 16px; }
            .main-content { margin: 0; border-radius: 0; border-top-left-radius: 24px; border-top-right-radius: 24px; box-shadow: 0 -4px 16px rgba(0,0,0,0.1); border: none; }
            .album-art-container { display: none; }
            .top-bar { display: none; }
            .mobile-header { display: flex; justify-content: space-between; width: 100%; margin-bottom: 24px; }
            .controls { padding: 0; gap: 16px; justify-content: center; }
        }
        @media (min-width: 769px) { .mobile-header { display: none; } }

        audio { display: none; }
    </style>
</head>
<body>

    <!-- SIDEBAR KIRI (NOW PLAYING) -->
    <aside class="sidebar">
        <!-- Tampil di Mobile -->
        <div class="mobile-header">
            <h1 style="margin:0; font-size:20px; font-weight:500; display:flex; align-items:center; gap:8px;">
                <span class="material-symbols-outlined" style="color:var(--md-sys-color-primary);">graphic_eq</span> Mu-Lib
            </h1>
            <div class="header-actions">
                <button class="icon-btn danger" onclick="document.getElementById('logoutModal').style.display='flex'"><span class="material-symbols-outlined">logout</span></button>
            </div>
        </div>

        <div class="album-art-container" id="albumArt">
            <span class="material-symbols-outlined" style="font-size:100px;">music_note</span>
        </div>
        
        <div class="track-info">
            <span class="badge" id="trackBadge">PILIH AUDIO</span>
            <h2 id="trackTitle">Tidak ada lagu</h2>
            <p id="trackArtist">Koleksi Lokal</p>
        </div>

        <div class="progress-container">
            <input type="range" class="progress-bar" id="progressSlider" min="0" max="100" value="0">
            <div class="time-info">
                <span id="currentTime">0:00</span>
                <span id="durationTime">0:00</span>
            </div>
        </div>

        <div class="controls">
            <button class="icon-btn" id="btnPrev"><span class="material-symbols-outlined" style="font-size:28px;">skip_previous</span></button>
            <button class="play-btn" id="btnPlay"><span class="material-symbols-outlined" id="playIcon">play_arrow</span></button>
            <button class="icon-btn" id="btnNext"><span class="material-symbols-outlined" style="font-size:28px;">skip_next</span></button>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN (PLAYLIST) -->
    <main class="main-content">
        <!-- Tampil di Desktop -->
        <header class="top-bar">
            <h1><span class="material-symbols-outlined">library_music</span> Library</h1>
            <div class="header-actions">
                <button class="icon-btn" onclick="toggleTheme()" title="Ubah Tema"><span class="material-symbols-outlined">contrast</span></button>
                <a href="settings.php" style="text-decoration: none;"><button class="icon-btn"><span class="material-symbols-outlined">settings</span></button></a>
                <button class="icon-btn danger" onclick="document.getElementById('logoutModal').style.display='flex'"><span class="material-symbols-outlined">logout</span></button>
            </div>
        </header>

        <div class="playlist-content" id="playlistContainer"></div>
    </main>

    <!-- MD3 DIALOG LOGOUT -->
    <div class="modal-overlay" id="logoutModal">
        <div class="modal">
            <h3>Keluar</h3>
            <p>Ingin mengakhiri sesi dan keluar dari Mu-Lib?</p>
            <div class="modal-actions">
                <button class="btn btn-text" onclick="document.getElementById('logoutModal').style.display='none'">Batal</button>
                <button class="btn btn-filled" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </div>

    <audio id="audioElement"></audio>

    <script>
        const tracks = <?php echo $tracks_json; ?>;
        const audio = document.getElementById('audioElement');
        const playBtn = document.getElementById('btnPlay');
        const playIcon = document.getElementById('playIcon');
        const slider = document.getElementById('progressSlider');
        const albumArt = document.getElementById('albumArt');
        const playlistContainer = document.getElementById('playlistContainer');

        let currentIdx = 0;
        let isPlaying = false;

        function toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('dark-mode');
            const isDark = html.classList.contains('dark-mode');
            localStorage.setItem('mulib_dark_mode', isDark);
        }

        function render() {
            if(tracks.length === 0) {
                playlistContainer.innerHTML = `
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:var(--md-sys-color-on-surface-variant); opacity:0.6;">
                        <span class="material-symbols-outlined" style="font-size:80px; margin-bottom:16px;">audio_file</span>
                        <p style="font-size:16px;">Folder audio kosong.</p>
                    </div>`;
                return;
            }
            
            playlistContainer.innerHTML = tracks.map((t, i) => `
                <div class="track-item ${i === currentIdx && isPlaying ? 'active' : ''}" onclick="playTrack(${i})">
                    <div class="track-icon">
                        <span class="material-symbols-outlined">
                            ${i === currentIdx && isPlaying ? 'graphic_eq' : 'music_note'}
                        </span>
                    </div>
                    <div class="track-details">
                        <p class="track-name">${t.title}</p>
                        <p class="track-meta">${t.ext} • File Lokal</p>
                    </div>
                </div>
            `).join('');
        }

        window.playTrack = function(i) {
            if (currentIdx !== i || audio.src === "" || !audio.src.includes(encodeURI(tracks[i].path))) {
                currentIdx = i;
                audio.src = tracks[i].path;
                document.getElementById('trackTitle').innerText = tracks[i].title;
                document.getElementById('trackBadge').innerText = tracks[i].ext + " LOSSLESS";
            }
            
            if (isPlaying && currentIdx === i && !audio.paused) {
                audio.pause();
                isPlaying = false;
                playIcon.innerText = "play_arrow";
                albumArt.classList.remove('playing');
            } else {
                audio.play();
                isPlaying = true;
                playIcon.innerText = "pause";
                albumArt.classList.add('playing');
            }
            render();
        };

        playBtn.onclick = () => { if (tracks.length > 0) playTrack(currentIdx); };
        document.getElementById('btnNext').onclick = () => { if (tracks.length > 0) playTrack((currentIdx + 1) % tracks.length); };
        document.getElementById('btnPrev').onclick = () => { if (tracks.length > 0) playTrack((currentIdx - 1 + tracks.length) % tracks.length); };

        audio.ontimeupdate = () => {
            slider.value = (audio.currentTime / audio.duration) * 100 || 0;
            document.getElementById('currentTime').innerText = formatTime(audio.currentTime);
            if (!isNaN(audio.duration)) document.getElementById('durationTime').innerText = formatTime(audio.duration);
        };

        slider.oninput = () => { 
            if(!isNaN(audio.duration)) audio.currentTime = (slider.value / 100) * audio.duration; 
        };

        audio.onended = () => document.getElementById('btnNext').click();

        function formatTime(s) {
            const m = Math.floor(s / 60);
            const sec = Math.floor(s % 60);
            return `${m}:${sec < 10 ? '0' : ''}${sec}`;
        }

        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if(e.target === this) this.style.display = 'none';
        });

        if(tracks.length > 0) {
            document.getElementById('trackTitle').innerText = tracks[0].title;
            document.getElementById('trackBadge').innerText = tracks[0].ext + " READY";
        }
        render();
    </script>
</body>
</html>