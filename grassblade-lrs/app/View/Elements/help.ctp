		<div class="help-text">
			<?php 
				$controller = $this->params['controller'];
				$action = $this->params['action'];
				$page = strtolower($controller."/".$action);
				switch($page) {
					case "reports/index":
						?>
						<p><?php echo __("Activity Stream is a chronological listing of all the statements received by the LRS."); ?></p>
						<ol>
							<li><?php echo __("<b>Summary Chart</b> shows you the amount of activity happening in the LRS in a visual representation. It plots the number of statements and the number of unique verbs per day."); ?></li>
							<li><?php echo __("<b>Score Distribution Chart:</b> When you have selected a sepecific Activity that has scores, you can see this chart. By default, it shows a bell chart with normal score distribution of first scores of each user who attempted this activity. You can select between First Attempt, Minimum Score, Maximum Score and Average Score to plot chart. It also shows the scores and its frequency on the chart as dots. You can also see the mean/average score, and latest scores by specific selected users (if users are also selected) as vertical lines."); ?></li>
							<li><?php echo __("<b>Timestamp</b> is the time when the statement occurred."); ?></li>
							<li><?php echo __("<b>Learners</b> here are the users performing the action in a statement."); ?></li>
							<li><?php echo __("<b>Verb</b> defines the action between Actor and Activity."); ?></li>
							<li><?php echo __("<b>Activity</b> is something with which an Actor interacted. It can be a unit of instruction, course, module, question, experience, or performance that is to be tracked in meaningful combination with a Verb."); ?></li>					
							<li><?php echo __("<b>Result, Percentage, Score, Min, Max</b> columns show the results and score information and are self explainatory."); ?></li>
							<li><?php echo __("<b>Response</b> columns show any text response made by the Actor in the statment."); ?></li>
							<li><?php echo sprintf(__("Click %s icon to see aditional options to view details, see similar statements or re-run triggers.", "<i class='fa fa-plus-square-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to download the report in csv format which works with Excel, Numbers or any other spreadsheet applications.", "<i class='fa fa-file-excel-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to download the report in PDF format.", "<i class='fa fa-file-pdf-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to print the current report page.", "<i class='fa fa-print'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to filter the results.", "<i class='fa fa-filter'></i>")); ?></li>
						</ol> 
						<br>
						
						<?php
						break;

					case "reports/dashboard":
						?>
						<p><?php echo __("Dashboard gives you a glimpse of your entire LRS."); ?></p>
						<ol>
							<li><?php echo __("<b>Summary Chart</b> shows you the amount of activity happening in the LRS in a visual representation. It plots the number of statements and the number of unique verbs per day."); ?></li>
							<li><?php echo __("<b>Statements</b> are independent pieces of information received by the LRS as a representation of an activity or experience which has occurred."); ?></li>
							<li><?php echo __("<b>Learners</b> are users performing the action in a statement. Dashboard shows the total number of unique Learners across all the statements. Learners are sometimes reffered as Agents."); ?></li>
							<li><?php echo __("<b>Managers</b> are individual accounts that have access to the LRS."); ?></li>
							<li><?php echo __("<b>Verbs</b> defines the action between Actor and Activity."); ?></li>
							<li><?php echo __("<b>Activities</b> are something with which an Actor interacted. It can be a unit of instruction, course, module, question, experience, or performance that is to be tracked in meaningful combination with a Verb."); ?></li>					
							<li><?php echo __("<b>Parent Level Activities</b> are higher level activities specified as parent of another activity in a statement. e.g. A course is parent of a lesson."); ?></li>
							<li><?php echo __("<b>Group Level Activities</b> are higher level activities specified as grouping of another activity in a statement. e.g. A course and/or a lesson can be group level activity for a topic."); ?></li>
							<li><?php echo __("<b>Triggers</b> are certain actions to be performed when a matching statement is received. e.g. Triggering completion when a course <i>completed</i> statement is received."); ?></li>
						</ol> 

						<?php
						break;

					case "reports/attempts":
						?>
						<p><?php echo __("Attempts Report shows a list of individual attempts to an Activity made by a Learner."); ?></p>
						<ol>
							<li><?php echo __("<b>Learner</b> is the users performing the action in a statement."); ?></li>
							<li><?php echo __("<b>Started</b> is the time when the learner started the attempt."); ?></li>
							<li><?php echo __("<b>Completed</b> is the time when the learner completed the attempt."); ?></li>
							<li><?php echo __("<b>Activity</b> is something with which an Actor interacted. It can be a unit of instruction, course, module, question, experience, or performance that is to be tracked in meaningful combination with a Verb."); ?></li>					
							<li><?php echo __("<b>Result</b> shows the scores, answers, time-spent or other result related details sent in the statement."); ?></li>
							<li><?php echo sprintf(__("Click %s icon to see aditional options. Click on <b>Attempt Details</b> to see all the statements in that attempt, or <b>Responses</b> to see only the questions/answers.", "<i class='fa fa-plus-square-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to download the report in csv format which works with Excel, Numbers or any other spreadsheet applications.", "<i class='fa fa-file-excel-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to download the report in PDF format.", "<i class='fa fa-file-pdf-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to print the current report page.", "<i class='fa fa-print'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to filter the results.", "<i class='fa fa-filter'></i>")); ?></li>
						</ol> 
						<br>
						<p><b><?php echo __("Filters:"); ?></b></p>
						<ol>
							<li><?php echo __("<b>Search this page</b> input box filters the results available on the page. It doesn't search or filter through the rest of pages."); ?></li>
							<li><i class='fa fa-filter'></i><?php echo __(" at the top right of the page brings the filter box which can be used to filter the results on the page."); ?></li>
							<li><?php echo __("<b>Add to Filter</b> link visible on hovering over the results adds the data to the filter and also brings the filter box which can be used to filter the results on the page."); ?></li>
							<li><?php echo __("<b>Apply Filters</b> does the actual task of filtering the results."); ?></li>
							<li><?php echo __("<b>Clear</b> removes the filters and reloads the page without any filters applied."); ?></li>
							<li><?php echo __("Clicking on a Learner, Verb, Activity or another <b>link</b> on the table will immediately filter the results by the data you clicked."); ?></li>
						</ol>

						<?php
						break;

					case "reports/attempts_summary":
						?>
						<p><?php echo __("Attempts Summary shows a summary of attempts made on each parent+group level activity."); ?></p>
						<ol>
							<li><?php echo __("<b>Learner</b> is the users performing the action in a statement."); ?></li>
							<li><?php echo __("<b>Started</b> is the time when the learner started the attempt."); ?></li>
							<li><?php echo __("<b>Completed</b> is the time when the learner completed the attempt."); ?></li>
							<li><?php echo __("<b>Activity</b> is something with which an Actor interacted. It can be a unit of instruction, course, module, question, experience, or performance that is to be tracked in meaningful combination with a Verb."); ?></li>					
							<li><?php echo __("<b>Result</b> shows the scores, answers, time-spent or other result related details sent in the statement."); ?><?php echo __("Result of the last attempt is shown in this report."); ?></li>
							<li><?php echo sprintf(__("Click %s icon to see aditional options.", "<i class='fa fa-plus-square-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to download the report in csv format which works with Excel, Numbers or any other spreadsheet applications.", "<i class='fa fa-file-excel-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to download the report in PDF format.", "<i class='fa fa-file-pdf-o'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to print the current report page.", "<i class='fa fa-print'></i>")); ?></li>
							<li><?php echo sprintf(__("Click %s icon to filter the results.", "<i class='fa fa-filter'></i>")); ?></li>
						</ol> 
						<br>
						<p><b><?php echo __("Filters:"); ?></b></p>
						<ol>
							<li><?php echo __("<b>Search this page</b> input box filters the results available on the page. It doesn't search or filter through the rest of pages."); ?></li>
							<li><i class='fa fa-filter'></i><?php echo __(" at the top right of the page brings the filter box which can be used to filter the results on the page."); ?></li>
							<li><?php echo __("<b>Add to Filter</b> link visible on hovering over the results adds the data to the filter and also brings the filter box which can be used to filter the results on the page."); ?></li>
							<li><?php echo __("<b>Apply Filters</b> does the actual task of filtering the results."); ?></li>
							<li><?php echo __("<b>Clear</b> removes the filters and reloads the page without any filters applied."); ?></li>
							<li><?php echo __("Clicking on a Learner, Verb, Activity or another <b>link</b> on the table will immediately filter the results by the data you clicked."); ?></li>
						</ol>

						<?php
						break;
					case "users/index":
						?>
						<p><?php echo __("This page shows the list of managers having access to the LRS."); ?></p>
						<ol>
							<li><?php echo __("<b>Admin</b> role has full access to the LRS."); ?></li>
							<li><?php echo __("<b>User</b> role has access to all the sections except the Managers section on the site. i.e. they cannot view, edit or delete managers. You can further adjust the access using permissions."); ?></li>
						</ol>
						<?php
						break;

					case "users/edit":
					case "users/add":
						?>
						<p><?php echo __("You can add/edit managers details and also create AuthTokens on this page."); ?></p>
						<ol>
							<li><?php echo __("<b>Admin</b> users have full access to the LRS."); ?></li>
							<li><?php echo __("<b>User</b> users have access to all the sections except the Users section on the site. i.e. they cannot view, edit or delete users."); ?></li>
							<li><?php echo __("<b>AuthToken</b> is the access token required by LMS or Tin Can content to send statements to the LRS. It consists of 3 things: EndPoint, API User, API Password. These three things are all you need to configure in your LMS, or Content or GrassBlade Plugin on WordPress."); ?></li>

						</ol>
						<?php
						break;

					case "configure/translations":
						?>
						<p><?php echo __("Some authoring tools like Articulate so far don't send important human readable details in the statements like Course Name, Question Text, Answer Text, Slide Name, etc. These are shown on the reports as complicated ID's which don't make much sense. This section helps you import the tincan.xml file included in the Tin Can package which contains the meaningful human readable translations."); ?></p>
						<ol>
							<li><?php echo __("<b>Load from tincan.xml URL:</b> If you have uploaded the tincan.xml file and have the url. You can load it directly."); ?></li>
							<li><?php echo __("<b>Load from GrassBlade:</b> If you have uploaded your content to WordPress using GrassBlade xAPI Companion, and if the LRS is installed on the same database, you can use this button to import all tincan.xml files at once."); ?></li>
						</ol>
						<br>
						<p><?php echo __("Loading the tincan.xml files here means that the translations will be stored in the database, and the reports will show the human readable text instead of ID's"); ?></p>
						<?php
						break;

					case "configure/database":
						?>
						<p><?php echo __("This page helps you create the required file for database configuration."); ?></p>
						<?php
						break;

					case "triggers/index":
						?>
						<p><?php echo __("<b>Triggers</b> are certain actions to be performed when a matching statement is received. e.g. Triggering completion when a course <i>completed</i> statement is received."); ?></p>
						<ol>
							<li><?php echo __("<b>Type</b> decides what the trigger does."); ?>
								<ul>
									<li><?php echo __("<b>completion</b> will send a completion trigger to the GrassBlade xAPI Companion WordPress plugin. The action generated by it is in turn used by other plugins like LearnDash to mark the content as completed. "); ?></li>
									<li><?php echo __("<b>POST to URL(POST/GET) </b> are for webhooks, it will post the statement to the specified url. This can then be processed by the script on that url to do further processing."); ?></li>
									<li><?php echo __("<b>Email</b> will send an email alert when a matching statement is received."); ?> <b><?php echo __("Coming Soon!"); ?></b></li>
									<li><?php echo __("<b>SMS</b> will send an SMS alert when a matching statement is received."); ?> <b><?php echo __("Coming Soon!"); ?></b></li>
								</ul>
							</li>
							<li><?php echo __("<b>Status:</b> Active triggers will be executed on a matching statement. InActive triggers will not be executed on a match."); ?></li>
						</ol>
						<?php
						break;
					case "triggers/add":
					case "triggers/edit":
						?>
						<p><?php echo __("<b>Triggers</b> are certain actions to be performed when a matching statement is received. e.g. Triggering completion when a course <i>completed</i> statement is received."); ?></p>
						<ol>
							<li><?php echo __("<b>Name</b> is just a reference to easily remind what the trigger is for."); ?></li>
							<li><?php echo __("<b>Type</b> decides what the trigger does."); ?>
								<ul>
									<li><?php echo __("<b>completion</b> will send a completion trigger to the GrassBlade xAPI Companion WordPress plugin. The action generated by it is in turn used by other plugins like LearnDash to mark the content as completed. "); ?></li>
									<li><?php echo __("<b>POST to URL(POST/GET) </b> are for webhooks, it will post the statement to the specified url. This can then be processed by the script on that url to do further processing."); ?></li>
									<li><?php echo __("<b>Email</b> will send an email alert when a matching statement is received."); ?> <b><?php echo __("Coming Soon!"); ?></b></li>
									<li><?php echo __("<b>SMS</b> will send an SMS alert when a matching statement is received."); ?> <b><?php echo __("Coming Soon!"); ?></b></li>
								</ul>
							</li>
							<li><?php echo __("<b>URL</b> is url of WordPress site in case on <i>completion</i> trigger, and is URL of the webhook for the <i>POST to URL</i> triggers."); ?></li>
							<li><?php echo __("<b>Criterion</b> decides the match with a statement. If a matching statement is received, the trigger is executed."); ?></li>
								<ul>
									<li><?php echo __("<b>Verb</b>: When a statement with this verb is received, the trigger gets executed. "); ?></li>
								</ul>
							<?php if($action != "add") { ?>
							<li><?php echo __("<b>Status:</b> Active triggers will be executed on a matching statement. InActive triggers will not be executed on a match."); ?></li>
							<?php } ?>
						</ol>
						<?php
						break;
					default:
						echo __("No help information available for this page.");
				}
			?>
		</div>
