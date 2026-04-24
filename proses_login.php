<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            
            $_SESSION['login'] = true;
            $_SESSION['username'] = $username; 

            header("Location: index.php");
            exit;
        }
    }
    header("Location: login.php?pesan=gagal");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>