/*******************************************************************************
 **
 ** xapi object to be used in SCORM wrapper
 ** 
 ** Version 1.1
 **
 ** Converts many SCORM 2004 data model elements to associated xAPI data
 **
 *******************************************************************************/
xapi = function () {
	
    var _debug = true;


    /*******************************************************************************
     **
     ** Configuration object for a specific instance of the wrapper
     **
     ** The following configuration values must be set in order for this 
     ** wrapper to function correctly:
     **
     ** LRS Data
     ** -----------
     ** endpoint - Points at the LRS endpoint
     ** user -  Username for the LRS
     ** password - Password for the LRS
     **
     ** Other Configuration Values
     ** ----------------------------
     ** courseId - IRI for the course this wrapper is used in
     ** lmsHomePage - LMS home page where the course is/will be imported
     ** isScorm - Whether the original course is SCORM 2004 (or SCORM Version 1.2)
     ** activityId - The ID that will identify the SCO/Activity in the LRS
     ** groupingContextActivity - Context activity for a synchronous workshop (if applicable)
     **
     ** Note: DO NOT UPDATE THE "constants" below.  These are used to indentify
     **       SCORM profile information and should not be changed
     **
     *******************************************************************************/
    var adl_activity_id = ADL.XAPIWrapper.lrs.activity_id !== undefined && ADL.XAPIWrapper.lrs.activity_id != null ?ADL.XAPIWrapper.lrs.activity_id:'';
    var adl_actor_email = ADL.XAPIWrapper.lrs.actor.mbox !== undefined && ADL.XAPIWrapper.lrs.actor.mbox != null ? ADL.XAPIWrapper.lrs.actor.mbox.replace('mailto:',''):'';
    var adl_actor_name = ADL.XAPIWrapper.lrs.actor.name !== undefined && ADL.XAPIWrapper.lrs.actor.name != null ? ADL.XAPIWrapper.lrs.actor.name:'';

    var config = {
        lrs: {
            endpoint: "https://lrs.adlnet.gov/xapi/",
            user: "",
            password: ""
        },
        courseId: "",
        lmsHomePage: "http://demostaging.nextsoftwaresolutions.com",
        isScorm:false,
        activityId: adl_activity_id,
        groupingContextActivity: {}
    };
    var actor = JSON.parse(ADL.XAPIWrapper.lrs.actor);
    // xAPI SCORM Profile IRI contstants
    // https://github.com/adlnet/xAPI-SCORM-Profile/blob/master/xapi-scorm-profile.md
    var constants = {
        activityProfileIri: "https://w3id.org/xapi/scorm/activity-profile",
        activityStateIri: "https://w3id.org/xapi/scorm/activity-state",
        actorProfileIri: "https://w3id.org/xapi/scorm/agent-profile",
        attemptStateIri: "https://w3id.org/xapi/scorm/attempt-state"
    };

    // used to hold the data model elements to be used based on SCORM Version
    var scormVersionConfig = {};

    // used to identify if a suspend occurred
    var exitSetToSuspend = false;

    /*******************************************************************************
     **
     ** Base statement
     ** 
     ** Must update verb, attempt and result (if applicable) to execute
     **
     *******************************************************************************/
    var getBaseStatement = function () {
        if (window.localStorage.learnerId == null) {
            window.localStorage.learnerId = retrieveDataValue(scormVersionConfig.learnerIdElement);
        }

        return {
            actor: actor ,
            verb: {},
            object: {
                id: config.activityId,
                definition: {
                    type: "http://adlnet.gov/expapi/activities/lesson"
                }
            },
            context: {
                contextActivities: {
                    grouping: [
                        {
                            id: "",
                            objectType: "Activity",
                            definition: {
                                type: "http://adlnet.gov/expapi/activities/attempt"
                            }
                  },
                        {
                            id: config.courseId,
                            objectType: "Activity",
                            definition: {
                                type: "http://adlnet.gov/expapi/activities/course"
                            }
                  }
               ],
                    category: [
                        {
                            id: "https://w3id.org/xapi/scorm"
                  }
               ]
                }
            }
        };
    }

    /*******************************************************************************
     **
     ** Interactions base statement
     ** 
     ** Must update object iri, attempt, result and interaction 
     ** type/description to execute
     **
     *******************************************************************************/
    var getInteractionsBaseStatement = function () {
        if (window.localStorage.learnerId == null) {
            window.localStorage.learnerId = retrieveDataValue(scormVersionConfig.learnerIdElement);
        }

        return {
            actor: actor,
            verb: ADL.verbs.answered,
            object: {
                objectType: "Activity",
                id: ADL.XAPIWrapper.lrs.activity_id,
                definition: {
                    type: "http://adlnet.gov/expapi/activities/cmi.interaction",
                    interactionType: "",
                    correctResponsesPattern: []
                }
            },
            context: {
                contextActivities: {
                    parent: [
                        {
                            id: config.activityId,
                            objectType: "Activity",
                            definition: {
                                type: "http://adlnet.gov/expapi/activities/lesson"
                            }
                     }
                  ],
                    grouping: [
                        {
                            id: "",
                            objectType: "Activity",
                            definition: {
                                type: "http://adlnet.gov/expapi/activities/attempt"
                            }
                     },
                        {
                            id: config.courseId,
                            objectType: "Activity",
                            definition: {
                                type: "http://adlnet.gov/expapi/activities/course"
                            }
                     }
                  ],
                    category: [
                        {
                            id: "https://w3id.org/xapi/scorm"
                     }
                  ]
                }
            },
            result: {
                response: ""
            }
        };
    }

    /*******************************************************************************
     **
     ** Voided base statement
     ** 
     ** Must set verb and object to execute
     **
     *******************************************************************************/
    var getVoidedBaseStatement = function () {
        if (window.localStorage.learnerId == null) {
            window.localStorage.learnerId = retrieveDataValue(scormVersionConfig.learnerIdElement);
        }

        return {
            actor: actor,
            verb: {},
            object: {
                objectType: "StatementRef",
                id: ""
            }
        };
    }

    /*******************************************************************************
     **
     ** Gets agent - account corresponding to LMS user registration
     ** 
     ** Used when accessing state objects
     **
     *******************************************************************************/
    var getAgent = function () {


        return actor;
    }

    /*******************************************************************************
     **
     ** This function is used to initiate an xAPI attempt
     **
     *******************************************************************************/
    var initializeAttempt = function () {        
        // configure SCORM version and data elements, get launch data from lms, etc
        configureXAPIData();
        
        // configure lrs
        configureLRS();

        // deprecated - set the agent profile information based on LMS learner_prefernces
        //setAgentProfile();

        // todo: add error handling to SCORM call
        // Determine whether this is a new or resumed attempt (based on cmi.entry)
        var entry = retrieveDataValue(scormVersionConfig.entryElement);


        var isResumed = (entry == "resume");

        // if "resume", determine if the user issued a suspend sequencing nav 
        // request and a terminate was called instead of a suspend and if so, fix
        if (isResumed) {
            adjustFinishStatementForResume();
        }

        // set the attempt context activity based on the SCOs state
        configureAttemptContextActivityID(entry);

        // Set activity profile info and attempt state every initialize
        // todo: these cause acceptable errors.  ensure they are not written to console
        setActivityProfile();
        setAttemptState();

        // Set the appropriate verb based on resumed or new attempt
        var startVerb = isResumed ? ADL.verbs.attempted : ADL.verbs.attempted;

        // Execute the statement
        sendSimpleStatement(startVerb);
    }

    /*******************************************************************************
     **
     ** This function looks at the last terminate or statement for a given attempt.
     ** If "terminated", the terminated stmt is voided and a suspend is issued
     **
     *******************************************************************************/
    var adjustFinishStatementForResume = function () {
        var search = ADL.XAPIWrapper.searchParams();
        search['verb'] = ADL.verbs.terminated.id;
        search['activity'] = window.localStorage[config.activityId];
        search['related_activities'] = true;

        var res = ADL.XAPIWrapper.getStatements(search);

        if (res.statements.length == 1) {
            // there is a terminate verb, so must void it and replace with suspended
            // Note: if there is length == 0, no issue.  
            //       if length > 1, things are very messed up. Do nothing.

            var terminateStmt = res.statements[0];

            // send the voided statement
            var voidedStmt = getVoidedBaseStatement();
            voidedStmt.verb = ADL.verbs.voided;
            voidedStmt.object.id = terminateStmt.id;
        			
            var response = ADL.XAPIWrapper.sendStatement(voidedStmt);

            // send a suspended statement to replace the (voided) terminated statement
            suspendAttempt(terminateStmt.timestamp);


        }

    }


    /*******************************************************************************
     **
     ** This function is used to resume an attempt
     **
     *******************************************************************************/
    var resumeAttempt = function () {
        sendSimpleStatement(ADL.verbs.resumed);
    }

    /*******************************************************************************
     **
     ** This function is used to suspent an attempt
     **
     *******************************************************************************/
    var suspendAttempt = function (timestamp) {
        //sendSimpleStatement(ADL.verbs.suspended);
        var stmt = getBaseStatement();
        stmt.verb = ADL.verbs.suspended;

        if (timestamp != undefined && timestamp != null) {
            stmt.timestamp = timestamp;
        }

        // window.localStorage[activity] uses activity id to return the most recent
        // attempt
        stmt.context.contextActivities.grouping[0].id = window.localStorage[config.activityId];
        
        // set the context activity from the manifest/launch_data to group together
        // for an event
        stmt.context.contextActivities.grouping.push(config.groupingContextActivity);

        var stmtWithResult = getStmtWithResult(stmt);
                			
        var response = ADL.XAPIWrapper.sendStatement(stmtWithResult);
    }

    /*******************************************************************************
     **
     ** This function is used to terminate an xAPI attempt
     **
     *******************************************************************************/
    var terminateAttempt = function () {
        //sendSimpleStatement(ADL.verbs.terminated);
        var stmt = getBaseStatement();

        // get the exit and use appropriate verb
        var stopVerb = (exitSetToSuspend) ? ADL.verbs.suspended : ADL.verbs.terminated;

        stmt.verb = stopVerb;

        // window.localStorage[activity] uses activity id to return the most recent
        // attempt
        stmt.context.contextActivities.grouping[0].id = window.localStorage[config.activityId];
        
        // set the context activity from the manifest/launch_data to group together
        // for an event
        stmt.context.contextActivities.grouping.push(config.groupingContextActivity);

        var stmtWithResult = getStmtWithResult(stmt);
        if(exitSetToSuspend){
                    			
        var response = ADL.XAPIWrapper.sendStatement(stmtWithResult);
        }

        window.localStorage.removeItem("learnerId");
    }

    /*******************************************************************************
     **
     ** This function is used to complete the stmt result for terminate and suspend
     **
     *******************************************************************************/
    var getStmtWithResult = function (baseStatement) {
        var success = retrieveDataValue(scormVersionConfig.successElement);
        var completion = retrieveDataValue(scormVersionConfig.completionElement);
        var scoreScaled = retrieveDataValue(scormVersionConfig.scoreScaledElement);
        var scoreRaw = retrieveDataValue(scormVersionConfig.scoreRawElement);
        var scoreMin = retrieveDataValue(scormVersionConfig.scoreMinElement);
        var scoreMax = retrieveDataValue(scormVersionConfig.scoreMaxElement);

        var resultSet = false;
        var resultJson = {};
        var scoreSet = false;
        var scoreJson = {};

        // create all of the statement json 

        // set success if known
        if (success == "passed") {
            resultSet = true;
            resultJson.success = true;
        } else if (success == "failed") {
            resultSet = true;
            resultJson.success = false;
        }

        // set completion if known
        if (completion == "completed") {
            resultSet = true;
            resultJson.completion = true;
        } else if (completion == "incomplete") {
            resultSet = true;
            resultJson.completion = false;
        }

        // set scaled score if set by sco
        if (scoreScaled != undefined && scoreScaled != "") {
            scoreSet = true;
            resultSet = true;
            scoreJson.scaled = parseFloat(scoreScaled);
        }

        // set raw score if set by sco
        if (scoreRaw != undefined && scoreRaw != "") {
            scoreSet = true;
            resultSet = true;
            scoreJson.raw = parseFloat(scoreRaw);

            // if SCORM 1.2, use raw score / 100 for scaled score
            if (!config.isScorm) {
                scoreJson.scaled = parseFloat(scoreRaw) / 100;
            }
        }

        // set min score if set by sco
        if (scoreMin != undefined && scoreMin != "") {
            scoreSet = true;
            resultSet = true;
            scoreJson.min = parseFloat(scoreMin);
        }

        // set max score if set by sco
        if (scoreMax != undefined && scoreMax != "") {
            scoreSet = true;
            resultSet = true;
            scoreJson.max = parseFloat(scoreMax);
        }

        // set the score object in with the rest of the result object
        if (scoreSet) {
            resultJson.score = scoreJson;
        }

        // add result to the base statement
        if (resultSet) {
            baseStatement.result = resultJson;
        }

        return baseStatement;
    }

    /*******************************************************************************
     **
     ** This function is used to set agent data based on SCORM learner prefs
     **
     ** Deprecated
     **
     *******************************************************************************/
    var setAgentProfile = function () {

        if (window.localStorage.learnerId == null) {
            window.localStorage.learnerId = retrieveDataValue(scormVersionConfig.learnerIdElement);
        }

        var lang = retrieveDataValue(scormVersionConfig.languageElement);
        var audioLevel = retrieveDataValue(scormVersionConfig.audioLevelElement);
        var deliverySpeed = retrieveDataValue(scormVersionConfig.deliverySpeedElement);
        var audioCaptioning = retrieveDataValue(scormVersionConfig.audioCaptioningElement);

        var profile = {
            language: lang,
            audio_level: audioLevel,
            delivery_speed: deliverySpeed,
            audio_captioning: audioCaptioning
        };

        ADL.XAPIWrapper.sendAgentProfile(actor,
            config.activityId,
            profile,
            null,
            "*"
        );
    }

    /*******************************************************************************
     **
     ** This function is used to set activity profile information 
     **
     ** Note: this data is scoped to an activity and does not (normally) change
     **
     *******************************************************************************/
    var setActivityProfile = function () {
        // see if the profile is already set
        var ap = ADL.XAPIWrapper.getActivityProfile(config.activityId, constants.activityProfileIri);

        if (ap == null) {
            // get comments from lms (if any)
            //var cmi_num_comments_from_lms_count = retrieveDataValue("cmi.comments_from_lms._count");
            // todo: get the comments, if any and add to array

            // get completion threshold (if supplied in manifest)
            var cmi_completion_threshold = retrieveDataValue(scormVersionConfig.completionThresholdElement);
            var cmi_launch_data = retrieveDataValue(scormVersionConfig.launchDataElement);
            var cmi_max_time_allowed = retrieveDataValue(scormVersionConfig.maxTimeAllowedElement);
            var cmi_scaled_passing_score = retrieveDataValue(scormVersionConfig.scaledPassingScoreElement);
            var cmi_time_limit_action = retrieveDataValue(scormVersionConfig.timeLimitActionElement);

            var activityProfile = {};

            if (config.isScorm && cmi_completion_threshold != "")
                activityProfile.completion_threshold = cmi_completion_threshold;

            if (cmi_launch_data != "")
                activityProfile.launch_data = cmi_launch_data;

            if (cmi_max_time_allowed != "")
                activityProfile.max_time_allowed = cmi_max_time_allowed;

            if (cmi_scaled_passing_score != "")
                activityProfile.scaled_passing_score = cmi_scaled_passing_score;

            if (cmi_time_limit_action != "")
                activityProfile.time_limit_action = cmi_time_limit_action;

            ADL.XAPIWrapper.sendActivityProfile(config.activityId, constants.activityProfileIri, activityProfile, null, "*");
        }
    }

    /*******************************************************************************
     **
     ** This function is used to set activity state
     **
     ** Note: State data about an activity that is different for each user
     **
     **       This is used to also update attempt iri array associated with 
     **       the user and activity
     **
     *******************************************************************************/
    var setActivityState = function () {
        // window.localStorage[activity] uses activity id to return the most recent
        // attempt
        var attemptIri = window.localStorage[config.activityId];

        var agent = getAgent();

        // see if the profile is already set
        var as = ADL.XAPIWrapper.getState(config.activityId, agent, constants.activityStateIri);

        // First time, create a new one
        if (as == null || as =='' || as == undefined) {
            ADL.XAPIWrapper.sendState(config.activityId, agent, constants.activityStateIri, null, {
                attempts: [attemptIri]
            });
        } else {
            // update state
            var asStr = JSON.stringify(as)
            var newAs = JSON.parse(asStr);

            newAs.attempts.push(attemptIri);

            ADL.XAPIWrapper.sendState(config.activityId, agent, constants.activityStateIri, null, newAs, ADL.XAPIWrapper.hash(asStr));
        }
    }

    /*******************************************************************************
     **
     ** This function is used to set attempt (activity) state
     **
     ** Note: State data about an activity that is different for each user, for each
     **       attempt.
     **
     *******************************************************************************/
    var setAttemptState = function () {
        // window.localStorage[activity] uses activity id to return the most recent
        // attempt
        var attemptIri = window.localStorage[config.activityId];
        var agent = getAgent();

        // location, preferences object, credit, lesson_mode, suspend_data, 
        // total_time, adl_data
        var cmi_location = retrieveDataValue(scormVersionConfig.locationElement);
        
        var cmi_language = retrieveDataValue(scormVersionConfig.languageElement);
        var cmi_audio_level = retrieveDataValue(scormVersionConfig.audioLevelElement);
        var cmi_delivery_speed = retrieveDataValue(scormVersionConfig.deliverySpeedElement);
        var cmi_audio_captioning = retrieveDataValue(scormVersionConfig.audioCaptioningElement);

        var preferences = {
            language: cmi_language,
            audio_level: cmi_audio_level,
            delivery_speed: cmi_delivery_speed,
            audio_captioning: cmi_audio_captioning
        };

        var cmi_credit = retrieveDataValue(scormVersionConfig.creditElement);
        var cmi_mode = retrieveDataValue(scormVersionConfig.modeElement);
        var cmi_suspend_data = retrieveDataValue(scormVersionConfig.suspendDataElement);
        var cmi_total_time = retrieveDataValue(scormVersionConfig.totalTimeElement);

        // todo: implement adl.data buckets and store in attempt state

        // create the state object
        var state = {};

        if (cmi_location != "")
            state.location = cmi_location;

        state.preferences = preferences;

        if (cmi_credit != "")
            state.credit = cmi_credit;

        if (cmi_mode != "")
            state.mode = cmi_mode;

        if (cmi_suspend_data != "")
            state.suspend_data = cmi_suspend_data;

        if (cmi_total_time != "")
            state.total_time = cmi_total_time;



        // see if the profile is already set
        var as = ADL.XAPIWrapper.getState(attemptIri, agent, constants.attemptStateIri);

        if (as == null) {
            // first set on this attempt
            ADL.XAPIWrapper.sendState(attemptIri, agent, constants.attemptStateIri, null, state);
        } else {
            var asStr = JSON.stringify(as);

            // updating existing attempt
            ADL.XAPIWrapper.sendState(attemptIri, agent, constants.attemptStateIri, null, state, ADL.XAPIWrapper.hash(asStr));
        }
    }

    /*******************************************************************************
     **
     ** This function is used to route set values to the appropriate functions
     **
     *******************************************************************************/
    var saveDataValue = function (name, value) {
            var isInteraction = name.indexOf("cmi.interactions") > -1;

            if (isInteraction) {
                setInteraction(name, value);
            } else {
                // Handle only non-array scorm data model elements  
                switch (name) {
                    case scormVersionConfig.scoreScaledElement:
                        setScore(value);
                        break;
                    case scormVersionConfig.completionElement:
                        setComplete(value);
                        break;
                    case scormVersionConfig.successElement:
                        setSuccess(value);
                        break;
                    case scormVersionConfig.exitElement:
                        exitSetToSuspend = (value == "suspend");
                        break;
                    default:
                        break;
                }
            }
        }
        /*******************************************************************************
         **
         ** This function/vars is used to handle the interaction type
         **
         *******************************************************************************/
    var setInteraction = function (name, value) {
        // key for interactions in local storage is scoped to an attempt
        var interactionsKey = window.localStorage[config.activityId] + "_interactions";

        // get the interactions from local storage
        var cachedInteractionsStr = window.localStorage.getItem(interactionsKey);
        var cachedInteractions = [];
        if (cachedInteractions != null) {
            // get as JSON object array
            var cachedInteractions = JSON.parse(cachedInteractionsStr);
        }

        // figure out what the set value was in the SCORM call
        elementArray = name.split(".");
        var intIndex = elementArray[2];
        var subElement = elementArray[3];

        if (subElement == "id") {
            // its a new interaction.  Set it in local storage
            var newInteraction = {
                index: intIndex,
                id: value,
                type: "",
                learner_response: "",
                result: "",
                description: ""
            };

            if (cachedInteractions != null) {
                // this is not the first interaction set
                cachedInteractions.push(newInteraction);

                // push to local storage
                window.localStorage.setItem(interactionsKey, JSON.stringify(cachedInteractions));
            } else {
                // this is the first interaction set
                window.localStorage.setItem(interactionsKey, JSON.stringify([newInteraction]));
            }
        } else if (subElement == "type") {
            // find interaction with the same index and set type in JSON array
            for (var i = 0; i < cachedInteractions.length; i++) {
                if (cachedInteractions[i].index == intIndex) {
                    // found matching index so update this object's type
                    cachedInteractions[i].type = value;

                    // update local storage
                    window.localStorage.setItem(interactionsKey, JSON.stringify(cachedInteractions));

                    break;
                }
            }
        } else if (subElement == "description") {
            // find interaction with the same index and set type in JSON array
            for (var i = 0; i < cachedInteractions.length; i++) {
                if (cachedInteractions[i].index == intIndex) {
                    // found matching index so update this object's type
                    cachedInteractions[i].description = value;

                    // update local storage
                    window.localStorage.setItem(interactionsKey, JSON.stringify(cachedInteractions));

                    break;
                }
            }
        } else if (subElement == "learner_response" || subElement == "student_response") {
            // find interaction with the same index and set type in JSON array
            for (var i = 0; i < cachedInteractions.length; i++) {
                if (cachedInteractions[i].index == intIndex) {
                    // found matching index so update this object's type
                    cachedInteractions[i].learner_response = value;

                    // update local storage
                    window.localStorage.setItem(interactionsKey, JSON.stringify(cachedInteractions));

                    // Send xAPI Statement
                    // Note: this implementation
                    var stmt = getInteractionsBaseStatement();
                    stmt.object.id = getInteractionIri(cachedInteractions[i].id);
                    stmt.context.contextActivities.grouping[0].id = window.localStorage[config.activityId];
                    
                    // set the context activity from the manifest/launch_data to group together
                    // for an event
                    stmt.context.contextActivities.grouping.push(config.groupingContextActivity);

                    // set the learner's response
                    stmt.result.response = cachedInteractions[i].learner_response;

                    // todo: shouldn't assume en-US - implement with default if not specified, or use what was sent
                    if (config.isScorm) {
                        stmt.object.definition.description = {
                            "en-US": cachedInteractions[i].description
                        };
                    }

                    // set the specific interaction type
                    stmt.object.definition.interactionType = cachedInteractions[i].type;

                    // get any type specific JSON that an LRS *may* require
                    switch (cachedInteractions[i].type) {
                        case "choice":
                            stmt.object.definition.choices = [];
                            break;
                        case "likert":
                            stmt.object.definition.scale = [];
                            break;
                        case "matching":
                            stmt.object.definition.source = [];
                            stmt.object.definition.target = [];
                            break;
                        case "performance":
                            stmt.object.definition.steps = [];
                            break;
                        case "sequencing":
                            stmt.object.definition.choices = [];
                            break;
                        default:
                            break;
                    }

                    // todo: make the subelement that you send stmt on configurable
                    // send statement
                    var response = ADL.XAPIWrapper.sendStatement(stmt);

                    // remove interaction from local storage array so its not processed again
                    cachedInteractions.splice(i, 1);
                }
            }
        }

    }

    /*******************************************************************************
     **
     ** This function is used to get an interaction iri
     **
     *******************************************************************************/
    var getInteractionIri = function (interactionId) {
        return config.activityId + "/interactions/" + encodeURIComponent(interactionId);
    }

    /*******************************************************************************
     **
     ** This function is used to set a scaled score
     **
     *******************************************************************************/
    var setScore = function (value) {
        // For scorm 1.2, must divide raw by 100
        var score = (config.isScorm) ? parseFloat(value) : parseFloat(value) / 100;

        var stmt = getBaseStatement();
        stmt.verb = ADL.verbs.scored;
        stmt.context.contextActivities.grouping[0].id = window.localStorage[config.activityId];
        
        // set the context activity from the manifest/launch_data to group together
        // for an event
        stmt.context.contextActivities.grouping.push(config.groupingContextActivity);

        // todo: add error handling if value is not a valid scaled score
        stmt.result = {
            score: {
                scaled: score
            }
        };
        			

        var response = ADL.XAPIWrapper.sendStatement(stmt);
    }

    /*******************************************************************************
     **
     ** This function is used to complete an activity
     **
     *******************************************************************************/
    var setComplete = function (value) {
        if (value == "completed" || value == "complete") {
            sendSimpleStatement(ADL.verbs.completed);
        }
    }

    /*******************************************************************************
     **
     ** This function is used to set pass/failed on an activity
     **
     *******************************************************************************/
    var setSuccess = function (value) {
        // if SCORM 1.2, these could be complete/incomplete
        if (value == "passed" || value == "failed")
            sendSimpleStatement(ADL.verbs[value]);
    }

    /*******************************************************************************
     **
     ** This function is used to configure LRS endpoint and other values
     **
     *******************************************************************************/
      var setConfig = function(iConfig)
      {
         config.lrs.endpoint = iConfig.lrs.endpoint;
         config.lrs.user = iConfig.lrs.user;
         config.lrs.password = iConfig.lrs.password;
         config.courseId = iConfig.courseId;
         config.lmsHomePage = iConfig.lmsHomePage;
         config.isScorm = false;
    
         // setup SCORM object based on configuration
         scormVersionConfig = {
            learnerIdElement: (config.isScorm) ? "cmi.learner_id" : "cmi.core.student_id",
            entryElement: ((config.isScorm == true) ? "cmi.entry" : "cmi.core.entry"),
            exitElement: (config.isScorm) ? "cmi.exit" : "cmi.core.exit",
            successElement: (config.isScorm) ? "cmi.success_status" : "cmi.core.lesson_status",
            completionElement: (config.isScorm) ? "cmi.completion_status" : "cmi.core.lesson_status",
            scoreRawElement: (config.isScorm) ? "cmi.score.raw" : "cmi.core.score.raw",
            scoreMinElement: (config.isScorm) ? "cmi.score.min" : "cmi.core.score.min",
            scoreMaxElement: (config.isScorm) ? "cmi.score.max" : "cmi.core.score.max",
            scoreScaledElement: (config.isScorm) ? "cmi.score.scaled" : "cmi.core.score.raw",
            languageElement: (config.isScorm) ? "cmi.learner_preference.language" : "cmi.student_preference.language",
            audioLevelElement: (config.isScorm) ? "cmi.learner_preference.audio_level" : "cmi.student_preference.audio",
            deliverySpeedElement: (config.isScorm) ? "cmi.learner_preference.delivery_speed" : "cmi.student_preference.speed",
            audioCaptioningElement: (config.isScorm) ? "cmi.learner_preference.audio_captioning" : "cmi.student_preference.text",
            completionThresholdElement: (config.isScorm) ? "cmi.completion_threshold" : "",
            launchDataElement: "cmi.launch_data",
            maxTimeAllowedElement: (config.isScorm) ? "cmi.max_time_allowed" : "cmi.student_data.max_time_allowed",
            scaledPassingScoreElement: (config.isScorm) ? "cmi.scaled_passing_score" : "cmi.student_data.mastery_score",
            timeLimitActionElement: (config.isScorm) ? "cmi.time_limit_action" : "cmi.student_data.time_limit_action",
            locationElement: (config.isScorm) ? "cmi.location" : "cmi.core.lesson_location",
            creditElement: (config.isScorm) ? "cmi.credit" : "cmi.core.credit",
            modeElement: (config.isScorm) ? "cmi.mode" : "cmi.core.lesson_mode",
            suspendDataElement: "cmi.suspend_data",
            totalTimeElement: (config.isScorm) ? "cmi.total_time" : "cmi.core.total_time"
         }
    
      }

    /*******************************************************************************
     **
     ** This function is used to configure LRS endpoint and other values
     **
     *******************************************************************************/
    var configureXAPIData = function () {

        // get configuration information from the LMS
        scormLaunchData = retrieveDataValue("cmi.launch_data");
//         try{scormLaunchDataJSON = JSON.parse(scormLaunchData);

//         // todo: confirm launch data exists, if not default values
        
//         // set local config object with launch data information
//         config.lrs.endpoint = scormLaunchDataJSON.lrs.endpoint;
//         config.lrs.user = scormLaunchDataJSON.lrs.user;
//         config.lrs.password = scormLaunchDataJSON.lrs.password;
//         config.courseId = scormLaunchDataJSON.courseId;
//         config.lmsHomePage = scormLaunchDataJSON.lmsHomePage;
//         config.isScorm = scormLaunchDataJSON.isScorm;
//         config.activityId = scormLaunchDataJSON.activityId;
//         config.groupingContextActivity = scormLaunchDataJSON.groupingContextActivity;
// }catch{
  var  scormLaunchDataJSON = {
        lrs:ADL.XAPIWrapper.lrs,
        courseId:ADL.XAPIWrapper.lrs,
        lmsHomePage:'',
        courseId:'',
        isScorm:false,
        activityId:adl_activity_id,
        groupingContextActivity:''
    };
 // set local config object with launch data information
        config.lrs.endpoint = scormLaunchDataJSON.lrs.endpoint;
        config.lrs.user = scormLaunchDataJSON.lrs.user;
        config.lrs.password = scormLaunchDataJSON.lrs.password;
        config.courseId = scormLaunchDataJSON.courseId;
//         config.lmsHomePage = scormLaunchDataJSON.lmsHomePage;
        config.isScorm = scormLaunchDataJSON.isScorm;
        config.activityId = scormLaunchDataJSON.activityId;
        config.groupingContextActivity = scormLaunchDataJSON.groupingContextActivity;

//     message('Launch data is not available');}
        
        // setup SCORM object based on configuration
        scormVersionConfig = {
            learnerIdElement: (config.isScorm) ? "cmi.learner_id" : "cmi.core.student_id",
            entryElement: ((config.isScorm == true) ? "cmi.entry" : "cmi.core.entry"),
            exitElement: (config.isScorm) ? "cmi.exit" : "cmi.core.exit",
            successElement: (config.isScorm) ? "cmi.success_status" : "cmi.core.lesson_status",
            completionElement: (config.isScorm) ? "cmi.completion_status" : "cmi.core.lesson_status",
            scoreRawElement: (config.isScorm) ? "cmi.score.raw" : "cmi.core.score.raw",
            scoreMinElement: (config.isScorm) ? "cmi.score.min" : "cmi.core.score.min",
            scoreMaxElement: (config.isScorm) ? "cmi.score.max" : "cmi.core.score.max",
            scoreScaledElement: (config.isScorm) ? "cmi.score.scaled" : "cmi.core.score.raw",
            languageElement: (config.isScorm) ? "cmi.learner_preference.language" : "cmi.student_preference.language",
            audioLevelElement: (config.isScorm) ? "cmi.learner_preference.audio_level" : "cmi.student_preference.audio",
            deliverySpeedElement: (config.isScorm) ? "cmi.learner_preference.delivery_speed" : "cmi.student_preference.speed",
            audioCaptioningElement: (config.isScorm) ? "cmi.learner_preference.audio_captioning" : "cmi.student_preference.text",
            completionThresholdElement: (config.isScorm) ? "cmi.completion_threshold" : "",
            launchDataElement: "cmi.launch_data",
            maxTimeAllowedElement: (config.isScorm) ? "cmi.max_time_allowed" : "cmi.student_data.max_time_allowed",
            scaledPassingScoreElement: (config.isScorm) ? "cmi.scaled_passing_score" : "cmi.student_data.mastery_score",
            timeLimitActionElement: (config.isScorm) ? "cmi.time_limit_action" : "cmi.student_data.time_limit_action",
            locationElement: (config.isScorm) ? "cmi.location" : "cmi.core.lesson_location",
            creditElement: (config.isScorm) ? "cmi.credit" : "cmi.core.credit",
            modeElement: (config.isScorm) ? "cmi.mode" : "cmi.core.lesson_mode",
            suspendDataElement: "cmi.suspend_data",
            totalTimeElement: (config.isScorm) ? "cmi.total_time" : "cmi.core.total_time"
        }

    }
var getConfig = function(){
//     return {config,scormVersionConfig};
 return config.isScorm;
}
var changeConfig = function(conf_key,conf_val){
   config[conf_key]=conf_val;
   return config;
}
    /*******************************************************************************
     **
     ** This function is used to configure LRS endpoint and basic auth values
     **
     *******************************************************************************/
    var configureLRS = function () {
        var conf = {
            endpoint: config.lrs.endpoint,
            user: config.lrs.user,
            password: config.lrs.password
        };

        ADL.XAPIWrapper.changeConfig(conf);
    }

    /*******************************************************************************
     **
     ** This function is used to get the attempt context activity (grouping) id
     **
     *******************************************************************************/
    var configureAttemptContextActivityID = function (cmiEntryValue) {
        // window.localStorage[config.activityId] uses activity id to return the most recent
        // attempt
        if (cmiEntryValue == "resume") {
            if (window.localStorage[config.activityId] == null) {
                window.localStorage[config.activityId] = config.activityId + "?attemptId=" + generateUUID();
            }

            // send a resume statement
            //resumeAttempt();

        } else {
            window.localStorage[config.activityId] = config.activityId + "?attemptId=" + generateUUID();

            // update the activity state with the new attempt IRI
            setActivityState();
        }
    }

    /*******************************************************************************
     **
     ** Sends same basic statement with varying verbs 
     **
     *******************************************************************************/
    var sendSimpleStatement = function (verb) {
        var stmt = getBaseStatement();
        stmt.verb = verb;
        stmt.context.contextActivities.grouping[0].id = window.localStorage[config.activityId];
        
        // set the context activity from the manifest/launch_data to group together
        // for an event
        stmt.context.contextActivities.grouping.push(config.groupingContextActivity);
        		        var response = ADL.XAPIWrapper.sendStatement(stmt);
    }


    /*******************************************************************************
     **
     ** This function is used to (most likely) get a unique guid to identify 
     ** an attempt
     **
     *******************************************************************************/
    var generateUUID = function () {
        var d = new Date().getTime();

        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = (d + Math.random() * 16) % 16 | 0;
            d = Math.floor(d / 16);
            return (c == 'x' ? r : (r & 0x7 | 0x8)).toString(16);
        });

        return uuid;
    }

    function message(str) {
        if (_debug) {
            output.log(str);
        }
    }

    return {
        //newSetConfig: newSetConfig,
        setConfig:setConfig,
        initializeAttempt: initializeAttempt,
        getBaseStatement: getBaseStatement,
        resumeAttempt: resumeAttempt,
        suspendAttempt: suspendAttempt,
        terminateAttempt: terminateAttempt,
        saveDataValue: saveDataValue,
        setScore: setScore,
        setComplete: setComplete,
        setSuccess: setSuccess,
        configureLRS: configureLRS,
        getConfig : getConfig,
         changeConfig : changeConfig,
    }

    // 
}(); // end xapi object
