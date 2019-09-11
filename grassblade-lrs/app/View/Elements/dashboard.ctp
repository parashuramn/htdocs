<?php
	$more_style = !empty($url)? "":"visibility:hidden";
	$url = !empty($url)? $url:"#";

	$value = intval($value);

	if($this->layout == "ajax") {
		$return = array(
				"url"	=> $url,
				"value"	=> $value,
				"desc"	=> $desc,
			);
		echo json_encode($return);
	}
	else
	{
		if(empty($value))
			$value = "-"
?>
	<div id="dashboard-<?php echo @$type; ?>" class="dashboard-stat <?php echo !empty($color)? $color:"blue"; ?>">
		<div class="visual">
			<i class="fa <?php echo !empty($icon)? $icon:""; ?>"></i>
		</div>
		<div class="details">
			<div class="number">
				 <?php echo $value; ?>
			</div>
			<div class="desc">
				<?php echo $desc; ?>
			</div>
		</div>
		<a class="more" href="<?php echo $url; ?>" style="<?php echo $more_style; ?>">
			 More <i class="m-icon-swapright m-icon-white"></i>
		</a>
	</div>
	<script type="text/javascript">
		jQuery(window).load(function() {
			var type = "<?php echo @$type; ?>";
		    show_dashboard_data(type);
		});
	</script>
<?php
	}