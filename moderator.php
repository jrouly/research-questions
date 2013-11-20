<?php
  require "lib.php";
  if( ! is_logged_in() ) {
    header('Location: login.php');
  }

  require "top.part";

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

<h1>Moderator Page</h1>

<p>You may use the interface below to moderate the discussion.</p>

<h3>Access Logs</h3>
<p>Allows you to see user access logs.<br />
<a href="view_logs.php">Access Logs</a></p>



<h3>Approved Users</h3>
<p>Allows you to view and edit existing approved users.<br />
<a href="view_users.php">Approved Users</a></p>


<!--
<h3>Modify an Existing User</h3>
<form name="modify-user" method="post" action="">
list users to modify, selecting one brings up modification dialog
<input type="submit" value="Modify(?)" name="mod-user-submit" id="mod-user-submit" />
<br /><br />
<?php process_modify_user(); ?>
</form>
-->

<!--
<h3>Moderate Questions</h3>
<form name="moderate-questions" method="post" action="">
list questions to modify, selecting one brings up modification dialog
<br />
this should include feedback moderation
<input type="submit" value="Register" name="mod-question-submit" id="mod-question-submit" />
<br /><br />
<?php process_moderate_questions(); ?>
</form>
-->

<?php
  ########################################## IS A MODERATOR
  }

  require "bottom.part";
?>
