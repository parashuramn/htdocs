<?php
global $license;
?>
	<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-key" style=" font-size: 35px; "></i><?php echo __('License'); ?>
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
							<label class="col-md-3 control-label"><?php echo __('License Domain'); ?></label>
							<div class="col-md-4">
								<?php 
								echo $this->Form->input('license_domain', array("disabled" => "disabled", "value" => $domain, "class" => "form-control"));
								?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('License Email'); ?></label>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">
									<i class="fa fa-envelope"></i>
									</span>
									<?php 
									echo $this->Form->input('license_email',array('default' => $license["email"], "class" => "form-control"));
									 ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __('License Key'); ?></label>
							<div class="col-md-4">
								<div class="input-group">
									<?php 
									echo $this->Form->input('license_key', array('placeholder' => empty($license['key'])? '':'*******************', "class" => "form-control",));
									?>						
									<span class="input-group-addon">
									<i class="fa fa-key"></i>
									</span>
								</div>
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