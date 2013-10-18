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

/*
 * This function populates the hidden fields
 * appropriately for submission.
 */
function submit_form( qid, type ) { 
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

      // set the qid hidden field
      var qid_box = document.getElementById( "qid" );
      qid_box.value = qid;

      // set the r(ating) hidden field
      var type_box = document.getElementById( "r" );
      type_box.value = type;

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
