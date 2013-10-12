/*
 * This function simply toggles the display of
 * the given block.
 */
function toggle_display( id ) { 

  var block = document.getElementById(id);
  if( block.style.display=="none" ) { 
    block.style.display="block";
  } else { 
    block.style.display="none";
  }

}

/*
 * This function simply turns off the display of
 * the given block.
 */
function hide_display( id ) { 
  var block = document.getElementById(id);
  block.style.display="none";
}
