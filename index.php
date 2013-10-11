<?php 
  require "lib.php";
  if( is_logged_in() ) { 
    require "top";
?>

<h1>Research Questions</h1>

<p>
Please provide feedback on these questions.
</p>

<br />

<div id="question_frame">
<form name="questionForm" method="post" action="feedback_processor.php">
<input type="hidden" id="qid" name="qid" value="" />
<?php pull_questions(); ?>
</form>
</div>

<?php 
    require "bottom";
  } else { 
    header('Location: login.php');
  }
?>
