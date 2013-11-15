<?php

# This function generates the questions box on the index page.
function generate_questions_box() { 
  global $tableq, $tablec, $tablecr;
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
      $cid      = $row["comment_id"];
      $qid      = $row["question_id"];

      $comments[$qid][$cid]["text"] = $row["comment"];
      $comments[$qid][$cid]["user"] = $row["user"];
    }
    
    # load all replies
    $result_replies  = $mysqli->query("SELECT * FROM `$tablecr`;");
    while( $row = $result_replies->fetch_array(MYSQLI_ASSOC) ) { 
      $rid      = $row["reply_id"];
      $cid      = $row["comment_id"];

      $replies[$cid][$rid]["text"] = $row["reply"];
      $replies[$cid][$rid]["user"] = $row["user"];
    }

    # display questions w/ comments
    foreach( $ratings as $qid => $rating ) { 
      $question   = stripslashes($questions[$qid]);
      $user       = $users[$qid];
      $name       = get_fullname_from_user( $user );
      $mycomments = isset($comments[$qid]) ? $comments[$qid] : null;
    
      #### QUESTION BLOCK ####
      echo "<div id=\"question$qid\" class=\"question-box\">".PHP_EOL;
      if( is_moderator() ) { 

        ## Question data
        #echo "<span class=\"question-id\">[ID: $qid]</span>".PHP_EOL;
        echo "<span class=\"question-rating\">(RATED: $rating)</span>".PHP_EOL;

        ## Remove Question button
        echo "<button onClick=\"remove_question('$qid');return false;\">";
        echo "Remove Question</button>".PHP_EOL;
        echo "<button onClick=\"change_rating('$qid');return false;\">";
        echo "Change Rating</button>".PHP_EOL;

        echo "<br />".PHP_EOL;

      }

      ## Question asker && email
      echo "<span class=\"question-asker\">Asked by <strong>";
      if( is_moderator() ) { 
        echo "<a href=\"mailto:$user@gmu.edu\">$name</a>";
      } else { 
        echo "FAKENAME";
      }
      echo "</strong>:</span>".PHP_EOL;
      echo "<br /><br />".PHP_EOL;

      echo "<span class=\"question-text\">$question</span>".PHP_EOL;
      echo "<br /><br />".PHP_EOL;

      #### LINK BLOCK ####
      echo "<a href=\"#\" onClick=\"toggle_display('comment-list$qid'); return false;\">Toggle Comments (";
      echo (($mycomments!=null)?count($mycomments):"0").")</a>".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "<a href=\"#\" onClick=\"toggle_display('make-comment$qid'); return false;\">Provide feedback.</a>";
      #### LINK BLOCK ####

      #### FEEDBACK BLOCK ####
      echo "<div id=\"make-comment$qid\" class=\"feedback\" style=\"display:none;\">".PHP_EOL;
      echo "<textarea class=\"feedback-text\" name=\"c\"></textarea>".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "Your name will be associated with any comments you make.".PHP_EOL;
      echo "<br />".PHP_EOL;
      echo "<input type=\"button\" value=\"Good Question\" name=\"g\" ".PHP_EOL;
      echo "onClick=\"submit_feedback($qid, 'g');\" />".PHP_EOL;
      echo "<input type=\"button\" value=\"Needs Work\" name=\"b\" ".PHP_EOL;
      echo "onClick=\"submit_feedback($qid, 'b');\" />".PHP_EOL;
      echo "<input type=\"button\" value=\"Cancel\" ";
      echo "onClick=\"hide_display('make-comment$qid');\" />".PHP_EOL;
      echo "</div>".PHP_EOL;
      #### FEEDBACK BLOCK ####

      #### COMMENT LIST BLOCK ####
      echo "<div id=\"comment-list$qid\" class=\"comment-list-box\" style=\"display:none;\">".PHP_EOL;
      if( $mycomments == null ) { 
        echo "<div class=\"comment-box\">";
        echo "<span class=\"comment-text\">None yet.</span>".PHP_EOL;
        echo "</div>";
      } else { 
        foreach( $mycomments as $cid => $comment ) { 

          $text = stripslashes($comment["text"]);
          $user = $comment["user"];
          $name = get_fullname_from_user( $user );
          $myreplies = isset($replies[$cid]) ? $replies[$cid] : null;

          #### COMMENT BOX BLOCK ####
          echo "<div id=\"comment$cid\" class=\"comment-box\">".PHP_EOL;

          if( is_moderator() ) { 
            echo "<span class=\"comment-id\">[ID: $cid]</span>".PHP_EOL;
            echo "<button onClick=\"remove_comment('$cid');return false;\">";
            echo "Remove Comment</button>".PHP_EOL;
            echo "<br />".PHP_EOL;
          }

          echo "<span class=\"comment-author\"><strong>";
          if( is_moderator() ) {
            echo "<a href=\"mailto:$user@gmu.edu\">$name</a>";
          } else {
            echo "FAKENAME";
          }
          echo "</strong> says:</span>".PHP_EOL;

          echo "<span class=\"comment-text\">$text</span>".PHP_EOL;

          echo "<br />".PHP_EOL;

          #### LINK BLOCK ####
          echo "<a href=\"#\"
          onClick=\"toggle_display('comment-replies$cid'); return false;\">Toggle Replies (";
          echo (($myreplies!=null)?count($myreplies):"0").")</a>".PHP_EOL;
          echo "<br />".PHP_EOL;
          echo "<a href=\"#\" onClick=\"toggle_display('make-reply$cid'); return false;\">Reply.</a>";
          #### LINK BLOCK ####

          #### FEEDBACK BLOCK ####
          echo "<div id=\"make-reply$cid\" class=\"feedback\" style=\"display:none;\">".PHP_EOL;
          echo "<textarea class=\"feedback-text\" name=\"reply\"></textarea>".PHP_EOL;
          echo "<br />".PHP_EOL;
          echo "Your name will be associated with any comments you make.".PHP_EOL;
          echo "<br />".PHP_EOL;
          echo "<input type=\"button\" value=\"Reply\" name=\"reply\" ".PHP_EOL;
          echo "onClick=\"submit_reply($cid);\" />".PHP_EOL;
          echo "<input type=\"button\" value=\"Cancel\" ";
          echo "onClick=\"hide_display('make-reply$cid');\" />".PHP_EOL;
          echo "</div>".PHP_EOL;
          #### FEEDBACK BLOCK ####

          #### REPLIES BOX BLOCK ####
          echo "<div id=\"comment-replies$cid\" class=\"replies-box\" style=\"display:none;\">".PHP_EOL;

          if( $myreplies == null ) { 
            echo "<div class=\"reply\">";
            echo "<span class=\"reply-text\">No replies.</span>";
            echo "</div>".PHP_EOL;
          } else {

            foreach( $myreplies as $rid => $reply ) { 

              $text = stripslashes($reply["text"]);
              $user = $reply["user"];
              $name = get_fullname_from_user( $user );

              echo "<div id=\"reply$rid\" class=\"reply\">".PHP_EOL;
              if( is_moderator() ) {
                echo "<button onClick=\"remove_reply('$rid');return false;\">";
                echo "Remove</button>".PHP_EOL;
              }
              echo "<span class=\"reply-author\"><strong>".PHP_EOL;
              if( is_moderator() ) {
                echo "<a href=\"mailto:$user@gmu.edu\">$name</a>".PHP_EOL;
              } else {
                echo "FAKENAME";
              }
              echo "</strong> says:</span>".PHP_EOL;
              echo "<span class=\"reply-text\">$text</span>".PHP_EOL;
              echo "</div>".PHP_EOL;
            }

          }


          echo "</div>".PHP_EOL;
          #### REPLIES BOX BLOCK ####

          echo "</div>".PHP_EOL;
          #### COMMENT BOX BLOCK ####
        }
      }
      echo "</div>".PHP_EOL;
      #### COMMENT LIST BLOCK ####

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
