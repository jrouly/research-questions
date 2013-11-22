<?php 
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  require "static/top.part";
?>

<h1>Submit a Research Question</h1>

<p>
Use this form to submit your tentative research question.
</p>
<p>
Only include the question text, there is no need to include your name or
other information!
</p>

<div>
<form id="question-submit-form" name="question-submit-form" method="post" action="">
<textarea class="big-textarea" name="question" id="question"></textarea>
<br /><br />
<input type="submit" name="submit" id="submit" value="Submit Question" />
<br /><br />

<?php process_form(); ?>

</form>
</div>

<?php 
  require "static/bottom.part";

function process_form() { 
  if( isset($_POST["submit"]) ) {
    
    $question = sanitize($_POST["question"]);
    $user = get_username();

    if( is_string($question) && $question != "" ) { 

      global $db_name,$tableq,$tableu;
      $mysqli = connect_to_mysql();

      # Insert question into database if possible.
      $query = $mysqli->prepare(
        "INSERT INTO `$db_name`.`$tableq`(`question`,`user`)".
        "VALUES(:question, :user);");
      $query->bindValue(':question', $question);
      $query->bindValue(':user', $user);

      if( ! $query->execute() ) { 
        echo "Error communicating with database. Please contact the webmaster.<br />".PHP_EOL;
      }

      # Update user's firstlogin value.
      $query = $mysqli->prepare(
        "UPDATE `$db_name`.`$tableu` SET `firstlogin`='0' WHERE `user`=:user;");
      $query->bindValue(':user', $user);
      if( ! $query->execute() ) { 
        echo "Error communicating with database. Please contact the webmaster.<br />".PHP_EOL;
      } else { 
        echo '<script type="text/javascript">window.location = "index.php";</script>';
        echo "Question submitted!".PHP_EOL;
        echo "See it <a href=\"index.php\">here</a>.".PHP_EOL;
      }

    } else { 
      echo "Please do not submit empty questions.".PHP_EOL;
    }

  }
}

?>
