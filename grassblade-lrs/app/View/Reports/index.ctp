<?php 
	echo $this->element("report_header");
	echo $this->Html->css('/app/webroot/css/pretty-json');
	echo $this->Html->script('/app/webroot/js/underscore-min');	
	echo $this->Html->script('/app/webroot/js/backbone-min');	
	echo $this->Html->script('/app/webroot/js/pretty-json-min');	
?>
<div id="overlay" style="display:none" onClick="hide_lightbox();"></div>
<div id="lightbox" class="lightbox" style="display:none"></div>
<div id="report-summary-chart" style="margin-top:30px;">
	<?php echo $this->element('dashboard-chart', array('color' => 'red', 'conditions' => $conditions)); ?>
</div>
<div id="report-bell-chart" style="margin-top:30px;">
	<?php echo $this->element('report-bell-chart', array('color' => 'red',  'conditions' => $conditions)); ?>
</div>
<div class="gbStatements activity_stream gb_report" id="gb_report">
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa icon-pedestrian" style=" font-size: 50px; line-height: 14px; height: 14px; position: relative; top: -9px;"></i><?php echo __("Activity Stream"); ?>
			</div>
			<?php echo $this->element("report_tools"); ?>
		</div>
		<div class="portlet-body form">

			<div class="table-scrollable" style="margin: 0 !important">
				<table class="table table-striped table-bordered table-hover" id="report_table">
				<thead>
				<tr>
					<th class="sno"><?php echo __("S.No."); ?></th>
					<th class="timestamp"><?php echo $this->Paginator->sort('timestamp', __("Timestamp")); ?></th>
					<th class="agent"><?php echo $this->Paginator->sort('agent_name', __("Learner")); ?></th>
					<th class="agent_id hide_column"><?php echo $this->Paginator->sort('agent_id', h("Learner ID/Email")); ?></th>
					<th class="verb"><?php echo $this->Paginator->sort('verb'); ?></th>
					<th class="activity"><?php echo $this->Paginator->sort('object_definition_name', __("Activity")); ?></th>
					<?php if(isset( $gbStatements[0]["Statement"]["attempts"] )) { ?>
					<th class="attempts"><?php echo __("Attempts"); ?></th>
					<?php } ?>
					<th class="result"><?php echo $this->Paginator->sort('result_score_raw', "Result"); ?></th>
					<th class="result_score_raw"><?php echo $this->Paginator->sort('result_score_raw', __("Score")); ?></th>
					<th class="result_score_scaled"><?php echo $this->Paginator->sort('result_score_scaled', __("Percentage")); ?></th>
					<th class="result_score_min"><?php echo $this->Paginator->sort('result_score_min', __("Min")); ?></th>
					<th class="result_score_max"><?php echo $this->Paginator->sort('result_score_max', __("Max")); ?></th>
					<th class="result_duration"><?php echo $this->Paginator->sort('result_duration', __("Time Spent")); ?></th>
					<th class="choices"><?php echo __("Choices"); ?></th>
					<th class="correct_response"><?php echo __("Correct Response"); ?></th>
					<th class="result_response"><?php echo __("Response"); ?></th>
					<th class="result_extensions"><?php echo __("Extra Info"); ?></th>
					<?php
						if(!empty($content_types))
						foreach ($content_types as $content_type_tag => $content_type) {
							?>
							<th class="<?php echo $content_type_tag; ?>"><?php echo __($content_type["name"]); ?></th>					
							<?php
						}
					?>
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
								<div class="details-data" id="details-<?php echo h($gbStatement['Statement']['id']); ?>" style="display:none;">
									<div class="center details-buttons">
										<a data-placement="top" data-original-title="<?php echo __('JSON Statement as received by the LRS'); ?>"  href="#" class="tooltips btn green"  href="javasctipt:;" onClick="show_json(this); show_only_siblings(this); copy_html('.json-statement-processed','.details-show',this, 2); prepend_heading(this, jQuery(this).parent().parent().children('.details-show')); return false;"><?php echo __("JSON"); ?></a>
										<a data-placement="top" data-original-title="<?php echo __('Complete Statement in tabular form.'); ?>"  href="#" class="tooltips btn purple" onClick="show_only_siblings(this); copy_html('.json-details','.details-show',this, 2); prepend_heading(this, jQuery(this).parent().parent().children('.details-show')); return false;"><?php echo __("Details"); ?></a>
										<a data-placement="top" data-original-title="<?php echo __('Statements with same User, Verb and Activity'); ?>"  href="<?php echo h($activity_stream_report_url_timestamp_asc."?agent_id=".$gbStatement['Statement']['agent_id']."&objectid=".urlencode($gbStatement['Statement']['objectid'])."&verb_id=".urlencode($gbStatement['Statement']['verb_id'])); ?>" class="tooltips btn yellow" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first')); jQuery(id).find('[data-column=2],[data-column=3],[data-column=4],[data-column=5]').hide();prepend_heading(id_this, id); }); return false;"><?php echo __("Repetition"); ?></a>
										<a data-placement="top" data-original-title="<?php echo __('Re-run all the triggers for this statement'); ?>"  class="tooltips retrigger btn red" href="#" onClick="show_siblings(this); return retrigger(this, <?php echo $gbStatement['Statement']['id']; ?>, '<?php echo $retrigger_url; ?>');"><?php echo __("Re-run triggers"); ?></a>

									</div>
									<div class="css_table json-details"  style="display:none">
									<?php 
									$statement_arr = json_decode($gbStatement['Statement']['statement']);
									$flat_statement = keys_humanize(flatten($statement_arr));
									foreach ($flat_statement as $key => $value) {
										echo "<div><div class='th'>".h($key)."</div><div>".h($value)."</div></div>";
									}
									?>
									</div>
									<div class="json-statement" style="display:none"><pre><?php echo h($gbStatement['Statement']['statement']); ?></pre></div>
									<div class="json-statement-processed" style="display:none"></div>

									<div class="details-show">
									</div>	
								
								</div>
							</div>
						</td>
						<td class="timestamp"><?php echo h($gbStatement['Statement']['readable_timestamp']); ?>&nbsp;
						</td>
						<td class="agent">
							<div style="" title="<?php		
										echo h(@$gbStatement['Statement']['agent_id']);
										$params = @$gbStatement['Statement']['agent_params'];
									?>">
								
								<?php 
								 echo '<img src="//www.gravatar.com/avatar/'.md5(strtolower(h(@$gbStatement['Statement']['agent_id']))).'?&s=50&d=mm" align="middle"/>';
								?> 
								<a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><span style='margin:10px;font-weight:bold;color:blue'><?php echo h(@$gbStatement['Statement']['agent_name']); ?></span></a>
							</div>
						</td>
						<td class="agent_id">
							<?php 	echo h(@$gbStatement['Statement']['agent_id']); ?>
						</td>
						<td  class="verb" title="<?php  echo h($gbStatement['Statement']['verb_id']); 
							$params = "verb_id=".urlencode($gbStatement['Statement']['verb_id']);
							?>"
							><a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><?php echo h($gbStatement['Statement']['verb']); ?></a>&nbsp;
						</td>
						<td  class="activity" title="<?php
							$params = "objectid=".urlencode($gbStatement['Statement']['objectid']);
							?>">
							<div style=""><a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><?php
								echo h(modified("object_name", $gbStatement["Statement"]["object_name"]));
							?></a>
							</div>
						</td>	
						<?php if(isset( $gbStatement["Statement"]["attempts"] )) { ?>
						<td class="attempts"><?php echo h($gbStatement["Statement"]["attempts"]); ?></td>
						<?php } ?>
						<td class="result"><div><?php 
							$result = '';
							if(!empty($gbStatement['Statement']['result_success'])) 
							if($gbStatement['Statement']['result_success'] == 1 or $gbStatement['Statement']['result_success'] == "true")
								echo "<i class='fa fa-trophy' style='color: gold;'></i> ";
							else
								echo  "<i class='fa fa-ban' style='color: red;'></i> ";			
							
							echo h($gbStatement["Statement"]["result"]);
							?>
							</div>
						</td>
						<td class="result_score_raw"><?php echo h($gbStatement['Statement']['result_score_raw']); ?></td>
						<td class="result_score_scaled"><?php  if(!is_null($gbStatement['Statement']['result_score_scaled']))	echo number_format(h($gbStatement['Statement']['result_score_scaled'])*100, 2)."%"; ?></td>
						<td class="result_score_min"><?php echo h($gbStatement['Statement']['result_score_min']); ?></td>
						<td class="result_score_max"><?php echo h($gbStatement['Statement']['result_score_max']); ?></td>
						<td class="result_duration"><?php if(!empty($gbStatement['Statement']['readable_result_duration'])) echo h($gbStatement['Statement']['readable_result_duration']); ?></td>
						<td class="choices"><?php if(!empty($gbStatement['Statement']['choices'])) echo str_replace("\r\n", "<Br>", h($gbStatement['Statement']['choices'])); ?></td>
						<td class="correct_response"><?php if(!empty($gbStatement['Statement']['correct_response'])) echo str_replace("\r\n", "<Br>", h($gbStatement['Statement']['correct_response'])); ?></td>		
						<td class="result_response"><?php if(!empty($statement->result->response))  echo $gbdb->get_response_translation_name($statement->result->response, true, "<br>", $statement); ?></td>
						<td class="result_extensions">
								<div class="extensions" style="word-wrap: break-word; max-width: 400px;">
								<?php 
								echo nl2br(h(@$gbStatement['Statement']['extensions']));
								?>
								</div>
								<?php
								//Video Played Segments
								echo get_video_played_segments_div($statement);
						 		?>
						</td>
						<?php
							if(!empty($content_types))
							foreach ($content_types as $content_type_tag => $content_type) {
								?>
								<td class="<?php echo $content_type_tag; ?>"><?php display_contents(@$gbStatement["Statement"][$content_type_tag]); ?></td>
								<?php
							}
						?>
					</tr>
				<?php } ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php echo $this->element("report_footer"); ?>
