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
  require "libmod.php";
?>

<h1>Access Logs</h1>

<br />

<?php view_access_logs(); ?>


<?php
  ########################################## IS A MODERATOR
  }

  require "bottom";
?>
