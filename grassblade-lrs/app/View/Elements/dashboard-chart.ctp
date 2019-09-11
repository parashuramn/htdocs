<?php
		echo $this->Html->script('/app/webroot/js/jquery.flot');
		echo $this->Html->script('/app/webroot/js/jquery.flot.time');
		echo $this->Html->script('/app/webroot/js/jquery.flot.resize');
		echo $this->Html->script('/app/webroot/js/charts');
?>
<div id="dashboard_chart_portlet" class="portlet box <?php echo $color; ?>">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-bar-chart-o" style=" font-size: 35px; "></i> <?php echo sprintf(__("%s Day Summary"), '<span id="dashboard_chart_daycount">--</span>'); ?>
		</div>
		<div class="tools">
			<a href="javascript:;"  data-placement="top" data-original-title="Minimize" class="tooltips expand minimize">
			</a>
		</div>
		<div class="page-toolbar">
			<div id="reportrange" class="pull-right tooltips btn btn-fit-height green-salt" data-placement="top" data-original-title="Select the Date Range">
				<i class="fa fa-calendar"></i>&nbsp; <span class="thin uppercase visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
			</div>
		</div>

	</div>
	<div class="portlet-body" style="display:none;">
		<div id="dashboard_chart" class="chart">
		</div>
	</div>
</div>
<script>
	jQuery(window).load(function() {
		databoard_chart();
	});
</script>
