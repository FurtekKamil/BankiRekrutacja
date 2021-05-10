<?php
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $adres = $_POST['adres'];
    $post_code = $_POST['post_code'];
    $miejscowosc = $_POST['miejscowosc'];
    $pesel = $_POST['pesel'];
    $telefon = $_POST['telefon'];
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];



    $url = "https://bankio.herokuapp.com/register";

        $ch = curl_init($url);

        // Setup request to send json via POST
        $data = array(
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'adres' => $adres,
            'kod_pocztowy' => $post_code,
            'miejscowosc' => $miejscowosc,
            'pesel' => $pesel,
            'telefon' => $telefon,
            'login' => $login,
            'haslo' => $haslo,

            
        );

        $payload = json_encode($data);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        curl_close($ch);

        if($result=="Dodano"){
            header("Location: login.php?check=2");
        }
        
        echo $result;
     

?>
