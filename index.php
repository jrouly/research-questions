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

<div>
<form id="feedback-form" name="feedback-form" method="post" action="feedback_processor.php">
<input type="hidden" id="action" name="action" value="" />
<input type="hidden" id="identifier" name="identifier" value="" />

<input type="hidden" id="r"   name="r"   value="" />
<input type="hidden" id="f"   name="f"   value="" />
<?php generate_questions_box(); ?>
</form>
</div>

<?php 
  require "bottom";
?>
