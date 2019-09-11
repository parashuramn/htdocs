<?php

$menus = array(
			"dashboard" => array(
						"name" 	=> __("Dashboard"),
						"icon"	=> "fa fa-dashboard",
						"url_array" => array(
								"controller" 	=> "Reports",
								"action"		=> "dashboard"
							),
						"name_class" => "title",
						"li_class" => "start",
				),
			"users" => array(
						"name"	=> __("Managers"),
						"role" 	=> "admin",
						"icon"	=> "fa fa-user",
						"arrow"	=> true,
						"tooltips" => __("Admins and Managers, and their permission and auth tokens"),
						"name_class" => "title",

						"sub-menu" => array(
							"users-all" => array(
										"name" 	=> __("All Managers"),
									/*	"tooltips" => __("List of Users"),*/
										"url_array" => array(
												"controller" 	=> "Users",
												"action"		=> "index"
											),
								),
							"users-add" => array(
										"name" 	=> __("Add New"),
										"url_array" => array(
												"controller" 	=> "Users",
												"action"		=> "add"
											),
								),

							)			
				),
			"groups" => array(
						"name"	=> __("Groups"),
						"role" 	=> "admin",
						"icon"	=> "fa fa-users",
						"arrow"	=> true,
						"name_class" => "title",

						"sub-menu" => array(
							"groups-all" => array(
										"name" 	=> __("All Groups"),
										"url_array" => array(
												"controller" 	=> "Groups",
												"action"		=> "index"
											),
								),
							"groups-add" => array(
										"name" 	=> __("Add New"),
										"url_array" => array(
												"controller" 	=> "Groups",
												"action"		=> "add"
											),
								),

							)
				),
			"reports" => array(
						"name"	=> __("Reports"),
						"icon"	=> "fa fa-bar-chart-o",
						"arrow"	=> true,
						"name_class" => "title",

						"sub-menu" => array(
							"reports-activity_stream" => array(
										"name" 	=> __("Activity Stream"),
										"arrow"	=> true,
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "index"
											),
										"sub-menu" => array(
											"reports-activity_stream-sub" => array( 
												"name" 	=> __("Activity Stream"),
												"tooltips" => __("Stream of all statements received by the LRS."),
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "index"
													),
												),
											"reports-activity_stream-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"url_array" => array(
													"controller" 	=> "Reports",
													"action"		=> "index"
												),
												"sub-menu" => array(
													"reports-activity_stream-filters-parent_level" => array(
																"name" 	=> __("Parent Level Activities"),
																"tooltips" => __("Generally your course level data."),
																"url_array" => array(
																		"controller" 	=> "Reports",
																		"action"		=> "index",
																	),
																"query" => "object_is_parent=1",
														),
													"reports-activity_stream-filters-answers" => array(
																"name" 	=> __("Question Attempts"),
																"tooltips" => __("Stream of all answered questions."),
																"url_array" => array(
																		"controller" 	=> "Reports",
																		"action"		=> "index",
																	),
																"query" => "verb_id%5B%5D=http%3A%2F%2Fadlnet.gov%2Fexpapi%2Fverbs%2Fanswered",
														),
													

													),
												),
										)
								),
							"reports-verbs" => array(
										"name" 	=> __("Verbs"),
										"arrow"	=> true,
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "verbs", 
												"sort" => "no_of_statements", 
												"direction" => "desc"

											),
										"sub-menu" => array(
											"reports-verbs-sub" => array( 
												"name" 	=> __("List of Verbs"),
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "verbs", 
														"sort" => "no_of_statements", 
														"direction" => "desc"

													),
											),
											"reports-verbs-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"url_array" => array(
													"controller" 	=> "Reports",
													"action"		=> "verbs", 
												),
												"sub-menu" => array(
													),
												),
											),
								),
							"reports-activities" => array(
										"name" 	=> __("Activities"),
										"arrow"	=> true,
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "activities", 
												"sort" => "no_of_statements", 
												"direction" => "desc"
											),
										"sub-menu" => array(
											"reports-activities-sub" => array( 
												"name" 	=> __("List of Activities"),
												"url_array" => array(
													"controller" 	=> "Reports",
													"action"		=> "activities", 
													"sort" => "no_of_statements", 
													"direction" => "desc"
												),
											),
											"reports-activities-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"url_array" => array(
													"controller" 	=> "Reports",
													"action"		=> "activities", 
												),
												"sub-menu" => array(
													"reports-activities-filters-parent_level" => array(
																"name" 	=> __("Parent Level Activities"),
																"tooltips" => __("Generally your course level data."),
																"url_array" => array(
																	"controller" 	=> "Reports",
																	"action"		=> "activities", 
																	"sort" => "no_of_statements", 
																	"direction" => "desc"
																),
																"query" => "object_is_parent=1",
														),
													"reports-activities-filters-answers" => array(
																"name" 	=> __("Question Analysis"),
																"tooltips" => __("All questions and related data."),
																"url_array" => array(
																	"controller" 	=> "Reports",
																	"action"		=> "activities", 
																	"sort" => "no_of_statements", 
																	"direction" => "desc"
																),
																"query" => "verb_id%5B%5D=http%3A%2F%2Fadlnet.gov%2Fexpapi%2Fverbs%2Fanswered",
														),
													),
												),
										)
								),
							"reports-agents" => array(
										"name" 	=> __("Learners"),
										"arrow"	=> true,
										"tooltips" => __("Your students or users and their related data"),
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "agents", 
												"sort" => "no_of_statements", 
												"direction" => "desc"
											),
										"sub-menu" => array(
											"reports-agents-sub" => array( 
												"name" 	=> __("List of Learners"),
												"url_array" => array(
													"controller" 	=> "Reports",
													"action"		=> "agents", 
													"sort" => "no_of_statements", 
													"direction" => "desc"
												),
											),
											"reports-agents-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "agents", 
													),
												"sub-menu" => array(
												),
											),
										)
								),
							"reports-timespent" => array(
										"name" 	=> __("Time Spent"),
										"arrow"	=> true,
										"tooltips" => __("Total Time Spent by Users"),
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "timespent", 
												"sort" => "timespent", 
												"direction" => "desc"
											),
										"query" => "timestamp_start=".date("Y-m-d", time() - 30*24*3600),

										"sub-menu" => array(
											"reports-timespent-sub" => array( 
												"name" 	=> __("Time Spent"),
												"url_array" => array(
													"controller" 	=> "Reports",
													"action"		=> "timespent", 
													"sort" => "timespent", 
													"direction" => "desc"
												),
												"query" => "timestamp_start=".date("Y-m-d", time() - 30*24*3600)
											),
											"reports-timespent-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "timespent", 
													),
												"sub-menu" => array(
												),
											),
										)
								),
							"reports-attempts" => array(
										"name" 	=> __("Attempts Report"),
										"arrow"	=> true,
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "attempts"
											),
										"sub-menu" => array(
											"reports-attempts-sub" => array( 
												"name" 	=> __("Attempts Report"),
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "attempts", 
													),
											),
											"reports-attempts-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "attempts", 
													),
												"sub-menu" => array(
													),
												),
											),
								),
							"reports-attempts_summary" => array(
										"name" 	=> __("Attempts Summary"),
										"arrow"	=> true,
										"url_array" => array(
												"controller" 	=> "Reports",
												"action"		=> "attempts_summary"
											),
										"sub-menu" => array(
											"reports-attempts_summary-sub" => array( 
												"name" 	=> __("Attempts Summary"),
												"url_array" => array(
														"controller" 	=> "Reports",
														"action"		=> "attempts_summary", 
													),
											),
											"reports-attempts_summary-filters" => array( 
												"name" 	=> __("Filters"),
												"icon"	=> "fa fa-filter",
												"arrow"	=> true,
												"sub-menu" => array(
													),
												),
											),
								),

							)
				),		
			"triggers" => array(
						"name"	=> __("Triggers"),
						"icon"	=> "fa fa-external-link",
						"arrow"	=> true,
						"name_class" => "title",

						"sub-menu" => array(
							"triggers-all" => array(
										"name" 	=> __("All Triggers"),
										"url_array" => array(
												"controller" 	=> "Triggers",
												"action"		=> "index"
											),
								),
							"triggers-add" => array(
										"name" 	=> __("Add New"),
										"url_array" => array(
												"controller" 	=> "Triggers",
												"action"		=> "add"
											),
								),

							)
				),	
			"configure" => array(
						"name"	=> __("Configure"),
						"icon"	=> "fa fa-gear",
						"arrow"	=> true,
						"name_class" => "title",
						"li_class"	=> "last",
						"sub-menu" => array(
							"configure-error-logs" => array(
										"name" 	=> __("Error Logs"),
										"icon"	=> "fa  fa-exclamation-triangle",
										"url_array" => array(
												"controller" 	=> "ErrorLogs",
												"action"		=> "index",
												"sort" 			=> "id", 
												"direction" 	=> "desc"
											),
								),
							"configure-translations" => array(
										"name" 	=> __("Translations"),
										"icon"	=> "fa fa-language",
										"url_array" => array(
												"controller" 	=> "Configure",
												"action"		=> "Translations"
											),
								),
							"configure-database" => array(
										"name" 	=> __("Database"),
										"icon"	=> "fa fa-database",
										"url_array" => array(
												"controller" 	=> "Configure",
												"action"		=> "Database"
											),
								),
							"configure-license" => array(
										"name" 	=> __("License"),
										"icon"	=> "fa fa-key",
										"url_array" => array(
												"controller" 	=> "Configure",
												"action"		=> "License"
											),
								),
							"configure-integrations" => array(
										"name" 	=> __("Integrations"),
										"role"	=> "admin",
										"icon"	=> "fa fa-link",
										"url_array" => array(
												"controller" 	=> "Configure",
												"action"		=> "Integrations"
											),
								),
							"configure-backup" => array(
										"name" 	=> __("Backup"),
										"role"	=> "admin",
										"icon"	=> "fa fa-archive",
										"url_array" => array(
												"controller" 	=> "Configure",
												"action"		=> "Backup"
											),
								),

							)
				),	
		);

