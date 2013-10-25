<?php
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  # if the form is properly submitted, with a comment and either a 
  # good or a bad selection, proceed.
  if(isset($_POST["qid"])) { 
  
    # connect to the SQL database.
    $mysqli = connect_to_mysql();
    $question_id = $_POST["qid"];

    # If this question is marked for feedback, submit feedback.
    if( isset($_POST["r"]) && isset($_POST["f"]) &&
        $_POST["r"] != "" && $_POST["f"] != "" ) { 

      # grab POST data
      $rating_adj = ($_POST["r"] == "b") ? -1 : 1;
      $comment = $_POST["f"];

      # clean up the user's comment/feedback
      $comment = htmlspecialchars( $comment );
      $comment = $mysqli->real_escape_string( $comment );
      $comment = addslashes( $comment );
      $comment = trim( $comment );

      if( # check that the question id is valid
          $question_id > 0 &&
          # make sure the comment is valid
          is_string($comment) && $comment != "" ) { 

        $user = get_username_from_hash( $_COOKIE["hash"] );

        global $db_name,$tablec,$tableq;

        # insert a new comment into the tabelc
        $sql_insert = "INSERT INTO `$db_name`.`$tablec`" . 
                      "(`comment_id`, `question_id`, `comment`, `user`) " .
                      "VALUES(NULL, '$question_id', '$comment', '$user');";
        if( ! $mysqli->query( $sql_insert ) ) {
          echo "Erorr unregistered user. Please contact webmaster.";
          return;
        }

        # update the question rating in tableq
        $sql_update = "UPDATE `$db_name`.`$tableq` " .
                      "SET `rating` = rating + $rating_adj " .
                      "WHERE `question_id` = '$question_id';";
        if( ! $mysqli->query( $sql_update ) ) { 
          echo "Error unable to rate question. Please contact webmaster.";
          return;
        }
      }
    } # If this question was marked for removal, remove it.
    else if( isset($_POST["removal"]) ) { 
      if($_POST["removal"] == "question") { 
        global $db_name,$tablec,$tableq;
        $sql_delete = "DELETE FROM `$db_name`.`$tableq` WHERE `question_id`='$question_id';";
        if( ! $mysqli->query($sql_delete) ) {
          echo "Error unable to remove question. Please contact webmaster.";
          return;
        }
      }
    }

    # disconnect from the database
    $mysqli->close();
  }

  # return to the homepage.
  header('Location: index.php');

?>
