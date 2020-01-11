<?php
  //establish connection
  $conn = new mysqli("localhost:3306", "root", "", "maxc_dev_localhost");

  //sends error if no connection
  if ($conn->connect_error) {
      die("Critical Connection Error: " . $conn->connect_error);
  }

  //returns the Id of the user from their name (which is unique)
  function getUserFromName($name) {
    global $conn;
    $result = $conn->query("SELECT UserID FROM User WHERE Username='$name'");
    $id = -1;
    if ($result) {
      while($row = $result->fetch_assoc()) {
          $id = $row["UserID"];
      }
    }
    $result->free();
    //returns default -1 if no result is found
    return $id;
  }

  //gets the basket cost
  function getBasketCost($userId) {
    global $conn;
    $result = $conn->query("SELECT SUM(ROUND(IFNULL(((Item.OriginalPrice*(100-Discount.DiscountPercent))/100)*Basket.Quantity, (Item.OriginalPrice*Basket.Quantity)), 2)) AS BasketTotal FROM Item INNER JOIN Basket ON Item.ItemID = Basket.ItemID AND Basket.UserID=$userId LEFT OUTER JOIN Discount ON Item.ItemID=Discount.ItemID AND Basket.ColourID = Discount.ColourID");
    $cost = 0;
    if ($result) {
      while($row = $result->fetch_assoc()) {
          $cost += $row["BasketTotal"];
      }
    }
    $result->free();
    //default return is 0
    return $cost;
  }

  /*
    this function adds the basket to the purchase table and creates a link
    between the purchase and the user since it is a many relationship.
    also clears the user's basket
  */
  function buyBasket($userId) {
    global $conn;
    $purchase = $conn->query("INSERT INTO Purchase (ItemID, ColourID, SizeID, Quantity, Price) SELECT Basket.ItemID, Basket.ColourID, Basket.SizeID, Basket.Quantity, (SELECT SUM(ROUND(IFNULL(((Item.OriginalPrice*(100-Discount.DiscountPercent))/100)*Basket.Quantity, (Item.OriginalPrice*Basket.Quantity)), 2)) AS BasketTotal FROM Item INNER JOIN Basket ON Item.ItemID = Basket.ItemID AND Basket.UserID=$userId LEFT OUTER JOIN Discount ON Item.ItemID=Discount.ItemID AND Basket.ColourID = Discount.ColourID) AS PurchaseCost FROM Basket WHERE Basket.UserID=$userId");
    $link = $conn->query("INSERT INTO LinkPurchaseUser (UserID, PurchaseID) SELECT $userId, PurchaseID FROM Purchase INNER JOIN Basket ON Purchase.ItemID=Basket.ItemID AND Purchase.ColourID=Basket.ColourID AND Purchase.SizeID=Basket.SizeID AND Basket.UserID=$userId");
    $drop = $conn->query("DELETE FROM Basket WHERE UserID=$userId");
    //returns true if al sql statements are executed correctly
    return $purchase && $link && $drop;
  }

 ?>
