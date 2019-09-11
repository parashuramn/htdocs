<div>
	<div>
	<?php echo $this->Html->link(__('Add New'), 
						array('action' => 'add'), array('class' => 'btn default green-stripe', 'escape' => false) ); ?>
	</div>
	<br>
	<div class="portlet box gblue">
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-users" style=" font-size: 60px; "></i><?php echo __('Groups List'); ?>
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
					<th><?php echo $this->Paginator->sort('created'); ?></th>
					<th><?php echo $this->Paginator->sort('modified'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($Groups as $Group): ?>
				<tr>
					<td><?php echo h($Group['Group']['id']); ?>&nbsp;</td>
					<td><?php echo h($Group['Group']['name']); ?>&nbsp;</td>
					<td><?php echo h($Group['Group']['type']); ?>&nbsp;</td>
					<td><?php echo (strtotime($Group['Group']['created']) == 0)? "":h(date("F j, Y H:i:s", strtotime($Group['Group']['created']))); ?>&nbsp;</td>
					<td><?php echo (strtotime($Group['Group']['modified']) == 0)? "":h(date("F j, Y H:i:s", strtotime($Group['Group']['modified']))); ?>&nbsp;</td>
					<td>
						<?php 
							$edit = '<i class="fa fa-edit"></i> '.__("Edit");
							$delete = '<i class="fa fa-times"></i> '.__("Delete");
							echo $this->Html->link($edit, array('action' => 'edit', $Group['Group']['id']), array('class' => 'btn btn-xs default green-stripe', 'escape' => false)); 
					

							echo $this->Form->postLink($delete, array('action' => 'delete', $Group['Group']['id']), array('class' => 'btn btn-xs default red-stripe', 'escape' => false), __('Are you sure you want to delete # %s?', $Group['Group']['id'])); 
						?>
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
