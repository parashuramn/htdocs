<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>GrassBlade LRS</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<?php
//<!-- BEGIN GLOBAL MANDATORY STYLES -->
		echo $this->Html->css('//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all');
		echo $this->Html->css('/app/webroot/assets/global/plugins/font-awesome/css/font-awesome.min');
		echo $this->Html->css('/app/webroot/assets/global/plugins/simple-line-icons/simple-line-icons.min');
		//echo $this->Html->css('/app/webroot/assets/global/plugins/untitled-font-1/styles');
		echo $this->Html->css('/app/webroot/assets/global/plugins/bootstrap/css/bootstrap.min');
		echo $this->Html->css('/app/webroot/assets/global/plugins/uniform/css/uniform.default');
		echo $this->Html->css('/app/webroot/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min');
/*<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->*/
		echo $this->Html->css('/app/webroot/assets/global/css/components');
		echo $this->Html->css('/app/webroot/assets/global/css/plugins');
		echo $this->Html->css('/app/webroot/assets/admin/layout/css/layout');
		echo $this->Html->css('/app/webroot/assets/admin/layout/css/themes/default');
		echo $this->Html->css('/app/webroot/css/custom');
/*<!-- END THEME STYLES -->*/
		echo $this->Html->meta('/app/webroot/icon');

		echo $this->Html->script('/app/webroot/assets/global/plugins/jquery-1.11.0.min');
		$controller = strtolower($this->request->params["controller"]);
		$action = strtolower($this->request->params["action"]);
		$id = implode("_", $this->request->params["pass"]);

		$body_class = array(
				"page-header-fixed",
				"page-quick-sidebar-over-content",
				"page-header-fixed-mobile",
				"page-footer-fixed1",
				"controller-".$controller,
				"action-".$action,
				$action."_".$controller,
			);
		if(!empty($id))
		{
			$body_class[] =	$action."_".$controller."_".$id;
			$body_class[] =	"id_".$id;
		}
?>
	<script type="text/javascript">
		var ajaxurl = "<?php echo Router::url('/', true); ?>";
		var pageurl = "<?php echo $this->here; ?>";
	</script>
</head>

<body class="<?php echo implode(" ", modified("body_class", $body_class, $this)); ?>">

<?php echo $this->element('header'); ?>

<div class="clearfix">
</div>

<div class="page-container">

	<div class="page-sidebar-wrapper">

		<div class="page-sidebar navbar-collapse collapse">

			<?php echo $this->element('sidebar_menu'); ?>

		</div>
	</div>

	<div class="page-content-wrapper">
		<div class="page-content">

			<?php echo $this->element('page_header'); ?>

			<div class="row">
				<div class="col-md-12">
					 	<?php  echo $this->Session->flash(); ?>
						<?php echo $this->fetch('content');  ?>
				</div>
			</div>

		</div>
	</div>

	<?php echo $this->element('filter_sidebar'); ?>
</div>
<?php echo $this->element('footer'); ?>
<!--[if lt IE 9]>
<?php	echo $this->Html->css('/app/webroot/assets/global/plugins/respond.min'); ?>
<?php	echo $this->Html->css('/app/webroot/assets/global/plugins/excanvas.min'); ?>
<![endif]-->
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
/*<!-- END CORE PLUGINS -->*/
		echo $this->Html->script('/app/webroot/assets/global/plugins/bootstrap-daterangepicker/moment.min');
		echo $this->Html->script('/app/webroot/assets/global/plugins/bootstrap-daterangepicker/daterangepicker');
		echo $this->Html->css('/app/webroot/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3');
		
		echo $this->Html->script('/app/webroot/assets/global/scripts/metronic');
		echo $this->Html->script('/app/webroot/assets/admin/layout/scripts/layout');
		echo $this->Html->script('/app/webroot/assets/admin/layout/scripts/quick-sidebar');
		echo $this->Html->script('/app/webroot/assets/admin/layout/scripts/demo');
		echo $this->Html->script('/app/webroot/js/grassblade');

		?>
<script>
      jQuery(document).ready(function() {    
      		Metronic.setAssetsPath(ajaxurl + "app/webroot/assets/");
      		Metronic.setGlobalImgPath("global/img/");
	        Metronic.init(); // init metronic core components
			Layout.init(); // init current layout
			QuickSidebar.init(); // init quick sidebar
			Demo.init(); // init demo features
      });
   </script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
