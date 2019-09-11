/**
 *  ==================================================
 *  SoftChalk LessonBuilder
 *  Copyright 2003-2009 SoftChalk LLC
 *  All Rights Reserved.
 *
 *  http://www.softchalk.com
 *  ==================================================
 *
 *  LB version 7
 *  File date: September 16, 2011
 */

var my_status = "incomplete";
var scormLessonStartTime;
var API = null;
var scorm = true;
var no_implementation = qfScormNoImpA + "\n\n" + qfScormNoImpB;
var no_such_item = qfScormErrorA + "\n\n" + qfScormErrorB;
var actNumberingStart = qOrder.length;
var use_mastery_score = false;


/*
 * official SCORM names for quiz types
 */
var q_type_names = new Array();
		q_type_names[0] = ""; // not used
		q_type_names[1] = "choice";
		q_type_names[2] = "true-false";
		q_type_names[3] = "choice";
		q_type_names[4] = "fill-in";
		q_type_names[5] = "matching";
		q_type_names[6] = "sequencing";
		q_type_names[7] = "performance";  //for activities


/*
 * other = 0 as a safety
 * 1 is not used
 */
var act_type_names = new Array();
		act_type_names[0] = "other"; // safety if there is an error
		act_type_names[1] = "";
		act_type_names[2] = "Flashcard activity";
		act_type_names[3] = "Seek A Word activity";
		act_type_names[4] = "Drag-N-Drop activity";
		act_type_names[5] = "Ordering activity";
		act_type_names[6] = "Crossword activity";
		act_type_names[7] = "Labeling activity";
		act_type_names[8] = "Sorting activity";
		act_type_names[9] = "Hot Spot activity";



function findAPI(win) {
  if (win.API != null) {                // look in this window
		return win.API;
	}
  else {
		if (win.frames.length > 0) {        // look in this window's frameset kin
			for (var i = 0; i < win.frames.length; i++) {
		 		if (win.frames[i] && win.frames[i].API != null)
		 			return win.frames[i].API;
	 		}
		}
		if (typeof(win.opener) != "undefined" && win.opener != null)	{
			return findAPI(win.opener);				// climb up to opener window & look there
		}
		if (win.parent != window && win.parent != win) {
			return findAPI(win.parent);				// climb up to parent window & look there
		}
		return null;
  }
}


function ScormOnload() {
	API = findAPI(window.self);
	if (API != null) {
		API.LMSInitialize("");
		my_time = API.LMSGetValue("cmi.core.total_time");
		API.LMSSetValue("cmi.core.lesson_location", file_name);
		API.LMSSetValue("cmi.core.lesson_status", my_status);

		if (typeof(mastery_score) !== 'undefined') {
			use_mastery_score = true;
			API.LMSSetValue("cmi.student_data.mastery_score", mastery_score);
		}

		for (var i = 0; i < (qOrder.length); i++) {				// setting up questions
			var cmi_id = "cmi.interactions." + i + ".id";
			var q_id = "Q" + qOrder[i];
			API.LMSSetValue(cmi_id, q_id);
		}

		for (var i = 0; i < actOrder.length; i++) {				// setting up activities
			var cmi_id = "cmi.interactions." + (actNumberingStart + i) + ".id";
			var a_id = "A" + actOrder[i];
			API.LMSSetValue(cmi_id, a_id);
		}

		scormLessonStartTime = new Date().getTime();
		API.LMSCommit("");
	}
	else {
		alert(no_implementation);
	}
}


function ScormOnunload() {
	if (API != null) {
		sendLessonTime();
		API.LMSSetValue("cmi.core.lesson_location", file_name);
		API.LMSSetValue("cmi.core.lesson_status", my_status);
		API.LMSCommit("");
		API.LMSFinish("");
		alert(qfScormFinishMsg);
	}
	else {
		alert(no_implementation);
	}
	window.parent.close();
}


/*
 * global variables:
 *
 * my_score
 * total_points
 * attempted_q
 * totalQ
 * scorm_completed_status
 *
 * qfSingleTry from q_functions_ext.js
 */
