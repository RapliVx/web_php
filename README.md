# Mu-Lib - Hi-Res Audio Player

WARNING VIBE CODE !!!!!!

## Features
- **Material Design 3 UI**: Clean and modern interface.
- **Auto-Playlist**: Automatically detects `.mp3` and `.flac` files in the `audio/` directory.
- **Dark Mode**: Seamless toggle for night-time listening.
- **Custom Controls**: Professional music player controls with progress seeking.
- **User Authentication**: Simple login and session management.

## Tech Stack
- **Backend**: PHP
- **Database**: SQLite3
- **Frontend**: Material Web Components (M3), Google Fonts (Roboto).

## Installation
1. Clone this repository to your local server directory (e.g., `htdocs/`).
2. Create a folder named `audio/` in the root directory.
3. Place your music files (`.mp3` or `.flac`) inside the `audio/` folder.
4. Ensure your server has the SQLite3 extension enabled.
5. Open `index.php` in your browser.

## Database Setup
The database is managed via SQLite3. The schema includes a `users` table for authentication and session management.
