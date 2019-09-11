<?php
echo $this->Form->create('Integrations', array("class" => "form-horizontal", 'inputDefaults' => array('label' => false, 'div' => false))); 

?>
<div id="content" class="row">
	<div class="col-md-12">
		<div class="tabbable tabbable-custom boxless tabbable-reversed">
			<ul class="nav nav-tabs">
				<li class="<?php echo ($select == "gb_xapi")? "active":"";?>">
					<a href="#tab_0" data-toggle="tab">
					<i class="fa fa-wordpress"></i>
					<?php echo __("WordPress"); ?> </a>
				</li>
				<li class="<?php echo ($select == "email")? "active":"";?>">
					<a href="#tab_1" data-toggle="tab">
					<i class="fa fa-envelope"></i>
					<?php echo __("Email"); ?> </a>
				</li>
				<li class="<?php echo ($select == "sso")? "active":"";?>">
					<a href="#tab_2" data-toggle="tab">
					<i class="fa fa-key"></i>
					<?php echo __("SSO"); ?> </a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane <?php echo ($select == "gb_xapi")? "active":"";?>" id="tab_0">
					<div class="portlet box gblue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-wordpress" style=" font-size: 60px; "></i>GrassBlade xAPI Companion
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("WordPress URL"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-link"></i>
												</span>
												<?php echo $this->Form->input('Integrations.gb_xapi.url', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("WordPress URL"))); ?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("User"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-user"></i>
												</span>
												<?php echo $this->Form->input('Integrations.gb_xapi.user', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("User"))); ?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("Password"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<?php echo $this->Form->input('Integrations.gb_xapi.pass', array( 'type' => 'password', "class" => "form-control", "placeholder" => __("Password"))); ?>						
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
											<?php echo $this->Form->button('Submit', array("class"	=> "btn gblue", "type"	=> "submit","name" => "select", "value" => "gb_xapi")); ?>
											<?php if(!empty($this->request->data["Integrations"]["gb_xapi"]["url"])) { ?>
											<a href="?test=gb_xapi" class="btn yellow config_integration_test" onClick="copy_html_url(undefined,'.gb_xapi_test_result',this, 1); return false;"><?php echo __("Test"); ?></a>
											<span class="config_integration_test gb_xapi_test_result"></span>
											<?php } ?>
										</div>
									</div>
								</div>
							
						</div>
					</div><!-- END ITEM -->
				</div>
				<div class="tab-pane <?php echo ($select == "email")? "active":"";?>" id="tab_1">
					<div class="portlet box gblue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-envelope" style=" font-size: 45px; "></i><?php echo __("Email SMTP"); ?>
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("Default From Email"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-envelope"></i>
												</span>
												<?php echo $this->Form->input('Integrations.email.from', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("Email"))); ?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("Server"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-link"></i>
												</span>
												<?php echo $this->Form->input('Integrations.email.server', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("Server"))); ?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("Port"); ?></label>
										<div class="col-md-4">												
												<?php echo $this->Form->input('Integrations.email.port', array('default' => 25, 'type' => 'text', "class" => "form-control")); ?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("User"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-user"></i>
												</span>
												<?php echo $this->Form->input('Integrations.email.user', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("User"))); ?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("Password"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<?php echo $this->Form->input('Integrations.email.pass', array( 'type' => 'password', "class" => "form-control", "placeholder" => __("Password"))); ?>						
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
											<?php echo $this->Form->button('Submit', array("class"	=> "btn gblue", "type"	=> "submit","name" => "select", "value" => "email")); ?>
										</div>
									</div>
								</div>
							
						</div>
					</div><!-- END ITEM -->
				</div>
				<div class="tab-pane <?php echo ($select == "sso")? "active":"";?>" id="tab_2">
					<div class="portlet box gblue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-key" style=" font-size: 45px; "></i><?php echo __("SSO"); ?>
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-3 control-label"><?php echo __("IP/Domain"); ?></label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">
												<i class="fa fa-envelope"></i>
												</span>
												<?php echo $this->Form->input('Integrations.sso.host', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("IP/Domain"))); ?>
											</div>
											<br>
											<small><?php echo __("IP or Domain of the site from where users will SSO to the LRS"); ?></small>
										</div>
									</div>
								</div>
								<div class="form-actions fluid">
									<div class="row">
										<div class="col-md-offset-3 col-md-9">
											<?php echo $this->Form->button('Submit', array("class"	=> "btn gblue", "type"	=> "submit","name" => "select", "value" => "sso")); ?>
										</div>
									</div>
								</div>
							
						</div>
					</div><!-- END ITEM -->
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>
