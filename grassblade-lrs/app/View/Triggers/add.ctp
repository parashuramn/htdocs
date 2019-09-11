<div class="triggers form">
	<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-external-link" style=" font-size: 35px; "></i><?php echo __("Add Trigger"); ?>
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
				<?php echo $this->Form->create('Trigger', array("class" => "form-horizontal", 'inputDefaults' => array('label' => false, 'div' => false))); 
			//	echo $this->Form->input('id');
				?>
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Name"); ?></label>
							<div class="col-md-4">
								<?php echo $this->Form->input('name', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("Enter Trigger Name"))); ?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Type"); ?></label>
							<div class="col-md-4">
								<div class="input-group">
									<?php echo $this->Form->input('type', array("class" => "form-control",
									'options' => array(
													'completion'	=> 'completion',
													'post_to_url_post' => 'POST to URL (POST)',
													'post_to_url_get' => 'POST to URL (GET)'
												)
									)); ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("URL"); ?></label>
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-link"></i>
									</span>
									<?php echo $this->Form->input('url', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("Enter URL"))); ?>
								</div>
									<span class="help-block">
										<?php echo __("URL of the website or script where the statement is sent on trigger."); ?>
									</span>

							</div>
						</div>
						<h3 class="form-section"><?php echo __("Trigger Criterion"); ?></h3>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Verb"); ?></label>
							<div class="col-md-4">
								<div class="input-icon">
									<?php
									echo $this->Form->input('verb_id', array("class" => "form-control",
										'options' => $verbs,
										'default' => "http://adlnet.gov/expapi/verbs/passed",
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