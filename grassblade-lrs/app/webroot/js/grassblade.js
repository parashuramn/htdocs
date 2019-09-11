function show_only_siblings(id) {
	show_siblings(id);
	hide(id);
}
function show_siblings(id) {
	jQuery(id).siblings().show();
}
function hide(id) {
	jQuery(id).hide();
}
function prepend_heading(id, to) {
	var heading = jQuery(id).text();
	var link = jQuery(id).attr("href");
	heading = "<div class='details-heading'><a href='" + link + "' target='_blank'>" + heading + "</a></div>";
	jQuery(to).html(heading + jQuery(to).html());
}
function copy_html(from, to, id, parent_level) {
	if(id == undefined)
		id = window;
	if(parent_level == undefined)
		parent_level = 0;
	var ref = jQuery(id);

	for(var i = 0; i < parent_level; i++) {
		ref = ref.parent();
	}

	var from_ref = ref.find(from);
	var to_ref = ref.find(to);

	if(to_ref != undefined && from_ref != undefined) {
		var html = from_ref[0].outerHTML;
		to_ref.html(html);
		to_ref.children().show();
	}
}
function copy_html_url(url_part, to, id, parent_level, callback) {
	if(id == undefined)
		id = window;
	if(parent_level == undefined)
		parent_level = 0;
	var ref = jQuery(id);

	for(var i = 0; i < parent_level; i++) {
		ref = ref.parent();
	}

	var to_ref = ref.find(to);
	var url = jQuery(id).attr("href");
	if(to_ref == undefined || url == undefined) {
		return;
	}
	to_ref.html('<div class="center"><div class="fa fa-spin fa-refresh"></div></div>');
	jQuery.get(url, {layout:"ajax_html"}, function(data) {
		if(url_part == undefined)
		to_ref.html(data);	
		else {
			to_ref.html(jQuery(data).find(url_part)[0].outerHTML);
		}

		if(callback != undefined)
		callback(to_ref, id);
	});
}
function hide_table_columns_with_less_data(id, datalevel) {
	gb_col = new Array();

	if(typeof datalevel == "undefined" || datalevel == 0 || datalevel == "" || datalevel == undefined)
		var datalevel = 0.2;

	jQuery(id).children("thead").find("th").each(function(j, g) {
		jQuery(this).attr("data-column", j);
	});

	jQuery(id).children("tbody").children("tr").each(function(i,v) {
		jQuery(v).children("td").each(function(j, g) {
			jQuery(this).attr("data-column", j);
			if(gb_col[j] == undefined)
				gb_col[j] = 0

			if(jQuery(g).text().trim().length > 0)
				gb_col[j]++;
		});
	});
	var rows = jQuery(id).children("tbody").children("tr").length;
	var required = rows * datalevel; 
	for(j = 1; j < gb_col.length; j++) {
		if(gb_col[j] < required)
			jQuery(id).find("[data-column=" + j + "]").hide();
	}
}
function hide_datatable_columns(table, toggler) {
	var ths = jQuery(table).find("th");
	for(var i=0; i < ths.length; i++) {
		if(jQuery(ths[i]).hasClass("hide_column"))
		jQuery(toggler).find("[data-column=" + i + "]").click(); //Hide Email Column By Default
	}
	jQuery.each(jQuery("table").find("th").index(jQuery("table .hide_column")), function(i) {
	});
}
function add_contents_to_filter(type, name, selected) {
	if(typeof selected != "object")
		selected = [""];

	jQuery.each(selected, function(selected_i, selected_v) {
		var options;
		options = "<option value=''>Select</option>";
		jQuery.each(contents_list[type], function(i, v) {
			if(i == selected_v)
				s = 'selected="selected"';
			else
				s = '';
			if(v["Content"]["parent"] > 0)
			options += "<option data-parent='" + v["Content"]["parent"] + "' value='" + i + "'  " + s + ">" + v["Content"]["name"] + "</option>"; 
			else
			options += "<option value='" + i + "'  " + s + ">" + v["Content"]["name"] + "</option>"; 
		});
		var html_li = "<li>" + name + " <a href='javascript:;' onclick='remove_parent(this)' class='pull-right'><i class='fa fa-times'></i></a> <select id='filter-" + type + "-select' class='form-control input-inline input-sm input-small' name='content[" + type + "][]' onchange=''>" + options + "</select></li>";
		jQuery(".list-" + type).append(html_li);
	});
	update_filter_count();
}
/*
function show_selected_content_childs() {
	jQuery("option[data-parent]").each(function(i,v){
		var parent = jQuery(v).data("parent"); 
		var parent_obj = jQuery("option[value=" + parent + "]").parent();
		var parent_selected = (parent_obj.length && (parent_obj.val() == ""  || parent_obj.val() == parent) );
		if(parent_selected) 
			jQuery(v).show(); 
		else 
			jQuery(v).hide(); 
		//console.log(parent + ":" + parent_selected);  
	});
}*/
function add_groups_to_filter(selected) {
	if(typeof selected != "object")
		selected = [""];

	jQuery.each(selected, function(selected_i, selected_v) {
		var options;
		options = "<option value=''>Select</option>";
		jQuery.each(groups_list, function(i, v) {
			if(i == selected_v)
				s = 'selected="selected"';
			else
				s = '';
			options += "<option value='" + i + "'  " + s + ">" + v + "</option>"; 
		});
		var html_li = "<li>Group <a href='javascript:;' onclick='remove_parent(this)' class='pull-right'><i class='fa fa-times'></i></a> <select id='filter-group-select' class='form-control input-inline input-sm input-small' name='group[]'>" + options + "</select></li>";
		jQuery(".list-group").append(html_li);
	});
	update_filter_count();
}
var verbs_list = '';
function add_verb_to_filter(selected) {
	if(jQuery(".list-verbs li").length == undefined)
		var len  = 1;
	else
		var len = jQuery(".list-verbs li").length + 1;

	var html_li = "<li>Verb <a href='javascript:;' onclick='remove_parent(this)' class='pull-right'><i class='fa fa-times'></i></a> <select id='filter-verbs-select-" + len + "' class='form-control input-inline input-sm input-small' name='verb_id[]'></select></li>";
	jQuery(".list-verbs").append(html_li);
	//console.log(select);
	get_verbs_list('#filter-verbs-select-' + len, selected);
}
function get_verbs_list(id, selected, force) {
	if(force != 1 && typeof verbs_list == "object") {
		show_verbs_list(id, selected, verbs_list);
		return;
	}
	jQuery.ajax({
		url: ajaxurl + "Reports/verbs?layout=ajax",
		dataType: "json",
		success: function( data ) {
			verbs_list = data;
			if(id != undefined) {
				show_verbs_list(id, selected, verbs_list);
			}
		}
	});
}
function show_verbs_list(id, selected, verbs_list) {
	var html = '', s = '';
	jQuery.each(verbs_list, function(i, v) {
		if(selected != undefined && i == selected)
			s = 'selected="selected"';
		else
			s = '';
		html += "<option value='" + i + "' " + s + ">" + v.verb + "</option>";
	});
	jQuery(id).html(html);

	update_filter_count();
}
var searching_users = 0;
function get_users_search_list(id, search, selected, force) {
	if(searching_users == 1 && typeof selected != "string")
		return;
	searching_users = 1;
	jQuery(id).html("<li><div style='text-align: center;'><i class='fa fa-spin fa-refresh'> </i></div><div>Searching learners matching '" + search + "' ...</div></li>");
	jQuery.ajax({
		url: ajaxurl + "Reports/agents?layout=ajax&limit=10&agent_like=" + search,
		dataType: "json",
		success: function( data ) {
			searching_users = 0;
			users_list = data;
			if(id != undefined) {
				show_users_search_list(id, selected, users_list);
			}
		}
	});
}


