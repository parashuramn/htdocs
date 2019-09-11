<?php echo $this->element("report_header"); ?>
<?php 
modified("report_after_header", '', $this); 
?>
<div class="gbStatements timespent gb_report">
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa icon-pedestrian" style=" font-size: 50px; line-height: 14px; height: 14px; position: relative; top: -9px;"></i><?php echo __('Time Spent'); ?>
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
					<th class="agent_id"><?php echo $this->Paginator->sort('agent_id', __("Learner Email/ID")); ?></th>
					<th class="timespent"><?php echo $this->Paginator->sort('timespent', __("Time Spent")); ?></th>
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
					$class = "statement-agent-".$counter;
					?>
					<tr class="<?php echo h($class); ?>"  data-id=".details-data">
						<td  class="sno">
							<span>
								<?php echo h($counter); ?>
							</span>
							<div style="display:none;">								
								<div class="details-data" style="display:none;">
									<div class="center  details-buttons">
										<a href="<?php echo h($activity_stream_report_url."?limit=10&agent_id=".urlencode($gbStatement['Statement']['agent_id'])); ?>" class="btn red" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=2],[data-column=3]').hide();prepend_heading(id_this, id);}); return false;"><?php echo __("Last 10 Statements"); ?></a>
										<a href="<?php echo h($attempts_report_url."?agent_id=".urlencode($gbStatement['Statement']['agent_id'])); ?>" class="btn ggreen" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=1],[data-column=2]').hide();prepend_heading(id_this, id);  }); return false;"><?php echo __("Attempts"); ?></a>
										<a href="<?php echo h($attempts_summary_report_url."?agent_id=".urlencode($gbStatement['Statement']['agent_id'])); ?>" class="btn blue" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=1],[data-column=2]').hide();prepend_heading(id_this, id);  }); return false;"><?php echo __("Attempt Summary"); ?></a>									
										<a href="<?php echo h($verbs_report_url."?agent_id=".urlencode($gbStatement['Statement']['agent_id'])); ?>" class="btn yellow" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Verbs"); ?></a>
										<a href="<?php echo h($activities_report_url."?agent_id=".urlencode($gbStatement['Statement']['agent_id'])); ?>" class="btn green" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Activities"); ?></a>
										<a href="<?php echo h($activities_report_url."?object_is_parent=1&agent_id=".urlencode($gbStatement['Statement']['agent_id'])); ?>" class="btn purple" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Parent Level Activities"); ?></a>
									</div>
									<div class="details-show">
									</div>									
								</div>
							</div>
						</td>
						<td  class="agent">
							<?php
										$params = @$gbStatement['Statement']['agent_params'];
							?>
							<div><?php 
								 echo '<img src="//www.gravatar.com/avatar/'.md5(strtolower(h(@$gbStatement['Statement']['agent_id']))).'?&s=50&d=mm" align="middle"/>';
								?> 
								<a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><span style='margin:10px;font-weight:bold;color:blue'><?php echo h(@$gbStatement['Statement']['agent_name']); ?></span></a>
							</div>
						</td>
						<td  class="agent_id">
							<?php
										$params = @$gbStatement['Statement']['agent_params'];
							?>
							<div><a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><?php
								echo h($gbStatement["Statement"]["agent_id"]);
							?></a>
							</div>
						</td>
						<td  class="timespent">
							<?php echo h($gbStatement['Statement']['readable_timespent']); ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php echo $this->element("report_footer"); ?>