<?php

require "lib.php";

if( isset($_COOKIE["user"]) ) { 
  logout_user($_COOKIE["user"]);
}

header('Location: ./index.php');

?>
