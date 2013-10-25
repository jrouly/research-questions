<?php 
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  require "top";
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
<textarea class="big-textarea"></textarea>
<br />
<input type="submit" name="submit" id="submit" value="Submit Question" />
<br /><br />

<?php process_form(); ?>

</form>
</div>

<?php 
  require "bottom";

function process_form() { 
  if( isset($_POST["submit"]) ) {
    #submit_question();
    echo "Question submitted!".PHP_EOL;
    echo "See it <a href=\"index.php\">here</a>.".PHP_EOL;
  }
}

?>
