<?php

  //establish connection
  $conn = new mysqli("localhost:3306", "root", "", "maxc_dev_localhost");

//send error if connection dead
  if ($conn->connect_error) {
      die("Critical Connection Error: " . $conn->connect_error);
  }

  //validates that a user exists
  function isValidUser($email, $password) {
    global $conn;
    $result = $conn->query("SELECT * FROM User WHERE Email = '$email' AND Password = '$password'");
    if ($result) {
      return $result->num_rows == 1;
    }
    $result->free();
    return false;
  }

  //gets the username of a user from their email
  function getUsername($email) {
    global $conn;
    $result = $conn->query("SELECT Username FROM User WHERE Email = '$email'");
    if ($result) {
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $result->free();
            return $row["Username"];
        }
      }
    }
    $result->free();
    return "n/a";
  }

  //registers a user and returns true if it is successful
  function registerUser($username, $email, $password, $phone) {
    global $conn;
    $statement = "INSERT INTO User (Username, Email, Password, PhoneNumber) VALUES ('$username', '$email', '$password', '$phone')";
    return $conn->query($statement);
  }

  //user profile class model for storing user data
  class UserProfile {
    public $id;
    public $name;
    public $email;
    public $phone;

    function __construct($id, $name, $email, $phone) {
      $this->id = $id;
      $this->name = $name;
      $this->email = $email;
      $this->phone = $phone;
    }
  }

  //returns a list of all users in an array of UserProfile
  function getAllUsers() {
    global $conn;
    $result = $conn->query("SELECT * FROM User");
    $users = array();
    if ($result) {
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($users, new UserProfile($row["UserID"], $row["Username"], $row["Email"], $row["PhoneNumber"]));
        }
      }
    }
    $result->free();
    return $users;
  }

 ?>