function show_users_search_list(id, selected, users_list) {
	var html = '';
	jQuery.each(users_list, function(i, u) {
		html_it = '<li class="media">';
		html_it += '	<div class="media-status">';
		if(typeof selected == "string" && selected == u.agent_id)
		{
			html_it += '		<a href="javascript:;" onclick="jQuery(this).parent().parent().remove();" class="pull-right"><i class="fa fa-times"></i></a>';
			html_it += '		<input type="hidden" name="agent_id[]" value="'+ u.agent_id +'">';
		}
		else
		html_it += '		<a href="javascript:;" onclick="add_this_user_to_filter(this)"><i class="fa fa-plus"></i></a>';

		html_it += '	</div>';
		if(typeof u.agent_mbox == "string" && u.agent_mbox.length > 3)
		html_it += '	<img class="media-object" src="' +  '//www.gravatar.com/avatar/' + MD5(u.agent_mbox) + '?d=mm" alt="...">';
		html_it += '	<div class="media-body">';
		html_it += '		<h4 class="media-heading">' + u.agent_name + '</h4>';
		html_it += '		<div class="media-heading-sub">';
		html_it += u.agent_id;
		html_it += '		</div>';
		html_it += '	</div>';
		html_it += '</li>';

		if(typeof selected == "string" && selected == u.agent_id)
			add_html_user_to_filter(html_it);
		else		
			html += html_it;
	});
	if(html.trim().length == 0)
		html = '<li>No matching learners found.</li>';
	else
		html += '<li class="text-align:center"><button class="btn default" onclick="jQuery(this).parent().parent().html(\'\'); jQuery(\'#users-search\').parent().parent().addClass(\'hidden\');">Clear</button></li>';

	if(typeof selected != "string" || selected.length == 0)
	jQuery(id).html(html);

	update_filter_count();
}
function add_html_user_to_filter(html) {
	jQuery("ul.list-users:first").prepend(html);

}
function add_this_user_to_filter(id) {
	var parent = jQuery(id).parent();
	var agent_id = parent.parent().find('.media-heading-sub').text().trim();
	parent.html('<a href="javascript:;" onclick="jQuery(this).parent().parent().remove();" class="pull-right"><i class="fa fa-times"></i></a><input type="hidden" name="agent_id[]" value="'+ agent_id +'">');
	parent.parent("li").insertBefore("ul.list-users:first > li:last");
}


