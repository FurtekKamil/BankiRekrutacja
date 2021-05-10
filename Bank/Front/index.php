<!doctype html>
<?php
  session_start();

  if(empty($_SESSION['id'])){
    header("Location: login.php");
  }

?>
<html lang="en">
  <head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <title>Bank 2</title>

  </head>

  <body>

    <nav class="navbar navbar-expand-lg navbar-light bg-dark">
        <a class="navbar-brand text-white" href="index.php">Bank 2</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
          <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
            <a class="nav-link text-white" href="przelewy.php">Przelewy</a>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Konta
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
  
                <?php
                  $url = "https://bankio.herokuapp.com/dane_konta?id=".$_SESSION['id'];
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_URL,$url);
                  $result=curl_exec($ch);
                  curl_close($ch);
                  $json = json_decode($result, true);
                  foreach($json as $elem){
                    echo '<a class="dropdown-item" href="switch.php?id='.str_replace(" ","%20",$elem['nr']).'">'.$elem['nr'].'</a>';
                    echo '<div class="dropdown-divider"></div>';
                  }
                  echo '<a class="dropdown-item" href="add_account.php">Dodaj nowy rachunek</a>';
              ?>
            </div>
          </li>
          </ul>
          <form class="form-inline my-2 my-lg-0 text-white">
                <a class=" nav-link my-2 my-sm-0 text-white" href="logout.php">Wyloguj</a>
          </form>
        </div>

      </nav>

      <div class="container">

        <div class="d-flex justify-content-center"><h1>

        <?php 

          if(!empty($_GET['check']) && $_GET['check']==1){
            echo '<div class="alert alert-success" role="alert">Przelew wewnętrzny wysłany. </div>';

          }else if(!empty($_GET['check']) && $_GET['check']==2){
            echo '<div class="alert alert-success" role="alert">Przelew do innego banku wysłany. </div>';
          }
        
        ?>

        <?php
          $url = "https://bankio.herokuapp.com/dane?nr_konta=".str_replace(" ","%20",$_SESSION['nr_konta'])."";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL,$url);
          $result=curl_exec($ch);
          curl_close($ch);
          $json = json_decode($result, true);
          echo $json[0]['imie']." ".$json[0]['nazwisko'];
        ?>
        
        </h1></div>
        <div class="d-flex justify-content-center"><h2>
        
        <?php
          $url = "https://bankio.herokuapp.com/dane?nr_konta=".str_replace(" ","%20",$_SESSION['nr_konta'])."";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL,$url);
          $result=curl_exec($ch);
          curl_close($ch);
          $json = json_decode($result, true);
          echo $json[0]['nr'];
        ?>
        
        
        </h2></div>
        <div class="d-flex justify-content-center"><h1>Saldo:</h1></div>
        <div class="d-flex justify-content-center"><h2>

        <?php
          $url = "https://bankio.herokuapp.com/dane?nr_konta=".str_replace(" ","%20",$_SESSION['nr_konta'])."";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL,$url);
          $result=curl_exec($ch);
          curl_close($ch);
          $json = json_decode($result, true);
          echo $json[0]['saldo']." zł";
        ?>
        
        </h2></div>
        
        <div style="margin-top:30px;" class="d-flex justify-content-center"><h3>Historia</h3></div>
        <table class="table">
        <thead>
          <tr>
            <th scope="col">Data</th>
            <th scope="col">Nadawca</th>
            <th scope="col">Odbiorca</th>
            <th scope="col">Kwota</th>
            <th scope="col">Tytuł</th>
          </tr>
        </thead>
        <tbody>

        <?php
          $url = "https://bankio.herokuapp.com/get_history?nr_konta=".str_replace(" ","%20",$_SESSION['nr_konta'])."";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL,$url);
          $result=curl_exec($ch);
          curl_close($ch);
          $json = json_decode($result, true);
          if(count($json)==0){
            echo '<div class="d-flex justify-content-center text-danger"><h2>Brak przelewów.</h2></div>';
          }else{

            foreach($json as $value){
              if($value['nr_nadawcy']==$_SESSION['nr_konta'] && $value['status']!=4 && ($value['status']==10 ||$value['status']==0 )){

                echo '<tr>
                <th class = "table-danger"scope="row">'.$value['data'].'</th>
                <td class = "table-danger">'.$value['nazwa_nadawcy'].'<br>'.$value['nr_nadawcy'].'</td>
                <td class = "table-danger">'.$value['nazwa_odbiorcy'].'<br>'.$value['nr_odbiorcy'].'</td>
                <td class = "table-danger">'.$value['kwota'].' zł</td>
                <td class = "table-danger">'.$value['tytul'].'</td>
              </tr>';
             

              }else if($value['nr_odbiorcy']==$_SESSION['nr_konta'] && $value['status']!=4){

                echo '<tr>
                <th class = "table-success" scope="row">'.$value['data'].'</th>
                <td class = "table-success">'.$value['nazwa_nadawcy'].'<br>'.$value['nr_nadawcy'].'</td>
                <td class = "table-success">'.$value['nazwa_odbiorcy'].'<br>'.$value['nr_odbiorcy'].'</td>
                <td class = "table-success">'.$value['kwota'].' zł</td>
                <td class = "table-success">'.$value['tytul'].'</td>

              </tr>';
              }
              
            }
         }
          
        ?>
          </tbody>
        </table>
        
      </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>