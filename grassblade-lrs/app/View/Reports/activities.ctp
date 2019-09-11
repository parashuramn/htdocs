<?php echo $this->element("report_header"); ?>
<div id="report-summary-chart" style="margin-top:30px;">
	<?php echo $this->element('dashboard-chart', array('color' => 'red', 'conditions' => $conditions)); ?>
</div>

<div class="gbStatements activities gb_report">
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa icon-pedestrian" style=" font-size: 50px; line-height: 14px; height: 14px; position: relative; top: -9px;"></i><?php echo __('Activities'); ?>
			</div>
			<?php echo $this->element("report_tools"); ?>
		</div>
		<div class="portlet-body form">

			<div class="table-scrollable" style="margin: 0 !important">
				<table class="table table-striped table-bordered table-hover" id="report_table">
				<thead>
				<tr>
						<th class="sno"><?php echo __("S.No."); ?></th>
						<th class="activity"><?php echo $this->Paginator->sort('object_definition_name', __("Activity")); ?></th>
						<th class="no_of_statements"><?php echo $this->Paginator->sort('no_of_statements', __("Number of Statements")); ?></th>
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
					$class = "statement-activity-".$counter;
					?>
					<tr class="<?php echo h($class); ?>" data-id=".details-data">
						<td  class="sno">
							<span>
								<?php echo h($counter); ?>
							</span>

							<div style="display:none;">								
								<div class="details-data" style="display:none;">
									<div class="center  details-buttons">
										<a href="<?php echo h($activity_stream_report_url."?limit=10&objectid=".urlencode($gbStatement['Statement']['objectid'])); ?>" class="btn red" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=5],[data-column=3]').hide(); prepend_heading(id_this, id);  });  return false;"><?php echo __("Last 10 Statements"); ?></a>
										<a href="<?php echo h($attempts_report_url."?objectid=".urlencode($gbStatement['Statement']['objectid'])); ?>" class="btn ggreen" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=3],[data-column=2]').hide();prepend_heading(id_this, id);  }); return false;"><?php echo __("Attempts"); ?></a>
										<a href="<?php echo h($attempts_summary_report_url."?objectid=".urlencode($gbStatement['Statement']['objectid'])); ?>" class="btn purple" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=3],[data-column=2]').hide();prepend_heading(id_this, id);  }); return false;"><?php echo __("Attempt Summary"); ?></a>	
										<a href="<?php echo h($verbs_report_url."?objectid=".urlencode($gbStatement['Statement']['objectid'])); ?>" class="btn yellow" onClick="show_only_siblings(this);  copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Verbs"); ?></a>
										<a href="<?php echo h($agents_report_url."?objectid=".urlencode($gbStatement['Statement']['objectid'])); ?>" class="btn blue" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Learners"); ?></a>
									</div>
									<div class="details-show">
									</div>									
								</div>
							</div>
						</td>
						<td  class="activity" title="<?php
							$params = "objectid=".h(urlencode($gbStatement['Statement']['objectid']));
							?>">
							<div><a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><?php
								echo h($gbStatement["Statement"]["object_name"]);
							?></a>
							</div>
						</td>
						<td  class="no_of_statements">
							<?php echo h($gbStatement['Statement']['no_of_statements']); ?>
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