var searching_acitivites = 0;
function get_activities_search_list(id, search, selected, force) {
	if(searching_acitivites == 1 && typeof selected != "string")
		return;
	searching_acitivites = 1;
	jQuery(id).html("<li><div style='text-align: center;'><i class='fa fa-spin fa-refresh'> </i></div><div>Searching activities matching '" + search + "' ...</div></li>");
	jQuery.ajax({
		url: ajaxurl + "Reports/activities?layout=ajax&limit=10&object_like=" + search,
		dataType: "json",
		success: function( data ) {
			searching_acitivites = 0;
			activities_list = data;
			if(id != undefined) {
				show_activities_search_list(id, selected, activities_list);
			}
		}
	});
}

function show_activities_search_list(id, selected, activities_list) {
	var html = '';
	if(typeof activities_list == "object")
	jQuery.each(activities_list, function(i, u) {
		html_it = '<li>';
		html_it += '	<div class="object-add">';
		if(typeof selected == "string" && selected == u.objectid)
		{
			html_it += '		<a href="javascript:;" onclick="jQuery(this).parent().parent().remove();" class="pull-right"><i class="fa fa-times"></i></a>';
			html_it += '		<input type="hidden" name="objectid[]" value="'+ u.objectid +'">';
		}
		else
		html_it += '		<a href="javascript:;" onclick="add_this_activity_to_filter(this)"><i class="fa fa-plus"></i></a>';

		html_it += '	</div>';
		html_it += '	<div class="object-body">';
		html_it += '		<h4 class="object-heading">' + u.object_name + '</h4>';
		html_it += '		<div class="object-heading-sub" title="' + u.objectid + '">';
		html_it += 				u.objectid;
		html_it += '		</div>';
		html_it += '	</div>';
		html_it += '</li>';

		if(typeof selected == "string" && selected == u.objectid)
			add_html_activity_to_filter(html_it);
		else		
			html += html_it;
	});
	if(html.trim().length == 0)
		html = '<li>No matching activities found.</li>';
	else
		html += '<li class="text-align:center"><button class="btn default" onclick="jQuery(this).parent().parent().html(\'\'); jQuery(\'#activities-search\').parent().parent().addClass(\'hidden\');">Clear</button></li>';
	
	if(typeof selected != "string" || selected.length == 0)
	jQuery(id).html(html);

	update_filter_count();
}
function add_html_activity_to_filter(html) {
	jQuery("ul.list-activities:first").prepend(html);
}
function add_this_activity_to_filter(id) {
	var parent = jQuery(id).parent();
	var objectid = parent.parent().find('.object-heading-sub').text().trim();
	parent.html('<a href="javascript:;" onclick="jQuery(this).parent().parent().remove();" class="pull-right"><i class="fa fa-times"></i></a><input type="hidden" name="objectid[]" value="'+ objectid +'">');
	parent.parent("li").insertBefore("ul.list-activities:first > li:last");
}
function daterange_filter_init(id,onSelected) {
    $(id).daterangepicker({
            opens: (Metronic.isRTL() ? 'left' : 'right'),
            startDate: moment().subtract('days', 29),
            endDate: moment(),
            minDate: '01/01/2012',
/*          maxDate: '01/01/2014',*/
/*            dateLimit: {
                days: 60
            },*/
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf('month'), moment()],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'This Year': [moment().startOf('year'), moment()]
            },
            buttonClasses: ['btn'],
            applyClass: 'green',
            cancelClass: 'default',
            format: 'MM/DD/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Apply',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        },
        
        function (start, end) {
            $(id).children('span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));

            if(typeof onSelected == "function")
            onSelected(start, end);
        }
    );
	$(".daterangepicker").css("z-index", 10000);
    //Set the initial state of the picker label
     $(id).children('span').html("Select...");
    //$(id).children('span').html(moment().subtract('days', 29).format('MMM D, YYYY') + ' - ' + moment().format('MMM YYYY'));
}
function daterange_filter_set(start,end) {
		var inputs_html = '';
		inputs_html = "<input type='hidden' name='timestamp_start' value='" + start.format("YYYY-MM-DD") + "' />";
		inputs_html += "<input type='hidden' name='timestamp_end' value='" + end.format("YYYY-MM-DD") + "' />";
		jQuery(".filter-daterange-inputs").html(inputs_html);
    	$("#daterange_filter").children('span').html(start.format('MMM DD, YYYY') + ' - ' + end.format('MMM DD, YYYY'));
    	show_daterange_filter(true);
}
function show_daterange_filter(show) {
	if(show) {
		jQuery(".list-daterange .hidden").removeClass("hidden");
		jQuery(".filter-daterange").html("<i class='fa fa-times'></i>")
	}
	else
	{
		jQuery(".list-daterange li:first").addClass("hidden");		
		jQuery(".filter-daterange").html("<i class='fa fa-plus'></i>");
		jQuery(".filter-daterange-inputs").html('');
	    jQuery("#daterange_filter").children('span').html("Select...");
	}
	update_filter_count();
}
function update_filter_count() {
        var count = 0;
        jQuery("#filter-sidebar").find("input[type=hidden], input[type=checkbox]:checked, select, .list-score input, .list-score-percentage input").each(function() {
                if(jQuery(this).val() != "" && jQuery(this).val() != 0 && jQuery(this).closest(".dont-count").length == 0)
                count += 1;
        });
        jQuery(".filter-count").html(count);
}
function remove_parent(id) {
	jQuery(id).parent("li").remove();
}
function subpage_search(element) {
	var search = element.value.toLowerCase();
	jQuery("#lightbox #subpage tr[class^='statement']").show();
	if(search.length < 3) {
		return;
	}
	jQuery("#lightbox #subpage tr[class^='statement']").each(function() {
		if(jQuery(this).html().toLowerCase().indexOf(search) < 0)
			jQuery(this).hide();
		//console.log(jQuery(this).html());
	});
}
function page_search(element) {
	var search = element.value.toLowerCase();
	jQuery("#main_table  tr[class^='statement']").show();
	if(search.length < 3) {
		return;
	}
	jQuery("#main_table tr[class^='statement']").each(function() {
		if(jQuery(this).html().toLowerCase().indexOf(search) < 0)
			jQuery(this).hide();
		//console.log(jQuery(this).html());
	});
}
var parent_temp;
function retrigger(buttonid, id, retrigger_url) {
	var url = retrigger_url + "/" + id + "?r=" + Math.random();
	jQuery(buttonid).html("Wait...");

	jQuery.ajax({
			url: url,
			dataType: "json",
			success: function( data ) {
				if(data != undefined && data.success == 1) {
					console.log(buttonid);
					console.log("success");
					jQuery(buttonid).html("Done");

				}
				else
				{
					jQuery(buttonid).html("Failed");
				}
			},
			error: function( data ) {
					jQuery(buttonid).html("Failed");
			}
		});
	return false;
}
var node;
function show_json(id) {
	var parent = jQuery(id).parent().parent();
	var processed = parent.find(".json-statement-processed");
	if(processed.text().length > 10)
		return false;

	json = parent.find(".json-statement").text().trim();

	try{ o = JSON.parse(json); }
	catch(e){ 
		alert('not valid JSON');
		return false;
	}

	 node = new PrettyJSON.view.Node({
		el: processed,
		data: o,
	});
	node.expandAll();
	//node.childs[0].expandAll();
	//node.childs[1].expandAll();
	//node.childs[2].expandAll();
	//node.childs[3].expandAll();
	//parent.find('.json-statement-processed').show();
	//parent.find('.json-button').hide();
	//parent.find('.details-button').show();
	//parent.find('.json-details').hide(); 

	return false;
}
function get_json(id) {
	json = jQuery("#json-statement-" + id).text().trim();
	result = jQuery("#json-" + id).text();

	try{ o = JSON.parse(json); }
	catch(e){ 
		alert('not valid JSON');
		return false;
	}

	var node = new PrettyJSON.view.Node({
		el: jQuery("#json-"+id),
		data: o,
	});
	show_lightbox("json-" + id);
	return false;
}
function show_lightbox(id) {
	jQuery("#" + id).show();
	jQuery("#overlay").show();
	return false;
}
function hide_lightbox() {
	jQuery(".lightbox").hide();
	jQuery("#overlay").hide();
}
function show_filters() {
	jQuery("#filters").show();
	jQuery("#show_filters").hide();
}
function add_to_filter(id, name, params, single) {
	var html, i;
	id_pairs = params.split("&");
	id_pairs_html = '';
	if(single)
		array_part = "";
	else
		array_part = "[]";
	for( i = 0; i < id_pairs.length; i++) {
		id_pairs_html += "<input type='hidden' name='" + id_pairs[i].split("=")[0] + array_part + "' value='" + decodeURIComponent(id_pairs[i].split("=")[1]) + "' />"; 
	}

	html = '<div class="' + name + '_parent filter_parent" ><b>' + name + ':</b>';
	html += '<div style="position:relative; border: 1px solid black; margin: 5px 0; padding: 10px;"  onclick="edit_filter(this);">';
	html += '<div class="' + name + '">' + jQuery(id).parent().parent().html() + '</div>';
	html += '<a style="position:absolute; right: 1px; top: 1px; cursor:pointer;" onClick="remove_from_filter(this);">X</a>';
	html += id_pairs_html;
	html += '</div></div>';
	jQuery("#filters_list").append(html);
	show_filters();	
	return false;
}
function add_input_to_filter(name, params, single, type) {
	var html, i;
	id_pairs = params.split("&");
	id_pairs_html = '';
	for( i = 0; i < id_pairs.length; i++) {
		var value = decodeURIComponent(id_pairs[i].split("=")[1]) ;
		if(value == undefined || value == "undefined") value = "";

		if(!type)
			type = "text";

		if(single)
			array_part = "";
		else
			array_part = "[]";

		if(type == "checkbox") {
			value = "1";
			checked = "checked='checked'";
		}
		else
			checked = '';

		id_pairs_html += "<input type='" + type + "' name='" + id_pairs[i].split("=")[0] + array_part + "' value='" + value + "'  ' " + checked + "/>&nbsp;"; 
	}

	html = '<div class="' + name + '_parent filter_parent" ><b>' + name + ':</b>';
	html += '<div style="position:relative; border: 1px solid black; margin: 5px 0; padding: 10px;">';
	html += '<a style="position:absolute; right: 1px; top: 1px; cursor:pointer;" onClick="remove_from_filter(this);">X</a>';
	html += id_pairs_html;
	html += '</div></div>';
	jQuery("#filters_list").append(html);
	show_filters();
	return false;
}
function remove_from_filter(id) {
	jQuery(id).parent().parent().remove(); 
	return false;
}
function edit_filter(filter) {
	jQuery(filter).find("input[type=hidden]").each(function(i, v) {
		var name = jQuery(this).attr("name");
		var value = jQuery(this).attr("value");
		var input_html = "<input type='text' name='" + name + "' value='" + value + "' />"; 
		jQuery(this).replaceWith(input_html);

	});
	jQuery
}

