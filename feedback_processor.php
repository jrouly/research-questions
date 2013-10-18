<?php
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  // if the form is properly submitted, with a comment and either a 
  // good or a bad selection, proceed.
  if(isset($_POST["qid"]) && isset($_POST["r"]) && isset($_POST["f"]) ) { 

    // connect to the SQL database.
    $mysqli = connect_to_mysql();

    // grab POST data
    $rating_adj = ($_POST["r"] == "b") ? -1 : 1;
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

      $user = get_username_from_hash( $_COOKIE["user"] );

      global $db_name,$tablec,$tableq;

      // insert a new comment into the tabelc
      $sql_insert = "INSERT INTO `$db_name`.`$tablec`" . 
                    "(`comment_id`, `question_id`, `comment`, `user`) " .
                    "VALUES(NULL, '$question_id', '$comment', '$user');";
      if( ! $mysqli->query( $sql_insert ) ) {
        echo "Erorr unregistered user. Please contact webmaster.";
        return;
      }

      // update the question rating in tableq
      $sql_update = "UPDATE `$db_name`.`$tableq` " .
                    "SET `rating` = rating + $rating_adj " .
                    "WHERE `question_id` = '$question_id';";
      if( ! $mysqli->query( $sql_update ) ) { 
        echo "Error unable to rate question. Please contact webmaster.";
        return;
      }
    }

    // disconnect from the database
    $mysqli->close();
  }

  // return to the homepage.
  header('Location: index.php');

?>
