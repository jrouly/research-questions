<?php
  require "lib.php";
  if( ! is_logged_in() ) {
    header('Location: login.php');
  }

  require "top";

  if( ! is_moderator() ) { 
  ########################################## NOT A MODERATOR
?>

<p>You must be an HNRS 110 Peer Research Mentor or Faculty to access this page.</p>

<?php
  ########################################## NOT A MODERATOR
  } else { 
  ########################################## IS A MODERATOR
?>

<h1>Administrator Page</h1>

<p>You may use the interface below to moderate the discussion.</p>

<?php
  ########################################## IS A MODERATOR
  }

  require "bottom";
?>
