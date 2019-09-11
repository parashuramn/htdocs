	<!-- BEGIN LOGIN FORM -->
	<?php echo $this->Form->create('User', array("class" => "login-form", 'inputDefaults' => array('label' => false, 'div' => false))); ?>
		<h3 class="form-title"><?php echo __("Login to your account"); ?></h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			<?php echo __("Enter any email and password."); ?></span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9"><?php echo __("Email"); ?></label>
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<?php echo $this->Form->input('email', array( 'type' => 'email', "class" => "form-control placeholder-no-fix", "placeholder" => __("Email Address"))); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9"><?php echo __("Password"); ?></label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<?php echo $this->Form->input('password', array( 'type' => 'password', "class" => "form-control placeholder-no-fix", "placeholder" => __("Password"), "autocomplete" => "off")); ?>						
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox"></label>
			<input type="hidden" name="data[User][return]" value="<?php echo $return; ?>" />
			<?php echo $this->Form->button(__('Login').' <i class="m-icon-swapright m-icon-white"></i>', array("class"	=> "btn blue pull-right", "type"	=> "submit")); ?>
		</div>
		<?php /*
		<div class="forget-password">
			<h4>Forgot your password ?</h4>
			<p>
				 no worries, click <a href="javascript:;" id="forget-password">
				here </a>
				to reset your password.
			</p>
		</div>
		*/ ?>
	<?php echo $this->Form->end(); ?>
	<!-- END LOGIN FORM -->
	<?php /*
	<!-- BEGIN FORGOT PASSWORD FORM -->
	<form class="forget-form" action="index.html" method="post">
		<h3>Forget Password ?</h3>
		<p>
			 Enter your e-mail address below to reset your password.
		</p>
		<div class="form-group">
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
			</div>
		</div>
		<div class="form-actions">
			<button type="button" id="back-btn" class="btn">
			<i class="m-icon-swapleft"></i> Back </button>
			<button type="submit" class="btn blue pull-right">
			Submit <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>
	<!-- END FORGOT PASSWORD FORM -->
	*/ ?>