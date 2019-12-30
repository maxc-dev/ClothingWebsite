<?php

//establish connection
$conn = new mysqli("localhost:3306", "root", "", "maxc_dev_localhost");

if ($conn->connect_error) {
    die("Critical Connection Error: " . $conn->connect_error);
}

function isValidUser($email, $password) {
  global $conn;
  $result = $conn->query("SELECT * FROM User WHERE Email = '$email' AND Password = '$password'");
  return $result->num_rows == 1;
}

function getUsername($email) {
  global $conn;
  $result = $conn->query("SELECT username FROM User WHERE Email = '$email'");
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        return $row["Username"];
    }
  }
  return null;
}

 ?>