function highlight_pulsate(id) {
	jQuery(id).pulsate({repeate: false, color:"green"});
}
function highlight(id) {
	var bg = jQuery(id).css("background");
	jQuery(id).css("background", 'lightyellow');
	setTimeout(function() {
		jQuery(id).css("background", bg);
	}, 500);
}
jQuery(function() { 
	jQuery(".controller-configure.action-integrations #content .tab-pane input, .controller-configure.action-integrations #content .tab-pane select").change(function(){
		jQuery(this).parents(".tab-pane").find(".config_integration_test").hide();
	});
	jQuery("#filters .filter_parent > div").click(function() {
//		alert('test');
		jQuery(this).find("input[type=hidden]").each(function(i, v) {
			var name = jQuery(this).attr("name");
			var value = jQuery(this).attr("value");
			var input_html = "<input type='text' name='" + name + "' value='" + value + "' />"; 
			jQuery(this).replaceWith(input_html);

		});
	});
	initialize_filters();
	update_filter_count();
	function initialize_filters() {
		var i;
		var url = window.location;
		var url_parts = url.search.split("&");
		for(i = 0; i < url_parts.length; i++)
		{
			v = url_parts[i].replace("?", "");
			if(v == "related=1") {
				add_input_to_filter('Sub Statements', 'related', true, 'checkbox');		
			}
			if(v == "object_is_parent=1") {
				add_input_to_filter('Module Level Statements (Object ID = Parent ID)', 'object_is_parent', true, 'checkbox');		
			}
			if(v == "object_is_group=1") {
				add_input_to_filter('Course Level Statements (Object ID = Grouping ID)', 'object_is_group', true, 'checkbox');
			}
			if(v.search("parent_id=") >= 0) {
				add_input_to_filter('Parent ID', v, true);
			}
			if(v.search("grouping_id=") >= 0) {
				add_input_to_filter('Grouping ID', v, true);
			}
			if(v.search("timestamp_start=") >= 0) {
				add_input_to_filter('Time From', v, true);
			}
			if(v.search("timestamp_end=") >= 0) {
				add_input_to_filter('Time To', v, true);
			}
			if(v.search("agent_name=") >= 0) {
				add_input_to_filter('Name', v, true);
			}
		}
	}

	if(jQuery('.page-sidebar-menu  a[href="' + pageurl + '"]').length)
	{
		jQuery('.page-sidebar-menu  a[href="' + pageurl + '"]').parent("li").addClass("active");
		if(jQuery('.page-sidebar-menu  a[href="' + pageurl + '"]').parent("li").parent("ul").parent("li").length)
		{
			jQuery('.page-sidebar-menu  a[href="' + pageurl + '"]').parent("li").parent("ul").parent("li").addClass("active open");
		}
	}
	jQuery("li.active").closest('ul.sub-menu').show();

});

