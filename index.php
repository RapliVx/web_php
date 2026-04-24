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
        (function() {
            const darkMode = localStorage.getItem('mulib_dark_mode');
            const theme = localStorage.getItem('mulib_theme');
            if (darkMode === 'true') document.documentElement.classList.add('dark-mode');
            if (theme) document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script type="importmap">
        { "imports": { "@material/web/": "https://esm.run/@material/web/" } }
    </script>
    <script type="module">
        import '@material/web/all.js';
        import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';
        document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
    </script>

    <style>
        :root {
            --md-sys-color-primary: #6750A4;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-on-primary-container: #21005D;
            
            --md-sys-color-surface: #FEF7FF;
            --md-sys-color-surface-container: #F3EDF7;
            --md-sys-color-surface-container-high: #ECE6F0;
            --md-sys-color-on-surface: #1D1B20;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline: #79747E;
        }

        html[data-theme="blue"] {
            --md-sys-color-primary: #0061A4; --md-sys-color-primary-container: #D1E4FF;
            --md-sys-color-on-primary-container: #001D36; --md-sys-color-surface: #FDFBFF;
            --md-sys-color-surface-container: #F0F4F9; --md-sys-color-on-surface: #1A1C1E;
        }

        html[data-theme="green"] {
            --md-sys-color-primary: #1A6C30; --md-sys-color-primary-container: #A4F5A9;
            --md-sys-color-on-primary-container: #002106; --md-sys-color-surface: #FCFDF6;
            --md-sys-color-surface-container: #F0F5F0; --md-sys-color-on-surface: #1A1C19;
        }

        html.dark-mode {
            --md-sys-color-primary: #D0BCFF; --md-sys-color-primary-container: #4F378B;
            --md-sys-color-on-primary-container: #EADDFF; --md-sys-color-surface: #141218;
            --md-sys-color-surface-container: #1D1B20; --md-sys-color-surface-container-high: #2B2930;
            --md-sys-color-on-surface: #E6E0E9; --md-sys-color-on-surface-variant: #CAC4D0;
        }

        body {
            background-color: var(--md-sys-color-surface);
            color: var(--md-sys-color-on-surface);
            margin: 0; padding: 24px; font-family: 'Roboto', sans-serif;
            display: flex; flex-direction: column; align-items: center;
            min-height: 100vh; box-sizing: border-box; transition: background-color 0.3s;
        }

        header {
            width: 100%; max-width: 450px; display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 32px;
        }

        /* PLAYER CARD */
        .player-card {
            background-color: var(--md-sys-color-surface-container);
            padding: 32px 24px; border-radius: 40px; width: 100%; max-width: 400px;
            text-align: center; box-shadow: 0px 4px 12px rgba(0,0,0,0.05);
        }

        .album-art {
            width: 220px; height: 220px; margin: 0 auto 32px;
            background: linear-gradient(145deg, var(--md-sys-color-primary), var(--md-sys-color-primary-container));
            border-radius: 36px; display: flex; justify-content: center; align-items: center;
            color: var(--md-sys-color-surface); box-shadow: 0px 12px 24px rgba(0,0,0,0.2);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .album-art.playing { transform: scale(1.05); }

        .badge {
            background-color: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container);
            padding: 6px 16px; border-radius: 12px; font-size: 12px; font-weight: 700;
            display: inline-block; margin-bottom: 16px; letter-spacing: 0.5px;
        }

        .progress-area { margin: 24px 0; }
        md-slider { width: 100%; --md-sys-color-primary: var(--md-sys-color-primary); }
        .time-info { display: flex; justify-content: space-between; font-size: 12px; margin-top: 8px; opacity: 0.7; font-weight: 500; }

        .controls-row { display: flex; justify-content: center; align-items: center; gap: 24px; }
        .play-btn {
            background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary);
            width: 80px; height: 80px; border-radius: 28px; border: none; cursor: pointer;
            box-shadow: 0px 8px 15px rgba(0,0,0,0.2); display: flex; justify-content: center; align-items: center;
            transition: 0.2s;
        }
        .play-btn:active { transform: scale(0.92); }
        .play-btn .material-symbols-outlined { font-size: 40px; }

        .playlist-section { width: 100%; max-width: 450px; margin-top: 40px; }
        .track-card {
            background-color: var(--md-sys-color-surface-container-high);
            margin-bottom: 12px; padding: 16px; border-radius: 24px;
            display: flex; align-items: center; gap: 16px; cursor: pointer;
            transition: 0.2s; border: 2px solid transparent;
        }
        .track-card:hover { background-color: var(--md-sys-color-primary-container); }
        .track-card.active { border-color: var(--md-sys-color-primary); background-color: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container); }
        
        .track-info h4 { margin: 0; font-size: 15px; font-weight: 600; }
        .track-info p { margin: 4px 0 0; font-size: 12px; opacity: 0.7; }

        audio { display: none; }
    </style>
