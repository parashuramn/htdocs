<?php
//	echo $this->Html->css('/app/webroot/css/pretty-json');
//	echo $this->Html->script('/app/webroot/js/underscore-min');	
//	echo $this->Html->script('/app/webroot/js/backbone-min');	
//	echo $this->Html->script('/app/webroot/js/pretty-json-min');	
	echo $this->Html->css('/app/webroot/assets/global/plugins/select2/select2');
	echo $this->Html->css('/app/webroot/assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min');
	echo $this->Html->css('/app/webroot/assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min');
	echo $this->Html->css('/app/webroot/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap');


?>
<div class="">
	<div class="portlet box gblue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-users" style=" font-size: 60px; "></i><?php echo __("Edit Group"); ?>
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse">
					</a>
				</div>
			</div>
			<div class="portlet-body form">
				<!-- BEGIN FORM-->
				<?php echo $this->Form->create('Group', array("class" => "form-horizontal", 'inputDefaults' => array('label' => false, 'div' => false))); 
					echo $this->Form->input('id');
				?>
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Name"); ?></label>
							<div class="col-md-4">
								<?php 
								if($this->request->data["Group"]["type"] == "Local")
								echo $this->Form->input('name', array( 'type' => 'text', "class" => "form-control", "placeholder" => __("Enter Name"))); 
								else
								echo "<label class='control-label'>".$this->request->data["Group"]["name"]."</label>";
								?>
								<span class="help-block">
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Type"); ?></label>
							<div class="col-md-4">
								<div class="input-icon">
									<?php
									if($this->request->data["Group"]["type"] == "Local")
									echo $this->Form->input('type', array('class'	=> 'form-control',
											'options' => array('Local' => 'Local', 'GrassBlade xAPI Companion' => 'GrassBlade xAPI Companion')
											));
									else
									echo "<label class='control-label'>".$this->request->data["Group"]["type"]."</label>";
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo __("Group Leaders"); ?></label>
							<div class="col-md-4">
								
									<?php
									if($this->request->data["Group"]["type"] == "Local")
									echo '<div class="input-icon">'.$this->Form->input('group_leaders', 
												array(	'class'	=> '',
														'options' => $users,
														'multiple' => 'multiple'
												)
											).'</div>';
									else
									{
										echo "<ul class='group_leaders_list'>";
										if(!empty($this->request->data["Group"]["group_leaders"]))
										foreach ($this->request->data["Group"]["group_leaders"] as $id => $group_leader) {
											echo "<li>".h($id.". ".$group_leader)."</li>";
										}
										echo "</ul>";
									}
									?>
							</div>
						</div>
					</div>
					<div class="form-actions fluid">
						<div class="row">
							<div class="col-md-offset-3 col-md-9"><?php 
								if($this->request->data["Group"]["type"] == "Local")
								echo $this->Form->button('Submit', array("class"	=> "btn gblue", "type"	=> "submit")); ?>
							</div>
						</div>
					</div>
				<?php echo $this->Form->end(); ?>
				<!-- END FORM-->
			</div>
		</div>
</div>





<!-- Begin: life time stats -->
<div class="portlet group_members">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-users"></i><?php echo __("Group Membership"); ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<div class="table-actions-wrapper">
				<span>
				</span>
				<select class="table-group-action-input form-control input-inline input-small input-sm">
					<option value=""><?php echo __("Select..."); ?></option>
					<option value="add"><?php echo __("Add to Group"); ?></option>
					<option value="remove"><?php echo __("Remove from Group"); ?></option>
				</select>
				<button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> <?php echo __("Submit"); ?></button>
			</div>
			<table class="table table-striped table-bordered table-hover" id="group_users_table">
			<thead>
			<tr role="row" class="heading">
				<th width="2%">
					<input type="checkbox" class="group-checkable">
				</th>
				<th width="5%">
					 <?php echo __("S.No."); ?>
				</th>
				<th width="15%">
					 <?php echo __("Name"); ?>
				</th>
				<th width="15%">
					 <?php echo __("Email or ID"); ?>
				</th>
				<th width="15%">
					 <?php echo __("Membership"); ?>
				</th>
				<th width="10%">
					 <?php echo __("Actions"); ?>
				</th>
			</tr>
			<tr role="row" class="filter">
				<td>
				</td>
				<td>
				</td>
				<td>
					<input type="text" class="form-control form-filter input-sm" name="agent_name">
				</td>
				<td>
					<input type="text" class="form-control form-filter input-sm" name="agent_id">
				</td>
			<td>
				<select name="membership_status" class="form-control form-filter input-sm">
					<option value=""><?php echo __("All"); ?></option>
					<option value="member" selected><?php echo __("Member"); ?></option>
					<option value="nonmember"><?php echo __("Non-Member"); ?></option>
				</select>
			</td>
				<td>
					<div class="margin-bottom-5">
						<button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> <?php echo __("Search"); ?></button>
						<button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> <?php echo __("Reset"); ?></button>
					</div>
					
				</td>
			</tr>
			</thead>
			<tbody>
			</tbody>
			</table>
		</div>
	</div>
</div>
<?php 		

echo $this->Html->script('/app/webroot/assets/global/plugins/select2/select2');
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/media/js/jquery.dataTables.min');
echo $this->Html->script('/app/webroot/js/table'); 
echo $this->Html->script('/app/webroot/js/md5'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap'); 
echo $this->Html->script('/app/webroot/assets/global/scripts/datatable'); 

?>
<script type="text/javascript">
	var group_id = "<?php echo $this->request->data['Group']['id']; ?>";
	jQuery(function() {
		TableAjax.init("#group_users_table", ajaxurl + "Groups/users/" + group_id);
		jQuery(".group_members .filter-submit").click();
	});
</script>