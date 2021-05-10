<?php
    session_start();
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];

    $ch = curl_init("https://jednroz.herokuapp.com/login");

        // Setup request to send json via POST
        $data = array(
            "login"=>$login,
            "haslo"=>$haslo
            
        );
        $payload = json_encode($data);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        $w = json_decode($result,true);
        $_SESSION['at'] = $w['aToken'];

        header("Location: index.php");

?>