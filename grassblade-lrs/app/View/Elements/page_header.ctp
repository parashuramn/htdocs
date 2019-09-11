<?php
	$action = ($this->request->params["action"] != "index")? ucwords(__($this->request->params["action"]))." ":"";
	$controller = ucwords(__($this->request->params["controller"]));

	$controller_name = str_replace("Users", __("Managers"), $controller);
	$controller_name = str_replace("User", 	__("Manager"), $controller_name);

	$action_name = str_replace("Agents", 	__("Learners"), $action);
	$action_name = str_replace("Agent", 	__("Learner"), $action_name);

if(empty($page_title))
{
	$page_title = ($action_name.$controller_name);
}
if(empty($page_sub_title))
$page_sub_title = "";
?>
<h3 class="page-title">
<?php echo h($page_title); ?><small><?php echo h($page_sub_title); ?></small>
</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo Router::url('/', true); ?>"><?php echo __("Home"); ?></a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo Router::url(array("controller" => $controller, "action" => "index")); ?>"><?php echo h($controller_name); ?></a>
		<?php if(!empty($action)) { ?>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo Router::url(array("controller" => $controller, "action" => $action)); ?>"><?php echo h($action_name); ?></a>
		<?php } ?>
		</li>
	</ul>
	<div class="page-toolbar">
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
			<?php echo __("Actions"); ?> <i class="fa fa-angle-down"></i>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<li>
					<a href="#help-popup" data-toggle="modal"><?php echo __("Help"); ?></a>
				</li>
			</ul>
		</div>
	</div>
</div>
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<div class="modal fade" id="help-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
							<h4 class="modal-title"><?php echo __("About this page"); ?></h4>
						</div>
						<div class="modal-body">
							 <?php echo $this->element('help'); ?>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn default" data-dismiss="modal"><?php echo __("Close"); ?></button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->