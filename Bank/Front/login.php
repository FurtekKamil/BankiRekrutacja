<!doctype html>

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

        <div class="d-flex justify-content-center "><h1>Zaloguj się</h1></div>
        <div style="width:30%;margin:auto">
        
        <form action = "login_check.php" method="POST" > 
            <div class="form-group">
                <label for="exampleInputEmail1">Login</label>
                <input type="text" class="form-control" name="login" placeholder="Login" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Hasło</label>
                <input type="password" class="form-control" name="haslo" id="exampleInputPassword1" placeholder="Hasło" required>
            </div>
        
            <button type="submit" class="btn btn-dark">Zaloguj</button><a href="glowna.php" style="margin-left:20px;"class="btn btn-dark ">Zarejestruj się</a>
        </form>
        
          <?php
            if(!empty($_GET['check']) && $_GET['check']==1){
              echo '<div class="alert alert-danger" role="alert">
              Błędne dane logowania.
            </div>';
            }else if(!empty($_GET['check']) && $_GET['check']==2){
              echo '<div class="alert alert-success" role="alert">
              Pomyślnie zarejestrowano. Możesz się zalogować.
            </div>';
            }

          ?>
    </div>
        
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>