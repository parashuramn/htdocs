<?php
global $gbdb;
if(!empty($gbdb)) {
	if(isset($_POST["load_from"]))
		if($_POST["load_from"] == "grassblade")
			echo $gbdb->loadxmls();
		else if(!empty($_POST['urls']) && $_POST["load_from"] == "url")
			echo $gbdb->loadxmls($_POST['urls']);
}
?>
<div class="portlet box gblue">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-language" style=" font-size: 35px; "></i><?php echo __('Load ID Translations'); ?>
		</div>
		<div class="tools">
			<a href="javascript:;" class="collapse">
			</a>
		</div>
	</div>
	<div class="portlet-body form">
		<div style="padding: 20px">
			<form method="POST">
				<b><?php echo __("Option 1:"); ?></b><br>
				<?php echo __("Load from tincan.xml URL:"); ?>
				<br>
				<input type="hidden" name="load_from" value="url">
				<input type="text" name="urls[]"/>
				<input type="submit"  name="submit" value="Load" class="btn btn-xs default green-stripe" /> 
			</form>
			<br><br>
			<b><?php echo __("Option 2:"); ?></b><br>
			<?php echo __("Load automatically from"); ?> <a href="http://www.nextsoftwaresolutions.com/grassblade-xapi-companion/"><?php echo __("GrassBlade xAPI Companion"); ?></a>:
			<form method="POST">
				<input type="hidden" name="load_from" value="grassblade">
				<input type="submit" name="submit" value="Load" class="btn btn-xs  default green-stripe"/> 
			</form>
			<br>
			<?php echo __("GrassBlade LRS should be installed on the same database as your WordPress instance for Option 2 to work."); ?>
		</div>
	</div>
</div>