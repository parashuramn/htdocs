<?php
	echo $this->element("report_header");	

	$report_url = $this->Html->url(array('controller' => 'Reports', 'action' => 'verbs',  "sort" => "no_of_statements", "direction" => "desc"));
	$activity_stream_report_url = $this->Html->url(array('controller' => 'Reports', 'action' => 'index'));
	$retrigger_url = $this->Html->url(array('controller' => 'Reports', 'action' => 'retrigger'));
	$agents_report_url = $this->Html->url(array('controller' => 'Reports', 'action' => 'agents',  "sort" => "no_of_statements", "direction" => "desc"));
	$verbs_report_url = $this->Html->url(array('controller' => 'Reports', 'action' => 'verbs',  "sort" => "no_of_statements", "direction" => "desc"));
	$activities_report_url = $this->Html->url(array('controller' => 'Reports', 'action' => 'activities',  "sort" => "no_of_statements", "direction" => "desc"));
?>
<div id="report-summary-chart" style="margin-top:30px;">
	<?php echo $this->element('dashboard-chart', array('color' => 'red', 'conditions' => $conditions)); ?>
</div>

<div class="gbStatements verbs gb_report">
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa icon-pedestrian" style=" font-size: 50px; line-height: 14px; height: 14px; position: relative; top: -9px;"></i><?php echo __('Verbs'); ?>
			</div>
			<?php echo $this->element("report_tools"); ?>
		</div>
		<div class="portlet-body form">

			<div class="table-scrollable" style="margin: 0 !important">
				<table class="table table-striped table-bordered table-hover" id="report_table">
				<thead>
				<tr>
					<th class="sno"><?php echo __("S.No."); ?></th>
					<th class="verb"><?php echo __("Verb"); ?></th>
					<th class="verb_id"><?php echo __("Verb ID"); ?></th>
					<th class="no_of_statements"><?php echo __("Number of Statements"); ?></th>
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
					$class = "statement-verb-".$counter;
					?>
					<tr class="<?php echo h($class); ?>"  data-id=".details-data">
						<td  class="sno">
							<span>
								<?php echo h($counter); ?>
							</span>
							<div style="display:none;">								
								<div class="details-data" style="display:none;">
									<div class="center  details-buttons">
										<a href="<?php echo h($activity_stream_report_url."?limit=10&verb_id=".urlencode($gbStatement['Statement']['verb_id'])); ?>" class="btn red" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {hide_table_columns_with_less_data(jQuery(id).find('table:first'));jQuery(id).find('[data-column=4],[data-column=3]').hide(); prepend_heading(id_this, id); }); return false;"><?php echo __("Last 10 Statements"); ?></a>
										<a href="<?php echo h($activities_report_url."?verb_id=".urlencode($gbStatement['Statement']['verb_id'])); ?>" class="btn green" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Activities"); ?></a>
										<a href="<?php echo h($agents_report_url."?verb_id=".urlencode($gbStatement['Statement']['verb_id'])); ?>" class="btn blue" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Users"); ?></a>
										<a href="<?php echo h($activities_report_url."?verb_id=".urlencode($gbStatement['Statement']['verb_id'])); ?>&object_is_parent=1" class="btn purple" onClick="show_only_siblings(this); copy_html_url('#report_table','.details-show',this, 2, function(id, id_this) {prepend_heading(id_this, id);  }); return false;"><?php echo __("Parent Level Activities"); ?></a>
									</div>
									<div class="details-show">
									</div>									
								</div>
							</div>

						</td>
						<td  class="verb" title="<?php 
							$params = "verb_id=".urlencode($gbStatement['Statement']['verb_id']);
							?>"
							><a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><?php echo h($gbStatement['Statement']['verb']); ?></a>&nbsp;
						</td>
						<td  class="verb_id" title="<?php
							$params = "verb_id=".urlencode($gbStatement['Statement']['verb_id']);
							?>"
							><a href="<?php echo h($activity_stream_report_url."?".$params); ?>"><?php echo h($gbStatement['Statement']['verb_id']); ?></a>&nbsp;
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
<?php echo $this->element("report_footer", array("disable_pagination" => true)); ?>