<?php 

require "config.php";
global $salt;
global $db_host,$db_user,$db_pass,$db_name;
global $tableq,$tablec,$tablea,$tablel,$tableu;

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

# Remove the user from the mysql database and set an empty cookie.
function logout_user($user) { 
  $mysqli = connect_to_mysql();
  
  # generate the stuff we're inserting
  $hash = sanitize($user);
  $stamp = date('Y-m-d H:i:s');

  global $db_name,$tableu,$tablel;

  # insert a user into the active user table
  $mysqli->query("DELETE FROM `$db_name`.`$tableu` WHERE `hash`='$hash';");

  # generate user cookie
  setcookie("user", "", time()-3600);

  $mysqli->close();
}

# Hash the username, add them to the mysql db, and set their cookie.
function login_user($user) { 
  $mysqli = connect_to_mysql();

  # generate the stuff we're inserting
  $user = sanitize($user);
  $hash = salted_hash($user);
  $stamp = timestamp();

  global $db_name,$tableu,$tablel;

  # insert a user into the active user table
  $mysqli->query("INSERT INTO `$db_name`.`$tableu`(`user`,`hash`,`timestamp`)
                  VALUES ('$user', '$hash','$stamp');");

  # generate user cookie
  setcookie("user", $hash, time()+3600);

  $mysqli->close();
}

# Log an access attempt.
function log_access_attempt($user) { 
  $mysqli = connect_to_mysql();

  # clean user
  $user = sanitize($user);
  $stamp = timestamp();

  global $db_name,$tablel;
  $mysqli->query("INSERT INTO `$db_name`.`$tableu`(`user`,`date`)
                  VALUES ('$user','$stamp');");

  $mysqli->close();
}

function is_logged_in() { 
  $mysqli = connect_to_mysql();
  $output = False;

  if(isset($_COOKIE["user"])) { 
    $hash = $_COOKIE["user"];
    $hash = sanitize($hash);

    global $db_name,$tableu;
    $result = $mysqli->query("SELECT * FROM `$db_name`.`$tableu` WHERE `hash`='$hash';");
    $rows = $result->num_rows;
    
    $output = ($rows == 1);
  }
  
  $mysqli->close();
  return $output;
}

function register_admin( $user ) { 
  $mysqli = connect_to_mysql();

  $user = sanitize($user);
  $hash = salted_hash($user);

  global $db_name, $tablea;
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tablea`
                            WHERE `user`='$user';");
  $rows = $result->num_rows;
  if( $rows == 0 ) { 
    $mysqli->query("INSERT INTO `$db_name`.`$tablea`(`user`,`hash`)
                    VALUES ('$user','$hash');");
  }
  $mysqli->close();
}

function is_admin( $user ) { 
  $hash = sanitize($user);

  // initial MYSQL connection
  $mysqli = connect_to_mysql();

  // see if this user is present in db
  global $db_name,$tablea;
  $result = $mysqli->query("SELECT * FROM `$db_name`.`$tablea` WHERE `hash`='$hash';");
  $rows = $result->num_rows;
  $mysqli->close();

  return $rows == 1;
}

function pull_questions() { 
  // initial MYSQL connection
  $mysqli = connect_to_mysql();

  // read from the questions
  global $tableq, $tablec;
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

    echo '<div class="question" id="q'.$id.'">' . PHP_EOL;
    echo '<span class="qrating" id="r'.$id.'">';
    echo '('.$rating.')';
    echo '</span>' . PHP_EOL;

    echo '<span class="qtext" id="t'.$id.'">';
    echo $question;
    echo '</span>' . PHP_EOL;

    echo '<br />';

    echo '<a class="feedbackLink" id="f'.$id.'" href="#"
    onclick="expand('.$id.')">Provide Feedback</a>' . PHP_EOL;

    echo '<br />';

    echo '<a class="feedbackLink" href="#"
    onclick="showComments('.$id.')">Show/Hide Comments</a>' . PHP_EOL;

    echo '<div style="display:none;" class="comments" id="z'.$id.'">' . PHP_EOL;
    if( isset($comments[$id]) ) { 
      $qcomments = $comments[$id];
      foreach( $qcomments as $qcid => $qcomment ) { 
        echo '<p>' . $qcomment . '</p>' . PHP_EOL;
      }
    } else { 
      echo '<p>No comments.</p>' . PHP_EOL;
    }
    echo '</div>' . PHP_EOL;

    echo "</div>" . PHP_EOL;
  }

  $result->close();
  $mysqli->close();

}

function connect_to_mysql() { 

  // hook into the MySQL database.
  global $db_host, $db_user, $db_pass, $db_name;
  global $tableq, $tablec, $tablea, $tablel,$tableu;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) { 
    return null;
  }


  return $mysqli;
}

function mysql_create_tables() { 

  // generate table if need be
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

  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablea` (
      `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `hash` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`id`)
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
      PRIMARY KEY (`id`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  # Create the User table. This is the table of active users.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tableu` (
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `hash` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      `timestamp` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`user`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");
}

?>