modify("grassblade_lrs_menu", "remove_extra_menus_grassblade_lrs_menu", 10, 2);
if(!function_exists('remove_extra_menus_grassblade_lrs_menu')) {
	function remove_extra_menus_grassblade_lrs_menu($menus, $context) {
		
		$role = User::get("role");

		if($role == "admin")
			return $menus;

		foreach ($menus as $key => $menu) {
			if(@$menu["role"] == "admin") {
				unset($menus[$key]);
				continue;
			}
			
			if(!empty($menu["sub-menu"]))
			{
				$menus[$key]["sub-menu"] = remove_extra_menus_grassblade_lrs_menu($menu["sub-menu"], $context);
			}
		}
		return $menus;
	}
}
modify("grassblade_lrs_menu", function($menus, $context) {
	if(empty($menus["reports"]["sub-menu"]))
		return $menus;

	$filters = gb_get_filters();
	if(empty($filters))
		return $menus;

	/*
	Array
	(
	    [filter_3] => Array
	        (
	            [GET] => Array
	                (
	                    [agent_id] => Array
	                        (
	                            [0] => test@test.com
	                        )

	                    [object_is_parent] => 1
	                    [filter_name] => User1 Verbs
	                    [filter_id] => 3
	                    [save_filter] => 1
	                )

	            [action] => verbs
	            [controller] => Reports
	        )
	    [filter_2] => Array
	        (
	            [GET] => Array
	                (
	                    [verb_id] => Array
	                        (
	                            [0] => http://adlnet.gov/expapi/verbs/answered
	                        )

	                    [object_is_parent] => 1
	                    [filter_name] => Test Filter 2
	                    [filter_id] => 2
	                )

	            [action] => index
	            [controller] => Reports
	        )

	)
	*/

	foreach ($menus["reports"]["sub-menu"] as $key => $menu) {
		foreach ($filters as $fkey => $filter) {
			$user_id = User::get("id");
			if( empty($user_id) || !check_permission("view_all_filters", $user_id) && $user_id != @$filter["user"] )
				continue;


			if(	strtolower(@$menu["url_array"]["controller"]."/".@$menu["url_array"]["action"]) == strtolower(@$filter["controller"]."/".@$filter["action"]) ) {
				if(!empty($menu["sub-menu"][$key."-filters"]))
				{
					$query = array();
					foreach ($filter["GET"] as $k => $v) {
						if(!is_array($v) && !is_object($v))
							$query[] = urlencode($k)."=".urlencode($v);
						else
						{
							foreach ($v as $kk => $vv) {
								if(!is_array($vv))
								$query[] = urlencode($k."[".$kk."]")."=".urlencode($vv);
							}
						}
					}
					$menus["reports"]["sub-menu"][$key]["sub-menu"][$key."-filters"]["sub-menu"][$fkey] = array(
							"name" => $filter["GET"]["filter_name"],
							"url_array" => array(
												"controller" 	=> $filter["controller"],
												"action"		=> $filter["action"]
											),
							"query" => "filter_id=".$filter["GET"]["filter_id"],//implode("&", $query),
						);
				}
			}
		}

		if(!empty($menu["sub-menu"][$key."-filters"]) && empty($menus["reports"]["sub-menu"][$key]["sub-menu"][$key."-filters"]["sub-menu"]))
		{
			unset($menus["reports"]["sub-menu"][$key]["sub-menu"]);
			unset($menus["reports"]["sub-menu"][$key]["arrow"]);
		}
	}
	return $menus;
}, 10, 2);

