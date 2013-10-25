<?php

function list_registered_users() { 
  $mysqli = connect_to_mysql();
  

  $mysqli->close();
}

function view_access_logs() { 
  $mysqli = connect_to_mysql();
  global $db_name,$tablel;

  echo "<table cellspacing=\"0\" cellpadding=\"0\">".PHP_EOL;
  # Header row.
  echo "<tr>".PHP_EOL;
  echo "  <th>Entry</th>".PHP_EOL;
  echo "  <th>User</th>".PHP_EOL;
  echo "  <th>Date</th>".PHP_EOL;
  echo "  <th>Result</th>".PHP_EOL;
  echo "</tr>".PHP_EOL;

  $sql = "SELECT * FROM `$db_name`.`$tablel`;";
  $result = $mysqli->query( $sql );
  while( $row = $result->fetch_row() ) { 
    echo "<tr>".PHP_EOL;
    echo "  <td>$row[0]</td>".PHP_EOL;
    echo "  <td>$row[1]</td>".PHP_EOL;
    echo "  <td>$row[2]</td>".PHP_EOL;
    echo "  <td>$row[3]</td>".PHP_EOL;
    echo "</tr>".PHP_EOL;
  }
  echo "</table>".PHP_EOL;

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
