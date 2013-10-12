<?php
  require "lib.php";
  
  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  // connect to the SQL database.
  $mysqli = connect_to_mysql();

  // if the form is properly submitted, with a comment and either a 
  // good or a bad selection, proceed.
  if( (isset($_POST["g"]) || isset($_POST["b"])) && isset($_POST["c"]) ) { 

    // grab POST data
    $rating_adj = isset($_POST["g"]) ? 1 : -1;
    $comment = $_POST["c"];
    $question_id = $_POST["qid"];

    // clean up the user's comment/feedback
    $comment = htmlspecialchars( $comment );
    $comment = $mysqli->real_escape_string( $comment );
    $comment = addslashes( $comment );
    $comment = trim( $comment );

    if( 
      // check that the question id is valid
      $question_id > 0 &&
      // make sure the comment is valid
      is_string($comment) && $comment != "" ) { 

      $t_questions = "questions";
      $t_comments = "comments";

      $mysqli->query("INSERT INTO $t_comments (question_id,comment)
                          VALUES('$question_id','$comment');")
                          or die(mysql_error());
      $mysqli->query("UPDATE $t_questions
                          SET rating = rating + $rating_adj
                          WHERE question_id = $question_id;")
                          or die(mysql_error());
    }
  }

  // disconnect from the database and return to the homepage.
  $mysqli->close();
  header('Location: index.php');

?>
