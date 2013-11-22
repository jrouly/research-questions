<?php 
  require "lib.php";

  if( ! is_logged_in() ) { 
    header('Location: login.php');
  }

  require "static/top.part";
?>

<div>
<form id="feedback-form" name="feedback-form" method="post" action="index_processor.php">
<input type="hidden" id="action" name="action" value="" />
<input type="hidden" id="identifier" name="identifier" value="" />

<input type="hidden" id="r"   name="r"   value="" />
<input type="hidden" id="f"   name="f"   value="" />
<?php generate_questions_box(); ?>
</form>
<p> <a href="#top">Return to top.</a></p>
</div>

<?php 
  require "static/bottom.part";
?>
