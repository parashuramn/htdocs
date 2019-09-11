<?php
	global $gbdb, $filter_conditions;
	echo $this->Html->css('pretty-json');
	echo $this->Html->script('underscore-min');	
	echo $this->Html->script('backbone-min');	
	echo $this->Html->script('pretty-json-min');	
	$filter_conditions = $conditions;
	$report_url = $this->Html->url(array('controller' => 'Statements', 'action' => 'index'));

	function filter_class($params){
		global $filter_conditions;
		//echo $params;
	//	print_r($filter_conditions);
		$params_list = explode("&", $params);
		$count = 0;
		foreach ($params_list as $param) {	
			$param = explode("=", $param);
			$k = $param[0];
			$v = urldecode($param[1]);
			if(isset($filter_conditions[$k])) {
				if(is_array($filter_conditions[$k])) {
					$keys = array_keys($filter_conditions[$k], $v);
					if(!empty($keys))
					{
						foreach($keys as $key) {
						//	echo $key;
							unset($filter_conditions[$k][$key]);
						}
						$count++;
					}
				}
				else if($filter_conditions[$k] == $v)
				{
					unset($filter_conditions[$k]);
					$count++;
				}
			}
		}
	//	print_r($filter_conditions);

		if(count($params_list) == $count)
			return "filter_class";
		else
			return "";
	}
?>

