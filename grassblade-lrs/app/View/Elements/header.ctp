<?php /* <!-- BEGIN HEADER --> */ ?>
<div class="page-header navbar navbar-fixed-top">
	<?php /* <!-- BEGIN HEADER INNER --> */ ?>
	<div class="page-header-inner">
		<?php /* <!-- BEGIN LOGO --> */ ?>
		<div class="page-logo">
			<?php echo $this->Html->link($this->Html->image('/app/webroot/img/logo.png'), array('controller' => 'Reports', 'action' => 'dashboard'), array('escape' => false, "class" => "logo-default")); ?>

			<div class="menu-toggler sidebar-toggler hide">
				<?php /* <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header --> */ ?>
			</div>
		</div>
		<?php /* <!-- END LOGO --> */ ?>
		<?php /* <!-- BEGIN RESPONSIVE MENU TOGGLER --> */ ?>
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<?php /* <!-- END RESPONSIVE MENU TOGGLER --> */ ?>
		<?php /* <!-- BEGIN TOP NAVIGATION MENU --> */ ?>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<?php /* <!-- BEGIN USER LOGIN DROPDOWN --> */ ?>
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<?php $email = User::get("email");
								$hash = md5(strtolower(trim($email))); ?>
					<img  class="img-circle"  src="//www.gravatar.com/avatar/<?php echo h($hash); ?>?&s=50&d=mm"/>
					<span class="username username-hide-on-mobile">
					<?php echo h(User::get("name")); ?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a href="<?php echo Router::url(array('controller' => 'Users', 'action' => 'edit', User::get("id"))); ?>">
							<i class="icon-user"></i> <?php echo __("My Profile"); ?> </a>
						</li>
						<li class="divider">
						</li>
						<li>
							<a href="<?php echo Router::url(array('controller' => 'Users', 'action' => 'logout')); ?>">
							<i class="icon-key"></i> <?php echo __("Log Out"); ?> </a>
						</li>
					</ul>
				</li>
				<?php /* <!-- END USER LOGIN DROPDOWN --> */ ?>
			</ul>
		</div>
		<?php /* <!-- END TOP NAVIGATION MENU --> */ ?>
	</div>
	<?php /* <!-- END HEADER INNER --> */ ?>
</div>
