/*
 * This function is intended to remove old comment
* boxes from the page. Cleans everything up.
 */
function removeOld() { 
  var old = document.getElementsByClassName("comment");
  while( old[0] ) { 
    old[0].parentNode.removeChild( old[0] );
  }
}

/*
 * This function is intended to open up the comment
 * submission box for a particular ID question. Will
 * first remove any old boxes, then generate the
 * submission field for this question, then add it into
 * the HTML in the appropriate location.
 */
function expand( id ) { 

  // remove any old input forms
  removeOld();

  var qid = document.getElementById("qid");
  qid.value = id;

  // i don't know how else to create breaks in JS
  var break1 = document.createElement("br");
  break1.className = "comment";
  var break2 = document.createElement("br");
  break2.className = "comment";

  // create a new input space
  var newinput = document.createElement("textarea");
  newinput.className = "comment commentText";
  newinput.name      = "c";
  newinput.id        = "c" + id;

  // create the "good" button
  var newgood = document.createElement("input");
  newgood.className = "comment commentButton";
  newgood.name = "g";
  newgood.type = "submit";
  newgood.value = "Good Question";

  // create the "bad" button
  var newbad = document.createElement("input");
  newbad.className = "comment commentButton";
  newbad.name = "b";
  newbad.type = "submit";
  newbad.value = "Needs Work";

  // create the "cancel" button
  var newcancel = document.createElement("input");
  newcancel.className = "comment commentButton";
  newcancel.type = "button";
  newcancel.value = "Cancel";
  newcancel.onclick = removeOld;
  
  // add everything to the document in-place
  var container = document.getElementById("q" + id);
  container.appendChild( break1 );
  container.appendChild( newinput );
  container.appendChild( break2 );
  container.appendChild( newgood );
  container.appendChild( newbad );
  container.appendChild( newcancel );
}

/*
 * This function quite simply toggles the display of
 * the existing comments for the question.
 */
function showComments( id ) { 
  
  var comments = document.getElementById("z" + id);
  if( comments.style.display=="none" ) { 
    comments.style.display="block";
  } else { 
    comments.style.display="none";
  }

}