<script>
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
function add_to_filter(id, name, params) {
	var html, i;
	id_pairs = params.split("&");
	id_pairs_html = '';
	for( i = 0; i < id_pairs.length; i++) {
		id_pairs_html += "<input type='hidden' name='" + id_pairs[i].split("=")[0] + "[]' value='" + decodeURIComponent(id_pairs[i].split("=")[1]) + "' />"; 
	}

	html = '<div class="' + name + '_parent filter_parent" ><b>' + name + ':</b>';
	html += '<div style="position:relative; border: 1px solid black; margin: 5px 0; padding: 10px;">';
	html += '<div class="' + name + '">' + jQuery(id).parent().parent().html() + '</div>';
	html += '<a style="position:absolute; right: 1px; top: 1px; cursor:pointer;" onClick="remove_from_filter(this);">X</a>';
	html += id_pairs_html;
	html += '</div></div>';
	jQuery("#filters_list").append(html);
	jQuery("#filters").show();
	return false;
}
function remove_from_filter(id) {
	jQuery(id).parent().parent().remove(); 
	return false;
}
jQuery(function() {
	jQuery(".filter_class").click();
});
var statements = new Array();
</script>
<div id="overlay" style="display:none" onClick="hide_lightbox();"></div>
<div class="gbStatements index">
	<h2><?php echo __('Reports'); ?></h2>
	<form method="GET" action="<?php echo h($report_url); ?>">
	<div id="filters" style="float:right; border: 2px solid black; padding: 15px; min-width:300px;margin-bottom: 20px; display:none;">
		<b><?php echo __('Filters'); ?>:</b>
		<hr style="margin:10px 0;">
		<div id="filters_list"></div><br>
		<input type="submit" value="Apply Filters"  class="btn blue" style="width: 100%;"/> <br><br>
		<input type="submit" value="Clear"  class="btn default" style="width: 100%;" onClick="window.location = '<?php echo h($report_url); ?>'; return false;"/>
	</div>
	</form>
	<div style="overflow:scroll; width: 100%;">
	<table cellpadding="0" cellspacing="0" class="css_table">
	<tr>
			<th><?php echo $this->Paginator->sort('timestamp'); ?></th>
			<th><?php echo $this->Paginator->sort('agent_name', __("Agent")); ?></th>
			<th><?php echo $this->Paginator->sort('verb'); ?></th>
			<th><?php echo $this->Paginator->sort('object_definition_name', __("Activity")); ?></th>
			<th><?php echo $this->Paginator->sort('result_score_raw', __("Result")); ?></th>
			<!--<th><?php echo $this->Paginator->sort('parent_ids'); ?></th>
			<th><?php echo $this->Paginator->sort('grouping_ids'); ?></th>
			-->
			<th class="actions"><?php echo __('View'); ?></th>
	</tr>
	<?php foreach ($gbStatements as $gbStatement):
				$statement =  json_decode($gbStatement['Statement']['statement']);
	?>

	<?php
	$class = "statement-".$gbStatement['Statement']['id'];
		if(!empty($gbStatement['Statement']['result_success']))
		if($gbStatement['Statement']['result_success'] == 1 or $gbStatement['Statement']['result_success'] == "true")
			$class .= " passed";
		else
			$class .= " failed";
	$class .= " statement-version-".$gbStatement['Statement']['version'];
	?>
	<tr class="<?php echo h($class); ?>">
		<td><?php echo h($gbStatement['Statement']['timestamp']); ?>&nbsp;</td>
		<td>
			<div style="width:200px" title="<?php		
						$filter_class = "";	
						echo h(@$gbStatement['Statement']['agent_id']);
						$params = @$gbStatement['Statement']['agent_params'];
					?>">
				
				<?php 
				 echo '<img src="//www.gravatar.com/avatar/'.md5(strtolower(h(@$gbStatement['Statement']['agent_mbox']))).'?&s=50&d=mm" align="middle"/>';
				?> 
				<a href="<?php echo h($report_url."?".$params); ?>"><span style='margin:10px;font-weight:bold;color:blue'><?php echo h(@$gbStatement['Statement']['agent_name']); ?></span></a>
			</div>
			<div class="show_on_hover"><a href="#" onClick="return add_to_filter(this, 'Agent', '<?php echo $params; ?>');"  class="<?php echo filter_class(h($params)); ?>"><?php echo __("Add to Filter"); ?></a></div>
		</td>
		<td title="<?php  echo h($gbStatement['Statement']['verb_id']); 
			$params = "verb_id=".urlencode($gbStatement['Statement']['verb_id']);
			?>"
			><a href="<?php echo h($report_url."?".$params); ?>"><?php echo h($gbStatement['Statement']['verb']); ?></a>&nbsp;
			<div class="show_on_hover"><a href="#" onClick="return add_to_filter(this, 'Verb', '<?php echo $params; ?>');"  class="<?php echo filter_class(h($params)); ?>"><?php echo __("Add to Filter"); ?></a></div>
		</td>
		<td title="<?php
			$params = "objectid=".urlencode($gbStatement['Statement']['objectid']);
			?>">
			<div style="width:300px"><a href="<?php echo h($report_url."?".$params); ?>"><?php
				echo h($gbStatement["Statement"]["object_name"]);
			?></a>
			</div>
			<div class="show_on_hover"><a href="#" onClick="return add_to_filter(this, 'Activity', '<?php echo $params; ?>');"  class="<?php echo filter_class(h($params)); ?>"><?php echo __("Add to Filter"); ?></a></div>		
		</td>	
		<td><div style="width:250px; overflow: scroll"><?php 
			if(!empty($gbStatement['Statement']['result_completion']) && $gbStatement['Statement']['result_completion'] == 1 or $gbStatement['Statement']['result_completion'] == "true")
				echo "<b>".__("Completed")."</b><br>";
			
			if(!empty($gbStatement['Statement']['result_success'])) 
			if($gbStatement['Statement']['result_success'] == 1 or $gbStatement['Statement']['result_success'] == "true")
				echo "<b>".__("Passed")."</b><br>";
			else
				echo "<b>".__("Failed")."</b><br>";			
		
			if(!empty($statement->result->response)) {
				echo "<b>".__("Response").":</b> <br>".$gbdb->get_response_translation_name($statement->result->response, true, "<br>", $statement)."<br>";
			}

			if(!is_null($gbStatement['Statement']['result_score_raw']) || !is_null($gbStatement['Statement']['result_score_raw']) || !is_null($gbStatement['Statement']['result_score_scaled'])) {
				echo "<b>".__("Score").": </b>";
				if(!is_null($gbStatement['Statement']['result_score_raw']))
				echo h($gbStatement['Statement']['result_score_raw'])." ";
				
				if(!is_null($gbStatement['Statement']['result_score_scaled']))
				{
					echo "(".(h($gbStatement['Statement']['result_score_scaled'])) * 100;
					echo "%)";
				}
				
				echo "<br>";
				if(!is_null($gbStatement['Statement']['result_score_min']))
				echo __("Min").": ".h($gbStatement['Statement']['result_score_min'])." ";
				
				if(!is_null($gbStatement['Statement']['result_score_max']))
				echo __("Max").":".h($gbStatement['Statement']['result_score_max']);
				
				if(!empty($gbStatement['Statement']['result_duration']))
				echo "<br>".__("Time Spent").": ".to_time(h($gbStatement['Statement']['result_duration']));
			}
			?>
			</div>
		</td>
		<td>
			<a href="#" onClick="return show_lightbox('details-<?php echo h($gbStatement['Statement']['id']); ?>');"><?php echo __("DETAILS"); ?></a>
			<a href="#" onClick="return get_json(<?php echo $gbStatement['Statement']['id']; ?>);"><?php echo __("JSON"); ?></a>

			<div id="details-<?php echo h($gbStatement['Statement']['id']); ?>" class="lightbox" style="display:none; height: auto;">
				<table class="css_table" style="margin: 0">
				<?php 
				$statement_arr = json_decode($gbStatement['Statement']['statement']);
				$flat_statement = keys_humanize(flatten($statement_arr));
				foreach ($flat_statement as $key => $value) {
					echo "<tr><th>".h($key)."</th><td>".h($value)."</td></tr>";
				}
				?>
				</table>
			</div>
			<div id="json-statement-<?php echo h($gbStatement['Statement']['id']); ?>" style="display:none"><?php echo h($gbStatement['Statement']['statement']); ?></div>
			<div id="json-<?php echo h($gbStatement['Statement']['id']); ?>" class="lightbox" style="display:none; text-align: left;">
				
			</div>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	</div>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