function get_selected_filter_columns() {
	var columns = new Array;
	jQuery('#report_table_column_toggler input:checked').each(function() 
	{
		columns.push(jQuery(this).attr("data-column"));
	   
	});
	return columns.join(",");
}

function show_dashboard_data(type) {
	var url = ajaxurl + "Reports/dashboard/" + type;
    if(typeof type == "string" && type.length > 0) {
	    var spinner = '<span class="fa fa-spin fa-spinner" style="font-size:20px"></span>';
	    jQuery("#dashboard-" + type + " .number").html(spinner);


	    jQuery.getJSON(url, function(data) {
	       console.log(data);
		   jQuery("#dashboard-" + type + " .number").html(data.value);
		});
	}
}

/**** Played Segments *****/
		jQuery(function() {
			show_played_segments();
		});
		function show_played_segments() {
			jQuery(".played-segments").each(function() {
				var played_segments_div_main = jQuery(this);
				if(played_segments_div_main.html() == "")
					played_segments_div_main.html("<div><div></div></div>");

				var played_segments_div = jQuery(played_segments_div_main).children("div");
				var played_segments = played_segments_div_main.data("played-segments");
				if(played_segments != undefined && played_segments != "") {
					var length = 1 * played_segments_div_main.data("video-length");
					if(length == undefined || isNaN(length)) {
						var progress = played_segments_div_main.data("video-progress");
						if(progress == undefined) {
							played_segments_div_main.hide();				
						    return; // stop processing this iteration
						}
						var dummy_length = Math.max.apply(Math, played_segments.split(/\[.\]/));
						//console.log("dummy: " + dummy_length);
						var progress_data = get_progress_data(played_segments, dummy_length);
						//console.log(progress_data);
						var length = 1 * (dummy_length * progress_data.progress / progress).toFixed(3);
						//console.log("length: " + length);
					}
					var progress_data = get_progress_data(played_segments, length);
						//console.log(progress_data);
					if(progress_data.segments.length > 0) {
						played_segments_div.html("");
						progress_data.segments.forEach(function(segment, i) {
							
							var left = (segment[0] * 100/length).toFixed(2) + "%";
							var width = ((segment[1] - segment[0])*100/length).toFixed(2) + "%";
							var style = "left:" + left + "; width:" + width;
							var left_indicator = "<div class='left_indicator'>" + segment[0].toFixed(1) + "s</div>";
							var right_indicator = "<div class='right_indicator'>" + segment[1].toFixed(1) + "s</div>";
							var css_class = "played-segment";
							played_segments_div.append(jQuery("<div class='"+ css_class + "' style='" + style + "'><div>" + left_indicator + right_indicator + "</div></div>"));
						});
						//played_segments_div.append(jQuery("<div class='left_indicator'>0s</div>"));
						played_segments_div.append(jQuery("<div class='right_indicator'>" + length.toFixed(1) + "s</div>"));
						played_segments_div.append(jQuery("<div class='progress'>" + progress_data.progress * 100 + "% completed, Time Spent: " + progress_data.time_spent.toFixed(1) + "s </div>"));
					}
				}
				else
					played_segments_div_main.hide();
			});		



			jQuery( ".played-segment" ).hover(
				function() {
					var a = jQuery(this).find('div div.left_indicator');
					var b = jQuery(this).find('div div.right_indicator')
					jQuery(a).position({my:'right top', at:'left bottom', of:jQuery(this)});
					jQuery(b).position({my:'left top', at:'right bottom', of:jQuery(this)});

					var limits_left = jQuery(this).parent().offset().left;
					var limits_right = jQuery(this).parent().offset().left +  jQuery(this).parent().width();

					var a_right = a.offset().left + a.width();
					var b_right = b.offset().left + b.width();

					if(b_right > limits_right) 
					{
						jQuery(b).position({my:'right+5 top', at:'right bottom', of:jQuery(this)});
						limits_right = b.offset().left;
					}

					if(a_right > limits_right) 
					{
						jQuery(a).position({my:'right top', at:'left top', of:a});
					}


					if(a.offset().left < limits_left) {
						jQuery(a).position({my:'left-5 top', at:'left bottom', of:jQuery(this)});
						limits_left = limits_left + a.width();
					}

					if(b.offset().left < limits_left) {
						jQuery(b).position({my:'left top', at:'right top', of:a});
						limits_left = limits_left + a.width();
					}
				}, 
				function() {

				}
			);

			jQuery("body").click(function(el) {
				if(jQuery(el.target).closest(".played-segments").length > 0)
					jQuery(el.target).closest(".played-segments").addClass("played-segments-full-screen");
				else
				if(jQuery(el.target).closest(".played-segments-full-screen").length == 0)
					jQuery(".played-segments-full-screen").removeClass("played-segments-full-screen");
			});
		}
		function get_progress_data(played_segments, length) {
        	var arr, arr2;
        	
        	//get played segments array
        	arr = (played_segments == "")? []:played_segments.split("[,]");

			arr2 = [];
			var time_spent = 0;
			arr.forEach(function(v,i) {
				arr2[i] = v.split("[.]");
				arr2[i][0] *= 1;
				arr2[i][1] *= 1;
				time_spent += arr2[i][1] - arr2[i][0];
				//console.log(time_spent + " += " + arr2[i][1] +" - " + arr2[i][0]);
			});

			//sort the array
			arr2.sort(function(a,b) { return a[0] - b[0];});

			var segments = jQuery.extend(true, [], arr2);

			//normalize the segments
			arr2.forEach(function(v,i) {
				if(i > 0) {
					if(arr2[i][0] < arr2[i-1][1]) { 	//overlapping segments: this segment's starting point is less than last segment's end point.
						//console.log(arr2[i][0] + " < " + arr2[i-1][1] + " : " + arr2[i][0] +" = " +arr2[i-1][1] );
						arr2[i][0] = arr2[i-1][1];
						if(arr2[i][0] > arr2[i][1])
							arr2[i][1] = arr2[i][0];
					}
				}
			});

			//calculate progress_length
			var progress_length = 0;
			arr2.forEach(function(v,i) {
				if(v[1] > v[0])
				progress_length += v[1] - v[0]; 
			});

			var progress = 1 * (progress_length / length).toFixed(2);
			return {"progress" : progress, "progress_length" : progress_length, "time_spent" : time_spent , "segments" : segments, "normalised_segments" : arr2};
        }
/****** Played Segments *****/
