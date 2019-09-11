<div class="users form">
	<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-user" style=" font-size: 60px; "></i><?php echo __('Edit Manager'); ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse">
					</a>
				</div>
			</div>
			<div class="portlet-body form">
				<!-- BEGIN FORM-->
				<?php echo $this->Form->create('User', array("class" => "form-horizontal", 'inputDefaults' => array('label' => false, 'div' => false))); 
				echo $this->Form->input('id');
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
							<div class="col-md-4 <?php if(User::get("role") != "admin") echo 'control-label'; ?>">
								<div class="input-icon">
									<?php
									if(User::get("role") != "admin" ) 
									echo $user["User"]["role"];
									else
									echo $this->Form->input('role', array('class'	=> 'form-control',
											'options' => array('user' => 'User', 'admin' => 'Admin')
											));
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Permissions"); ?></label>
							<div class="col-md-4 <?php if(User::get("role") != "admin") echo 'control-label'; ?>">
								<?php 
								global $global_permissions;
								if(is_array($global_permissions)) { 
									if(User::get("role") == "admin") {
									?>
									<select name="data[User][permissions][]" multiple="multiple" style="width: 100%;height: 90px;">
										<?php
										foreach ($global_permissions as $key => $permission) {
											$user_role = $user["User"]["role"];
											if(empty($permission["roles"]) && $user_role == "admin" || !empty($permission["roles"]) && empty($permission["roles"][$user_role]))
												continue;

											$selected = in_array($key,  (array) $user["User"]["permissions"])? 'selected="selected"':'';
											?>
											<option value="<?php echo h($key); ?>" <?php echo h($selected); ?>><?php echo h($permission['label']); ?></option>
											<?php
										}
										?>
									</select>
									<?php
									}
									else
									{
										if(!empty($user["User"]["permissions"]))
										{
											$permissions = array();
											foreach ($user["User"]["permissions"] as $permission) {
												if(!empty( $global_permissions[$permission]))
												$permissions[] = $global_permissions[$permission]["label"];
											}
											echo implode(", ", $permissions);
										}
									}
								}
								?>
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
		<form method="post">
				<input type="Submit" name="data[UsersAuth][add_auth]" class="btn default green-stripe" value="Add New Basic AuthToken" />
		</form>
		<br>
			<div class="portlet box gblue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-key" style=" font-size: 35px; "></i><?php echo __('AuthToken List'); ?>
					</div>
					<div class="tools">
						<a href="javascript:;" class="collapse">
						</a>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="table-scrollable" style="margin: 0 !important">
						<form method="post">
						<table class="table table-striped table-bordered table-hover" style="margin: 0 !important">
							<thead>
							<tr>
									<th><?php echo __("S.No."); ?></th>
									<th><?php echo __("API User"); ?></th>
									<th><?php echo __("API Password"); ?></th>
									<th><?php echo __("Action"); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php 
							$sn = 1;
							if(!empty($user["UsersAuth"]))
							foreach($user["UsersAuth"] as $auth){
								echo "<tr>";
								echo "<td>".$sn++.". </td>";
								echo "<td>".h($auth["api_user"])."</td>";
								echo "<td>".h($auth["api_pass"])."</td>";
								echo "<td><a href='javascript:;' class='btn btn-sm default purple-stripe' onClick='jQuery(\"#api_user_selected\").html(\"".$auth["api_user"]."\");jQuery(\"#api_pass_selected\").html(\"".$auth["api_pass"]."\");highlight(\"#api_user_selected\");highlight(\"#api_pass_selected\"); return false;'>".__("Select")."</a>"." <input type='submit' value='".__("Delete")."' class='btn btn-sm default red-stripe' name='data[UsersAuth][delete][".$auth["id"]."]' /></td>";
								echo "</tr>";
							}
							?>
							</tbody>
						</table>
						</form>
					</div>
				</div>
			</div>
		
		<br><br>
		<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-key" style=" font-size: 35px; "></i><?php echo __('Authentication Details'); ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse">
					</a>
				</div>
			</div>
			<div class="portlet-body form">
				<div style="padding: 10px;">
					<?php echo __("These are the authentication details you would need to configure in");?> <a href="http://www.nextsoftwaresolutions.com/grassblade-xapi-companion/" target="_blank"><?php echo __("GrassBlade xAPI Companion</a> or any other xAPI LMS/Content."); ?>
				</div>
				<table class="table table-striped table-bordered table-advance table-hover" style="margin: 0 !important">
					<tr>
						<th>Endpoint:</th> <td><?php echo $this->Html->url(array("controller" => "xAPI", "action" => "index"), true); ?>/</td>
					</tr>
					<tr>
						<th><?php echo __("API User:"); ?></th><td id='api_user_selected'><?php echo __("Any API User authentication information from above table."); ?></td>
					</tr>
					<tr>
						<th><?php echo __("API Password:"); ?></th><td id='api_pass_selected'><?php echo __("Corresponding API Password from above table."); ?></td>
					</tr>
				</table>
			</div>
		</div>
</div>