function gb_menu_is_open($menu, $context) {
	if(strtolower(@$menu["url_array"]["controller"]."/".@$menu["url_array"]["action"])   == strtolower($context->request["controller"] ."/". $context->request["action"]))
	{
		if(!empty($_GET["filter_id"]) && (@$menu["icon"] == "fa fa-filter" || strpos( 'a'.@$menu["query"], "filter_id=".@$_GET["filter_id"] ) > 0))
		return true;
	}
	if($menu["name"] == __("Reports") && strtolower($context->request["controller"]) == "reports" && !in_array(strtolower($context->request["action"]), array("dashboard")))
	{
		return true;
	}
	return false;
}
function gb_menu_generate($menus, $context) {
	$menu_html = '';
	foreach ($menus as $key => $menu) {
		if(@$menu["arrow"] && empty($menu["sub-menu"]))
			continue;

		$li_class = !empty($menu["li_class"])? $menu['li_class']:"";
		$open = gb_menu_is_open($menu, $context)? " open active":"";
		$li_class .= $open;

		if(!empty($menu["tooltips"]))
		{
			$li_class .= " tooltips";
			$tooltips = 'data-container="body" data-placement="right" data-html="true" data-original-title="'.$menu['tooltips'].'"';
		}
		else
			$tooltips =  '';
		$menu_html .= "<li class='".$li_class."' ".$tooltips.">";
		$url = !empty($menu['url'])? $menu['url']:(!empty($menu['url_array'])? Router::url($menu['url_array']):"javascript:;");
		if(!empty($menu['query']))
		{
			$url .= strpos('a'.$url, "?")? "&".$menu['query']:"?".$menu["query"];
		}
		$menu_html .= "<a href='".$url."'>";
		if(!empty($menu['icon']))
			$menu_html .= "<i class='".$menu['icon']."'></i>";
		if(!empty($menu['name_class']))
			$menu_html .= "<span class='".$menu['name_class']."'>&nbsp;".$menu['name']."</span>";
		else
			$menu_html .= "&nbsp;".$menu['name'];
		
		if(!empty($menu['arrow']))
			$menu_html .= "<span class='arrow ".$open."'></span>";

		$menu_html .= "</a>";
	
		if(!empty($menu["sub-menu"]))
		{
			$menu_html .= $open? "<ul class='sub-menu' style='display:block'>":"<ul class='sub-menu'>";
			$menu_html .= gb_menu_generate($menu['sub-menu'], $context);
			$menu_html .= "</ul>";
		}

		$menu_html .= "</li>";

	}

	return $menu_html;
}

?>
<ul class="page-sidebar-menu " data-auto-scroll="true" data-slide-speed="200">
	<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
	<li class="sidebar-toggler-wrapper">
		<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
		<div class="sidebar-toggler">
		</div>
		<!-- END SIDEBAR TOGGLER BUTTON -->
	</li>
	<?php 
	$menus = modified("grassblade_lrs_menu", $menus, $this);
	echo gb_menu_generate($menus, $this);
	?>
</ul>
