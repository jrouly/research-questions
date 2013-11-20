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
  require "libmod.php";
?>

<h1>Registered User List</h1>

<h3>Register New Users</h3>
<p>Approve new users as students or moderators.</p>

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

<h3>Modify Existing Users</h3>
<p>View or modify any of the currently registered users.</p>

<form>
<?php list_registered_users(); ?>
</form>

<br />

<?php
  ########################################## IS A MODERATOR
  }

  require "static/bottom.part";
?>
