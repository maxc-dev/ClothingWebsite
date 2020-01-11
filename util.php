<?php
  //sends a notication, $good: false = bad (red), true = good (green)
  function notification($good, $string) {
    echo '<span class="notif" id="' . (($good) ? "good" : "bad") . '">' . $string . '<br></span>';
  }

  //sends an error message in red
  function echoRed($string) {
    echo '<div class="red"><b>' . $string . '</b></div>';
  }

 ?>
