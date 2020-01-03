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
      require_once('filter.php');

      function notification($good, $string) {
        echo '<span class="notif" id="' . (($good) ? "good" : "bad") . '">' . $string . '<br></span>';
      }

      function echoRed($string) {
        echo '<div class="red"><b>' . $string . '</b></div>';
      }

      $brand = new ActiveFilter("Brand", "Brand", "BrandName", "Item", "BrandID", "BrandID");
      $colour = new ActiveFilter("Colour", "Colour", "ColourName", "LinkItemColour", "ColourID", "ColourID");
      $gender = new Filter("Gender", "Item", "Gender", true);
      $category = new ActiveFilter("Category", "Category", "CategoryName", "Item", "CategoryID", "CategoryID");
      $topSleeve = new ConditionalFilter("Sleeve", "ItemTop", "SleeveLength", array("Hoodie", "Jackets", "T-Shirts", "Polo Shirts"));
      $topFit = new ConditionalFilter("Shirt Fit", "ItemShirt", "Fit", array("T-Shirts", "Polo Shirts"));
      $topJacket = new ConditionalFilter("Length", "ItemJacket", "Length", array("Jackets"));
      $trouserFit = new ConditionalFilter("Leg Fit", "ItemTrouser", "LegFit", array("Jeans", "Joggers"));
      $shoeFastener = new ConditionalFilter("Fastener", "ItemShoe", "Fastener", array("Trainers", "Boots"));
      $filterList = array($brand, $category, $gender, $colour, $topSleeve, $topFit, $topJacket, $trouserFit, $shoeFastener);

      $conditions = "";
      $columnAddition = "";
      $parentCategory = -1;


      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //this should be the add to basket stuff
        $userId = $_POST["UserID"];
        $itemId = $_POST["ItemID"];
        $colourId = $_POST["ColourID"];
        $size = $_POST["sizing"];
        if (addToBasket($userId, $itemId, $colourId, getIdFromSize($size))) {
          notification(true, "Basket updated.");
        } else {
          notification(false, "An error occured, could not update basket.");
        }
      }

      if ($_SERVER["REQUEST_METHOD"] == "GET") {
        foreach(array_reverse($filterList) as $filter) {
          $name = $filter->getName();
          if (!empty($_GET["$name"])) {
            $value = $_GET["$name"];
            $table = $filter->getTable();
            $column = $filter->getColumn();
            if ($filter->isConditional()) {
              $columnAddition .= ", $table.$column";
              $conditions .= " INNER JOIN $table ON $table.ItemID=Item.ItemID AND $table.$column=" . '"' . $value . '"';
            } else {
              $conditions .= (strpos($conditions, "WHERE") ? " AND" : " WHERE") . " $table.$column=" . '"' . $value . '"';
            }
          }
        }
        if (empty($_GET['Category'])) {
          $parentCategory = -1;
        } else {
          $parentCategory = $_GET['Category'];
        }
      }

      $productsFound = 0;
      $productList = getAllProducts($columnAddition, $conditions);
      if ($productList) {
        $productsFound = $productList->num_rows;
      }

      function isUserLogged() {
        return isset($_COOKIE["user"]);
      }

      function getProductList() {
        global $productList;
        global $productsFound;
        if ($productsFound == 0) {
          echoRed("OUT OF STOCK");
        } else {
          while($row = $productList->fetch_assoc()) {
            $id = $row['ItemID'];
            $colour = $row['ColourName'];
            $colourId = $row['ColourID'];
            $gender = $row['Gender'];
            $category = $row['CategoryName'];
            $productTitle = strtoupper($row["BrandName"]) . " | $gender $category ($id#$colourId)";
            $source = $id . "_" . $colourId . "_1.jpg";
            $price = getItemPriceFormat($row['OriginalPrice'], $id, $colourId);
            echo '<div class="item-wrapper"><div class="item-image">';
            echo '<br><img src="assets/products/' . $source . '"></div><div class="item-details">';
            echo "<br><b>$productTitle</b><br>";
            echo "<br>Colour: $colour";
            echo "<br>$price<br>";
            $sizes = getSizeOptions($id, $colourId);
            if ($sizes->num_rows > 0) {
              if (isUserLogged()) {
                echo '<br><form method="POST" action="';
                echo htmlspecialchars($_SERVER["PHP_SELF"]);
                echo '"><select name="sizing">';
                while($size = $sizes->fetch_assoc()) {
                  $quantity = $size['SizeQuantity'];
                  $name = $size['SizeName'];
                  echo '<option value="' . $name . '">' . "$name" . (($quantity <= 10) ? ": $quantity left" : "") . "</option>";
                }
                echo '</select><input type="hidden" name="ItemID" value="' . $id . '">';
                echo '</select><input type="hidden" name="ColourID" value="' . $colourId . '">';
                echo '</select><input type="hidden" name="UserID" value="' . getUserID($_COOKIE["user"]) . '">';
                echo '<br><input type="submit" name="submit" value="Basket+1"></form>';
              } else {
                echoRed("Login to add to basket");
              }
            } else {
              echo "<br><b>OUT OF STOCK</b>";
            }
            echo '</div></div><br><br><br>';
          }
        }

      }

      function clean($string) {
        $string = str_replace("enum('", "", $string);
        $string = str_replace("')", "", $string);
        return trim($string);
      }

      function getSelectionOptions($filter) {
        global $parentCategory;
        if ($filter->isConditional()) {
          if (!in_array($parentCategory, $filter->getCategory())) {
            return;
          }
        }
        $subvalues = array();
        if ($filter->isEnum()) {
          $subvalues = explode("','", getEnumValues($filter->getTable(), $filter->getColumn()));
        } else {
          $subvalues = getColumList($filter->getTable(), $filter->getColumn(), $filter->getJoinTable(), $filter->getJoinColumn(), $filter->getComparator());
        }
        if (!empty($subvalues)) {
          $filterName = $filter->getName();
          echo $filterName . ": ";
          echo '<select name="' . $filterName . '">' . $filterName;
          echo '<option></option>';
          foreach($subvalues as $sub) {
              $option = clean($sub);
              echo '<option value="' . $option . '"' . (empty($_GET["$filterName"]) ? "" : " selected") . '>' . $option . "</option>";
          }
          echo "</select>  ";
        }
      }

      function getFilters() {
        global $filterList;
        foreach ($filterList as $filter) {
          echo getSelectionOptions($filter);
        }
        echo '<button type="submit" value="submit">Save Filters</button>';
      }

      function getUserInfo() {
        if (isUserLogged()) {
          echo '<a href="user.php">User: ' . $_COOKIE["user"] . "</a>"; //link to user info + sign out
        } else {
          echo '<a href="login.php">Sign in/Register for basket</a>';
        }
      }

      function getBasketInfo() {
        if (isUserLogged()) {
          $basketSize = getBasketSize($_COOKIE["user"]);
          echo '<a href="purchase.php">Basket</a>'; //link to purchase page
        }
      }
    ?>
    <!-- user action bar -->
    <span class="top-bar"> <?php echo getUserInfo(); ?> | <?php echo getBasketInfo(); ?></span>

    <h1>Products</h1>
    <div id="product-count"><?php echo $productsFound . " Products found"; ?></div>
    <hr>

    <b>Select Filters:</b>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <?php echo getFilters(); ?>
    </form>
    <hr>

    <?php echo getProductList(); ?>
  </body>
</html>
