<?php
  require "lib.php";
  if( ! is_logged_in() ) {
    header('Location: login.php');
  }

  require "static/top.part";

  if( ! is_moderator() ) { 
  ########################################## NOT A MODERATOR
?>

<p>You must be an HNRS 110 Peer Research Mentor or Faculty to access this page.</p>

<?php
  ########################################## NOT A MODERATOR
  } else { 
  ########################################## IS A MODERATOR
?>

<h1>Moderator Page</h1>

<p>You may use the interface below to moderate the discussion.</p>

<h3>Access Logs</h3>
<p>Allows you to see user access logs.<br />
<a href="view_logs.php">Access Logs</a></p>



<h3>Approved Users</h3>
<p>Allows you to view and edit existing approved users.<br />
<a href="view_users.php">Approved Users</a></p>


<?php
  ########################################## IS A MODERATOR
  }

  require "static/bottom.part";
?>
