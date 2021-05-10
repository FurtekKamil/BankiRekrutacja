<!doctype html>
<?php
  session_start();
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

      <div class="container ">
        <?php 
          if(!empty($_GET['check'])){

            echo '<div class="alert alert-danger" role="alert">
            Podano nieprawidłowy numer konta.
          </div>';

          }
        ?>
        <div class="d-flex justify-content-center"><h1>


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
        
        <form action="send.php" method="POST">
            <div class="form-group">
                <label for="exampleInputEmail1">Nazwa odbiorcy</label>
                <input type="text" class="form-control" name="nazwa" placeholder="Nazwa odbiorcy" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Numer odbiorcy</label>
                <input type="text" minlength="32" onchange="stroke(this)" onkeyup="countChars(this)" maxlength="32" class="form-control" name="numer" placeholder="SK BBBB BBBB WWWW WWWW WWWW WWWW" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Tytuł</label>
                <input type="text" class="form-control" name="tytul" placeholder="Tytuł" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Kwota</label>
                <input type="text" class="form-control" name="kwota" placeholder="Kwota" required>
            </div>
            <button type="submit" class="btn btn-primary">Wyślij</button>
        </form>



        <script>
      function countChars(obj){
          if(obj.value.length==2){
            obj.value+=" ";
          }

          if(obj.value.length==7){
            obj.value+=" ";
          }

          if(obj.value.length==12){
            obj.value+=" ";
          }

          if(obj.value.length==17){
            obj.value+=" ";
          }

          if(obj.value.length==22){
            obj.value+=" ";
          }

          if(obj.value.length==27){
            obj.value+=" ";
          }
      }
      

    function stroke(obj){
        if(obj.value[2]!=" "){
          obj.value=obj.value.slice(0,2)+" "+obj.value.slice(2);
          
        }
        if(obj.value[7]!=" "){
          obj.value=obj.value.slice(0,7)+" "+obj.value.slice(7);
         
        }
        if(obj.value[12]!=" "){
          obj.value=obj.value.slice(0,12)+" "+obj.value.slice(12);
        
        }
        if(obj.value[17]!=" "){
          obj.value=obj.value.slice(0,17)+" "+obj.value.slice(17);
         
        }
        if(obj.value[22]!=" "){
          obj.value=obj.value.slice(0,22)+" "+obj.value.slice(22);
          
        }
        if(obj.value[27]!=" "){
          obj.value=obj.value.slice(0,27)+" "+obj.value.slice(27);

        }
    }


        </script>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>