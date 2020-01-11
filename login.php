<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <link rel="stylesheet" href="main.css">
    <meta charset="utf-8">
    <title>Max Clothing</title>
  </head>
  <body>
    <?php
      require_once('util.php');
      require_once('userdb.php');

      //sends the user to the product page and sets the user session variable
      function redirectToProductList($name) {
        session_start();
        $_SESSION['user'] = $name;
        header("Location: products.php"); //redirects to products list
        exit();
      }

      /*
        The DataForm class creates an object which allows a dynamic php based
        form to be generated in html. Such as a register or login form.
      */
      class DataForm {
        public $title;
        public $arrKey;
        public $arrVal;

        function __construct($title, $arrKey, $arrVal) {
          $this->title = $title;
          $this->arrKey = $arrKey;
          $this->arrVal = $arrVal;
        }
      }

      //processes the form once it is submit
      function processForm($login) {
        $email = $_POST["Email"];
        $password = $_POST["Password"];
        if ($login) {
          //if the form is logging in it will validate the user and log them in
          if (isValidUser($email, $password)) {
            redirectToProductList(getUsername($email));
          } else {
            //if not valid requests new credentials
            notification(false, "Invalid user credentials to login.");
          }
        } else { //register user instead
          if (!isValidUser($email, $password)) {
            //user must be invalid (/not exist) for them to be registered
            $username = $_POST["Username"];
            $phone = $_POST["Number"];
            //registerUser returns true if successful and redirects if successful
            if (registerUser($username, $email, $password, $phone)) {
              redirectToProductList($username);
            } else {
              notification(false, "Invalid register credentials.");
              return;
            }
          } else {
            notifiication(false, "User already exists.");
            return;
          }
        }
      }

      //array of data forms
      $attr = array();

      $dataLogin = new DataForm("Login", array("email", "password"), array("Email", "Password"));
      $dataRegister = new DataForm("Register", array("username", "email", "password", "tel"), array("Username", "Email", "Password", "Number"));

      array_push($attr, $dataLogin);
      array_push($attr, $dataRegister);

      //when a form is submitted via post it validates and processes...
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        foreach ($attr as $dataForm) {
          if ($_POST["FormType"] == $dataForm->title) {
            foreach ($dataForm->arrVal as $postTag) {
              if (empty($_POST["$postTag"])) { //esnures all inputs in the form are not empty
                notification(false, "Invalid credentials for $postTag.");
                exit();
              }
            }
            processForm(($dataForm->title == "Login" ? true : false));
            return; //prevents the other forms from being checked
          }
        }

      }

      //displays the DataForm using the arrays
      function displayFormFeatures($dataForm) {
        echo '<h1>' . $dataForm->title . '</h1><hr>';
        echo '<div class="form-center"><form method="POST" action="';
        echo htmlspecialchars($_SERVER["PHP_SELF"]);
        echo '">';
        //loops through the array of contens to be sent to post and creates an input
        for ($i = 0; $i < sizeof($dataForm->arrKey); ++$i) {
          echo $dataForm->arrVal[$i] . ":<br>";
          echo '<input type="' . $dataForm->arrKey[$i] . '" name="' . $dataForm->arrVal[$i] . '"><br><br>';
        }
        echo '<input type="hidden" name="FormType" value="' . $dataForm->title . '">';
        echo '<input type="submit" name="submit_' . $dataForm->title . '" value="' . $dataForm->title . '">';
        echo "</form></div>";
      }

      echo displayFormFeatures($dataLogin);
      echo displayFormFeatures($dataRegister);

    ?>
  </body>
</html>
