<?php

# This function generates the questions box on the index page.
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

    if( is_moderator() ) { 
      echo "<input type=\"hidden\" name=\"removal\" id=\"removal\" value=\"\" />".PHP_EOL;
    }

    # display questions w/ comments
    foreach( $ratings as $qid => $rating ) { 
      $question = stripslashes($questions[$qid]);
      $user     = $users[$qid];
      $qcomms   = isset($comments[$qid]) ? $comments[$qid] : null;
    
      #### QUESTION BLOCK ####
      echo "<div id=\"$qid\" class=\"question\">".PHP_EOL;
      if( is_moderator() ) { 
        echo "<button><a href=\"#\" onClick=\"remove_question('$qid');return false;\">";
        echo "Remove</a></button>".PHP_EOL;
        echo "<span class=\"question-id\">[ID: $qid]</span>".PHP_EOL;
        echo "<span class=\"question-rating\">(RATED: $rating)</span>".PHP_EOL;
        echo "<span class=\"question-asker\">(<strong>$user</strong>)</span>".PHP_EOL;
      }
      echo "<span class=\"question-text\">$question</span>".PHP_EOL;

      #### LINK BLOCK ####
      echo "<br />".PHP_EOL;
      echo "<a href=\"#\" onClick=\"toggle_display('c$qid'); return false;\">Toggle Comments (";
      echo (($qcomms!=null)?count($qcomms):"0").")</a>".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "<a href=\"#\" onClick=\"toggle_display('f$qid'); return false;\">Provide feedback.</a>";
      #### LINK BLOCK ####

      #### FEEDBACK BLOCK ####
      echo "<div id=\"f$qid\" class=\"feedback\" style=\"display:none;\">".PHP_EOL;
      echo "<textarea class=\"feedback-text\" name=\"c\"></textarea>".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "<input type=\"button\" value=\"Good Question\" name=\"g\" ".PHP_EOL;
      echo "onClick=\"submit_feedback($qid, 'g');\" />".PHP_EOL;
      echo "<input type=\"button\" value=\"Needs Work\" name=\"b\" ".PHP_EOL;
      echo "onClick=\"submit_feedback($qid, 'b');\" />".PHP_EOL;
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
          $text    = stripslashes($comment["text"]);
          $author  = $comment["user"];
          $name = get_fullname_from_user( $author );
          
          if( is_moderator() ) { 
            echo "<button><a href=\"#\">Remove</a></button>".PHP_EOL;
            echo "<span class=\"comment-email\"><em>";
            echo "<a href=\"mailto:$author@gmu.edu\">$author</a></em></span>".PHP_EOL;
            echo "<span class=\"comment-id\">[ID: $cid]</span>".PHP_EOL;
          }
          echo "<span class=\"comment-author\"><strong>$name</strong> says:</span>".PHP_EOL;
          echo "<span class=\"comment-text\">$text</span>".PHP_EOL;
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


?>
