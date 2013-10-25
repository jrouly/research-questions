<?php

function list_registered_users() { 
  $mysqli = connect_to_mysql();
  

  $mysqli->close();
}

function view_access_logs() { 
  $mysqli = connect_to_mysql();
  global $db_name,$tablel;

  echo "<table>".PHP_EOL;
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
  $output = "Nothing happened.";

  if( isset($_POST["reg-user-submit"]) ) { 
    $usernames = $_POST["username"];
    $realnames = $_POST["realname"];
    $levels = $_POST["level"];
    
    foreach($usernames as $key=>$value) { 
      $username = trim($usernames[$key]);
      $realname = trim($realnames[$key]);
      $level = trim($levels[$key]);

      if( is_string($username) && $username != "" && 
          is_string($realname) && $realname != "" &&
          is_string($level)    && $level != "" ) {

        # if everything is filled in, then register this user
        if( is_user_registered( $username ) ) { 
          echo "Username $username already exists. No action taken.<br/>".PHP_EOL;
        }
        register_user( $username, $level, $realname );
        $output = "User(s) registered.";
      }
    }

    echo $output.PHP_EOL;
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
