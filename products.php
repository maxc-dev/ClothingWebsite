<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Max Clothing</title>
    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    <?php
      require_once('select.php');
      require_once('filter.php');
      require_once('util.php');

      session_start(); //starts session for user name

      //inits filters for the website
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

      //when a user clicks add to basket...
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //extract post info
        $userId = $_POST["UserID"];
        $itemId = $_POST["ItemID"];
        $colourId = $_POST["ColourID"];
        $size = $_POST["sizing"];
        //addToBasket returns a boolean if it was successful or not, determining the web page response
        if (addToBasket($userId, $itemId, $colourId, getIdFromSize($size))) {
          notification(true, "Basket updated.");
        } else {
          notification(false, "An error occured, could not update basket.");
        }
      }

      //when the user applies a filter
      if ($_SERVER["REQUEST_METHOD"] == "GET") {
        //loops through all the filters defined at the top
        foreach(array_reverse($filterList) as $filter) {
          $name = $filter->getName();
          //if the filter is set...
          if (!empty($_GET["$name"])) {
            $value = $_GET["$name"];
            $table = $filter->getTable();
            $column = $filter->getColumn();
            if ($filter->isConditional()) {
              /*
                if a filter is conditional, it will only appear if a parent
                category is selected and needs a different SQL query addition
              */
              $columnAddition .= ", $table.$column";
              $conditions .= " INNER JOIN $table ON $table.ItemID=Item.ItemID AND $table.$column=" . '"' . $value . '"';
            } else {
              $conditions .= (strpos($conditions, "WHERE") ? " AND" : " WHERE") . " $table.$column=" . '"' . $value . '"';
            }
          }
        }
        //updates the parent category of the page, -1 being no category selected
        if (empty($_GET['Category'])) {
          $parentCategory = -1;
        } else {
          $parentCategory = $_GET['Category'];
        }
      }

      //requests all the products in the database with the filter queries
      $productsFound = 0;
      $productList = getAllProducts($columnAddition, $conditions);
      if ($productList) {
        $productsFound = $productList->num_rows;
      }

      //returns true if the session variable for user is set
      function isUserLogged() {
        return isset($_SESSION['user']);
      }

      //returns true if the user ID is 1 (User with ID of 1 is the default admin)
      /*
        The details for the admin account are:
        Email: max@gmail.com
        Password: admin

        This will give you access to localhost/user.php with admin access
      */
      function isUserAdmin() {
        $user = $_SESSION['user'];
        return checkAdminStatus($user);
      }

      //echos all the product received
      function getProductList() {
        global $productList;
        global $productsFound;
        if ($productsFound == 0) {
          //if no products, indiciate no stock
          echoRed("OUT OF STOCK");
        } else {
          while($row = $productList->fetch_assoc()) {
            //create local variables out of the sql query
            $id = $row['ItemID'];
            $colour = $row['ColourName'];
            $colourId = $row['ColourID'];
            $gender = $row['Gender'];
            $category = $row['CategoryName'];
            $productTitle = strtoupper($row["BrandName"]) . " | $gender $category ($id#$colourId)";
            $source = $id . "_" . $colourId . "_1.jpg";
            $price = getItemPriceFormat($row['OriginalPrice'], $id, $colourId);
            //echo all the information with a html div wrap to format the output
            echo '<div class="item-wrapper"><div class="item-image">';
            echo '<br><img src="assets/products/' . $source . '"></div><div class="item-details">';
            echo "<br><b>$productTitle</b><br>";
            echo "<br>Colour: $colour";
            echo "<br>$price<br>";
            //gets size options
            $sizes = getSizeOptions($id, $colourId);
            if ($sizes->num_rows > 0) {
              if (isUserLogged()) {
                /*
                  I had to use a form for the 'add to basket' button since PHP is server
                  side not client side, so an in-built form will notify the server that
                  the user wishes to add the item to their basket
                */
                echo '<br><form method="POST" action="';
                echo htmlspecialchars($_SERVER["PHP_SELF"]);
                echo '"><select name="sizing">';
                //adds all sizes to a selection list
                while($size = $sizes->fetch_assoc()) {
                  $quantity = $size['SizeQuantity'];
                  $name = $size['SizeName'];
                  echo '<option value="' . $name . '">' . "$name" . (($quantity <= 10) ? ": $quantity left" : "") . "</option>";
                }
                //adds hidden post info about the item being added to basket
                echo '</select><input type="hidden" name="ItemID" value="' . $id . '">';
                echo '</select><input type="hidden" name="ColourID" value="' . $colourId . '">';
                echo '</select><input type="hidden" name="UserID" value="' . getUserID($_SESSION['user']) . '">';
                echo '<br><input type="submit" name="submit" value="Basket+1"></form>';
              } else {
                //users need to be logged in since Baskets are saved on the database with a UserID key
                echoRed("Login to add to basket");
              }
            } else {
              //if no stock is found, an error is displayed
              echo "<br><b>OUT OF STOCK</b>";
            }
            echo '</div></div><br><br><br>';
          }
        }

      }

      //removes clutter from the sql enum list
      function clean($string) {
        $string = str_replace("enum('", "", $string);
        $string = str_replace("')", "", $string);
        return trim($string);
      }

      //displays all the filters available
      function getSelectionOptions($filter) {
        global $parentCategory;
        if ($filter->isConditional()) {
          //displays the filter if the parent category is in the children category list
          if (!in_array($parentCategory, $filter->getCategory())) {
            //if its not found the function is returned
            return;
          }
        }
        //subvalues is a list of the items to add to the selection drop down of the filter
        $subvalues = array();
        if ($filter->isEnum()) {
          $subvalues = explode("','", getEnumValues($filter->getTable(), $filter->getColumn()));
        } else {
          $subvalues = getColumList($filter->getTable(), $filter->getColumn(), $filter->getJoinTable(), $filter->getJoinColumn(), $filter->getComparator());
        }
        //if the dropdown is not empty, it'll create a select drop down box and add the values as options
        if (!empty($subvalues)) {
          $filterName = $filter->getName();
          echo $filterName . ": ";
          echo '<select name="' . $filterName . '">' . $filterName;
          echo '<option></option>';
          foreach($subvalues as $sub) {
              $option = clean($sub);
              echo '<option value="' . $option . '"' . (($_GET["$filterName"] == $option) ? " selected" : "") . '>' . $option . "</option>";
          }
          echo "</select>  ";
        }
      }

      //displays all the filters
      function getFilters() {
        global $filterList;
        foreach ($filterList as $filter) {
          echo getSelectionOptions($filter);
        }
        echo '<button type="submit" value="submit">Save Filters</button>';
      }

      //displays a 'top bar' with hrefs to user login, user viewing (admin only) and the basket
      function getUserBarInfo() {
        if (isUserLogged()) {
          if (isUserAdmin()) {
            echo '<a href="user.php">User: ' . $_SESSION['user'] . "</a>";
          } else {
            echo 'User: ' . $_SESSION['user'];
          }
          echo ' | <a href="purchase.php">Basket</a>';
        } else {
          echo '<a href="login.php">Sign in/Register</a>';
        }
      }

    ?>
    <!-- user action bar -->
    <span class="top-bar"> <?php echo getUserBarInfo(); ?></span>

    <h1>Products</h1>
    <div class="subtle"><?php echo $productsFound . " Products found"; ?></div>
    <hr>

    <b>Select Filters:</b>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <?php echo getFilters(); ?>
    </form>
    <hr>

    <?php echo getProductList(); ?>
  </body>
</html>
