<?php

  require "lib.php";

  if( is_logged_in() ) { 
    header('Location: index.php');
  }

  if( isset($_POST["user"]) && isset($_POST["pass"]) ) {

    $user = sanitize($_POST["user"]);
    $pass = sanitize($_POST["pass"]);

    $authenticated = False;
    $registered = False;

    # success of user authentication
    $authenticated = authenticate($user,$pass);

    # check if the user was registered by a moderator
    if( $authenticated ) { 
      $registered = is_user_registered( $user );
    }

    $login_success = ($registered) ? 'success' : 'failed';
    log_access_attempt( $user, $login_success );

    if( $registered ) { 
      logout_user($user); # log out any active instances of this username
      login_user($user);

      if( ! is_user_moderator($user) && first_login($user) ) { 
        header('Location: submit-question.php');
      } else { 
        header('Location: index.php');
      }

    } else if( $authenticated ) { 
      header('Location: login.php?m=r');
    } else { 
      header('Location: login.php?m=a');
    }
  }

?>
