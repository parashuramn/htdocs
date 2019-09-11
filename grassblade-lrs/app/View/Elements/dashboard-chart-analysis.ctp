<?php
		echo $this->Html->script('/app/webroot/js/jquery.flot');
		echo $this->Html->script('/app/webroot/js/jquery.flot.time');
		echo $this->Html->script('/app/webroot/js/jquery.flot.resize');
		echo $this->Html->script('/app/webroot/js/charts');
?>
<div id="dashboard_chart_portlet" class="portlet box <?php echo $color; ?>">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-bar-chart-o" style=" font-size: 35px; "></i> <?php echo "Characteristic Curve"; ?>
		</div>
		<div class="tools">
			<a href="javascript:;"  data-placement="top" data-original-title="Minimize" class="tooltips expand minimize">
			</a>
		</div>
		<!-- <div class="page-toolbar">
			<div id="reportrange" class="pull-right tooltips btn btn-fit-height green-salt" data-placement="top" data-original-title="Select the Date Range">
				<i class="fa fa-calendar"></i>&nbsp; <span class="thin uppercase visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
			</div>
		</div> -->

	</div>
	<div class="portlet-body" style="display:none;">
		<div id="dashboard_characteristic_curve" class="chart">
		</div>
        <div id="dashboard_characteristic_curve_legend">
        </div>
	</div>
</div>
<script>
	jQuery(window).load(function() {
		var objectid = '<?php echo $objectid;?>';
		characteristic_curve(objectid);
	});
</script>
<style type="text/css">
	.chart {
			width: 100%;
	font-size: 14px;
	line-height: 1.2em;
	}
</style>