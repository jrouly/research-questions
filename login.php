<?php 
  require "lib.php";

  if( is_logged_in() ) { 
    header('Location: index.php');
  }

  require "static/top.part";
?>

<h1>Log In</h1>

<p>
  Please log in using your GMU NetID.
</p>

<?php

$auth_error = "<p>Error: unrecognized username or password.</p>" . PHP_EOL;
$reg_error  = "<p>Error: your account may not have been registered.
Please contact your PRM.</p>" . PHP_EOL;

if( isset($_GET["m"]) ) { 
  if( $_GET["m"] == "a" ) { 
    echo $auth_error;
  }
  if( $_GET["m"] == "r" ) { 
    echo $reg_error;
  }
}
?>

<form id="login-form" action="authenticate.php" method="post">
  <label for="user">user</label><br />
  <input type="text" name="user" id="user" />
  <br /><br />
  <label for="pass">password</label><br />
  <input type="password" name="pass" id="pass" />
  <br /><br />
  <input type="submit" name="submit" value="Login" />
</form>

<?php 
  require "static/bottom.part";
?>
