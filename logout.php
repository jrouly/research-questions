<?php

require "lib.php";

if( isset($_COOKIE["hash"]) ) { 
  logout_user($_COOKIE["hash"]);
}

header('Location: ./index.php');

?>