function sendScorm(item_number, q_type, act_type, student_answer, correct, act_percent) {
	if (API == null) {
		return;
	}

	// my_score as percentage for comparing to mastery score
	var my_score_percent = 0;

	// set completed status
	my_status = "incomplete";

	if (use_mastery_score) {
		if (my_score > 0)
			my_score_percent = Math.round((my_score / total_points) * 100);

		if (my_score_percent >= mastery_score)
			my_status = "completed";
	}
	else if (scorm_completed_status && (qfSingleTry || attempted_q == totalQ)) { // maybe a no-repeat lesson
		my_status = "completed";
	}


	// set the id for the item to be scored,
	// set the student response
	var cmi_id;
	var student_response;

	if (q_type != 7) {																	// questions
		var display_order = -1;
		for (var i = 0; i < qOrder.length; i++) {
		  if (qOrder[i] == item_number) {
		  	display_order = i;
		  	break;
		  }
		}

		if (display_order == -1) {
			alert(no_such_item);
			return;
		}

		var q_id = "Q" + (display_order + 1); // avoid the 0
		student_response = q_id + ": " + student_answer;
		cmi_id = display_order;
	}
	else {																								// activities
		var display_order = -1;
		for (var i = 0; i < actOrder.length; i++) {
			if (actOrder[i] == item_number) {
				display_order = i;
				break;
			}
		}

		if (display_order == -1) {
			alert(no_such_item);
			return;
		}

		var a_id = "A" + (display_order + 1); // avoid the 0
		student_response = a_id + ": " + act_type_names[act_type] + ", " + act_percent + "% correct.";
		cmi_id = actNumberingStart + display_order;
	}


	// set the values with the id
	var cmi_type = "cmi.interactions." + cmi_id + ".type";
	var cmi_response = "cmi.interactions." + cmi_id + ".student_response";
	var cmi_result = "cmi.interactions." + cmi_id + ".result";

	API.LMSSetValue(cmi_type, q_type_names[q_type]);
	API.LMSSetValue(cmi_response, student_response);
	API.LMSSetValue("cmi.core.score.min", "0");

	if (use_mastery_score) {
	  API.LMSSetValue("cmi.core.score.max", "100");
	  API.LMSSetValue("cmi.core.score.raw", my_score_percent);
	}
	else {
	  API.LMSSetValue("cmi.core.score.max", total_points);
	  API.LMSSetValue("cmi.core.score.raw", my_score);
	}

	API.LMSSetValue("cmi.core.lesson_status", my_status);

	if (correct == "yes") {
		API.LMSSetValue(cmi_result, "correct");
	}
	else {
		API.LMSSetValue(cmi_result, "wrong");
	}


	if (q_type != 7) {
		var right_answers = eval("right_answers" + item_number);
		for (var i = 0; i < (right_answers.length); i++) {
			var my_pattern = "cmi.interactions." + cmi_id + ".correct_responses." + i + ".pattern";
			API.LMSSetValue(my_pattern, right_answers[i]);
		}
	}

	API.LMSCommit("");  //make sure that LMS is storing data sent - not sure whether to leave in??
}


function sendLessonTime() {
	var formattedTime;
	if (scormLessonStartTime != 0) {
		var currentDate = new Date().getTime();
		var elapsedSeconds = ((currentDate - scormLessonStartTime) / 1000);
		formattedTime = convertTotalSeconds(elapsedSeconds);
	}
	else {
		formattedTime = "00:00:00.0";
	}
	API.LMSSetValue("cmi.core.session_time", formattedTime);
}


function convertTotalSeconds(ts) {
	var sec = (ts % 60);

  ts -= sec;
  var tmp = (ts % 3600);  //# of seconds in the total # of minutes
  ts -= tmp;              //# of seconds in the total # of hours

  // convert seconds to conform to CMITimespan type (e.g. SS.00)
  sec = Math.round(sec * 100) / 100;

  var strSec = new String(sec);
  var strWholeSec = strSec;
  var strFractionSec = "";

  if (strSec.indexOf(".") != -1) {
		strWholeSec =  strSec.substring(0, strSec.indexOf("."));
    strFractionSec = strSec.substring(strSec.indexOf(".") + 1, strSec.length);
  }

  if (strWholeSec.length < 2) {
    strWholeSec = "0" + strWholeSec;
  }

  strSec = strWholeSec;

  if (strFractionSec.length) {
    strSec = strSec + "." + strFractionSec;
  }

  if ((ts % 3600) != 0 ) var hour = 0;
  else var hour = (ts / 3600);

  if ( (tmp % 60) != 0 ) var min = 0;
  else var min = (tmp / 60);

  if ((new String(hour)).length < 2) hour = "0" + hour;

  if ((new String(min)).length < 2) min = "0" + min;

  var rtnVal = hour + ":" + min + ":" + strSec;

  return rtnVal;
}
