<style>
    :root {
        --primary: #6750A4;
        --bg: #FEF7FF;
        --card: #F3EDF7;
        --text: #1D1B20;
    }

    html[data-theme="blue"] {
        --primary: #0061A4; --bg: #FDFBFF; --card: #F0F4F9;
    }

    html[data-theme="green"] {
        --primary: #1A6C30; --bg: #FCFDF6; --card: #F0F5F0;
    }

    html.dark-mode {
        --primary: #D0BCFF; --bg: #141218; --card: #1D1B20; --text: #E6E0E9;
    }

    body {
        background-color: var(--bg);
        color: var(--text);
        transition: background-color 0.3s, color 0.3s;
    }
</style>