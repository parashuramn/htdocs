<div class="gbStatements view">
<h2><?php  echo __('Gb Statement'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Statement Id'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['statement_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Agent Name'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['agent_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Agent Mbox'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['agent_mbox']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User Id'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['user_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Version'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['version']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Verb Id'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['verb_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Verb'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['verb']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Objectid'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['objectid']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Object ObjectType'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['object_objectType']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Object Definition Type'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['object_definition_type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Object Definition Name'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['object_definition_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Object Definition Description'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['object_definition_description']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Stored'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['stored']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Timestamp'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['timestamp']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Score Raw'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_score_raw']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Score Scaled'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_score_scaled']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Score Min'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_score_min']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Score Max'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_score_max']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Completion'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_completion']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Success'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_success']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result Duration'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['result_duration']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Parent Ids'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['parent_ids']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Grouping Ids'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['grouping_ids']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Statement'); ?></dt>
		<dd>
			<?php echo h($gbStatement['Statement']['statement']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Gb Statement'), array('action' => 'edit', $gbStatement['Statement']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Gb Statement'), array('action' => 'delete', $gbStatement['Statement']['id']), null, __('Are you sure you want to delete # %s?', $gbStatement['Statement']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Gb Statements'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Gb Statement'), array('action' => 'add')); ?> </li>
	</ul>
</div>
