<?php 
	echo $this->element("report_header");
	echo $this->Html->css('/app/webroot/css/pretty-json');
	echo $this->Html->script('/app/webroot/js/underscore-min');	
	echo $this->Html->script('/app/webroot/js/backbone-min');	
	echo $this->Html->script('/app/webroot/js/pretty-json-min');
?>
<div id="overlay" style="display:none" onClick="hide_lightbox();"></div>
<div id="lightbox" class="lightbox" style="display:none"></div>
<div class="gbStatements attempts gb_report">
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa icon-pedestrian" style=" font-size: 50px; line-height: 14px; height: 14px; position: relative; top: -9px;"></i><?php echo __('Attempts Report'); ?>
			</div>
			<?php echo $this->element("report_tools"); ?>
		</div>
		<div class="portlet-body form">

			<div class="table-scrollable" style="margin: 0 !important">
				<table class="table table-striped table-bordered table-hover" id="report_table">
				<thead>
				<tr>
					<th class="sno"><?php echo __("S.No."); ?></th>
					<th class="agent"><?php echo $this->Paginator->sort('agent_name', __("Learner")); ?></th>
					<th class="agent_id hide_column"><?php echo $this->Paginator->sort('agent_id', __("Learner ID/Email")); ?></th>
					<th class="activity"><?php echo $this->Paginator->sort('object_definition_name', __("Activity")); ?></th>
					<th class="started"><?php echo $this->Paginator->sort('timestamp', __("Started")); ?></th>
					<th class="completed"><?php echo __("Completed"); ?></th>
					<th class="result"><?php echo $this->Paginator->sort('result_score_raw', __("Result")); ?></th>
					<th class="result_score_raw"><?php echo $this->Paginator->sort('result_score_raw', __("Score")); ?></th>
					<th class="result_score_scaled"><?php echo $this->Paginator->sort('result_score_scaled', __("Percentage")); ?></th>
					<th class="result_score_min"><?php echo $this->Paginator->sort('result_score_min', __("Min")); ?></th>
					<th class="result_score_max"><?php echo $this->Paginator->sort('result_score_max', __("Max")); ?></th>
					<th class="result_duration"><?php echo $this->Paginator->sort('result_duration', __("Time Spent")); ?></th>
					<th class="result_response"><?php echo __("Response"); ?></th>
					<th class="result_extensions"><?php echo __("Extra Info"); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$page = $this->params['paging']['Statement']['page'];
				$limit = $this->params['paging']['Statement']['limit'];
				$counter = ($page * $limit) - $limit;

				if(!empty($gbStatements))
				foreach ($gbStatements as $gbStatement) {
					$counter++;
					$statement =  json_decode($gbStatement['Statement']['statement']);
					$class = "statement-".$gbStatement['Statement']['id'];
					if(!empty($gbStatement['Statement']['result_success']))
					if($gbStatement['Statement']['result_success'] == 1 or $gbStatement['Statement']['result_success'] == "true")
						$class .= " passed";
					else
						$class .= " failed";
					$class .= " statement-version-".$gbStatement['Statement']['version'];
					?>
					<tr class="<?php echo h($class); ?>" data-id=".details-data">
						<td  class="sno">
							<span>
								<?php echo h($counter); ?>
							</span>
							<div style="display:none;">								
								<div class="details-data" style="display:none;">
									<div class="center  details-buttons">
										<a href="<?php echo h($activity_stream_report_url_timestamp_asc."?timestamp_start=".@$gbStatement['Statement']['attempt_start']."&timestamp_end=".@$gbStatement['Statement']['attempt_end']."&grouping_id=".$gbStatement['Statement']['objectid']."&n_objectid=".$gbStatement['Statement']['objectid']."&agent_id=".$gbStatement['Statement']['agent_id']); ?>" class="btn yellow" onClick="copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'), 0); jQuery(id).find('[data-column=2],[data-column=3]').hide();prepend_heading(id_this, id); }); return false;"><?php echo __("Attempt Details"); ?></a>
										<a href="<?php echo h($activity_stream_report_url_timestamp_asc."?timestamp_start=".@$gbStatement['Statement']['attempt_start']."&timestamp_end=".@$gbStatement['Statement']['attempt_end']."&grouping_id=".$gbStatement['Statement']['objectid']."&n_objectid=".$gbStatement['Statement']['objectid']."&agent_id=".$gbStatement['Statement']['agent_id']."&verb_id=http://adlnet.gov/expapi/verbs/answered"); ?>" class="btn green" onClick="copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'), 0); jQuery(id).find('[data-column=10],[data-column=1],[data-column=2],[data-column=3],[data-column=4]').hide();prepend_heading(id_this, id); }); return false;"><?php echo __("Responses"); ?></a>
									</div>
									<div class="details-show">
									</div>									
								</div>
							</div>
						</td>
						<td class="agent">
							<div title="<?php		
										$filter_class = "";	
										echo h(@$gbStatement['Statement']['agent_id']);
										$params = @$gbStatement['Statement']['agent_params'];
									?>">
								
								<?php 
								 echo '<img src="//www.gravatar.com/avatar/'.md5(strtolower(h(@$gbStatement['Statement']['agent_id']))).'?&s=50&d=mm" align="middle"/>';
								?> 
								<a href="<?php echo h($attempts_report_url."?".$params); ?>"><span style='margin:10px;font-weight:bold;color:blue'><?php echo h(@$gbStatement['Statement']['agent_name']); ?></span></a>
							</div>
						</td>
						<td class="agent_id">
							<?php 	echo h(@$gbStatement['Statement']['agent_id']); ?>
						</td>
						<td  class="activity" title="<?php
							$params = "objectid=".urlencode($gbStatement['Statement']['objectid']);
							?>">
							<div><a href="<?php echo h($attempts_report_url."?".$params); ?>"><?php
								echo h($gbStatement["Statement"]["object_name"]);
							?></a>
							</div>
						</td>	
						<td class="started"><div><?php echo h($gbStatement['Statement']['readable_timestamp']); ?>
						</div></td>
						<td class="completed"><div ><?php echo h($gbStatement['Statement']['completion_timestamp']); ?></div></td>
						<td class="result"><div><?php 
							$result = '';
							if(!empty($gbStatement['Statement']['result_success'])) 
							if($gbStatement['Statement']['result_success'] == 1 or $gbStatement['Statement']['result_success'] == "true")
								$result = "<i class='fa fa-trophy' style='color: gold;'></i> ".(($gbStatement["Statement"]["verb"] == "answered")? __("Correct"):__("Passed"));
							else
								$result =  "<i class='fa fa-ban' style='color: red;'></i> ".(($gbStatement["Statement"]["verb"] == "answered")? __("Wrong"):__("Failed"));			
							
							if(empty($result))
							if(!empty($gbStatement['Statement']['result_completion']) && $gbStatement['Statement']['result_completion'] == 1 or $gbStatement['Statement']['result_completion'] == "true")
								$result =  "Completed";
							
							if(!empty($result))
							echo $result;
							?>
							</div>
						</td>
						<td class="result_score_raw"><?php echo h($gbStatement['Statement']['result_score_raw']); ?></td>
						<td class="result_score_scaled"><?php  if(!is_null($gbStatement['Statement']['result_score_scaled']))	echo h(number_format($gbStatement['Statement']['result_score_scaled']*100, 2)."%"); ?></td>
						<td class="result_score_min"><?php echo h($gbStatement['Statement']['result_score_min']); ?></td>
						<td class="result_score_max"><?php echo h($gbStatement['Statement']['result_score_max']); ?></td>
						<td class="result_duration"><?php if(!empty($gbStatement['Statement']['readable_result_duration'])) echo h($gbStatement['Statement']['readable_result_duration']); ?></td>
						<td class="result_response"><?php if(!empty($statement->result->response))  echo $gbdb->get_response_translation_name($statement->result->response, true, "<br>", $statement); ?></td>
						<td class="result_extensions"><?php 
							if(!empty($statement->result->extensions))  
							{
								foreach ($statement->result->extensions as $key => $value) {
									echo "<br>".h($key. ': '.maybe_to_time($value));
								}
							}
						 ?></td>
					</tr>
				<?php } ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php echo $this->element("report_footer", array("datalevel" => 0.01) ); ?>