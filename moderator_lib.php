<?php

require "lib.php";

function list_registered_users() { 
  $mysqli = connect_to_mysql();
  

  $mysqli->close();
}

function process_register_user() { 
  
  if( isset($_POST["reg-user-submit"]) ) { 
    echo "stuff";
  }

}

function process_modify_user() { 
  
  if( isset($_POST["mod-user-submit"]) ) { 
    echo "stuff";
  }

}

function process_moderate_questions() { 
  
  if( isset($_POST["mod-question-submit"]) ) { 
    echo "stuff";
  }

}

?>
