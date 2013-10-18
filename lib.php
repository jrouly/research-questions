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



function generate_questions_box() { 
  global $tableq, $tablec;
  $mysqli = connect_to_mysql();

  # load all questions
  $result_questions = $mysqli->query("SELECT * FROM `$tableq`;");
  if( $result_questions->num_rows > 0 ) { 
    while( $row = $result_questions->fetch_array(MYSQLI_ASSOC) ) { 
      $qid = $row["question_id"];
      $users[$qid] = $row["user"];

      $ratings[$qid] = $row["rating"];
      $questions[$qid] = $row["question"];
    }

    # sort by rating
    arsort( $ratings );

    # load all comments
    $result_comments  = $mysqli->query("SELECT * FROM `$tablec`;");
    while( $row = $result_comments->fetch_array(MYSQLI_ASSOC) ) { 
      $cid     = $row["comment_id"];
      $qid     = $row["question_id"];

      $comments[$qid][$cid]["text"] = $row["comment"];
      $comments[$qid][$cid]["user"] = $row["user"];
    }

    # display questions w/ comments
    foreach( $ratings as $qid => $rating ) { 
      $question = $questions[$qid];
      $user     = $users[$qid];
      $qcomms   = isset($comments[$qid]) ? $comments[$qid] : null;

      #### QUESTION BLOCK ####
      echo "<div id=\"$qid\" class=\"question\">".PHP_EOL;
      if( is_moderator() ) { 
        echo "<span class=\"question-id\">[ID: $qid]</span>".PHP_EOL;
        echo "<span class=\"question-rating\">(RATED: $rating)</span>".PHP_EOL;
        echo "<span class=\"question-asker\">(BY: $user)</span>".PHP_EOL;
      }
      echo "<span class=\"question-text\">$question</span>".PHP_EOL;

      #### LINK BLOCK ####
      echo "<br />".PHP_EOL;
      echo "<a href=\"#\" onClick=\"toggle_display('c$qid');\">Toggle Comments (";
      echo (($qcomms!=null)?count($qcomms):"0").")</a>".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "<a href=\"#\" onClick=\"toggle_display('f$qid');\">Provide feedback.</a>";
      #### LINK BLOCK ####

      #### FEEDBACK BLOCK ####
      echo "<div id=\"f$qid\" class=\"feedback\" style=\"display:none;\">".PHP_EOL;
      echo "<textarea class=\"feedback-text\" name=\"c\"></textarea>".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "<input type=\"button\" value=\"Good Question\" name=\"g\" ".PHP_EOL;
      echo "onClick=\"submit_form($qid, 'g');\" />".PHP_EOL;
      echo "<input type=\"button\" value=\"Needs Work\" name=\"b\" ".PHP_EOL;
      echo "onClick=\"submit_form($qid, 'b');\" />".PHP_EOL;
      echo "<input type=\"button\" value=\"Cancel\" ";
      echo "onClick=\"hide_display('f$qid');\" />".PHP_EOL;
      echo "</div>".PHP_EOL;
      #### FEEDBACK BLOCK ####

      #### COMMENT BLOCK ####
      echo "<div id=\"c$qid\" class=\"comments\" style=\"display:none;\">".PHP_EOL;
      if( $qcomms == null ) { 
        echo "<span class=\"comment-text\">None yet.</span>".PHP_EOL;
      } else { 
        foreach( $qcomms as $cid => $comment ) { 
          $text    = $comment["text"];
          $author  = $comment["user"];
          
          if( is_moderator() ) { 
            echo "<span class=\"comment-id\">[ID: $cid]</span>".PHP_EOL;
          }
          echo "<span class=\"comment-text\">$text</span>".PHP_EOL;
          if( is_moderator() ) { 
            echo "<span class=\"comment-author\">(BY: $author)</span>".PHP_EOL;
          }
          echo "<br />".PHP_EOL;
        }
      }
      echo "</div>".PHP_EOL;
      #### COMMENT BLOCK ####

      echo "</div>".PHP_EOL;
      #### QUESTION BLOCK ####
      
    }
  } else { 
    echo "<div class=\"question\"><span class=\"question-text\">".PHP_EOL;
    echo "No questions here. It's so lonely...</span></div>".PHP_EOL;
  }
  $mysqli->close();
}



function connect_to_mysql() { 

  # hook into the MySQL database.
  global $db_host, $db_user, $db_pass, $db_name;
  global $tableq, $tablec, $tableu, $tablel,$tablea;
  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
  if( $mysqli->connect_errno ) { 
    return null;
  }

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

  # Create the Questions table. This is the database of student
  # research questions.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tableq` (
      `question_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      PRIMARY KEY (`question_id`),
      `rating` INT(10) SIGNED NOT NULL DEFAULT 0,
      `question` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      CONSTRAINT `fk_user_question` FOREIGN KEY (`user`)
        REFERENCES `$db_name`.`$tableu`(`user`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");

  # Create the Comments table. This is the database of feedback.
  $mysqli->query(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablec` (
      `comment_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `question_id` INT(10) UNSIGNED NOT NULL,
      `comment` VARCHAR(1000) CHARACTER SET 'utf8' NOT NULL,
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`comment_id`),
      CONSTRAINT `fk_questionID` FOREIGN KEY (`question_id`)
        REFERENCES `$db_name`.`$tableq`(`question_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_user_comment` FOREIGN KEY (`user`)
        REFERENCES `$db_name`.`$tableu`(`user`)
        ON DELETE CASCADE ON UPDATE CASCADE
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
