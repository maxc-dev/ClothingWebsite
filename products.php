<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Max Clothing</title>
    <link rel="stylesheet" href="css/main.css">
  </head>
  <body>
    <?php
      require_once('select.php');

      $productList = getAllProducts();
      $productsFound = $productList->num_rows;

      function getProductList() {
        global $productList;
        while($row = $productList->fetch_assoc()) {
          $id = $row['ItemID'];
          $colour = $row['ColourName'];
          $colourId = $row['ColourID'];
          $productTitle = strtoupper($row["BrandName"]) . " | " . $row['ItemName'] . " (#" . $id . ")";
          $source = $id . "_" . $colourId . "_1.jpg";
          $price = getItemPriceFormat($row['OriginalPrice'], $id, $colourId);
          echo '<div class="item-wrapper">';
          echo '<div class="item-image">';
          echo '<br><img src="assets/products/' . $source . '">';
          echo '</div><div class="item-details">';
          echo "<br><b>$productTitle</b><br>";
          echo "<br>Colour: $colour";
          echo "<br>$price<br>";
          $sizes = getSizeOptions($id, $colourId);
          if ($sizes->num_rows > 0) {
            echo "<br>Select a size:";
            echo "<br><select>";
            while($size = $sizes->fetch_assoc()) {
              $quantity = $size['SizeQuantity'];
              $name = $size['SizeName'];
              echo '<option value="' . $name . '">' . "$name: $quantity left</option>";
            }
            echo "</select><br><button>Add To Basket</button>";
          } else {
            echo "<br><b>OUT OF STOCK</b>";
          }
          echo '</div></div><br><br><br>';
        }
      }

    ?>


    <h1>Products</h1>
    <div id="product-count"><?php echo $productsFound . " Products found"; ?></div>
    <hr>

    <h2>Select Filters:</h2>
    <hr>

    <?php echo getProductList(); ?>
  </body>
</html>
