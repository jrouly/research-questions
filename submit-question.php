<?php 
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  require "top";
?>

<h1>Submit a Research Question</h1>

<p>
Please use this form to submit your tentative research question.
</p>

<div>
<form id="question-submit-form" name="question-submit-form" method="post" action="">
</form>
</div>

<?php 
  require "bottom";
?>
