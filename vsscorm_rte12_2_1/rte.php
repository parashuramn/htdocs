<?php 

/*

VS SCORM 1.2 RTE - rte.php
Rev 2009-11-30-01
Copyright (C) 2009, Addison Robson LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, 
Boston, MA	02110-1301, USA.

*/

// read SCOInstanceID from the GET parameters
 $SCOInstanceID = $_GET['SCOInstanceID'] * 1;
?>
<?php 

/* 

VS SCORM 1.2 RTE - api.php
Rev 2010-04-30-01
Copyright (C) 2009, Addison Robson LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, 
Boston, MA 02110-1301, USA.

*/

//  essential functions
require "subs.php";
// global $SCOInstanceID;
// input data
 $SCOInstanceID = $_REQUEST['SCOInstanceID'] * 1;

//  read database login information and connect
require "config.php";
dbConnect();

// initialize data elements in the database if they're not already set, and
// dynamically create the javascript code to initialize the local cache
$initializeCache = initializeSCO();

?>
<html>
<head>
<title>VS SCORM</title>
<!-- <script src="apijs.php?SCOInstanceID=<?php echo $SCOInstanceID ?>" type="text/javascript"></script> -->
<!-- <script src="scorm-parms.js" type="text/javascript"></script> -->
<!--<script src="pipwerks_scorm_api_wrapper.js" type="text/javascript"></script> -->
<script>
function createRequest() {
	var request;
	try {
		request = new XMLHttpRequest();
	}
	catch (tryIE) {
		try {
		request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (tryOlderIE) {
		try {
			request = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (failed) {
			alert("Error creating XMLHttpRequest");
		}
		}
	}
	return request;
	}
		// ------------------------------------------
	//   URL Encoding
	// ------------------------------------------
	function urlencode( str ) {
		//
		// Ref: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_urlencode/
		//
		// http://kevin.vanzonneveld.net
		// +   original by: Philip Peterson
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +      input by: AJ
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Brett Zamir (http://brettz9.blogspot.com)
		// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +      input by: travc
		// +      input by: Brett Zamir (http://brettz9.blogspot.com)
		// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Lars Fischer
		// %          note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
		// *     example 1: urlencode('Kevin van Zonneveld!');
		// *     returns 1: 'Kevin+van+Zonneveld%21'
		// *     example 2: urlencode('http://kevin.vanzonneveld.net/');
		// *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
		// *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
		// *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'

		var histogram = {}, unicodeStr='', hexEscStr='';
		var ret = (str+'').toString();

		var replacer = function(search, replace, str) {
			var tmp_arr = [];
			tmp_arr = str.split(search);
			return tmp_arr.join(replace);
		};

		// The histogram is identical to the one in urldecode.
		histogram["'"]   = '%27';
		histogram['(']   = '%28';
		histogram[')']   = '%29';
		histogram['*']   = '%2A';
		histogram['~']   = '%7E';
		histogram['!']   = '%21';
		histogram['%20'] = '+';
		histogram['\u00DC'] = '%DC';
		histogram['\u00FC'] = '%FC';
		histogram['\u00C4'] = '%D4';
		histogram['\u00E4'] = '%E4';
		histogram['\u00D6'] = '%D6';
		histogram['\u00F6'] = '%F6';
		histogram['\u00DF'] = '%DF';
		histogram['\u20AC'] = '%80';
		histogram['\u0081'] = '%81';
		histogram['\u201A'] = '%82';
		histogram['\u0192'] = '%83';
		histogram['\u201E'] = '%84';
		histogram['\u2026'] = '%85';
		histogram['\u2020'] = '%86';
		histogram['\u2021'] = '%87';
		histogram['\u02C6'] = '%88';
		histogram['\u2030'] = '%89';
		histogram['\u0160'] = '%8A';
		histogram['\u2039'] = '%8B';
		histogram['\u0152'] = '%8C';
		histogram['\u008D'] = '%8D';
		histogram['\u017D'] = '%8E';
		histogram['\u008F'] = '%8F';
		histogram['\u0090'] = '%90';
		histogram['\u2018'] = '%91';
		histogram['\u2019'] = '%92';
		histogram['\u201C'] = '%93';
		histogram['\u201D'] = '%94';
		histogram['\u2022'] = '%95';
		histogram['\u2013'] = '%96';
		histogram['\u2014'] = '%97';
		histogram['\u02DC'] = '%98';
		histogram['\u2122'] = '%99';
		histogram['\u0161'] = '%9A';
		histogram['\u203A'] = '%9B';
		histogram['\u0153'] = '%9C';
		histogram['\u009D'] = '%9D';
		histogram['\u017E'] = '%9E';
		histogram['\u0178'] = '%9F';

		// Begin with encodeURIComponent, which most resembles PHP's encoding functions
		ret = encodeURIComponent(ret);

		for (unicodeStr in histogram) {
			hexEscStr = histogram[unicodeStr];
			ret = replacer(unicodeStr, hexEscStr, ret); // Custom replace. No regexing
		}

		// Uppercase for full PHP compatibility
		return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
			return "%"+m2.toUpperCase();
		});
	}
	function sendDataToAjax(sendData){
			// create request object
			var req = createRequest();

			// code to prevent caching
			// set up request parameters - uses POST method
			req.open('POST','./ajax_request.php',false);

			// create a POST-formatted list of cached data elements 
			// include only SCO-writeable data elements
            request_param = '';
			request_param +='&type='+urlencode(sendData['type']);
			request_param +='&params_key='+urlencode(sendData['params_key']);
			request_param +='&value='+urlencode(sendData['value']);
			request_param +='&timestamp='+urlencode(sendData['timestamp']);
			request_param +='&activity_id='+urlencode(sendData['activity_id']);
// 			var params = 'SCOInstanceID=&code='+d.getTime()+request_param;
// 			for(curind in cache){
// 				params += "&data["+curind+"]="+urlencode(cache[curind]);
// 				var unique_fld = ['cmi.core._children','cmi.core.score._children','cmi.core.student_id',
// 				'cmi.core.student_name','cmi.core.score.raw','adlcp:masteryscore',
// 				'cmi.launch_data','cmi.suspend_data','cmi.core.lesson_location','cmi.core.credit',
// 				'cmi.core.lesson_status','cmi.core.entry','cmi.core.exit','cmi.core.total_time',
// 				'cmi.core.session_time','cmi.interactions._count'];

// 			}
			// request headers
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// submit to the server for processing
			//req.send(request_param);
			// process returned data - error condition
			//if (req.status != 200 ) {
		//}
	}
</script>
<script src="./xAPIWrapper/dist/xapiwrapper.min.js" type="text/javascript"></script>
<script src="./SCORM-to-xAPI-Wrapper-master/SCORMToXAPIFunctions.js" type="text/javascript"></script>
<script>
var API = (function(){
	// ------------------------------------------
	//   AJAX Request Handling
	// ------------------------------------------
	function createRequest() {
	var request;
	try {
		request = new XMLHttpRequest();
	}
	catch (tryIE) {
		try {
		request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (tryOlderIE) {
		try {
			request = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (failed) {
			alert("Error creating XMLHttpRequest");
		}
		}
	}
	return request;
	}

	// ------------------------------------------
	//   URL Encoding
	// ------------------------------------------
	function urlencode( str ) {
		//
		// Ref: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_urlencode/
		//
		// http://kevin.vanzonneveld.net
		// +   original by: Philip Peterson
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +      input by: AJ
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Brett Zamir (http://brettz9.blogspot.com)
		// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +      input by: travc
		// +      input by: Brett Zamir (http://brettz9.blogspot.com)
		// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Lars Fischer
		// %          note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
		// *     example 1: urlencode('Kevin van Zonneveld!');
		// *     returns 1: 'Kevin+van+Zonneveld%21'
		// *     example 2: urlencode('http://kevin.vanzonneveld.net/');
		// *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
		// *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
		// *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'

		var histogram = {}, unicodeStr='', hexEscStr='';
		var ret = (str+'').toString();

		var replacer = function(search, replace, str) {
			var tmp_arr = [];
			tmp_arr = str.split(search);
			return tmp_arr.join(replace);
		};

		// The histogram is identical to the one in urldecode.
		histogram["'"]   = '%27';
		histogram['(']   = '%28';
		histogram[')']   = '%29';
		histogram['*']   = '%2A';
		histogram['~']   = '%7E';
		histogram['!']   = '%21';
		histogram['%20'] = '+';
		histogram['\u00DC'] = '%DC';
		histogram['\u00FC'] = '%FC';
		histogram['\u00C4'] = '%D4';
		histogram['\u00E4'] = '%E4';
		histogram['\u00D6'] = '%D6';
		histogram['\u00F6'] = '%F6';
		histogram['\u00DF'] = '%DF';
		histogram['\u20AC'] = '%80';
		histogram['\u0081'] = '%81';
		histogram['\u201A'] = '%82';
		histogram['\u0192'] = '%83';
		histogram['\u201E'] = '%84';
		histogram['\u2026'] = '%85';
		histogram['\u2020'] = '%86';
		histogram['\u2021'] = '%87';
		histogram['\u02C6'] = '%88';
		histogram['\u2030'] = '%89';
		histogram['\u0160'] = '%8A';
		histogram['\u2039'] = '%8B';
		histogram['\u0152'] = '%8C';
		histogram['\u008D'] = '%8D';
		histogram['\u017D'] = '%8E';
		histogram['\u008F'] = '%8F';
		histogram['\u0090'] = '%90';
		histogram['\u2018'] = '%91';
		histogram['\u2019'] = '%92';
		histogram['\u201C'] = '%93';
		histogram['\u201D'] = '%94';
		histogram['\u2022'] = '%95';
		histogram['\u2013'] = '%96';
		histogram['\u2014'] = '%97';
		histogram['\u02DC'] = '%98';
		histogram['\u2122'] = '%99';
		histogram['\u0161'] = '%9A';
		histogram['\u203A'] = '%9B';
		histogram['\u0153'] = '%9C';
		histogram['\u009D'] = '%9D';
		histogram['\u017E'] = '%9E';
		histogram['\u0178'] = '%9F';

		// Begin with encodeURIComponent, which most resembles PHP's encoding functions
		ret = encodeURIComponent(ret);

		for (unicodeStr in histogram) {
			hexEscStr = histogram[unicodeStr];
			ret = replacer(unicodeStr, hexEscStr, ret); // Custom replace. No regexing
		}

		// Uppercase for full PHP compatibility
		return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
			return "%"+m2.toUpperCase();
		});
	}
	// ------------------------------------------
	//   Status Flags
	// ------------------------------------------
	var flagFinished = false;
	var flagInitialized = false;

	// ------------------------------------------
	//   SCO Data Cache - Initialization
	// ------------------------------------------
	// var cache = new Object();
	<?php print $initializeCache; ?>
	// ------------------------------------------
	//   SCORM RTE Functions - Initialization
	// ------------------------------------------
	// var sendData ='';
	// sendData = ;
	var activity_id= location.href.split('?')[1].split('&')[1].split('=')[1];
	
	var interactions_count =0;
	var inner_prop=this;
	var SCOInstanceID=location.href.split('?')[1].split('=')[1].split('&')[0];
	var Content_ar = ['Captivate 1.2','Ispring 1.2','Articulate 1.2','Articulate 1.2']
	var Content = Content_ar[SCOInstanceID-1];
	console.log(activity_id);
		function sendDataToAjax(sendData){
			// create request object
			var req = createRequest();

			// code to prevent caching
			// set up request parameters - uses POST method
			req.open('POST','./ajax_request.php',false);

			// create a POST-formatted list of cached data elements 
			// include only SCO-writeable data elements
            request_param = '';
			request_param +='&type='+urlencode(sendData['type']);
			request_param +='&params_key='+urlencode(sendData['params_key']);
			request_param +='&value='+urlencode(sendData['value']);
			request_param +='&timestamp='+urlencode(sendData['timestamp']);
			request_param +='&activity_id='+urlencode(sendData['activity_id']);
// 			var params = 'SCOInstanceID=&code='+d.getTime()+request_param;
// 			for(curind in cache){
// 				params += "&data["+curind+"]="+urlencode(cache[curind]);
// 				var unique_fld = ['cmi.core._children','cmi.core.score._children','cmi.core.student_id',
// 				'cmi.core.student_name','cmi.core.score.raw','adlcp:masteryscore',
// 				'cmi.launch_data','cmi.suspend_data','cmi.core.lesson_location','cmi.core.credit',
// 				'cmi.core.lesson_status','cmi.core.entry','cmi.core.exit','cmi.core.total_time',
// 				'cmi.core.session_time','cmi.interactions._count'];

// 			}
			// request headers
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// submit to the server for processing
			//req.send(request_param);
			// process returned data - error condition
			//if (req.status != 200 ) {
		//}
	}
	return{
		getCache: function() {return cache;},
		LMSInitialize:function (dummyString) {
		console.log("INIT");

			// already initialized or already finished
			if ((flagInitialized) || (flagFinished)) { return "false"; }

			// set initialization flag
			flagInitialized = true;
			// xAPI Extensions
			xapi.initializeAttempt();
			//this.
			// return success value
			return "true";
		
		},

		// ------------------------------------------
		//   SCORM RTE Functions - Getting and Setting Values
		// ------------------------------------------
		LMSGetValue:function (varname) {
			var d = new Date();
			var timestamp_str =d.toLocaleString();
		//console.log({GETVAL:'',varname:varname });

			// not initialized or already finished
		if ((! flagInitialized) || (flagFinished)) { return "false"; }
		
		var d = new Date();
			var timestamp_str =d.toLocaleString();
			console.log({'GETVAL':'',varname:varname,varvalue:cache[varname] , time:timestamp_str});
			if(varname =='cmi.interactions._count'){
				varvalue_count = parseInt(++interactions_count);varvalue='"'+varvalue_count+'"';
				// console.log(varvalue_count); console.log(varvalue);
				return varvalue_count;
				}
				//var activity_id =
				// params_key= (varname === undefined) ?'undefiend':varname;
				varvalue= (cache[varname] === undefined) ?'undefiend':cache[varname];
				// activity_id= (activity_id === undefined) ?'undefiend':activity_id;
				console.log({type:'GetValue',params_key:varname,value:cache[varname],timestamp:timestamp_str,activity_id:activity_id});
				//sendDataToAjax({type:'GetValue',params_key:varname,value:varvalue,timestamp:timestamp_str,activity_id:activity_id});
// 				xapi.changeConfig('isScorm2004',false);
			// otherwise, return the requested data
			return cache[varname];

		},

		LMSSetValue:function (varname,varvalue) {
			var d = new Date();
			var timestamp_str =d.toLocaleString();
		//console.log({SETVAL:'',varname:varname,varvalue:varvalue,time:timestamp_str });

			// not initialized or already finished
			if ((! flagInitialized) || (flagFinished)) { return "false"; }

		    //  otherwise, set the requested data, and return success value
			cache['cmi.interactions._count']=interactions_count;
			cache[varname]=varvalue;
			// varname= (varname === undefined) ?'undefiend':varname;
			// params_key= (varname === undefined) ?'undefiend':varname;
			// varvalue= (cache[varvalue] === undefined) ?'undefiend':cache[varname];
			// activity_id= (activity_id === undefined) ?'undefiend':activity_id;
			// console.log({type:'SetValue',params_key:varname,value:cache[varname],timestamp:timestamp_str,activity_id:activity_id});	
			//sendDataToAjax({type:'SetValue',params_key:varname,value:cache[varname],timestamp:timestamp_str,activity_id:activity_id});
			//this.LMSCommit('setvalue');
			//  xAPI Extension
// xapi.changeConfig('isScorm2004',false);
			xapi.saveDataValue(varname, varvalue);
			return "true";

		},

		// ------------------------------------------
		//   SCORM RTE Functions - Saving the Cache to the Database
		// ------------------------------------------
		LMSCommit:function (dummyString) {
			console.log("COMMIT");
			// not initialized or already finished
			
			//if ((! flagInitialized) || (flagFinished)) { return "false"; }
			var request_param = '';
			var d = new Date();
			var timestamp_str =d.getUTCDate()+"-"+d.getUTCMonth()+d.getUTCFullYear()+"  "+d.getHours()+":"+d.getMinutes()+d.getSeconds()+":"+d.getMilliseconds(); 
			var timestamp_str =d.toLocaleString();
			if(dummyString =='finish'){
				var query_uievent= 'suspend' ;
				var query_functioncalled=  'LMSFinish' ;
				var query_remark= 'LMSCommit function is called from LMSFinish.';
			}else if(dummyString =='setvalue'){
				var query_uievent= 'setvalue';
				var query_functioncalled='SetValue';
				var query_remark= 'LMSCommit function is called from SetValue.';
			}else{
				var query_uievent='commit';
				var query_functioncalled= 'LMSCommit';
				var query_remark= 'LMSCommit function is called.';
			}
			// create request object
			var req = createRequest();

			// code to prevent caching
			// set up request parameters - uses POST method
			req.open('POST','./commit.php',false);

			// create a POST-formatted list of cached data elements 
			// include only SCO-writeable data elements

			request_param +='&Content='+Content;
			request_param +='&uiEvent='+query_uievent;
			request_param +='&eventTime='+timestamp_str;
			request_param +='&functionCalled='+query_functioncalled;
			request_param +='&Remark='+query_remark;
			var params = 'SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime()+request_param;
			for(curind in cache){
				params += "&data["+curind+"]="+urlencode(cache[curind]);
				var unique_fld = ['cmi.core._children','cmi.core.score._children','cmi.core.student_id',
				'cmi.core.student_name','cmi.core.score.raw','adlcp:masteryscore',
				'cmi.launch_data','cmi.suspend_data','cmi.core.lesson_location','cmi.core.credit',
				'cmi.core.lesson_status','cmi.core.entry','cmi.core.exit','cmi.core.total_time',
				'cmi.core.session_time','cmi.interactions._count'];
// 				if(unique_fld.indexOf(curind) == -1){
// 				delete cache[curind];
// 				}
			}

		// 	params += "&data[cmi.core.lesson_location]="+urlencode(cache['cmi.core.lesson_location']);
		// 	params += "&data[cmi.core.lesson_status]="+urlencode(cache['cmi.core.lesson_status']);
		// 	params += "&data[cmi.core.exit]="+urlencode(cache['cmi.core.exit']);
		// 	params += "&data[cmi.core.session_time]="+urlencode(cache['cmi.core.session_time']);
		// 	params += "&data[cmi.core.score.raw]="+urlencode(cache['cmi.core.score.raw']);
		// 	params += "&data[cmi.suspend_data]="+urlencode(cache['cmi.suspend_data']);
		// 	params += "&data[cmi.cmi.interactions._count]="+urlencode(cache['cmi.cmi.interactions._count']);

			// request headers
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// submit to the server for processing
			req.send(params);
			// process returned data - error condition
			if (req.status != 200 ) {
				alert('Problem with AJAX Request in LMSCommit()');
				return "false";
			}
			// process returned data - OK
				else {
				return "true";
			}

		},

		// ------------------------------------------
		//   SCORM RTE Functions - Closing The Session
		// ------------------------------------------
		LMSFinish:function (dummyString) {
			console.log("FINISH");
			// not initialized or already finished
			if ((! flagInitialized) || (flagFinished)) { return "false"; }

			// commit cached values to the database
			this.LMSCommit('finish');

			// create request object
			var req = createRequest();

			// code to prevent caching
			var d = new Date();
		//  var stm= xapi.getBaseStatement();
		//  //console.log(stm);
		//  if(stm !==undefined && stm !=null){
		// 	//set up request parameters - uses GET method
		// 	req.open('POST','finish.php?SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime()+'&xapi_content=""'+stm+'""',false);
		// }else{
			var request_param = '';
			var timestamp_str =d.getUTCDate()+"-"+d.getUTCMonth()+d.getUTCFullYear()+"  "+d.getHours()+":"+d.getMinutes()+d.getSeconds()+":"+d.getMilliseconds(); 
			var timestamp_str =d.toLocaleString();
			var query_uievent= 'LMSFinish';
			var query_functioncalled= 'LMSFinish';
			var query_remark= 'LMSFinish function is called.';
			request_param +='&Content='+Content;
			request_param +='&uiEvent='+query_uievent;
			request_param +='&eventTime='+timestamp_str;
			request_param +='&functionCalled='+query_functioncalled;
			request_param +='&Remark='+query_remark;
			var params = 'SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime()+request_param;

			req.open('POST','finish.php?SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime(),false);
		// }
			//req.setRequestHeader("Content-type", "application/json");
			req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		//  params=JSON.stringify(JSON.parse(stm));
		//  submit to the server for processing
			req.send();
		//  process returned data - error condition
			if (req.status != 200) {
				alert('Problem with AJAX Request in LMSFinish()');
				return "";
			}
		//  xAPI Extension
			xapi.terminateAttempt();
			// set finish flag
			flagFinished = true;

			// return to calling program
			return "true";
		
		},

		// ------------------------------------------
		//   SCORM RTE Functions - Error Handling
		// ------------------------------------------
		LMSGetLastError:function () {
		console.log("LMSGETLASTERROR");
			return 0;
		},

		LMSGetDiagnostic:function (errorCode) {
			return "diagnostic string";
		},

		LMSGetErrorString:function (errorCode) {
			return "error string";
		},

}

})();
initializeCommunication = API.LMSInitialize;
terminateCommunication = API.LMSFinish;
storeDataValue = API.LMSSetValue;
retrieveDataValue = API.LMSGetValue;
</script>
<!-- <script src="./SCORM-to-xAPI-Wrapper-master/SCORM1.2/APIWrapper.js" type="text/javascript"></script> -->

</head>

<!-- <frameset frameborder="0" framespacing="0" border="0" rows="*" cols="*" onbeforeunload="doLMSFinish('');" onunload="doLMSFinish('');">-->
<!-- <frameset frameborder="0" framespacing="0" border="0" rows="50,*" cols="*" onbeforeunload="pipwerks.SCORM.connection.terminate('');" onunload="pipwerks.SCORM.connection.terminate('');"> -->
<?php // echo $SCOInstanceID; ?>
<!-- <frame src="api.php?SCOInstanceID=<?php echo $SCOInstanceID ?>" name="API" noresize scrolling="no"> -->
<!--iSpring Demo Course (SCORM 2004 4th)/ -->
<!-- ../res/index.html -->
<!--  -->
	
<?php 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Path for SCORM bundles
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//'../iSpring Demo Course (SCORM 1.2)/res/index.html';
$path_ar = ["../Demo Captivate Quiz SCORM 1.2/",
"../iSpring Demo Course (SCORM 1.2)/res/",
"../WWII_Sample_sco/course/",
"../BigBrute_daily_demo_SCORM_12-20090804-1211/course/",
"../FMLA_Sample/course/",
"../Quadratic_sco/course/",
"../PuzzleQuizSCORMExport/course/",
"../Demo Captivate Slides Scorm 1.2/",
"../iSpring Demo Course scorm 1.2 new/res/",
"../iSpring SCORM 1.2 Quiz with Survey/res/"
];
 $path = $path_ar[$SCOInstanceID-1]; 
//exit;
// $path = '../Demo Captivate Quiz SCORM 1.2/';
$i=0;	

//print_r($dom->manifest->children());
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the list of course content
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(file_exists($path.'imsmanifest.xml')){
// echo "Sdfg";
// exit;
	//echo "File";
	//echo '<frameset frameborder="0" framespacing="0" border="0" rows="*" cols="*" onbeforeunload="API.LMSFinish(\'\');" onunload="API.LMSFinish(\'\');">';
$SCOdata = readIMSManifestFile($path.'imsmanifest.xml');
// print_r($SCOdata);
$ORGdata=getORGdata($path.'imsmanifest.xml');
 print_r($ORGdata);
$scorm_version=getScormVersion($path.'imsmanifest.xml');
 print_r($scorm_version);
$mastery_score = getMasteryScore($path.'imsmanifest.xml');
 print_r($mastery_score);
// echo "<html>\n";
foreach ($SCOdata as $identifier => $SCO)
{
	$page[$i] = $path.cleanVar($SCO['href']);
	$i++;
}
// print_r($SCOdata);
foreach ($ORGdata as $identifier => $ORG)
{
	if ($ORG['identifierref']==''){  
		echo "<h3>".$ORG['name']."</h3>\n";
	}
	else{           
		$key_ref=0;
		foreach ($SCOdata as $identifier_temp => $SCO)	{
			if ($identifier_temp==$identifier )
			{break;}
			else {$key_ref++;}
		}
		if ($key_ref>=0){	
			// echo "<h5><a href=".$page[$key_ref]."  target='course'>".$ORG['name']."</a></h5>\n";
			//echo '<frame src="'.$page[$key_ref].'" name="course">';
		}
		else{ echo "Invalid Data in - imsmanifest.xml. Check the file and try again"; return;}
	}
}
//echo '</frameset>';
}
exit;
// echo "</html>\n";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Make variable safe to display
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 	  sleep(2);
	echo '<frameset frameborder="0" framespacing="0" border="0" rows="*" cols="*" onbeforeunload="API.LMSFinish(\'\');" onunload="API.LMSFinish(\'\');">';

if($SCOInstanceID==1){
	echo '<frame src="../Demo Captivate Quiz SCORM 1.2/index_scorm.html" name="course">';
}else if($SCOInstanceID==2){
	echo '<frame src="../iSpring Demo Course (SCORM 1.2)/res/index.html" name="course">';
}else if($SCOInstanceID==3){ 
	echo '<frame src="../WWII_Sample_sco/course/index.html" name="course">';
 }else if($SCOInstanceID==4){
	echo '<frame src="../BigBrute_daily_demo_SCORM_12-20090804-1211/course/index_lms.html" name="course">';
}else if($SCOInstanceID==5){
	echo '<frame src="../FMLA_Sample/course/index_lms.html" name="course">';
}else if($SCOInstanceID==6){
	echo '<frame src="../Quadratic_sco/course/index.html" name="course">';
}else if($SCOInstanceID==7){
	echo '<frame src="../Demo Captivate Quiz SCORM 1.2/index_scorm.html" name="course">';
}
else if($SCOInstanceID==8){
	echo '<frame src="../Demo Captivate Slides Scorm 1.2/index_scorm.html" name="course">';
}else if($SCOInstanceID==9){
	echo '<frame src="../iSpring Demo Course scorm 1.2 new/res/index.html" name="course">';
}
else if($SCOInstanceID==10){
	echo '<frame src="../iSpring SCORM 1.2 Quiz with Survey/res/index.html" name="course">';
}
//../iSpring Demo Course (SCORM 2004 4th)/res/index.html
else{ 
	// echo '<frame src="'.$page[$key_ref].'" name="course">';
 }	
?>
</frameset>

</html>