</head>
<body>

    <header>
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="material-symbols-outlined" style="color:var(--md-sys-color-primary); font-size:32px;">graphic_eq</span>
            <h1 class="md-typescale-headline-small" style="margin:0; font-weight:700;">Mu-Lib</h1>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="settings.php"><md-icon-button><span class="material-symbols-outlined">settings</span></md-icon-button></a>
            <md-icon-button id="btnLogout" style="color:#B3261E;"><span class="material-symbols-outlined">logout</span></md-icon-button>
        </div>
    </header>

    <div class="player-card">
        <div class="album-art" id="albumArt">
            <span class="material-symbols-outlined" style="font-size:110px;">music_note</span>
        </div>
        <span class="badge" id="trackBadge">HI-RES AUDIO</span>
        <h2 id="trackTitle" style="margin:0; font-size:24px; font-weight:700;">Pilih Lagu</h2>
        <p id="trackArtist" style="margin:8px 0 0; opacity:0.7; font-weight:500;">Daftar Putar Lokal</p>

        <div class="progress-area">
            <md-slider id="progressSlider" min="0" max="100" value="0"></md-slider>
            <div class="time-info">
                <span id="currentTime">0:00</span>
                <span id="durationTime">0:00</span>
            </div>
        </div>

        <div class="controls-row">
            <md-icon-button id="btnPrev"><span class="material-symbols-outlined" style="font-size:32px;">skip_previous</span></md-icon-button>
            <button class="play-btn" id="btnPlay">
                <span class="material-symbols-outlined" id="playIcon">play_arrow</span>
            </button>
            <md-icon-button id="btnNext"><span class="material-symbols-outlined" style="font-size:32px;">skip_next</span></md-icon-button>
        </div>
    </div>

    <div class="playlist-section">
        <h3 style="margin-left:12px; font-size:20px; font-weight:700;">Library</h3>
        <div id="playlistContainer"></div>
    </div>

    <audio id="audioElement"></audio>

    <md-dialog id="logoutDialog">
        <div slot="headline">Logout</div>
        <div slot="content">Ingin keluar dari Mu-Lib?</div>
        <div slot="actions">
            <md-text-button id="btnCancelLogout">Batal</md-text-button>
            <a href="logout.php" style="text-decoration:none;"><md-filled-button style="--md-sys-color-primary:#B3261E;">Keluar</md-filled-button></a>
        </div>
    </md-dialog>

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

        function render() {
            if(tracks.length === 0) {
                playlistContainer.innerHTML = "<div style='text-align:center; padding:40px; opacity:0.5;'><span class='material-symbols-outlined' style='font-size:48px;'>folder_off</span><p>Folder audio/ kosong</p></div>";
                return;
            }
            playlistContainer.innerHTML = tracks.map((t, i) => `
                <div class="track-card ${i === currentIdx ? 'active' : ''}" onclick="playTrack(${i})">
                    <span class="material-symbols-outlined" style="font-size:28px;">${i === currentIdx && isPlaying ? 'pause_circle' : 'play_circle'}</span>
                    <div class="track-info">
                        <h4>${t.title}</h4>
                        <p>${t.ext} • Hi-Res Audio</p>
                    </div>
                </div>
            `).join('');
        }

        window.playTrack = function(i) {
            if (currentIdx !== i || audio.src === "") {
                currentIdx = i;
                audio.src = tracks[i].path;
                document.getElementById('trackTitle').innerText = tracks[i].title;
                document.getElementById('trackBadge').innerText = tracks[i].ext + " LOSSLESS";
            }
            
            if (isPlaying && currentIdx === i && audio.src !== "") {
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

        playBtn.onclick = () => playTrack(currentIdx);
        document.getElementById('btnNext').onclick = () => playTrack((currentIdx + 1) % tracks.length);
        document.getElementById('btnPrev').onclick = () => playTrack((currentIdx - 1 + tracks.length) % tracks.length);

        audio.ontimeupdate = () => {
            slider.value = (audio.currentTime / audio.duration) * 100 || 0;
            document.getElementById('currentTime').innerText = formatTime(audio.currentTime);
            document.getElementById('durationTime').innerText = formatTime(audio.duration);
        };

        slider.onchange = () => { audio.currentTime = (slider.value / 100) * audio.duration; };
        audio.onended = () => document.getElementById('btnNext').click();

        function formatTime(s) {
            const m = Math.floor(s / 60);
            const sec = Math.floor(s % 60);
            return `${m}:${sec < 10 ? '0' : ''}${sec}`;
        }

        const diag = document.getElementById('logoutDialog');
        document.getElementById('btnLogout').onclick = () => diag.show();
        document.getElementById('btnCancelLogout').onclick = () => diag.close();

        render();
    </script>
</body>
</html>