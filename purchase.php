<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <link rel="stylesheet" href="main.css">
    <meta charset="utf-8">
    <title>Max Clothing</title>
  </head>
  <body>
    <?php
      require_once('userdb.php');
      require_once('util.php');
      require_once('basket.php');

      session_start();

      //returns user to the product page
      function noAccess() {
        header("Location: products.php");
        exit;
      }

      //echos the total cost of the users basket
      function getBasketTotalCost() {
        if (isset($_SESSION['user'])) {
          $cost = getBasketCost(getUserFromName($_SESSION['user']));
          if ($cost > 0) {
            echo $cost;
          } else {
            //returns user to product page if their basket is empty
            noAccess();
          }
        } else {
          noAccess(); //returns user to product page to browse/log in.
        }
      }

      //when a purchase is complete it will display the price they just paid and a response (un/successful)
      function getPurchaseDetails() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          if (isset($_POST['cost'])) {
            $cost = $_POST['cost'];
            if (buyBasket(getUserFromName($_SESSION['user']))) {
              echo '<b>Purchase Details:</b><br> Cost: £' . $cost;
              notification(true, "Purchase successful!");
            } else {
              notification(false, "Error occured during purchase.");
            }
          }
        }
      }

    ?>
    <h1>Basket</h1>
    <hr>
    <b>Total Cost: £<?php echo getBasketTotalCost(); ?></b>
    <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <input type="hidden" name="cost" value="<?php echo getBasketTotalCost(); ?>">
      <input type="submit" name="Pay">
    </form>
    <br><br>
    <?php echo getPurchaseDetails() ?>
  </body>
</html>
