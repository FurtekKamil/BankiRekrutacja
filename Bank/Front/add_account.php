<?php
    session_start();

    $url = "https://bankio.herokuapp.com/add_account";

        $ch = curl_init($url);

        // Setup request to send json via POST
        $data = array(
            'id' => $_SESSION['id']
        );
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        
        echo $result;
        header('Location: index.php');

?>