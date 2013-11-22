<?php
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  if(isset($_POST["action"])) { 

    $mysqli = connect_to_mysql();
    $action = sanitize($_POST["action"]);
    $identifier = sanitize($_POST["identifier"]);

    switch( $action ) { 

      # This use-case is the student adding a comment to a question.
      case "add-comment":
        if( !isset($_POST["r"]) || !isset($_POST["f"]) ||
            $_POST["r"] == "" || $_POST["f"] == "" ) { break; }

        # grab POST data
        $rating_adj = ($_POST["r"] == "b") ? -1 : 1;
        $comment = sanitize( $_POST["f"] );

        # Validate identifier and comment.
        if( $identifier > 0 && is_string($comment) && $comment != "" ) { 
          $user = get_username_from_hash( $_COOKIE["hash"] );

          # insert a new comment into the tabelc
          $query = $mysqli->prepare(
            "INSERT INTO `$db_name`.`$tablec`" . 
            "(`comment_id`, `question_id`, `comment`, `user`) " .
            "VALUES(NULL, :identifier, :comment, :user);");
          $query->bindValue(':identifier', $identifier);
          $query->bindValue(':comment', $comment);
          $query->bindValue(':user', $user);

          if( ! $query->execute() ) {
            echo "Erorr unregistered user. Please contact webmaster.";
            break;
          }

          # update the question rating in tableq
          $query = $mysqli->prepare(
            "UPDATE `$db_name`.`$tableq` " .
            "SET `rating` = rating + :ratingadj " .
            "WHERE `question_id` = :identifier;");
          $query->bindValue(':ratingadj', $rating_adj);
          $query->bindValue(':identifier', $identifier);

          if( ! $query->execute() ) {
            echo "Error unable to rate question. Please contact webmaster.";
            break;
          }
        }

        break;

      # This use-case is a moderator removing an existing question.
      case "remove-question":
        if( !is_moderator() ) { break; }

        $query = $mysqli->prepare("DELETE FROM `$db_name`.`$tableq` WHERE
          `question_id`=:identifier;");
        $query->bindValue(':identifier', $identifier);

        if( ! $query->execute() ) {
          echo "Error unable to remove question. Please contact webmaster.";
          break;
        }

        break;

      # This use-case is a moderator removing an unwanted comment.
      case "remove-comment":
        if( !is_moderator() ) { break; }

        $query = $mysqli->prepare("DELETE FROM `$db_name`.`$tablec` WHERE
          `comment_id`=:identifier;");
        $query->bindValue(':identifier', $identifier);

        if( ! $query->execute() ) {
          echo "Error unable to remove comment. Please contact webmaster.";
          break;
        }

        break;

      # This use-case is a moderator manually updating the rating on a question.
      case "change-rating":
        if( !is_moderator() ) { break; }

        $new_rating = sanitize( $_POST["r"] );

        $query = $mysqli->prepare("UPDATE `$db_name`.`$tableq` SET
          `rating`=:newrating WHERE `question_id`=:identifier;");
        $query->bindValue(':new_rating', $new_rating);
        $query->bindValue(':identifier', $identifier);

        if( ! $query->execute() ) {
          echo "Error unable to change rating. Please contact webmaster.";
          break;
        }

        break;

      # This use-case is the student replying to a comment.
      case "add-reply":
        if( !isset($_POST["f"]) || $_POST["f"] == "" ) { break; }
        
        # grab POST data
        $reply = sanitize($_POST["f"]);
        
        # Validate identifier and reply.
        if( $identifier > 0 && is_string($reply) && $reply != "" ) { 
          $user = get_username_from_hash( $_COOKIE["hash"] );

          # insert a new comment into the tabelc
          $query = $mysqli->prepare(
            "INSERT INTO `$db_name`.`$tablecr`" . 
            "(`reply_id`, `comment_id`, `reply`, `user`) " .
            "VALUES(NULL, :identifier, :reply, :user);");
          $query->bindValue(':identifier', $identifier);
          $query->bindValue(':reply', $reply);
          $query->bindValue(':user', $user);

          if( ! $query->execute() ) {
            echo "Erorr unregistered user. Please contact webmaster.";
            break;
          }
        }

        break;

      # This use-case is a moderator removing an unwanted reply.
      case "remove-reply":
        if( !is_moderator() ) { break; }

        $query = $mysqli->prepare(
          "DELETE FROM `$db_name`.`$tablecr` WHERE `reply_id`=:identifier;");
        $query->bindValue(':identifier', $identifier);

        if( ! $query->execute() ) {
          echo "Error unable to remove reply. Please contact webmaster.";
          break;
        }

        break;

      default:
        break;

    }

  }

  # return to the homepage.
  header('Location: index.php');

?>
