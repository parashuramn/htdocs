<?php

	$parent_level = !empty($filter_params["object_is_parent"])? "checked":"";
	$group_level = !empty($filter_params["object_is_group"])? "checked":"";
	$save_filter = !empty($filter_params["save_filter"])? "checked":"";
	$save_filter_hidden = empty($filter_params["save_filter"])? "hidden":"";

	if(empty($filter_params["verb_id"]))
		$selected_verbs = "";
	else if(is_array($filter_params["verb_id"]))
		$selected_verbs = ($filter_params["verb_id"]);
	else
		$selected_verbs = array($filter_params["verb_id"]);

	if(empty($filter_params["objectid"]))
		$selected_activities = "";
	else if(is_array($filter_params["objectid"]))
		$selected_activities = ($filter_params["objectid"]);
	else
		$selected_activities = array($filter_params["objectid"]);

	if(empty($filter_params["agent_id"]))
		$selected_users = "";
	else if(is_array($filter_params["agent_id"]))
		$selected_users = ($filter_params["agent_id"]);
	else
		$selected_users = array($filter_params["agent_id"]);

	if(empty($filter_params["group"]))
		$selected_group = "";
	else if(is_array($filter_params["group"]))
		$selected_group = $filter_params["group"];
	else
		$selected_group = array($filter_params["group"]);
