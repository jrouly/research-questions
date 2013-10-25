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

<h1>Administrator Page</h1>

<p>You may use the interface below to moderate the discussion.</p>

<h3>View Access Logs</h3>
<p>This page allows you to simply see the access logs of the website, and
see who is logging in successfully/failing.</p>

<p><button><a href="view_logs.php">View Logs</a></button></p>



<h3>Register New Users</h3>

<form name="register-user" id="register-user" method="post" action="">

  <button onClick="add_user_registration_row(); return false;">
  Add Row
  </button>

  <input type="submit" value="Register" name="reg-user-submit" id="reg-user-submit" />
  <br /><br />

  <table id="register-user-table">
  <tr>
    <th>username</th>
    <th>real name</th>
    <th>account type</th>
  </tr>
  <tr>
    <td><input type="text" name="username[]" /></td>
    <td><input type="text" name="realname[]" /></td>
    <td>
      <select name="level[]">
        <option value="student" selected="selected">Student</option>
        <option value="moderator">Professor</option>
        <option value="moderator">GTA</option>
        <option value="moderator">PRM</option>
      </select>
    </td>
  </tr>
  </table>

  <br />
  <?php process_register_user(); ?>
</form>

<h3>Modify an Existing User</h3>
<form name="modify-user" method="post" action="">
list users to modify, selecting one brings up modification dialog
<input type="submit" value="Modify(?)" name="mod-user-submit" id="mod-user-submit" />
<br /><br />
<?php process_modify_user(); ?>
</form>

<h3>Moderate Questions</h3>
<form name="moderate-questions" method="post" action="">
list questions to modify, selecting one brings up modification dialog
<br />
this should include feedback moderation
<input type="submit" value="Register" name="mod-question-submit" id="mod-question-submit" />
<br /><br />
<?php process_moderate_questions(); ?>
</form>

<?php
  ########################################## IS A MODERATOR
  }

  require "bottom";
?>
