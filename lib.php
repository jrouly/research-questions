<?php 

require "config.php";
global $salt;
global $db_host,$db_user,$db_pass,$db_name;
global $tableq,$tablec,$tableu,$tablel,$tablea;
global $ldap_host,$ldap_port;

# Generates a salted hash of the username.
function salted_hash( $user ) { 
  global $salt;
  $hashed_user = hash('sha512', $user.$salt);
  return $hashed_user;
}

# Clean up any input for insertion in the mysql database.
function sanitize($input) { 
  $mysqli = connect_to_mysql();
  $input = htmlspecialchars( $input );
  $input = $mysqli->real_escape_string( $input );
  $input = addslashes( $input );
  $input = trim( $input );
  $mysqli->close();
  return $input;
}

# Generate a standard format timestamp.
function timestamp() { 
  return date('Y-m-d H:i:s');
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

# Remove the user from the mysql database and set an empty cookie.
function logout_user($user) { 
  global $db_name,$tablea,$tablel;
  $mysqli = connect_to_mysql();
  
  # generate the stuff we're inserting
  $hash = sanitize($user);
  $stamp = date('Y-m-d H:i:s');

  # insert a user into the active user table
  $mysqli->query("DELETE FROM `$db_name`.`$tablea` WHERE `hash`='$hash';");

  # generate user cookie
  setcookie("user", "", time()-3600);

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
  setcookie("user", $hash, time()+3600);

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

  if(isset($_COOKIE["user"])) { 
    $hash = $_COOKIE["user"];
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
  $hash  = salted_hash($user);
  $level = sanitize($level);
  $name  = sanitize($name);

  # make sure we don't already have this user
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu`
                            WHERE `user`='$user';");
  $rows = $result->num_rows;

  if( $rows == 0 ) { 
    $mysqli->query("INSERT INTO `$db_name`.`$tableu`(`user`,`hash`,`level`,`name`)
                    VALUES ('$user','$hash','$level','$name');");
    $output = True;
  }
  $mysqli->close();
  return $output;
}

# Ease-of-use function to determine if a user is a moderator.
function is_moderator() { 
  global $db_name,$tableu;
  $mysqli = connect_to_mysql();
  $output = False;

  if(isset($_COOKIE["user"])) { 
    $hash = $_COOKIE["user"];
    $hash = sanitize($hash);

    # verify that the user is in the Known Users table
    $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu` WHERE `hash`='$hash';");
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $output = ($row["level"] == "moderator" );
  }
  
  $mysqli->close();
  return $output;

}

function generate_student_view() { 
  // initial MYSQL connection
  global $tableq, $tablec;
  $mysqli = connect_to_mysql();

  // read from the questions
  $result = $mysqli->query( "SELECT * FROM `$tableq`;" );
  while( $row = $result->fetch_array(MYSQLI_ASSOC) ) { 
    // grab relevant pieces
    $qid      = $row["question_id"];
    $question = $row["question"];
    $rating   = $row["rating"];

    // construct an array of questions and their ratings
    $questions[$qid] = $question;
    $ratings[$qid] = $rating;
  }

  // sort the questions by their rating
  arsort( $ratings );

  // read from the comments
  $result = $mysqli->query( "SELECT * FROM `$tablec`;" );
  while( $row = $result->fetch_array(MYSQLI_ASSOC) ) { 
    // grab the relevant pieces
    $qid     = $row["question_id"];
    $comment = $row["comment"];

    // construct a final 2D array of comments
    if( isset($comments[$qid]) ) { 
      array_push($comments[$qid], $comment);
    } else { 
      $comments[$qid] = array($comment);
    }
  }

  foreach( $ratings as $id => $rating ) { 
    $question = $questions[$id];

    echo "<div class=\"question\" id=\"q$id\">".PHP_EOL;
    echo "<span class=\"qrating\" id=\"r$id\">($rating)</span>".PHP_EOL;
    echo "<span class=\"qtext\" id=\"t$id\">$question</span>".PHP_EOL;

    echo "<br />";

    echo "<a class=\"feedbackLink\" id=\"f$id\" href=\"#\" ";
    echo "onclick=\"expand($id)\">Provide Feedback</a>".PHP_EOL;

    echo "<br />".PHP_EOL;

    echo "<a class=\"feedbackLink\" href=\"#\"";
    echo "onclick=\"showComments($id)\">Show/Hide Comments</a>".PHP_EOL;

    echo "<div style=\"display:none;\" class=\"comments\" id=\"z$id\">".PHP_EOL;
    if( isset($comments[$id]) ) { 
      $qcomments = $comments[$id];
      foreach( $qcomments as $qcid => $qcomment ) { 
        echo "<p>$qcomment</p>".PHP_EOL;
      }
    } else { 
      echo "<p>No comments.</p>".PHP_EOL;
    }
    echo "</div>".PHP_EOL;

    echo "</div>".PHP_EOL;
  }

  $result->close();
  $mysqli->close();
}

function connect_to_mysql() { 

  // hook into the MySQL database.
  global $db_host, $db_user, $db_pass, $db_name;
  global $tableq, $tablec, $tableu, $tablel,$tablea;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) { 
    return null;
  }

  # Create the Questions table. This is the database of student
  # research questions.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tableq` (
      `question_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `question` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      `rating` INT(10) SIGNED NOT NULL DEFAULT 0,
      PRIMARY KEY (`question_id`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  # Create the Comments table. This is the database of feedback.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablec` (
      `comment_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      PRIMARY KEY (`comment_id`),
      `question_id` INT(10) UNSIGNED NOT NULL,
      `comment` VARCHAR(1000) CHARACTER SET 'utf8' NOT NULL,
      CONSTRAINT `fk_questionID` FOREIGN KEY (`question_id`)
        REFERENCES `$db_name`.`$tableq`(`question_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  # Create the Known Users table. This is the record of all known, accepted
  # users and their user levels.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tableu` (
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `hash` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      `level` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `name` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`user`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  # Create the Log table. This is the log of access attempts.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablel` (
      `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `date` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `result` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`id`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  # Create the Active Users table. This is the table of active users.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablea` (
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `hash` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      `timestamp` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`user`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  return $mysqli;
}

?>
