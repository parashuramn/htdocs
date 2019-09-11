const bundleAuth = bundle.authData;
const bundleInput = bundle.inputData;
const auth = 'Basic ' + new Buffer(bundleAuth.username + ':' + bundleAuth.password).toString('base64')
const actor_email = (bundleInput.actor_email !== undefined) ? 'mailto:' + bundleInput.actor_email : bundleInput.actor_email;
const actor_account_details = (bundleInput.actor_account_name === undefined || (bundleInput.actor_account_homepage === undefined )) ? false : true;

//validate account details 
var all_errors = '';
var all_error_flag = false;
all_error_msgs = {
    actor_email: {
        require: 'Actor Email is required. ',
        valid: 'Valid actor email is required. '
    },
    account:{
multiple_IFI:'Only single Inverse Function Identifier is required. Please provide either actor email or actor account name and homepage. ',
none_IFI:"Invalid actor account detail. Please provide either actor email or actor account name and homepage. "
    } ,
    invalid_id_errors: {
        object_ids: 'Activity Id is invalid.',
        verb: 'Verb is invalid.',
        object_definition_type: 'Activity type is invalid. ',
        parent_ids: 'Parent Id is invalid. ',
        group_ids: 'Group Id is invalid. ',
        actor_account_homepage: 'Actor account homepage is invalid. '

    }
    ,
    invalid_extension_errors: {
        result_extensions: 'Result extensions is invalid. ',
        context_extensions: 'Context extensons is invalid. '

    },
    invalid_extension_json_errors: {
        result_extensions: 'Result extensions JSON is invalid. ',
        context_extensions: 'Context extensions JSON is invalid. '
    },
    result:{
        scaled:{numeric:'Result scaled value must be numeric. ',
          range:'Result scaled must be exist between 1, -1 decimal value. '
        },
        raw:{min:'Result raw value must be greater than min. ',
          max:'Result raw value must be less than max. ',
          numeric:'Result raw value must be numeric. '
        },
        min:{numeric:'Result Min value must be numeric. ',
        range:'Result min value less than max. '
        },
        max:{numeric:'Result max value must be numeric. ',
        range:'Result max value greater than min. '
        },
        duration:'Result duration value must be integer. '
    }
};
//return {ret:bundleInput.actor_account_homepage}
if(actor_account_details ===false  && actor_email === undefined){
    all_errors += all_error_msgs.account.none_IFI + '\r\n';
    all_error_flag = true;
}else if(bundleInput.actor_account_name !== undefined && actor_email !== undefined 
    ||  bundleInput.actor_account_homepage !== undefined && actor_email !== undefined  ){
    all_errors += all_error_msgs.account.multiple_IFI + '\r\n';
    all_error_flag = true;
}else{
    // if (bundleInput.actor_email === undefined) {
    //     all_errors += all_error_msgs.actor_email.require + '\n';
    //     all_error_flag = true;
    // }
    const emailRegexp = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
//	return {'ip':emailRegexp.test(bundleInput.actor_email)};
if (bundleInput.actor_email !== undefined && emailRegexp.test(bundleInput.actor_email) === false) {
    all_errors += all_error_msgs.actor_email.valid + '\n';
    all_error_flag = true;
}
}


// object_ids, verb, activity_type_id, parent_id, gorup_id
var id_ar = {
    object_ids: bundleInput.object_ids,
    verb: bundleInput.verb,
    object_definition_type: bundleInput.object_definition_type,
    parent_ids: bundleInput.parent_ids,
    group_ids: bundleInput.group_ids,
    actor_account_homepage:bundleInput.actor_account_homepage
};
var invalid_id_ar = [];
var invalid_ids = { object_ids: false, verb: false, object_definition_type: false, parent_ids: false, group_ids: false, actor_account_homepage: false };
for (let x_key in id_ar) {

    if (id_ar[x_key] !=="" && id_ar[x_key] !==undefined && /^[a-z]+:/i.test(id_ar[x_key]) === false) {
      //return {sdf:id_ar[x_key],res:/^[a-z]+:/i.test(id_ar[x_key]),df:x_key};
        invalid_ids.x_key = true;
        invalid_id_ar.push(x_key);
    }
}
//return {GH:id_ar[x_key]};
for (let item_key in invalid_id_ar) {
    console.log(invalid_id_ar[item_key]);
    if (bundleInput[invalid_id_ar[item_key]] !== undefined) {
        all_errors += all_error_msgs.invalid_id_errors[invalid_id_ar[item_key]] + '\n';
        all_error_flag = true;

    }
}

