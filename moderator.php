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
  require "moderator_lib.php";
?>

<h1>Administrator Page</h1>

<p>You may use the interface below to moderate the discussion.</p>

<h3>Register New Users</h3>
<form name="register-user" method="post" action="">
<input type="text" name="reg-username" id="reg-username" value="username" />
<input type="text" name="reg-name" id="reg-name" value="real name" />
<select name="reg-level" id="reg-level">
  <option value="moderator">Professor</option>
  <option value="moderator">GTA</option>
  <option value="moderator">PRM</option>
  <option value="student" selected="selected">Student</option>
</select>
<a href="#">add row</a>
<br /><br />
<input type="submit" value="Register" name="reg-user-submit" id="reg-user-submit" />
<br /><br />
<?php process_register_user(); ?>
&nbsp;
</form>

<h3>Modify an Existing User</h3>
<form name="modify-user" method="post" action="">
list users to modify, selecting one brings up modification dialog
<input type="submit" value="Register" name="mod-user-submit" id="mod-user-submit" />
<br /><br />
<?php process_modify_user(); ?>
&nbsp;
</form>

<h3>Moderate Questions</h3>
<form name="moderate-questions" method="post" action="">
list questions to modify, selecting one brings up modification dialog
<br />
this should include feedback moderation
<input type="submit" value="Register" name="mod-question-submit" id="mod-question-submit" />
<br /><br />
<?php process_moderate_questions(); ?>
&nbsp;
</form>

<h3><a href="view_logs.php">View Access Logs</a></h3>


<?php
  ########################################## IS A MODERATOR
  }

  require "bottom";
?>
