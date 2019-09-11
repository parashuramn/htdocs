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
return{
LMSInitialize:function (dummyString) {
 
	// already initialized or already finished
	if ((flagInitialized) || (flagFinished)) { return "false"; }
  console.log('2');

	// set initialization flag
	flagInitialized = true;
	  // xAPI Extensions
//     var config = {
//         lrs:{
//            endpoint:"https://test.gblrs.com/xAPI/",
//            user:"29-a84cad798d5777b",
//            password:"58c262a7f3ec044954b47430c "
//         },
//         courseId:"https://test.gblrs.com",
//         lmsHomePage:"https://test.gblrs.com",
//         isScorm2004:false
//     }; // isSCORM2004:true above - to convert SCORM 2004 courses
//     xapi.setConfig(config);
    xapi.initializeAttempt();
	// return success value
	return "true";
 
},

// ------------------------------------------
//   SCORM RTE Functions - Getting and Setting Values
// ------------------------------------------
LMSGetValue:function (varname) {

	// not initialized or already finished
  if ((! flagInitialized) || (flagFinished)) { return "false"; }
  console.log(cache);

	// otherwise, return the requested data
	return cache[varname];

},

LMSSetValue:function (varname,varvalue) {
  console.log({varname:varname,varvalue:varvalue });

	// not initialized or already finished
	if ((! flagInitialized) || (flagFinished)) { return "false"; }

	// otherwise, set the requested data, and return success value
  cache[varname] = varvalue;
   // xAPI Extension
//       xapi.saveDataValue(name, value);
  console.log({setval:cache});
	return "true";

},

// ------------------------------------------
//   SCORM RTE Functions - Saving the Cache to the Database
// ------------------------------------------
LMSCommit:function (dummyString) {
  console.log({dummyString:dummyString});
	// not initialized or already finished
	if ((! flagInitialized) || (flagFinished)) { return "false"; }

	// create request object
	var req = createRequest();

	// code to prevent caching
	var d = new Date();

	// set up request parameters - uses POST method
	req.open('POST','./commit.php',true);

	// create a POST-formatted list of cached data elements 
	// include only SCO-writeable data elements
	var params = 'SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime();

	params += "&data[cmi.core.lesson_location]="+urlencode(cache['cmi.core.lesson_location']);
	params += "&data[cmi.core.lesson_status]="+urlencode(cache['cmi.core.lesson_status']);
	params += "&data[cmi.core.exit]="+urlencode(cache['cmi.core.exit']);
	params += "&data[cmi.core.session_time]="+urlencode(cache['cmi.core.session_time']);
	params += "&data[cmi.core.score.raw]="+urlencode(cache['cmi.core.score.raw']);
	params += "&data[cmi.suspend_data]="+urlencode(cache['cmi.suspend_data']);

	// request headers
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	// req.setRequestHeader("Content-length", params.length);
	// req.setRequestHeader("Connection", "close");
	console.log({param:params});
	// submit to the server for processing
	req.send(params);
console.log(req);
	// process returned data - error condition
	if (req.status != 200) {
		// alert('Problem with AJAX Request in LMSCommit()');
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
  console.log({LMSFinish:dummyString});

	// not initialized or already finished
	if ((! flagInitialized) || (flagFinished)) { return "false"; }

	// commit cached values to the database
	this.LMSCommit('');

	// create request object
	var req = createRequest();

	// code to prevent caching
	var d = new Date();
var stm= xapi.getBaseStatement();
console.log(stm);
if(stm !==undefined && stm !=null){
	// set up request parameters - uses GET method
	req.open('POST','finish.php?SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime()+'&xapi_content=""'+JSON.stringify(stm)+'""',false);
}else{
	req.open('POST','finish.php?SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime(),false);
}
	//req.setRequestHeader("Content-type", "application/json");
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
// params=JSON.stringify(JSON.parse(stm));
	// submit to the server for processing
	req.send();
console.log({var:req});
	// process returned data - error condition
	if (req.status != 200) {
		alert('Problem with AJAX Request in LMSFinish()');
		return "";
	}
// xAPI Extension
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
}
		}

		})();
// 		    initializeCommunication = API.LMSInitialize;
// terminateCommunication = API.LMSFinish;
// storeDataValue = API.LMSSetValue;
// retrieveDataValue = API.LMSGetValue;

		//window.onbeforeunload="API.LMSFinish('');" window.onunload="API.LMSFinish('');"
</script>