var isValidJson = (json) => {
    try {
        JSON.parse(json);
        return true;
    } catch (e) {
        return false;
    }
}
var extension_ar = {
    result_extensions: bundleInput.result_extensions,
    context_extensions: bundleInput.context_extensions
};
var invalid_extension_ar = [];
var invalid_extensions = { result_extensions: false, context_extensions: false };

for (let x_key in extension_ar) {
    if (bundleInput[x_key] !== undefined) {
        if (isValidJson(bundleInput[x_key]) === false) {
            all_errors += all_error_msgs.invalid_extension_json_errors[x_key] + '\n';
            all_error_flag = true;
        } else {
            var extension_keys = Object.keys(JSON.parse(bundleInput[x_key]));
            //return {ret:JSON.parse(bundleInput[x_key]),df:bundleInput[x_key]} 
            //console.log(extension_keys);
            //return {sdf:JSON.parse(bundleInput[x_key])}
            if(extension_keys.length !=0){
            for (let x_item in extension_keys) {
                if (/^[a-z]+:\/\//i.test(extension_keys[x_item]) === false) {
                    all_errors += all_error_msgs.invalid_extension_errors[x_key] + '\n';
                    all_error_flag = true;
                }
            }
            }else{
                    all_errors += all_error_msgs.invalid_extension_errors[x_key] + '\n';
                    all_error_flag = true;
            }
        }
    }
}
//start validating result fields
// return {'ret':typeof bundleInput.result_score_min};

// if (typeof bundleInput.result_score_min !== "number") {

// return {'ret':Number.isNaN(bundleInput.result_score_min)};
// }
  if (bundleInput.result_scaled_score !== undefined 
    && Number.isNaN(bundleInput.result_scaled_score)){
    all_errors += all_error_msgs.result.scaled.numeric + '\n';
    all_error_flag = true;
    }
    if (bundleInput.result_score_raw !== undefined 
    && Number.isNaN(bundleInput.result_score_raw)){
    all_errors += all_error_msgs.result.raw.numeric + '\n';
    all_error_flag = true;
    }
if (bundleInput.result_score_min !== undefined 
    && Number.isNaN(bundleInput.result_score_min)){
    all_errors += all_error_msgs.result.min.numeric + '\n';
    all_error_flag = true;
    }
    if (bundleInput.result_score_max !== undefined 
    && isNaN(bundleInput.result_score_max)){
    all_errors += all_error_msgs.result.max.numeric + '\n';
    all_error_flag = true;
    }
   
     
if ( bundleInput.result_scaled_score > 1 
    || bundleInput.result_scaled_score < -1) {
    all_errors += all_error_msgs.result.scaled.range + '\n';
    all_error_flag = true;
}
if (bundleInput.result_raw_score !== undefined 
    && bundleInput.result_score_min !== undefined
    && bundleInput.result_raw_score < bundleInput.result_score_min ) {
    all_errors += all_error_msgs.result.raw.min + '\n';
    all_error_flag = true;
}
if (bundleInput.result_raw_score !== undefined 
    && bundleInput.result_score_max !== undefined
    && bundleInput.result_raw_score > bundleInput.result_score_max) {
    all_errors += all_error_msgs.result.raw.max + '\n';
    all_error_flag = true;
}
if (bundleInput.result_score_min !== undefined 
&& bundleInput.result_score_max !== undefined 
     && bundleInput.result_score_min > bundleInput.result_score_max
  ) {
    all_errors += all_error_msgs.result.min.range + '\n';
    all_error_flag = true;
}
bundleInput.result_duration=parseInt(bundleInput.result_duration);

if(bundleInput.result_duration !== undefined && !isNaN(bundleInput.result_duration)){
    bundleInput.result_duration = 'PT'+bundleInput.result_duration/1000+'S';      
    }
    // else if(isNaN(bundleInput.result_duration)){
    //     all_errors += all_error_msgs.result.duration + '\n';
    //     all_error_flag = true;
    // }
//validate for  empty extensions values and also for code identation
if (all_error_flag === true && all_errors !== "") {
    throw new z.errors.HaltedError(all_errors);
}

// start create options for request
var requestBody= {};
//start creating actor object
var actor ={};
if (bundleInput.actor_name !== undefined) {
    actor.name = bundleInput.actor_name;
}
if (actor_email !== undefined) {
    actor.mbox = actor_email;
}
var account = {}
if (bundleInput.actor_account_name !== undefined) {
    account.name = bundleInput.actor_account_name;
    account.homePage = bundleInput.actor_account_homepage;
    actor.account = account;
}
requestBody.actor = actor;

