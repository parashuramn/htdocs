<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>GrassBlade LRS - <?php echo __("Login"); ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<?php
		echo $this->Html->css((isset($_SERVER['HTTPS']) ? "https://" : "http://").'fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all');
		echo $this->Html->css('/app/webroot/assets/global/plugins/font-awesome/css/font-awesome.min');
		echo $this->Html->css('/app/webroot/assets/global/plugins/simple-line-icons/simple-line-icons.min');
		//echo $this->Html->css('/app/webroot/assets/global/plugins/untitled-font-1/styles');
		echo $this->Html->css('/app/webroot/assets/global/plugins/bootstrap/css/bootstrap.min');
		echo $this->Html->css('/app/webroot/assets/global/plugins/uniform/css/uniform.default');
		echo $this->Html->css('/app/webroot/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min');
/*<!-- END GLOBAL MANDATORY STYLES -->*/
		echo $this->Html->css('/app/webroot/assets/global/plugins/select2/select2');
		echo $this->Html->css('/app/webroot/assets/admin/pages/css/login-soft.css');
/*
<!-- BEGIN THEME STYLES -->*/
		echo $this->Html->css('/app/webroot/assets/global/css/components');
		echo $this->Html->css('/app/webroot/assets/global/css/plugins');
		echo $this->Html->css('/app/webroot/assets/admin/layout/css/layout');
		echo $this->Html->css('/app/webroot/assets/admin/layout/css/themes/default');
		echo $this->Html->css('/app/webroot/css/custom');
/*<!-- END THEME STYLES -->*/
		echo $this->Html->meta('/app/webroot/icon');

		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery-1.11.0.min');
?>
	<script type="text/javascript">
		var ajaxurl = "<?php echo Router::url('/', true); ?>";
		var pageurl = "<?php echo $this->here; ?>";
	</script>
</head>
<body class="login">
<div class="logo">
	<?php echo $this->Html->link($this->Html->image('/app/webroot/img/logo.png'), array('controller' => 'Reports', 'action' => 'dashboard'), array('escape' => false, "class" => "logo-default")); ?>
</div>
<?php // <!-- BEGIN SIDEBAR TOGGLER BUTTON --> ?>
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<?php // <!-- BEGIN LOGIN --> ?>
<div class="content">
	<?php echo  $this->Session->flash(); ?>
	<?php echo $this->fetch('content'); ?>
</div>
<?php // <!-- END LOGIN --> ?>
<?php echo $this->element('footer'); ?>
<?php
		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery-migrate-1.2.1.min');
/*<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->*/
		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/bootstrap/js/bootstrap.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery.blockui.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery.cokie.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/uniform/jquery.uniform.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min');

		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery-validation/js/jquery.validate.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/backstretch/jquery.backstretch.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/select2/select2.min');

/*<!-- END CORE PLUGINS -->*/
		echo $this->Html->script('/app/webroot/assets/global/scripts/metronic');
		echo $this->Html->script('/app/webroot/assets/admin/layout/scripts/layout');
		echo $this->Html->script('/app/webroot/assets/admin/layout/scripts/quick-sidebar');
		echo $this->Html->script('/app/webroot/assets/admin/layout/scripts/demo');
		echo $this->Html->script('/app/webroot/assets/admin/pages/scripts/login-soft');
		echo $this->Html->script('/app/webroot/js/grassblade');

		?>
<script>
jQuery(document).ready(function() {     
	  	Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		QuickSidebar.init(); // init quick sidebar
		Demo.init(); // init demo features
	  	Login.init();
       // init background slide images
       $.backstretch([
        ajaxurl + "app/webroot/img/bg/1.jpg",
        ajaxurl + "app/webroot/img/bg/2.jpg",
        ajaxurl + "app/webroot/img/bg/3.jpg",
        ajaxurl + "app/webroot/img/bg/4.jpg"
        ], {
          fade: 1000,
          duration: 8000
    }
    );
});
</script>
</body>
</html>