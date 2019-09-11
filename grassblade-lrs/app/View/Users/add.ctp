<div class="">
	<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-user" style=" font-size: 60px; "></i><?php echo __("Add Manager"); ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse">
					</a>
				</div>
			</div>
			<div class="portlet-body form">
				<!-- BEGIN FORM-->
				<?php echo $this->Form->create('User', array("class" => "form-horizontal", 'inputDefaults' => array('label' => false, 'div' => false))); 
				//echo $this->Form->input('id');
				?>
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Name"); ?></label>
							<div class="col-md-4">
								<?php echo $this->Form->input('name', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("Enter Name"))); ?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Email Address"); ?></label>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">
									<i class="fa fa-envelope"></i>
									</span>
									<?php echo $this->Form->input('email', array( 'type' => 'email', "class" => "form-control", "placeholder" => __("Email Address"))); ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Password"); ?></label>
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
							<label class="col-md-3 control-label"><?php echo __("Role"); ?></label>
							<div class="col-md-4">
								<div class="input-icon">
									<?php
									echo $this->Form->input('role', array('class'	=> 'form-control',
											'options' => array('user' => 'User', 'admin' => 'Admin')
											));
									?>
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
</div>