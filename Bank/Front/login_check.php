<?php
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];
    session_start();


    $url = "https://bankio.herokuapp.com/login";

        $ch = curl_init($url);

        // Setup request to send json via POST
        $data = array(
            'login' => $login,
            'haslo' => $haslo
        );

        $payload = json_encode($data);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        if($result=="Notfound"){
            header("Location: login.php?check=1");
        }else{
            $w = json_decode($result,true);
            $_SESSION['nr_konta'] = $w['nr'];
            $_SESSION['id'] = $w['id_klienta'];
            $_SESSION['at'] = $w['aToken'];
            header("Location: index.php");
        }
?>
