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

            <div class="d-flex justify-content-center"><h1>Zarejestruj się</h1></div>
        <div style="width:30%;margin:auto">
        
        <form action = "register.php" method="POST" > 
            <div class="form-group">
                <label for="exampleInputEmail1">Imie</label>
                <input type="text" class="form-control" name="imie" placeholder="Imie" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Nazwisko</label>
                <input type="text" class="form-control" name="nazwisko" id="exampleInputPassword1" placeholder="Nazwisko" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Adres</label>
                <input type="text" class="form-control" name="adres" id="exampleInputPassword1" placeholder="Adres" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Kod pocztowy</label>
                <input type="text" class="form-control" name="post_code" onkeyup="countChars(this)" maxlength="6" placeholder="XX-YYY" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Miejscowosc</label>
                <input type="text" class="form-control" name="miejscowosc" id="exampleInputPassword1" placeholder="Miejscowosc" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Pesel</label>
                <input type="text" class="form-control" name="pesel" id="exampleInputPassword1" maxlength="11" minlength="11" placeholder="Pesel" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Telefon</label>
                <input type="text" class="form-control" name="telefon" id="exampleInputPassword1" maxlength="11" minlength="11" placeholder="Telefon" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Login</label>
                <input type="text" class="form-control" name="login" id="exampleInputPassword1" placeholder="Login" required>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Hasło</label>
                <input type="password" class="form-control" name="haslo" id="exampleInputPassword1" placeholder="Hasło" required>
            </div>
        
            <button type="submit" class="btn btn-primary bg-dark center-block">Zarejestruj</button>
        </form>
    </div>
    <script>
          function countChars(obj){
          if(obj.value.length==2){
            obj.value+="-";
          }
          }
          function phonenumber(obj){
          if(obj.value.length==0){
            obj.value+="+48";
          }
          }

            </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>