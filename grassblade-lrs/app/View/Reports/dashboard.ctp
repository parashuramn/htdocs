<?php
	//	echo $this->Html->css('/app/webroot/css/bootstrap.min');
	//	echo $this->Html->css('/app/webroot/css/style1');
	//	echo $this->Html->css('/app/webroot/css/style2');


?>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php echo $this->element('dashboard-chart', array('color' => 'red')); ?>
			</div>
		</div>
		<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-agents', array('color' => 'blue')); ?>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-allactivities', array('color' => 'green')); ?>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-statements', array('color' => 'red')); ?>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-verbs', array('color' => 'yellow')); ?>
				</div>
		</div>

		<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-groupactivities', array('color' => 'red')); ?>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-parentactivities', array('color' => 'yellow')); ?>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-users', array('color' => 'blue')); ?>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<?php echo $this->element('dashboard-triggers', array('color' => 'green')); ?>
				</div>
		</div>
		 <?php
                modified("dashboard_gadgets", '', $this);



