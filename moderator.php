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

<form name="modForm" method="post" action="moderator_processor.php">
<label for="reg-username">Username:</label>
<input type="text" name="reg-username" id="reg-username" />
</form>

<?php
  ########################################## IS A MODERATOR
  }

  require "bottom";
?>