//start creating verb object
var verb ={};
if (bundleInput.verb !== undefined) {
    verb.id = bundleInput.verb;
}
if (bundleInput.verb_display !== undefined) {
  verb.display = {en:''};
    verb.display.en = bundleInput.verb_display;
}
requestBody.verb = verb;

//start creating result object
var score ={};
if(bundleInput.result_score_min !== undefined){
    score.min = bundleInput.result_score_min;
}
if(bundleInput.result_score_min !== undefined){
    score.max = bundleInput.result_score_max;
}
if(bundleInput.result_raw_score !== undefined){
    score.raw = bundleInput.result_raw_score;
}
if(bundleInput.result_scaled_score !== undefined){
    score.scaled = bundleInput.result_scaled_score;
}
var result ={};
if(Object.keys(score).length !== 0 && score.constructor === Object){
    result.score = score;
}
if(bundleInput.result_duration !== undefined){
    result.duration = bundleInput.result_duration;
}
if(bundleInput.result_completion !== undefined){
    result.completion = bundleInput.result_completion;
}
if(bundleInput.result_success !== undefined){
    result.success = bundleInput.result_success;
}
if(bundleInput.result_response !== undefined){
    result.response = bundleInput.result_response;
}
if(Object.keys(result).length !== 0 && result.constructor === Object){
requestBody.result = result;
}
//start creating activity object
var objects = {};
var  object_definition = {name:{en:''}};
if(bundleInput.activity_name !== undefined){
    object_definition.name.en = bundleInput.activity_name;
    objects.definition = object_definition;

}
if(bundleInput.object_type_ids !== undefined){
  if(objects.definition !== undefined){
        objects.definition.type = bundleInput.object_type_ids;
  }else{
        object_definition = {};
        objects.definition.type = bundleInput.object_type_ids;
  }


}
if(bundleInput.activity_description !== undefined){
  if(objects.definition !== undefined){
        objects.definition.description = {en:bundleInput.activity_description};
  }else{
        object_definition = {};
        objects.definition.description = {en:bundleInput.activity_description};
  }


}

if(bundleInput.object_ids !== undefined){
    objects.id = bundleInput.object_ids;
}

if(Object.keys(objects).length !== 0 && objects.constructor === Object){
    requestBody.object = objects;
}
//start creating context object 
var context = {};
if(bundleInput.group_ids !== undefined){
    if(context.contextActivities === undefined){
        context.contextActivities ={};
        context.contextActivities.grouping=[];
        context.contextActivities.grouping.push({id:bundleInput.group_ids});
    }else{
        context.contextActivities.grouping=[];
        context.contextActivities.grouping.push({id:bundleInput.group_ids});
    }
} 
if(bundleInput.parent_ids !== undefined){
    if(context.contextActivities === undefined){
        context.contextActivities ={};
        context.contextActivities.parent=[];
        context.contextActivities.parent.push({id:bundleInput.parent_ids}) ;
    }else{
        context.contextActivities.parent =[];
        context.contextActivities.parent.push({id:bundleInput.parent_ids}) ;
    }
}
if(Object.keys(context).length !== 0 && context.constructor === Object){
    requestBody.context = context;
}
//start creating extension object
if (bundleInput.result_extensions !== undefined) {
     if(requestBody.result === undefined){
    requestBody.result = {};
        requestBody.result.extensions = JSON.parse(bundleInput.result_extensions);
  }else{
            requestBody.result.extensions = JSON.parse(bundleInput.result_extensions);
  }
}

if (bundleInput.context_extensions !== undefined) {
  if(requestBody.context === undefined){
    requestBody.context = {};
        requestBody.context.extensions = JSON.parse(bundleInput.context_extensions);
  }else{
            requestBody.context.extensions = JSON.parse(bundleInput.context_extensions);
  }
}
const options = {
    url: bundleAuth.api_end_point + 'statements',
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        "X-Experience-API-Version": "1.0.3",
        'Authorization': auth

    },
    params: {

    },
    body: requestBody
}
// return requestBody;
return z.request(options)
    .then((response) => {
        response.throwForStatus();
        const results = z.JSON.parse(response.content);
        const results2 = { 'statement_id': results[0] };
        // You can do any parsing you need for results here before returning them
        return results2;
    });

