<?php
  require "lib.php";
  if( is_logged_in() ) {
    require "top";
    if( is_admin($_COOKIE["user"]) ) {
?>

<h1>Administrator Page</h1>

<p>You may use the interface below to moderate the discussion.</p>

<?php
    } else { 
?>

<p>You must be an HNRS 110 Peer Research Mentor or Faculty to access this page.</p>

<?php
    }
    require "bottom";
  } else { 
    header('Location: login.php');
  }
?>
