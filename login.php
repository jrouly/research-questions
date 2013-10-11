<?php 
  require "lib.php";
  if( is_logged_in() ) { 
    header('Location: index.php');
  }
  require "top";
    
?>

<h1>Log In</h1>

<p>
  Please log in using your GMU NetID.
</p>


<?php
  if( isset($_POST["user"]) && isset($_POST["pass"]) ) {

    $error = "<p>Error: unrecognized username or password.</p>";
    
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    # success of user login
    $sucesss = authenticate($user,$pass);
    log_access_attempt( $user, $success ); # succeeded

    if( $sucesss ) {
      login_user($user);
      header('Location: index.php');
    }

    echo "<p>Error: unrecognized username or password.</p>";
    
  }
?>

<form id="login-form" action="" method="post">
  <label for="user">user</label><br />
  <input type="text" name="user" id="user" />
  <br /><br />
  <label for="pass">password</label><br />
  <input type="password" name="pass" id="pass" />
  <br /><br />
  <input type="submit" name="submit" value="Login" />
</form>

<?php 
  require "bottom";
?>
