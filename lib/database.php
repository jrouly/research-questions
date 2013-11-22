<?php

# Clean up any input for display.
function sanitize($input) { 
  $input = htmlspecialchars( $input );
  $input = ereg_replace( "[\n+]|[\r+]", " ", $input);
  $input = addslashes( $input );
  $input = trim( $input );
  return $input;
}

# Generate a standard format timestamp.
function timestamp() { 
  return date('Y-m-d H:i:s');
}

function connect_to_mysql() {

  # hook into the MySQL database.
  global $db_host, $db_user, $db_pass, $db_name;
  global $tableq, $tablec, $tablecr, $tableu, $tablel,$tablea,$tableuh;

  $mysqli = new PDO(
    "mysql:host=$db_host;dbname=$db_name",
    $db_user,
    $db_pass
  );

  # Create the Known Users table. This is the record of all known, accepted
  # users and their user levels.
  $query = $mysqli->prepare(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tableu` (
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `name` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL,
      `section` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `role` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `firstlogin` INT(1) UNSIGNED NOT NULL DEFAULT '1',
      PRIMARY KEY (`user`)
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");
  $query->execute();

  # Create the Questions table. This is the database of student
  # research questions.
  $query = $mysqli->prepare(
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
  $query->execute();

  # Create the Comments table. This is the database of feedback.
  $query = $mysqli->prepare(
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
  $query->execute();

  # Create the Comment Replies table. This is the extended database of feedback.
  $query = $mysqli->prepare(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablecr` (
      `reply_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `comment_id` INT(10) UNSIGNED NOT NULL,
      `reply` VARCHAR(1000) CHARACTER SET 'utf8' NOT NULL,
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`reply_id`),
      CONSTRAINT `fk_commentID` FOREIGN KEY (`comment_id`)
        REFERENCES `$db_name`.`$tablec`(`comment_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_user_reply` FOREIGN KEY (`user`)
        REFERENCES `$db_name`.`$tableu`(`user`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");
  $query->execute();

  # Create the Log table. This is the log of access attempts.
  $query = $mysqli->prepare(
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
  $query->execute();

  # Create the Active Users table. This is the table of active users.
  $query = $mysqli->prepare(
    "CREATE TABLE IF NOT EXISTS `$db_name`.`$tablea` (
      `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      `hash` VARCHAR(500) CHARACTER SET 'utf8' NOT NULL,
      `timestamp` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
      PRIMARY KEY (`id`),
      CONSTRAINT `fk_user_active` FOREIGN KEY (`user`)
        REFERENCES `$db_name`.`$tableu`(`user`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) 
    ENGINE = InnoDB 
    DEFAULT CHARACTER SET = latin1
    AUTO_INCREMENT=1;");
  $query->execute();

  return $mysqli;
}

?>
