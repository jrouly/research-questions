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

  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tableu` WHERE `user`=:user;");
  $query->bindValue(':user', $user);
  $query->execute();

  $row = $query->fetch(PDO::FETCH_ASSOC);
  return $row['name'];
}

function get_username_from_hash($hash) { 
  global $db_name,$tablea;
  $mysqli = connect_to_mysql();

  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tablea` WHERE `hash`=:hash;");
  $query->bindValue(':hash', $hash);
  $query->execute();

  $row = $query->fetch(PDO::FETCH_ASSOC);
  return $row['user'];
}

function get_user_handle( $userid, $questionid ) {
  # We have around 80k words to play with.
  # 16^4 is just less than 80k, meaning it is
  # in the valid index space.
  $index = md5( $userid.$questionid );
  $index = substr( $index, 0, 4 );
  $index = hexdec( $index );

  $words = new SplFileObject("static/words");
  $word = $words->seek($index);
  $word = $words->current();

  $handle = ucwords("Anonymous ".$word);
  return $handle;
}

function first_login($user) { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();

  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tableu` WHERE `user`=:user;");
  $query->bindValue(':user', $user);
  $query->execute();

  $row = $query->fetch(PDO::FETCH_ASSOC);
  return $row['firstlogin'];
}

# Remove the user hash from the mysql database and set an empty cookie.
# This is just a wrapper of the logout_user function which uses a hash
# value.
function logout_hash($hash) { 
  $user = get_username_from_hash($hash);
  logout_user($user);
}

# Remove the user from the mysql database and set an empty cookie.
function logout_user($user) { 
  global $db_name,$tablea;
  $mysqli = connect_to_mysql();

  $query = $mysqli->prepare("DELETE FROM `$db_name`.`$tablea` WHERE `user`=:user;");
  $query->bindValue(':user', $user);
  $query->execute();

  # generate dead cookie
  setcookie("hash", "", time()-3600);
}

# Hash the username, add them to the mysql db, and set a cookie.
function login_user($user) { 
  global $db_name,$tablea,$tablel;
  $mysqli = connect_to_mysql();

  # generate the stuff we're inserting
  $hash = salted_hash($user);
  $stamp = timestamp();

  # insert a user into the active user table
  $query = $mysqli->prepare(
    "INSERT INTO `$db_name`.`$tablea` (`user`,`hash`,`timestamp`)
                              VALUES (:user, :hash, :stamp);");
  $query->bindValue(':user', $user);
  $query->bindValue(':hash', $hash);
  $query->bindValue(':stamp', $stamp);
  $query->execute();

  # generate user cookie
  setcookie("hash", $hash, time()+3600);
}

# Log an access attempt.
function log_access_attempt($user, $success) { 
  global $db_name,$tablel;
  $mysqli = connect_to_mysql();
  $stamp = timestamp();

  $query = $mysqli->prepare(
    "INSERT INTO `$db_name`.`$tablel`(`user`,`date`,`result`)
                  VALUES (:user, :stamp, :success);");
  $query->bindValue(':user', $user);
  $query->bindValue(':stamp', $stamp);
  $query->bindValue(':success', $success);
  $query->execute();
}

# Check if the user has logged in successfully.
function is_logged_in() { 
  global $db_name,$tablea;
  $mysqli = connect_to_mysql();
  $output = False;

  if(isset($_COOKIE["hash"])) { 
    $hash = $_COOKIE["hash"];

    # verify that the user is in the Active Users table
    $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tablea` WHERE `hash`=:hash;");
    $query->bindValue(':hash', $hash);
    $query->execute();
    $output = ($query->rowCount() == 1);
  }
  
  return $output;
}

# Verify if this user has been registered.
function is_user_registered( $user ) { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();
  $output = False;

  # verify that the user is in the Registered Users table
  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tableu` WHERE `user`=:user;");
  $query->bindValue(':user', $user);
  $query->execute();
  $output = ($query->rowCount() == 1);

  return $output;
}

# Register a user with specified stats.
function register_user( $user, $name, $section, $role ) { 
  global $db_name, $tableu;
  $mysqli = connect_to_mysql();

  $output = False;

  # make sure we don't already have this user
  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tableu` WHERE `user`=:user;");
  $query->bindValue(':user', $user);
  $query->execute();
  $row_count = $query->rowCount();

  if( $row_count == 0 ) { 
    $query = $mysqli->prepare(
      "INSERT INTO `$db_name`.`$tableu`(`user`,`name`,`section`,`role`)
                              VALUES (:user, :name, :section, :role);");
    $query->bindValue(':user', $user);
    $query->bindValue(':name', $name);
    $query->bindValue(':section', $section);
    $query->bindValue(':role', $role);
    $query->execute();
    $output = True;
  }
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

  # verify that the user is in the Known Users table
  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tableu` WHERE `user`=:user;");
  $query->bindValue(':user', $user);
  $query->execute();

  $row = $query->fetch(PDO::FETCH_ASSOC);
  $output = ($row["role"] == "moderator" );

  return $output;
}

?>