?>
	<div class="page-quick-sidebar-wrapper">
		<div class="page-quick-sidebar" id="filter-sidebar">
			<div class="nav-justified">
				<a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-close"></i></a>
				<ul class="nav nav-tabs nav-justified">
					<li class="active filter-title">
						<a href="#quick_sidebar_tab_filter" data-toggle="tab">
						<?php echo __("Filters"); ?> <span class="filter-count badge badge-danger" style="right: -3px"></span>
						</a>
					</li>
					<li class="hidden">
						<a href="#quick_sidebar_tab_2" data-toggle="tab">
						<?php echo __("Search"); ?> <span class="badge badge-success"><?php echo __("7"); ?></span>
						</a>
					</li>
					<li class="expand-contract" style="width: 61px !important;">
						<div class="badge fliter-close" onclick="jQuery('.page-quick-sidebar-wrapper').removeClass('filter-fullscreen'); jQuery('body').toggleClass('page-quick-sidebar-open'); return false;">
							<i class="fa fa-times"></i>
						</div>
						<div class="badge filter-expand" onClick="jQuery('.page-quick-sidebar-wrapper').toggleClass('filter-fullscreen');">
							<i class="fa fa-expand"></i>
							<i class="fa fa-compress"></i>
						</div>

					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active page-quick-sidebar-settings" id="quick_sidebar_tab_filter">
						<form method="GET">
						<div class="page-quick-sidebar-settings-list">
							<h3 class="list-heading">
								<?php echo __("Date Range"); ?>
								<a href="javascript:;" class="filter-add filter-daterange">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-daterange">
								<li class="hidden">
									<div id="daterange_filter" class="btn default">
										<i class="fa fa-calendar"></i>
										&nbsp; <span>
										</span>
										<b class="fa fa-angle-down"></b>
									</div>
									<div class="filter-daterange-inputs">

									</div>
								</li>

							</ul>
							<h3 class="list-heading">
								<?php echo __("Learners"); ?>
								<a href="javascript:;" class="filter-add filter-users">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-users">
								<li class="hidden">
									<div class="input-group">
										<input id="users-search" type="text" class="form-control" placeholder="Search...">
										<span class="input-group-btn">
										<a href="javascript:;" class="btn"><i class="icon-magnifier"></i></a>
										</span>
									</div>
								</li>
							</ul>
							<ul class="list-items list-users list-users-search">
							</ul>
							<h3 class="list-heading">
								<?php echo __("Verbs"); ?>
								<a href="javascript:;" class="filter-add filter-verbs">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items  list-verbs">
							</ul>
							<h3 class="list-heading">
								<?php echo __("Activities"); ?>
								<a href="javascript:;" class="filter-add filter-activities">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-activities">
								<li class="hidden">
									<div class="input-group">
										<input id="activities-search" type="text" class="form-control" placeholder="Search...">
										<span class="input-group-btn">
										<a href="javascript:;" class="btn"><i class="icon-magnifier"></i></a>
										</span>
									</div>
								</li>
							</ul>
							<ul class="list-items list-activities list-activities-search">
							</ul>
							<h3 class="list-heading">
								<?php echo __("Score"); ?>
								<a href="javascript:;" class="filter-add filter-score">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-score">
								<li class="hidden">
									<div class="input-group">
										<input id="score-min" type="text" class="form-control pull-left maxw30pc" name="score_min" placeholder="<?php echo __('Min'); ?>">
										<input id="score-max" type="text" class="form-control pull-left maxw30pc margin10l" name="score_max" placeholder="<?php echo __('Max'); ?>">
										<a class="pull-right" onclick="jQuery(this).closest('li').find('input').val('');jQuery(this).closest('li').addClass('hidden');" href="javascript:;"><i class="fa fa-times"></i></a>
									</div>
								</li>
							</ul>
							<h3 class="list-heading">
								<?php echo __("Score Percentage"); ?>
								<a href="javascript:;" class="filter-add filter-score-percentage">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-score-percentage">
								<li class="hidden">
									<div class="input-group">
										<input id="score-percentage-min" type="text" class="form-control pull-left maxw30pc" name="score_percentage_min" placeholder="<?php echo __('Min'); ?>">
										<input id="score-percentage-max" type="text" class="form-control pull-left maxw30pc margin10l" name="score_percentage_max" placeholder="<?php echo __('Max'); ?>">
										<a class="pull-right" onclick="jQuery(this).closest('li').find('input').val('');jQuery(this).closest('li').addClass('hidden');" href="javascript:;"><i class="fa fa-times"></i></a>
									</div>
								</li>
							</ul>
							<h3 class="list-heading">
								<?php echo __("Group"); ?>
								<a href="javascript:;" class="filter-add filter-group">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-group hidden">
							</ul>
							<hr>
							<h3 class="list-heading" style="margin-bottom: 20px;">
								<?php echo __("Content Filter"); ?>
							</h3>
							<?php
							if(!empty($content_types))
							foreach ($content_types as $content_type_tag => $content_type) {
							?>
							<h3 class="list-heading">
								<?php echo __($content_type["name"]); ?>
								<a href="javascript:;" class="filter-add filter-contents filter-<?php echo $content_type_tag; ?>" data-type="<?php echo $content_type_tag; ?>" data-name="<?php echo __($content_type["name"]); ?>">
									<i class="icon-plus"></i>
								</a>
							</h3>
							<ul class="list-items list-contents list-<?php echo $content_type_tag; ?> hidden">
							</ul>
							<?php
							}
							?>
							<hr>
							<h3 class="list-heading">
								<?php echo __("Activity Level"); ?>
							</h3>
							<ul class="list-items list-activity-level">
								<li>
									 <?php echo __("Include Sub Activities"); ?> <input type="checkbox" name="related" value="1" class="make-switch filter-parent-level"  <?php echo h($parent_level); ?>  data-size="small" data-on-color="success" data-on-text="<?php echo __("ON"); ?>" data-off-color="default" data-off-text="<?php echo __("OFF"); ?>">
								</li>
								<li>
									 <?php echo __("Parent Level"); ?> <input type="checkbox" name="object_is_parent" value="1" class="make-switch filter-parent-level"  <?php echo h($parent_level); ?>  data-size="small" data-on-color="success" data-on-text="<?php echo __("ON"); ?>" data-off-color="default" data-off-text="<?php echo __("OFF"); ?>">
								</li>
								<li>
									 <?php echo __("Group Level"); ?> <input type="checkbox" name="object_is_group" value="1" class="make-switch filter-group-level" <?php echo h($group_level); ?> data-size="small" data-on-color="success" data-on-text="<?php echo __("ON"); ?>" data-off-color="default" data-off-text="<?php echo __("OFF"); ?>">
								</li>
							</ul>
							<div class="inner-content">
								<button class="btn btn-success" type="submit" onclick="jQuery('#filter-sidebar .hidden input, #filter-sidebar .hidden select').remove(); jQuery('#filter-sidebar form').submit();"><i class="fa fa-filter"></i> <?php echo __("Filter"); ?></button>
							</div>
							<h3 class="list-heading">
							</h3>
							<ul class="list-items list-save-filter dont-count">
								<li>
									 <?php echo __("Save/Edit Filter"); ?> <input type="checkbox" name="save_filter" value="1" class="make-switch filter-save"  <?php  echo h(@$save_filter);  ?>  data-size="small" data-on-color="success" data-on-text="<?php echo __("ON"); ?>" data-off-color="default" data-off-text="<?php echo __("OFF"); ?>" onChange="if(jQuery(this).attr('checked') == 'checked') { jQuery('.filter-save-filter').removeClass('hidden'); } else { jQuery('.filter-save-filter').addClass('hidden');  }">
								</li>
							</ul>

							<div class="inner-content filter-save-filter <?php echo h($save_filter_hidden);?> dont-count">
								<input id="input-save-filter" type="text" class="form-control" name="filter_name" placeholder="<?php echo __("Filter Name"); ?>" value="<?php echo h(@$filter_params['filter_name']); ?>">
								<input  type="hidden" name="filter_id"  value="<?php echo intVal(h(@$filter_params['filter_id'])); ?>">

							</div>
							<div class="inner-content filter-save-filter <?php echo h($save_filter_hidden);?> dont-count">
								<button name="save_filter" value="1" class="btn btn-success" type="submit" onclick="jQuery('#filter-sidebar form').submit();"><i class="fa fa-save"></i> <?php if(!empty($_REQUEST["filter_id"])) echo __("Update"); else echo __("Save") ?></button>
							</div>
							<?php
							$filters = gb_get_filters($this->request->params["controller"]."/".$this->request->params["action"]);
							if(!empty($filters)) {
							?>
							<br>
							<h3 class="list-heading">
								<?php echo __("Saved Filters"); ?>
							</h3>
							<ul class="list-items list-save-filter dont-count">
								<?php 
								$user_id = User::get("id");
								foreach ($filters as $key => $filter) {
									if( empty($user_id) || !check_permission("view_all_filters", $user_id) && $user_id != @$filter["user"] )
									continue;

									$filter_id =  $filter["GET"]["filter_id"];
									$filter_url = Router::url(array('controller' => $filter["controller"], 'action' => $filter["action"]))."?filter_id=".$filter["GET"]["filter_id"];
								?>
								<li id="filter_<?php echo h($filter["GET"]["filter_id"]); ?>">
									<a href="<?php echo h($filter_url); ?>"><?php echo h($filter["GET"]["filter_name"]); ?></a>
									<a href="<?php echo h($filter_url."&delete_filter=1"); ?>" class="pull-right">
										<i class="fa fa-times"></i>
									</a>
									<a href="javascript:;" data-container="body" data-placement="top" data-original-title="<?php echo __('Set up Automated Emails'); ?>" class="pull-right tooltips" onClick="addFilterEmail(<?php echo $filter_id; ?>);">
										<i class="fa fa-envelope"></i>
									</a>
									<div class="filter-emails">
									<?php 
										$filter_emails = grassblade_config_get("filter_emails_".$filter_id);
										$freqs = array(
												"every_hour" => __("Every Hour"),
												"every_day" => __("Every Day"),
												"every_week" => __("Every Week"),
												"every_month" => __("Every Month"),
												"every_year" => __("Every Year"),
											);
										if(!empty($filter_emails)) {
											echo "<ul>";
											foreach ($filter_emails as $filter_email_id => $filter_email) {
												if($filter_email["type"] == "CSV")
													$file_icon_class = "fa fa-file-excel-o green";
												else
													$file_icon_class = "fa fa-file-pdf-o red";
												?>
												<li class="tooltips" data-container="body" data-placement="left" data-html="true" data-original-title="<?php echo __("Email ID(s)")." : ".$filter_email["email_ids"]; ?><br>
															<?php echo __("No of Previous Days to Report")." : ".@$filter_email["days"]; ?><br>
															<?php echo __("Type")." : ".@$filter_email["type"]; ?><br>
															<?php echo __("Frequency")." : ".@$freqs[$filter_email["freq"]]." ".@$filter_email["freq_text"];
echo "Next: ".date("F j, Y H:i:s", gb_get_filter_email_timestamp($filter_email)); 
 ?>
															">
															<i class="<?php echo $file_icon_class; ?>"></i>

													<?php echo substr($filter_email["email_ids"], 0, 12); if(strlen($filter_email["email_ids"]) > 12) echo "..."; ?>
													<a href="<?php echo h($filter_url."&filter_id=".$filter_id."&delete_filter_email=".$filter_email_id); ?>" 
														>
														<i class="fa fa-times"></i>
													</a>
												</li>
												<?php 
											}
											echo "</ul>";
										}
									?>
									</div>
									<div class="filter-email-add hidden">
										<ul class="list-items">
											<li><input name="filter_email_email_ids_<?php echo h($filter_id); ?>" type="text" placeholder="<?php echo __("Email ID(s)"); ?>" class="form-control"><br></li>
											<li><input name="filter_email_no_of_days_<?php echo h($filter_id); ?>" type="text" placeholder="<?php echo __("No of Previous Days to Report"); ?>" class="form-control"><br></li>
											<li><select name="filter_email_freq_<?php echo h($filter_id); ?>" class="form-control" style="margin-right: 10px; width: 45% !important; float: left;">
													<option value=""><?php echo __("Set Frequency"); ?></option>
													<?php
														foreach ($freqs as $freq_key => $freq_name) {
															?>
															<option value="<?php echo h($freq_key); ?>"><?php echo h($freq_name); ?></option>
															<?php
														}
													?>
												<select>
												 <input name="filter_email_freq_text_<?php echo h($filter_id); ?>" type="text" placeholder="<?php echo __("e.g. 10:00AM"); ?>" class="form-control" style="width: 50% !important;"><br></li>
										  	<li><select name="filter_email_type_<?php echo h($filter_id); ?>" class="form-control">
													<option value="CSV"><?php echo __("CSV"); ?></option>
													<option value="PDF"><?php echo __("PDF"); ?></option>
												<select><br></li>
											<li>
												<div class="filter_email-column_html" ></div>
												<input class="filter_email-column_values" name="filter_email_column_values_<?php echo h($filter_id); ?>" type="hidden" value="">
												<input  name="filter_email_sort_<?php echo h($filter_id); ?>" type="hidden" value="<?php echo h(@$this->request->named['sort']); ?>">
												<input  name="filter_email_sort_direction_<?php echo h($filter_id); ?>" type="hidden" value="<?php echo h(@$this->request->named['direction']); ?>">
										  	</li>
										  	<li>
										  		<button name="save_filter_email" value="<?php echo h($filter_id); ?>" class="btn btn-success" type="submit" onclick=""><i class="fa fa-save"></i> <?php echo __("ADD") ?> 
											  	<button class="btn default" onclick="jQuery(this).closest('.filter-email-add').addClass('hidden'); return false;" style="margin-left: 4px"><?php echo __("Cancel") ?> 
										  	</li>
										</ul>
									</div>
								</li>
								<?php
								}
								?>
								</li>
							</ul>
							<?php } 
								if(!empty($filter_email)) {
									$cron_time = grassblade_config_get("gb_cron_id");
									if(empty($cron_time))
										$last_run = "Never";
									else if(time() -  $cron_time > 86400)
										$last_run = date("F j, Y H:i:s", $cron_time);
									
									if(!empty($last_run))
									{
										?>
										<br><br>
										<div style="margin:10px">
										<?php printf(__("To send emails please make sure Cron Job is setup. Last Run: %s. <br>
										Cron URL: <b>%s</b>"), $last_run, Router::url(array("controller" => "Configure", "action" => "cron"), true)); ?>
										</div>
										<?php
									}

									$Integrations = grassblade_config_get("Configure_Integrations");
									if(empty($Integrations["email"]["server"]))
									{
										?>
										<div style="margin:10px">
										<?php printf(__("To send emails please make sure <a href='%s'>SMTP details</a> are configured."), Router::url(array("controller" => "Configure", "action" => "Integrations"), true)); ?>
										</div>
									
										<?php
									}

								}
							?>
						</div>
						</form>

					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
			var timestamp_start = "<?php echo !empty($filter_params['timestamp_start'])? $filter_params['timestamp_start']:''; ?>";
			var timestamp_end = "<?php echo !empty($filter_params['timestamp_end'])? $filter_params['timestamp_end']:''; ?>";
			var score_min = "<?php echo !empty($filter_params['score_min'])? $filter_params['score_min']:''; ?>";
			var score_max = "<?php echo !empty($filter_params['score_max'])? $filter_params['score_max']:''; ?>";
			var score_percentage_max = "<?php echo !empty($filter_params['score_percentage_max'])? $filter_params['score_percentage_max']:''; ?>";
			var score_percentage_min = "<?php echo !empty($filter_params['score_percentage_min'])? $filter_params['score_percentage_min']:''; ?>";
			var groups_list = <?php echo json_encode(@$groups_list); ?>;
			var selected_group = <?php echo json_encode($selected_group); ?>;
			var contents_list = <?php echo json_encode(@$contents_list); ?>;
			var selected_contents = <?php echo json_encode(@$filter_params["content"]); ?>;
			var content_types = <?php echo json_encode(@$content_types); ?>;

		jQuery(window).load(function() {
			var selected_users = <?php echo json_encode($selected_users); ?>;
			var selected_activities = <?php echo json_encode($selected_activities); ?>;
			var selected_verbs = <?php echo json_encode($selected_verbs); ?>;

			jQuery(".filter-add").click(function() {
				if(jQuery(this).hasClass("filter-users")) {
					jQuery(".list-users .hidden").removeClass("hidden");
				}
				if(jQuery(this).hasClass("filter-activities")) {
					jQuery(".list-activities .hidden").removeClass("hidden");
				}
				if(jQuery(this).hasClass("filter-daterange")) {
					if(jQuery(this).find(".fa-times").length)
						show_daterange_filter(false);
					else
						show_daterange_filter(true);
				}
				if(jQuery(this).hasClass("filter-verbs")) {
					add_verb_to_filter();
				}
				if(jQuery(this).hasClass("filter-group")) {
					jQuery(".list-group.hidden").removeClass("hidden");
					add_groups_to_filter();
				}
				if(jQuery(this).hasClass("filter-score")) {
					jQuery(".list-score .hidden").removeClass("hidden");
				}
				if(jQuery(this).hasClass("filter-score-percentage")) {
					jQuery(".list-score-percentage .hidden").removeClass("hidden");
				}
				if(jQuery(this).hasClass("filter-contents")) {
					var type = jQuery(this).data("type");
					var name = jQuery(this).data("name");
					if(jQuery("#filter-" + type + "-select").length == 0) {
						if(selected_contents != null && selected_contents[type] != null)
						var selected = selected_contents[type];
						else
						var selected = [""];
						add_contents_to_filter(type, name, selected);
						jQuery(".list-" + type).removeClass("hidden");
					}
					update_filter_count();
				}
			});
			jQuery("#filter-sidebar .fa.fa-times").click(function() {
				if(jQuery(this).hasClass("hide_parent_parent_parent"))
					jQuery(this).parent().parent().parent().addClass("hidden");
				else
					remove_parent(this);
			});
			if(typeof selected_group == "object") {
				jQuery(".list-group.hidden").removeClass("hidden");
				add_groups_to_filter(selected_group);
			}
			if(typeof selected_verbs == "object") {
				get_verbs_list();
				setTimeout(function() {
					jQuery.each(selected_verbs, function(i,v) {
						add_verb_to_filter(v);
					});
				}, 2000);
			}
			if(typeof selected_activities == "object") {
				if(selected_activities.length < 3)
				jQuery.each(selected_activities, function(i,v) {
					get_activities_search_list('',v,v); //Add Activity to Filter
				});
				else
				jQuery.each(selected_activities, function(i,v) {
					var v_list = {};
					v_list[v] = {"objectid" : v, "object_name" : ""};
					show_activities_search_list('',v,v_list); //Add Activity to Filter
				});
			}
			if(typeof selected_users == "object") {
				if(selected_users.length < 3)
				jQuery.each(selected_users, function(i,v) {
					get_users_search_list('',v,v); //Add Agent to Filter
				});
				else
				jQuery.each(selected_users, function(i,v) {
					var v_list = {};
					v_list[v] = {"agent_id" : v, "agent_mbox" : v, "agent_name" : ""};
					show_users_search_list('',v,v_list); //Add Agent to Filter
				});							
			}
			if(typeof selected_contents == "object" && selected_contents != null) {
				jQuery.each(selected_contents, function(i, v) {
					if(typeof v == "object") {
						jQuery.each(v, function(i2, v2) {
							if(typeof v2 == undefined || v2 == "")
								delete selected_contents[i][i2];
						});
						var type = i;
						if(v.length > 0 && v[0] != undefined) {
							add_contents_to_filter(type, content_types[type]["name"], v);
							jQuery(".list-" + type).removeClass("hidden");			
						}
					}
				});

				//console.log(selected_contents);
			}
			if(score_min != "")
				jQuery("#filter-sidebar #score-min").val(score_min);
			if(score_max != "")
				jQuery("#filter-sidebar #score-max").val(score_max);
			if(score_percentage_min != "")
				jQuery("#filter-sidebar #score-percentage-min").val(score_percentage_min);
			if(score_percentage_max != "")
				jQuery("#filter-sidebar #score-percentage-max").val(score_percentage_max);
			if(score_min != "" || score_max != "")
				jQuery(".filter-score").click();
			if(score_percentage_min != "" || score_percentage_max != "")
				jQuery(".filter-score-percentage").click();

			jQuery("#users-search").keyup(function() {
				var search = jQuery("#users-search").val();
				if(search.length < 3)
					return;
				
				if(search.indexOf(' ') == -1 && (search.indexOf('//') > 0 || search.indexOf('@') > 0)) {
					var search_list = {};
					search_list[search] = {"agent_id" : search, "agent_name" : ""};
					show_users_search_list(".list-users-search", null, search_list);
				}
				get_users_search_list(".list-users-search", search);
			});
			jQuery("#activities-search").keyup(function() {
				var search = jQuery("#activities-search").val();
				if(search.length < 3)
					return;
				if(search.indexOf(' ') == -1 && search.indexOf(':')) {
					var search_list = {};
					search_list[search] = {"objectid" : search, "object_name" : ""};
					show_activities_search_list(".list-activities-search", null, search_list);
				}
				get_activities_search_list(".list-activities-search", search);
			});
			daterange_filter_init("#daterange_filter", daterange_filter_set);
			if(timestamp_start != '' || timestamp_end != '') {
				if(timestamp_start == '')
					start = moment().subtract("years", 2);//.format("MMM DD, YYYY");
				else
					start = moment(timestamp_start);//.format("MMM DD, YYYY");
				
				if(timestamp_end == '')
					end = moment();//.format("MMM DD, YYYY");
				else
					end = moment(timestamp_end);//.format("MMM DD, YYYY");
				daterange_filter_set(start, end);
			}
			update_filter_count();
			jQuery("#filter-sidebar form").click(function() {
				update_filter_count();
			});
			jQuery("#filter-sidebar form").change(function() {
				update_filter_count();
			});
		});
	function addFilterEmail (filter_id) {
		jQuery("#filter_" + filter_id + " .filter-email-add").removeClass("hidden");
		var columns = get_selected_filter_columns();
		var columns_html = '<div><?php echo __("Selected Columns"); ?></div><ul class="selected_filter_email_columns">';
		var column_values = {};
		jQuery.each(columns.split(","), function(i,v) {
			if(jQuery("#report_table_column_toggler input[data-column=" + v + "]:checked").length) {
				var column_name = jQuery("#report_table_column_toggler input[data-column=" + v + "]:checked").closest("label").text();
				columns_html += "<li>" + column_name + "</li>";

				column_values[v] = column_name; 
			}
		});
		columns_html += "</ul>";
		jQuery("#filter_" + filter_id + " .filter-email-add .filter_email-column_values").val(JSON.stringify(column_values));

		jQuery("#filter_" + filter_id + " .filter-email-add .filter_email-column_html").html(columns_html);

	}
	</script>
