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

    log_access_attempt( $user );
    
    # make sure the forms are filled out
    if( strlen($user) > 0 && strlen($pass) > 0 ) { 
      $bind = "uid=$user,ou=people,o=gmu.edu";
      $success = False;

      /*
      $ldap_host = "ldaps://directory.gmu.edu/";
      if( $ldap_host ) { 
        $ldap = ldap_connect($ldap_host)
                or die("Could not connect to LDAP server.");
        echo "<br />";

        if( $bind = ldap_bind( $ldap, $_POST["user"], $_POST["pass"] ) ) { 
          echo "great";
        } else { 
          echo "noo :(";
        }
      }
      */

      $sucesss = True;
      if( $sucesss ) { 
        login_user($user);
        header('Location: index.php');
      } # else just end the conditional
    } 

    echo "<p>Error: unrecognized username or password.</p>";
    
  } # end-if( post )
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
