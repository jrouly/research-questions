<?php

require "lib.php";

if( isset($_COOKIE["hash"]) ) { 
  logout_hash($_COOKIE["hash"]);
}

header('Location: ./index.php');

?>
