<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <link rel="stylesheet" href="css/main.css">
    <meta charset="utf-8">
    <title>Max Clothing</title>
  </head>
  <body>
    <?php
      $error = $username = $email = "";
      $valid = true;

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['email'])) {
          $error = "Invalid email.";
          $valid = false;
        } else {
          $email = $_POST['email'];
        }
        if (empty($_POST['password'])) {
          $error = "Invalid password.";
          $valid = false;
        } else {
          $password = $_POST['password'];
        }

        if ($valid) {
          require_once('userdb.php');
          if (isValidUser($email, $password)) {
            $error = "User: " . getUsername($email);
            //send user to product list page

          } else {
            $error = "Invalid user credentials.";
          }
        }

      }
    ?>
    <h1>I DONT KNOW</h1>
    <hr>

    <h2>User Login</h2>
    <div class="login">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Email:<br>
        <input type="email" name="email"><br><br>
        Password:<br>
        <input type="password" name="password"><br><br>
        <input type="submit" name="submit" value="Login">
        <span style="color:red"><?php echo $error;?></span>
      </form>
    </div>
  </body>
</html>
