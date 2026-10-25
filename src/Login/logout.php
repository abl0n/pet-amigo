<?php
session_start();
$base = '/pet-amigo-main'; 
session_destroy();
header("Location: ../Home/home.php");
exit();