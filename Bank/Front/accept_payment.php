<!doctype html>
<?php
  session_start();
  //$_SESSION['nr_konta']="22 2222 2222 2222 2222 2222 2222";
  //$_SESSION['id']=22;
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

      <div style="width:100%">
        <div style="margin:auto">
        
      <?php
            $url = "https://bankio.herokuapp.com/get_history_check?nr_konta=".str_replace(" ","%20",$_SESSION['nr_konta'])."";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$url);
            $result=curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result, true);
            
            echo '
                <form action="send.php" method="POST">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Nazwa odbiorcy</label>
                        <input type="text" class="form-control" name="nazwa" value="'.$json[0]['nazwa_odbiorcy'].'">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Numer odbiorcy</label>
                        <input type="text" class="form-control" name="numer" value="'.$json[0]['nr_odbiorcy'].'">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Tytuł</label>
                        <input type="text" class="form-control" name="tytul" value="'.$json[0]['tytul'].'">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Kwota</label>
                        <input type="text" class="form-control" name="kwota" value="'.$json[0]['kwota'].'">
                        <input type="text" style="visibility:hidden;height:0;" class="form-control" name="check" value="1">
                    </div>
                      <button type="submit" class="btn btn-success">Potwierdź</button>  
                </form>
 
                <a href="index.php"><button type="button" class="btn btn-danger">Odrzuć</button>';
        ?>
        
        </div>
        
      </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>