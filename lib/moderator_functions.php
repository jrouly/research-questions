<?php

function list_registered_users() { 
  $mysqli = connect_to_mysql();
  global $db_name,$tableu;

  echo "<table>".PHP_EOL;
  # Header row.
  echo "<tr>".PHP_EOL;
  echo "  <th>NetID</th>".PHP_EOL;
  echo "  <th>Full Name</th>".PHP_EOL;
  echo "  <th>Level</th>".PHP_EOL;
  echo "  <th>Modify</th>".PHP_EOL;
  echo "</tr>".PHP_EOL;

  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tableu`;");
  $query->execute();
  while( $row = $query->fetch(PDO::FETCH_ASSOC) ) { 
    echo "<tr>".PHP_EOL;
    echo "  <td><a href=\"mailto:".$row['user']."@gmu.edu\">".$row['user']."</a></td>".PHP_EOL;
    echo "  <td>".$row['name']."</td>".PHP_EOL;
    echo "  <td>".$row['role']."</td>".PHP_EOL;
    echo "  <td>modify</td>".PHP_EOL;
    echo "</tr>".PHP_EOL;
  }
  echo "</table>".PHP_EOL;
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

  $query = $mysqli->prepare("SELECT * FROM `$db_name`.`$tablel`;");
  $query->execute();

  while( $row = $query->fetch(PDO::FETCH_ASSOC) ) { 
    echo "<tr>".PHP_EOL;
    echo "  <td>".$row['id']."</td>".PHP_EOL;
    echo "  <td>".$row['user']."</td>".PHP_EOL;
    echo "  <td>".$row['date']."</td>".PHP_EOL;
    echo "  <td>".$row['result']."</td>".PHP_EOL;
    echo "</tr>".PHP_EOL;
  }
  echo "</table>".PHP_EOL;
}

function process_register_user() { 

  if( isset($_POST["reg-user-submit"]) ) { 
    $users = $_POST["user"];
    $names = $_POST["name"];
    $roles = $_POST["role"];
    $sections = $_POST["section"];

    foreach($users as $key=>$value) { 
      $user = sanitize($users[$key]);
      $name = sanitize($names[$key]);
      $role = sanitize($roles[$key]);
      $section = sanitize($sections[$key]);

      if( is_string($user) && $user != "" && 
          is_string($name) && $name != "" &&
          is_string($role) && $role != "" &&
          is_string($section) && $section != "" ) {

        # if everything is filled in, then register this user
        if( is_user_registered( $user ) ) { 
          echo "Username $user already exists. No action taken.<br/>".PHP_EOL;
        } else { 
          register_user( $user, $name, $section, $role );
          echo "User $user ($name) registered.<br />";
        }
      } else {
        echo "Please make sure to fill out every field. No action taken.<br />".PHP_EOL;
      }
    }

    echo "Done.<br />".PHP_EOL;
  }

}

function process_modify_user() { 
  
  if( isset($_POST["mod-user-submit"]) ) { 
    echo "stuff";
  }

}

?>
