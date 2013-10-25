/*
 * This function simply toggles the display of
 * the given block.
 */
function toggle_display( id ) { 
  var oldPos = window.scrollY;

  var block = document.getElementById(id);
  if( block.style.display=="none" ) { 
    block.style.display="block";
  } else { 
    block.style.display="none";
  }

  window.scrollTo( 0, oldPos );
}

/*
 * This function simply turns off the display of
 * the given block.
 */
function hide_display( id ) { 
  var oldPos = window.scrollY;

  var block = document.getElementById(id);
  block.style.display="none";

  window.scrollTo( 0, oldPos );
}

/*
 * This function populates the hidden fields
 * appropriately for submission.
 */
function submit_feedback( qid, type ) { 

  // find the feedback div marked by qid
  var question_div = document.getElementById( "f" + qid );
  var feedback_in = null;

  // loop through its elements to find the textarea
  for( var i = 0; i < question_div.childNodes.length; i++ ) { 
    if(question_div.childNodes[i].name == "c" ) { 
      feedback_in = question_div.childNodes[i];
      break;
    }
  }

  // if we successfully found the textarea:
  if( feedback_in != null ) { 

    // clean up the feedback and make sure it's valid
    var feedback = feedback_in.value.trim();
    if( feedback.length > 0 ) { 

      // set the action hidden field
      var action_box = document.getElementById( "action" );
      action_box.value = "add-comment";

      // set the id hidden field
      var id_box = document.getElementById( "identifier" );
      id_box.value = qid;

      // set the r(ating) hidden field
      var rating_box = document.getElementById( "r" );
      rating_box.value = type;

      // set the f(eedback) hidden field
      var feedback_box = document.getElementById( "f" );
      feedback_box.value = feedback_in.value;

      // submit the form
      var form = document.getElementById( "feedback-form" );
      form.submit();

    } else { 
      alert("Your feedback is empty!");
      return;
    }
  } else { // somehow an element was missing....
    alert("Your page was broken; please alert the webmaster.");
    return;
  }
}

/*
 * Add a row to the user registration form.
 */
function add_user_registration_row() {
  var oldPos = window.scrollY;

  var table = document.getElementById( "register-user-table" );
  var rowCount = table.rows.length;
  var row = table.insertRow( rowCount );
  //row.id = 'register-user-' + (rowCount - 1);

  var cell_username = row.insertCell(0);
  cell_username.innerHTML = '<input type="text" name="username[]" />';

  var cell_realname = row.insertCell(1);
  cell_realname.innerHTML = '<input type="text" name="realname[]" />';

  var cell_level = row.insertCell(2);
  cell_level.innerHTML = '<select name="level[]">' + 
  '<option value="student" selected="selected">Student</option>' +
  '<option value="moderator">Professor</option>' +
  '<option value="moderator">GTA</option>' +
  '<option value="moderator">PRM</option>' +
  '</select>';

  window.scrollTo( 0, oldPos );
}

/*
 * Begin the process of removing a question from the database.
 */
function remove_question( qid ) { 
  var question_block = document.getElementById( 'question'+qid );
  var question_text = question_block.getElementsByClassName("question-text")[0].innerHTML;
  var confirmation_text = "Are you sure you wish to remove this question: \n\n";
  confirmation_text += question_text;
  
  var res = confirm( confirmation_text );

  if( res ) { 
    var action_field = document.getElementById( "action" );
    action_field.value = "remove-question";

    var id_field = document.getElementById( "identifier" );
    id_field.value = qid;

    var removal_form = document.getElementById( "feedback-form" );
    removal_form.submit();
  } else { 
    return false;
  }
}


function remove_comment( cid ) { 
  var comment_block = document.getElementById( 'comment'+cid );
  var comment_text = comment_block.getElementsByClassName("comment-text")[0].innerHTML;
  var confirmation_text = "Are you sure you wish to remove this comment: \n\n";
  confirmation_text += comment_text;
  
  var res = confirm( confirmation_text );
  
  if( res ) { 
    var action_field = document.getElementById( "action" );
    action_field.value = "remove-comment";

    var id_field = document.getElementById( "identifier" );
    id_field.value = cid;

    var removal_form = document.getElementById( "feedback-form" );
    removal_form.submit();
  } else { 
    return false;
  }
}
