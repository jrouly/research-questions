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

    $auth_error = "<p>Error: unrecognized username or password.</p>" . PHP_EOL;
    $reg_error  = "<p>Error: your account may not have been registered.
    Please contact your PRM.</p>" . PHP_EOL;
    
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    # success of user authentication
    $authenticated = authenticate($user,$pass);

    # check if the user was registered by a moderator
    $registered = False;
    if( $authenticated ) { 
      $registered = is_user_registered( $user );
    }

    $login_success = ($registered) ? 'success' : 'failed';
    log_access_attempt( $user, $login_success );

    if( $registered ) { 
      login_user($user);
      header('Location: index.php');
    } else if( $authenticated ) {
      echo $reg_error;
    } else { 
      echo $auth_error;
    }

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
