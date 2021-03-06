<?php

//establish connection
$conn = new mysqli("localhost:3306", "root", "", "maxc_dev_localhost");

if ($conn->connect_error) {
  die("Critical Connection Error: " . $conn->connect_error);
}

//gets all the products from the database, taking account of additional conditions/columns in the arguments
function getAllProducts($columnAddition, $conditions) {
  global $conn;
  $statement = "SELECT Item.ItemID, Item.OriginalPrice, Item.Gender, Brand.BrandName, Colour.ColourID, Colour.ColourName, Category.CategoryName$columnAddition FROM Item INNER JOIN LinkItemColour ON Item.ItemID=LinkItemColour.ItemID INNER JOIN Colour ON Colour.ColourID=LinkItemColour.ColourID INNER JOIN Brand ON Brand.BrandID=Item.BrandID INNER JOIN Category ON Category.CategoryID=Item.CategoryID $conditions";
  return $conn->query($statement);
}

//gets an object from a table with a condition and default return
function getObjectFromID($objectName, $tableName, $matchId, $id, $defaultReturn) {
  global $conn;
  $result = $conn->query("SELECT $objectName FROM $tableName WHERE $matchId=$id");
  if ($result) {
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          return $row["$objectName"];
      }
    }
  }
  $result->free();
  return $defaultReturn;
}

//returns a list of filters in array form
function getColumList($table, $column, $joinTable, $joinColumn, $comparator) {
  global $conn;
  $result = $conn->query("SELECT DISTINCT $column FROM $table INNER JOIN $joinTable ON $joinTable.$joinColumn=$table.$comparator ORDER BY $column ASC");
  $listResults = array();
  if ($result) {
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          array_push($listResults, $row["$column"]);
      }
    }
  }
  $result->free();
  return $listResults;
}

//a user is an admin if their ID is 1
function checkAdminStatus($user) {
  global $conn;
  $result = $conn->query("SELECT UserID FROM User WHERE Username='$user' AND UserID=1");
  if ($result) {
    while($row = $result->fetch_assoc()) {
        if ($row['UserID'] == 1) {
          $result->free();
          return true;
        }
    }
  }
  $result->free();
  return false;
}

//returns a format for a products's price since it requires new format if on sale
function getItemPriceFormat($price, $itemId, $colourId) {
  global $conn;
  $result = $conn->query("SELECT DiscountPercent FROM Discount WHERE ItemID=$itemId AND ColourID=$colourId");
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $discountPercent = $row['DiscountPercent'];
        $discountPrice = round((($price * (100-$discountPercent))/100), 2);
        $priceFormat = "<strike>Price: £$price</strike> " . '<div class="red">' . "<b>SALE: £$discountPrice [$discountPercent% OFF]</b></div>";
        $result->free();
        return $priceFormat;
    }
  }
  $result->free();
  return "Price: £$price";
}

//returns a query of size option or a product
function getSizeOptions($itemId, $colourId) {
  global $conn;
  $result = $conn->query("SELECT Size.SizeName, LinkItemSize.SizeQuantity FROM Size INNER JOIN LinkItemSize ON LinkItemSize.SizeID=Size.SizeID WHERE LinkItemSize.ItemID=$itemId AND LinkItemSize.ColourID=$colourId");
  return $result;
}

//gets the enum values of a table
function getEnumValues($table, $column) {
  global $conn;
  $result = $conn->query("SELECT column_type FROM information_schema.columns WHERE table_name = '$table' AND column_name = '$column'");
  if ($result) {
    while($row = $result->fetch_assoc()) {
        $result->free();
        return $row["column_type"];
    }
  }
  $result->free();
  return "N/A";
}

//gets the quantity available of a product
function getQuantityAvailable($itemId, $colourId, $sizeId) {
  global $conn;
  $result = $conn->query("SELECT LinkItemSize.SizeQuantity FROM LinkItemSize WHERE ItemID=$itemId AND ColourID=$colourId AND SizeID=$sizeId");
  $quantity = 0;
  if ($result) {
    while($row = $result->fetch_assoc()) {
        $quantity = $row["SizeQuantity"];
    }
  }
  $result->free();
  return $quantity;
}

//gets the basket size of a user
function getBasketSize($userId) {
  global $conn;
  $result = $conn->query("SELECT Quantity FROM Basket WHERE UserID=$userId");
  $size = 0;
  if ($result) {
    while($row = $result->fetch_assoc()) {
        $size += $row["Quantity"];
    }
  }
  $result->free();
  return $size;
}

//adds an item to a basket
function addToBasket($userId, $itemId, $colourId, $sizeId) {
  $quantityAvailable = getQuantityAvailable($itemId, $colourId, $sizeId);
  if ($quantityAvailable < 1) { //cancels request if out of stock
    return false;
  }
  global $conn;
  //the quantity column has to be updated
  $result = $conn->query("SELECT Quantity FROM Basket WHERE ItemID=$itemId AND ColourID=$colourId AND UserID=$userId AND SizeID=$sizeId");
  $quantity = 0;
  $statement = "INSERT INTO Basket (UserID, ItemID, ColourID, SizeID) VALUES ($userId, $itemId, $colourId, $sizeId)";
  if ($result) {
    if ($result->num_rows == 1) {
      while($row = $result->fetch_assoc()) {
        $quantity = $row["Quantity"];
      }
      $quantity++;
      if ($quantityAvailable < $quantity) {
        $quantity = $quantityAvailable;
      }
      $statement = "UPDATE Basket SET Quantity=$quantity WHERE ItemID=$itemId AND ColourID=$colourId AND UserID=$userId AND SizeID=$sizeId";
    }
  }
  $result->free();
  return $conn->query($statement);
}

//gets the size ID from the size name
function getIdFromSize($size) {
  global $conn;
  $result = $conn->query("SELECT SizeID FROM Size WHERE SizeName='$size'");
  $size = -1;
  if ($result) {
    while($row = $result->fetch_assoc()) {
      $size = $row["SizeID"];
    }
  }
  $result->free();
  return $size;
}

//gets the user's ID from their Username
function getUserID($username) {
  global $conn;
  $result = $conn->query("SELECT UserID FROM User WHERE Username='$username'");
  $id = "";
  if ($result) {
    while($row = $result->fetch_assoc()) {
      $id = $row["UserID"];
    }
  }
  $result->free();
  return $id;
}

 ?>
