<?php
    session_start();
    $v = $_GET['id'];
    $_SESSION['nr_konta']=$v;
    header("Location: index.php");
?>