<?php 
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

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
<?php generate_student_view(); ?>
</form>
</div>

<?php 
  require "bottom";
?>
