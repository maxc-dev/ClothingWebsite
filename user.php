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

      session_start();
      //get user data
      $allUsers = getAllUsers();
      $usersFound = sizeof(getAllUsers());

      //list all users in simple format
      function displayAllUsers() {
        global $allUsers;
        foreach ($allUsers as $user) {
          echo "<b>[#" . $user->id . "]</b> Name: " . $user->name . " | Email: " . $user->email . " | Phone Number: " . $user->phone . "<br><br>";
        }
      }

      //redirects user if they are not admin
      function noAccess() {
        header("Location: login.php");
        exit;
      }

      //requires user to be logged in, and have admin status
      if (isset($_SESSION['user'])) {
        global $usersFound;
        if ($_SESSION['user'] == "Max") { //if the user is not the admin, throw error
          echo '<h1>User List</h1>';
          echo '<div class="subtle">' . $usersFound . ' Users found </div><hr><br>';
          displayAllUsers();
        } else {
          noAccess();
        }
      } else {
        noAccess();
      }

    ?>
  </body>
</html>
