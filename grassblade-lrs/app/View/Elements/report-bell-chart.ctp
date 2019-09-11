<?php
if(!empty($conditions["objectid"]) && (is_string($conditions["objectid"]) || count($conditions["objectid"]) == 1)) {
	$agent_ids = @$conditions["agent_id"];
	if(is_string($agent_ids))
		$agent_ids = array($agent_ids);
	if(empty($agent_ids))
		$agent_ids = array();

	if(is_array($conditions["objectid"]))
		$objectid = $conditions["objectid"][0];
	else
		$objectid = $conditions["objectid"];
		?>
		<div id="dashboard_bell_chart_portlet" class="portlet box <?php echo h($color); ?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-bar-chart-o" style=" font-size: 35px; "></i> <?php echo __("Score Distribution Chart"); ?>
				</div>
				<div class="tools">
					<a href="javascript:;"  data-placement="top" data-original-title="Minimize" class="tooltips expand minimize">
					</a>
				</div>
				<div class="page-toolbar">
					<div class="btn-group pull-right score_options">
						<button type="button" class="btn btn-fit-height ggreen dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
						<?php echo __("Score Options"); ?> <i class="fa fa-angle-down"></i>
						</button>
						<ul class="dropdown-menu pull-right" role="menu">
							<li>
								<a href="#" onClick="return plot_report_bell_chart_loader(this, 'first');" data-toggle="modal"><?php echo __("First Attempt"); ?></a>
							</li>
							<li>
								<a href="#" onClick="return plot_report_bell_chart_loader(this, 'max');"  data-toggle="modal"><?php echo __("Maximum Score"); ?></a>
							</li>
							<li>
								<a href="#" onClick="return plot_report_bell_chart_loader(this, 'min');"  data-toggle="modal"><?php echo __("Minimum Score"); ?></a>
							</li>
							<li>
								<a href="#" onClick="return plot_report_bell_chart_loader(this, 'avg');"  data-toggle="modal"><?php echo __("Average Score"); ?></a>
							</li>
						</ul>
					</div>
				</div>

			</div>
			<div class="portlet-body" style="display:none;">
				<div id="dashboard_bell_chart" class="chart">
					<div class="not_enough_data" style="display:none;"><?php echo __("Not enough data to generate chart."); ?></div>
				</div>
			</div>
		</div>
		<script>
			function plot_report_bell_chart_loader(context, score_type) {
				jQuery("#dashboard_bell_chart_portlet .score_options button").html(jQuery(context).html() + ' <i class="fa fa-angle-down">');
				plot_report_bell_chart_load("#dashboard_bell_chart", score_type, "<?php echo $objectid; ?>", <?php  echo json_encode($agent_ids); ?> );
			}
			jQuery(window).load(function() {
				jQuery("#dashboard_bell_chart_portlet .score_options ul li:first a").click();
			});
		</script>
		<?php
}
