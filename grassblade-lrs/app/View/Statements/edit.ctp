<div class="gbStatements form">
<?php echo $this->Form->create('Statement'); ?>
	<fieldset>
		<legend><?php echo __('Edit Statement'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('statement_id');
		echo $this->Form->input('agent_name');
		echo $this->Form->input('agent_mbox');
		echo $this->Form->input('user_id');
		echo $this->Form->input('version');
		echo $this->Form->input('verb_id');
		echo $this->Form->input('verb');
		echo $this->Form->input('objectid');
		echo $this->Form->input('object_objectType');
		echo $this->Form->input('object_definition_type');
		echo $this->Form->input('object_definition_name');
		echo $this->Form->input('object_definition_description');
		echo $this->Form->input('stored');
		echo $this->Form->input('timestamp');
		echo $this->Form->input('result_score_raw');
		echo $this->Form->input('result_score_scaled');
		echo $this->Form->input('result_score_min');
		echo $this->Form->input('result_score_max');
		echo $this->Form->input('result_completion');
		echo $this->Form->input('result_success');
		echo $this->Form->input('result_duration');
		echo $this->Form->input('parent_ids');
		echo $this->Form->input('grouping_ids');
		echo $this->Form->input('statement');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Statement.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Statement.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Gb Statements'), array('action' => 'index')); ?></li>
	</ul>
</div>
