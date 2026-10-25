<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se não estiver logado ou não for admin, expulsa para a home
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: /pet-amigo-main/src/Home/home.php");
    exit();
}
?>