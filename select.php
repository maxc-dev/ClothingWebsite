<?php

//establish connection
$conn = new mysqli("localhost:3306", "root", "", "maxc_dev_localhost");

if ($conn->connect_error) {
  die("Critical Connection Error: " . $conn->connect_error);
}

function getAllProducts() {
  global $conn;
  return $conn->query("SELECT Item.ItemID, Item.ItemName, Item.OriginalPrice, Item.Category, Item.Gender, Brand.BrandName, Colour.ColourID, Colour.ColourName FROM Item INNER JOIN LinkItemColour ON Item.ItemID=LinkItemColour.ItemID INNER JOIN Colour ON Colour.ColourID=LinkItemColour.ColourID INNER JOIN Brand ON Brand.BrandID=Item.BrandID");
}

function getObjectFromID($objectName, $tableName, $id, $defaultReturn) {
  global $conn;
  $result = $conn->query("SELECT $objectName FROM $tableName WHERE ID=$id");
  if (!$result) {
    return $defaultReturn;
  }
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        return $row["$objectName"];
    }
  } else {
    return $defaultReturn;
  }
}

function getItemPriceFormat($price, $itemId, $colourId) {
  global $conn;
  $result = $conn->query("SELECT DiscountPercent FROM Discount WHERE ItemID=$itemId AND ColourID=$colourId");
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $discountPercent = $row['DiscountPercent'];
        $discountPrice = round((($price * (100-$discountPercent))/100), 2);
        $priceFormat = "<strike>Price: £$price</strike> <b>SALE: £$discountPrice [$discountPercent% OFF]</b>";
        return $priceFormat;
    }
  }
  return "Price: £$price";
}

function getSizeOptions($itemId, $colourId) {
  global $conn;
  $result = $conn->query("SELECT SizeName, SizeQuantity FROM Size WHERE ItemID=$itemId AND ColourID=$colourId");
  return $result;
}

 ?>
