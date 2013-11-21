<?php

# Generates a randomly salted hash of the input.
function salted_hash( $input ) { 
  mt_srand(microtime(true)*100000 + memory_get_usage(true));
  $salt = md5(uniqid(mt_rand(), true));
  $hashed_input = hash('sha512', $input.$salt);
  return $hashed_input;
}


# Bounce the user credentials against the LDAP user database.
function authenticate( $user, $pass ) { 
  global $ldap_host,$ldap_port;
  $success = False;

  $ld_user = "uid=$user,ou=people,o=gmu.edu";
  $ldap = ldap_connect($ldap_host, $ldap_port)
            or die("Could not connect to LDAP server.");
  $bind = ldap_bind($ldap, $ld_user, $pass);

  if( $bind ) { 
    $success = True;
  }

  ldap_unbind($ldap);
  return $success;
}

function get_username() { 
  if(isset($_COOKIE["hash"])) { 
    $hash = $_COOKIE["hash"];
    $user = get_username_from_hash($hash);
    return $user;
  }
  return null;
}

function get_fullname_from_user($user) { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu` WHERE `user`='$user';");
  if( $result ) { 
    $row = $result->fetch_row();
    $mysqli->close();
    return $row['name'];
  }
  $mysqli->close();
  return null;
}

function get_username_from_hash($hash) { 
  global $db_name,$tablea;
  $mysqli = connect_to_mysql();
  $hash = sanitize($hash);
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tablea` WHERE `hash`='$hash';");
  if( $result ) { 
    $row = $result->fetch_row();
    $mysqli->close();
    return $row[1];
  }
  $mysqli->close();
  return null;
}

function first_login($user) { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();
  $sql_query = "SELECT * FROM `$db_name`.`$tableu` WHERE `user`='$user';";
  $result = $mysqli->query($sql_query);
  if( $result ) { 
    $row = $result->fetch_row();
    $mysqli->close();
    return $row[3] == 1;
  }
  $mysqli->close();
  return false;
}

# Remove the user hash from the mysql database and set an empty cookie.
# This is just a wrapper of the logout_user function which uses a hash
# value.
function logout_hash($hash) { 
  $hash = sanitize($hash);
  $user = get_username_from_hash($hash);
  logout_user($user);
}

# Remove the user from the mysql database and set an empty cookie.
function logout_user($user) { 
  global $db_name,$tablea;
  $mysqli = connect_to_mysql();
  
  $user = sanitize($user);
  
  # remove a user from the active user table
  $mysqli->query("DELETE FROM `$db_name`.`$tablea` WHERE `user`='$user';");

  # generate dead cookie
  setcookie("hash", "", time()-3600);

  $mysqli->close();
}

# Hash the username, add them to the mysql db, and set a cookie.
function login_user($user) { 
  global $db_name,$tablea,$tablel;
  $mysqli = connect_to_mysql();

  # generate the stuff we're inserting
  $user = sanitize($user);
  $hash = salted_hash($user);
  $stamp = timestamp();

  # insert a user into the active user table
  $mysqli->query("INSERT INTO `$db_name`.`$tablea`(`user`,`hash`,`timestamp`)
                  VALUES ('$user', '$hash','$stamp');");

  # generate user cookie
  setcookie("hash", $hash, time()+3600);

  $mysqli->close();
}

# Log an access attempt.
function log_access_attempt($user, $success) { 
  global $db_name,$tablel;
  $mysqli = connect_to_mysql();

  $user = sanitize($user);
  $stamp = timestamp();

  $mysqli->query("INSERT INTO `$db_name`.`$tablel`(`user`,`date`,`result`)
                  VALUES ('$user','$stamp','$success');");

  $mysqli->close();
}

# Check if the user has logged in successfully.
function is_logged_in() { 
  global $db_name,$tablea;
  $mysqli = connect_to_mysql();
  $output = False;

  if(isset($_COOKIE["hash"])) { 
    $hash = $_COOKIE["hash"];
    $hash = sanitize($hash);

    # verify that the user is in the Active Users table
    $result = $mysqli->query("SELECT * FROM `$db_name`.`$tablea` WHERE `hash`='$hash';");
    $rows = $result->num_rows;
    $output = ($rows == 1);
  }
  
  $mysqli->close();
  return $output;
}

# Verify if this user has been registered.
function is_user_registered( $user ) { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();
  $output = False;

  # verify that the user is in the Registered Users table
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu` WHERE `user`='$user';");
  $rows = $result->num_rows;
  $output = ($rows == 1);

  $mysqli->close();
  return $output;
}

# Register a user with specified stats.
function register_user( $user, $level, $name ) { 
  global $db_name, $tableu;
  $mysqli = connect_to_mysql();

  $output = False;

  $user  = sanitize($user);
  $level = sanitize($level);
  $name  = sanitize($name);

  # make sure we don't already have this user
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu`
                            WHERE `user`='$user';");
  $rows = $result->num_rows;

  if( $rows == 0 ) { 
    $mysqli->query("INSERT INTO `$db_name`.`$tableu`(`user`,`level`,`name`)
                    VALUES ('$user','$level','$name');");
    $output = True;
  }
  $mysqli->close();
  return $output;
}

# Ease-of-use function to determine if a user is a moderator.
function is_moderator() { 
  $output = False;

  if(isset($_COOKIE["hash"])) { 
    $hash = $_COOKIE["hash"];
    $user = get_username_from_hash($hash);
    $output = is_user_moderator( $user );
  }

  return $output;
}

function is_user_moderator( $user ) { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();
  $output = False;
  $user = sanitize($user);

  # verify that the user is in the Known Users table
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu` WHERE `user`='$user';");
  if( $result ) { 
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $output = ($row["role"] == "moderator" );
  }

  $mysqli->close();
  return $output;
}

?>
