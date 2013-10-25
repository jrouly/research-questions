<?php
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }


  if(isset($_POST["action"])) { 

    global $db_name,$tablec,$tableq;
    $mysqli = connect_to_mysql();
    $action = $_POST["action"];
    $identifier = $_POST["identifier"];
    $identifier = sanitize($identifier);

    switch( $action ) { 

      # This use-case is the student adding a comment to a question.
      case "add-comment":
        if( !isset($_POST["r"]) || !isset($_POST["f"]) ||
            $_POST["r"] == "" || $_POST["f"] == "" ) { break; }
        
        # grab POST data
        $rating_adj = ($_POST["r"] == "b") ? -1 : 1;
        $comment = $_POST["f"];
        $comment = sanitize($comment);
        
        # Validate identifier and comment.
        if( $identifier > 0 && is_string($comment) && $comment != "" ) { 
          $user = get_username_from_hash( $_COOKIE["hash"] );

          # insert a new comment into the tabelc
          $sql_insert = "INSERT INTO `$db_name`.`$tablec`" . 
                        "(`comment_id`, `question_id`, `comment`, `user`) " .
                        "VALUES(NULL, '$identifier', '$comment', '$user');";
          if( ! $mysqli->query( $sql_insert ) ) {
            echo "Erorr unregistered user. Please contact webmaster.";
            return;
          }

          # update the question rating in tableq
          $sql_update = "UPDATE `$db_name`.`$tableq` " .
                        "SET `rating` = rating + $rating_adj " .
                        "WHERE `question_id` = '$identifier';";
          if( ! $mysqli->query( $sql_update ) ) { 
            echo "Error unable to rate question. Please contact webmaster.";
            return;
          }
        }
        break;

      case "remove-question":
        if( !is_moderator() ) { break; }

        $sql_delete = "DELETE FROM `$db_name`.`$tableq` WHERE `question_id`='$identifier';";
        if( ! $mysqli->query($sql_delete) ) {
          echo "Error unable to remove question. Please contact webmaster.";
          return;
        }

        break;

      case "remove-comment":
        if( !is_moderator() ) { break; }

        $sql_delete = "DELETE FROM `$db_name`.`$tablec` WHERE `comment_id`='$identifier';";
        if( ! $mysqli->query($sql_delete) ) { 
          echo "Error unable to remove comment. Please contact webmaster.";
          return;
        }

        break;

      default:
        break;

    }

    $mysqli->close();

  }

  # return to the homepage.
  header('Location: index.php');

?>
