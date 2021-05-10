<?php
    $id = $_GET['id'];
    session_start();
    $ch = curl_init("https://jednroz.herokuapp.com/accept");

        // Setup request to send json via POST
        $data = array(
            'id_payment' => $id,
            
        );
        $payload = json_encode($data);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','debet:Bearer '.$_SESSION['at']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;

        header("Location: index.php");
?>