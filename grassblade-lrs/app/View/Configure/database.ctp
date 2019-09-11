<?php
if(defined("GBDB_DATABASE_CONNECT_ERROR")) {
if(file_exists(DATABASE_FILE)) {
	echo __("Please check your database configuration.");
}
else
{
if(empty($database_file_content)) {
?>
	<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-key"></i><?php echo __('Database Configuration'); ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse">
					</a>
					<a href="#portlet-config" data-toggle="modal" class="config">
					</a>
					<a href="javascript:;" class="reload">
					</a>
					<a href="javascript:;" class="remove">
					</a>
				</div>
			</div>
			<div class="portlet-body form">
				<!-- BEGIN FORM-->
				<?php echo $this->Form->create('Configure', array("class" => "form-horizontal", 'inputDefaults' => array('label' => false, 'div' => false))); 
				?>
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('Host'); ?></label>
							<div class="col-md-4">
								<?php 
								echo $this->Form->input('host', array("default" => "localhost", "class" => "form-control"));
								?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('Username'); ?></label>
							<div class="col-md-4">
								<?php 
								echo $this->Form->input('username', array("class" => "form-control"));
								?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('Password'); ?></label>
							<div class="col-md-4">
								<div class="input-group">
									<?php echo $this->Form->input('password', array( 'type' => 'password', "class" => "form-control", "placeholder" => __("Password"))); ?>						
									<span class="input-group-addon">
									<i class="fa fa-key"></i>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('Database Name'); ?></label>
							<div class="col-md-4">
								<?php 
								echo $this->Form->input('database_name', array("class" => "form-control"));
								?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('Database Prefix'); ?></label>
							<div class="col-md-4">
								<?php 
								echo $this->Form->input('database_prefix', array("default" => "wp_", "class" => "form-control"));
								?>
								<span class="help-block">
									<?php echo __("If you are using LRS along with wordpress, we recommend using the same database configuration as wordpress for a better integration."); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-actions fluid">
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<?php echo $this->Form->button('Submit', array("class"	=> "btn gblue", "type"	=> "submit")); ?>
							</div>
						</div>
					</div>
				<?php echo $this->Form->end(); ?>
				<!-- END FORM-->
			</div>
		</div>
<?php
}
else
{
	?>
<h2><?php echo __('Database Configuration'); ?></h2>
<p><?php echo sprintf(__("Please create or replace the file (%s) with following code:"), "<u>".DATABASE_FILE."</u>"); ?><br><br><br>
<textarea style="min-width:400px; width: 60%;height: 300px;">
<?php echo $database_file_content; ?>
</textarea>

</p>
	<?php
}


}
}
