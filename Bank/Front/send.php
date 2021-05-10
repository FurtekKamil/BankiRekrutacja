<?php
    session_start();
    $debitedaccountnumber;
    $debitednameandaddress;
    $creditedaccountnumber=$_POST['numer'];
    $creditednameandaddress=$_POST['nazwa'];
    $title=$_POST['tytul'];
    $amount=$_POST['kwota'];

    if(!empty($_POST['check'])){
        $check=$_POST['check'];
    }else{
        $check=0;
    }


 $sk=substr($creditedaccountnumber,0,2);
 $bn=substr($creditedaccountnumber,3,9);
 $n=substr($creditedaccountnumber,13,20);
 
 function calculateAccountNumber($sk,$bankNumber,$number) {
        $countryNumber = "2521";
		$countryNumber = $countryNumber.$sk;
		
		$bankNumber=str_replace(' ', '', $bankNumber);
		$number = str_replace(' ', '', $number);

        $subnum = substr($bankNumber, 0, 4);
        $sk1 = $subnum % 97;
        $sk1 = strval($sk1);

        $num = $sk1;
        $subnum = substr($bankNumber, 4, 4);
        $num .= $subnum;
        $num = (int)$num;
        $sk1 = $num % 97;
        $sk1 = strval($sk1);

        $num = $sk1;
        $subnum = substr($number, 0, 4);
        $num .= $subnum;
        $num = (int)$num;
        $sk1 = $num % 97;
        $sk1 = strval($sk1);

        $num = $sk1;
        $subnum = substr($number, 4, 4);
        $num .= $subnum;
        $num = (int)$num;
        $sk1 = $num % 97;
        $sk1 = strval($sk1);

        $num = $sk1;
        $subnum = substr($number, 8, 4);
        $num .= $subnum;
        $num = (int)$num;
        $sk1 = $num % 97;
        $sk1 = strval($sk1);

        $num = $sk1;
        $subnum = substr($number, 12, 4);
        $num .= $subnum;
        $num = (int)$num;
        $sk1 = $num % 97;
        $sk1 = strval($sk1);

        $num = $sk1;
        $num .= $countryNumber;
        $num = (int)$num;

        $roznica = $num % 97;
        $sk = 98 - $roznica;
      
        return $roznica;
    }
    
   
    if(calculateAccountNumber($sk,$bn,$n)!=1){
        header("Location: przelewy.php?check=3");
    }else{

    $string = str_replace(' ', '', $creditedaccountnumber);

    if(!is_numeric($string)){

        header("Location: przelewy.php?check=3");

    }else{
    
    $url = "https://bankio.herokuapp.com/dane_konta?id=".$_SESSION['id'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);
    $result=curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true);
    $debitedaccountnumber = $_SESSION['nr_konta'];
    $debitednameandaddress = $json[0]['imie']." ".$json[0]['nazwisko'];
    

    $url = "https://bankio.herokuapp.com/send_normal";

        $ch = curl_init($url);

        // Setup request to send json via POST
        $data = array(
            'PaymentSum' => $amount,
            'DebitedAccountNumber'=> $debitedaccountnumber,
            'DebitedNameAndAddress'=> $debitednameandaddress,
            'CreditedAccountNumber'=> $creditedaccountnumber,
            'CreditedNameAndAddress'=> $creditednameandaddress,
            'Title'=> $title,
            'Amount'=> $amount,
            'check'=>$check,
            'auth'=>$_SESSION['at']
        );
        $payload = json_encode($data);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        if($result=="Wewnetrzny"){
            header("Location: index.php?check=1");
        }else if($result=="Zewnetrzny"){
            header("Location: index.php?check=2");

        }else if($result=="0"){
            
            $url = "https://bankio.herokuapp.com/set_to_check";

        $ch = curl_init($url);

        // Setup request to send json via POST
        $data = array(
            'PaymentSum' => $amount,
            'DebitedAccountNumber'=> $debitedaccountnumber,
            'DebitedNameAndAddress'=> $debitednameandaddress,
            'CreditedAccountNumber'=> $creditedaccountnumber,
            'CreditedNameAndAddress'=> $creditednameandaddress,
            'Title'=> $title,
            'Amount'=> $amount,
        );
        $payload = json_encode($data);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        header("Location:accept_payment.php");



        }else if($result=="Bad Request"){
            header("Location: index.php?check=3");
        }
    }
}

?>
