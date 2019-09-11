<div>
	<div>
	<?php echo $this->Html->link(__('Add New'), 
						array('action' => 'add'), array('class' => 'btn default green-stripe', 'escape' => false) ); ?>
	</div>
	<br>
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-external-link" style=" font-size: 35px; "></i><?php echo __('Triggers'); ?>
			</div>
			<div class="tools">
				<a href="javascript:;" class="collapse">
				</a>
			</div>
		</div>
		<div class="portlet-body form">
			<div class="table-scrollable" style="margin: 0 !important">
				<table class="table table-striped table-bordered table-hover">
				<thead>
				<tr>
						<th><?php echo $this->Paginator->sort('id'); ?></th>
						<th><?php echo $this->Paginator->sort('name'); ?></th>
						<th><?php echo $this->Paginator->sort('type'); ?></th>
						<th><?php echo $this->Paginator->sort('status'); ?></th>
						<th class="actions"><?php echo __('Actions'); ?></th>
				</tr>
				<?php foreach ($triggers as $trigger): ?>
				<tr>
					<td><?php echo h($trigger['Trigger']['id']); ?>&nbsp;</td>
					<td><?php echo h($trigger['Trigger']['name']); ?>&nbsp;</td>
					<td><?php echo h($trigger['Trigger']['type']); ?>&nbsp;</td>
					<td><?php $active = (!empty($trigger['Trigger']['status']))?  "checked":""; ?>&nbsp;

						<input type="checkbox" name="data[Trigger][status]" value="1" class="make-switch filter-group-level" disabled <?php echo h($active); ?> data-size="small" data-on-color="success" data-on-text="<?php echo __("ON"); ?>" data-off-color="default" data-off-text="<?php echo __("OFF"); ?>">

					</td>
					<td class="">
						<?php 
						$edit = '<i class="fa fa-edit"></i> '.__("Edit");
						$delete = '<i class="fa fa-times"></i> '.__("Delete");
						echo $this->Html->link($edit, array('action' => 'edit', $trigger['Trigger']['id']), array('class' => 'btn btn-xs default green-stripe', 'escape' => false) ); ?>
						<?php echo $this->Form->postLink( $delete, array('action' => 'delete', $trigger['Trigger']['id']), array('class' => 'btn btn-xs default red-stripe', 'escape' => false), __('Are you sure you want to delete # %s?', $trigger['Trigger']['id'])); ?>
					</td>
				</tr>
			<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
