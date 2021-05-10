<!doctype html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="css/css.css">
    <title>JR</title>

  </head>

  <body>
    

    <div class = "container">
        <div class="form" style="width:1100px;">
          <div style="width:100%;background-color:green;text-align:center;height:50px;line-height:50px;font-size:30px;">Przelewy oczekujące na akceptacje</div>
          <table style="margin:auto;">
   <thead>
      <tr style="width:100%;">
         <th>Kwota</th> <th>Od</th> <th>Do</th> <th>Tytuł</th> <th>Średnia konta</th> <th>Decyzja</th>
      </tr>
   </thead>
          <tbody >
            <div style="display:inline-block;width:100%;">
                <tr>

                  <?php
                  $json = file_get_contents('https://jednroz.herokuapp.com/get_check');
                  $obj = json_decode($json);
                  if(count($obj)>0){
                  foreach($obj as $value){
                    $link = str_replace(" ","%20",$value->debitedaccountnumber);
                    $ww = file_get_contents('https://jednroz.herokuapp.com/get_avg?nr='.$link.'');
                    $w = json_decode($ww);


                    echo '<tr>
                    <td>'.$value->amount.' zł 
                    |</td> <td'.$value->debitedaccountnumber.'|
                    </td> <td>'.$value->debitedaccountnumber.'|
                    </td> <td>'.$value->creditedaccountnumber.'|
                    </td> <td>'.$value->title.'|
                    </td><td>'.floor($w[0]->avg).' zł</td>
                    
                    <td>
                    <a href="accept.php?id='.$value->id_payment.'" class="myButton" style="background-color:green;display:inline-block">Akceptuj</a>
                    <a href="decline.php?id='.$value->id_payment.'" class="myButton" style="background-color:red;display:inline-block">Odrzuć</a>
          
                    </td>';
                  }
                }else{
                  echo '<div style="width:100%;text-align:center">Brak przelewów do rozpatrzenia.</div>';
                }

                  ?>
                
                </tr>
            </div>
          </tbody>
        </table>
        </div>
    </div>

    
  </body>
</html>