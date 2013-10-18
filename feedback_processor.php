<?php
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  // connect to the SQL database.
  $mysqli = connect_to_mysql();

  // if the form is properly submitted, with a comment and either a 
  // good or a bad selection, proceed.
#  if( (isset($_POST["g"]) || isset($_POST["b"])) && isset($_POST["c"]) ) { 
  if(isset($_POST["qid"]) && isset($_POST["r"]) && isset($_POST["f"]) ) { 

    // grab POST data
    $rating_adj = ($_POST["r"] > 0) ? 1 : -1;
    $comment = $_POST["f"];
    $question_id = $_POST["qid"];

    // clean up the user's comment/feedback
    $comment = htmlspecialchars( $comment );
    $comment = $mysqli->real_escape_string( $comment );
    $comment = addslashes( $comment );
    $comment = trim( $comment );

    if( // check that the question id is valid
        $question_id > 0 &&
        // make sure the comment is valid
        is_string($comment) && $comment != "" ) { 

      $user = "plcaeholder";
      global $db_name,$tablec,$tableq;

      $sql_insert = "INSERT INTO `$db_name`.`$tablec`" + 
                    "(`comment_id`, `question_id`, `comment`, `user`) " +
                    "VALUES(NULL, '$question_id', '$comment', '$user');";
      if( ! $mysqli->query( $sql_insert ) ) {
        echo "Erorr unregistered user. Please contact webmaster.";
        return;
      }

      $sql_update = "UPDATE `$db_name`.`$tableq` " +
                    "SET `rating` = rating + $rating_adj " +
                    "WHERE `question_id` = '$question_id';";
      if( ! $mysqli->query( $sql_update ) ) { 
        echo "Error unable to rate question. Please contact webmaster.";
        return;
      }
    }
  }

  // disconnect from the database and return to the homepage.
  $mysqli->close();
  header('Location: index.php');

?>
