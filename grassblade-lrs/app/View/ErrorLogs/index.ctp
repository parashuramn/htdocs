<style>
.error td{
	background: #ffc0c0 !important;
}
.success td{
	background: #6cff6c !important;
}
.waiting td{
	background: #f5f576 !important;
}
/*
 a:hover {
    margin-top: -7px;
    margin-left: -2px;
    margin-right: 2px;
}
a:hover:before {
	box-shadow: 0 15px 10px -10px rgba(31, 31, 31, 0.5);	
}
*/
/*
a.all {
	background: #eee;
}
*/
.filter-links {
	margin: 20px 0;
}
/*
.filter-links a {
    display: inline-block;
    padding: 9px 29px;
    text-decoration: none;
    color: black;
    text-transform: uppercase;
}
*/

/* Flatten effect – flattens sides on hover */

a:hover:before, a:hover:after, .filter-links a {
  transition: box-shadow 600ms ease-out, left 200ms, right 200ms;
  box-shadow: 0 8px 8px rgba(31, 31, 31, 0.5);
}
.selected {
	box-shadow: none !important;
}
</style>
<?php
	$status = @$filters["status"];
	$error_class = ($status === 0)? "btn red error selected":"btn red error";
	$waiting_class = ($status == 2)? "btn yellow waiting selected":"btn yellow waiting";
	$success_class = ($status == 1)? "btn green success selected":"btn green success";
?>
<div class="ErrorLogs index" style="overflow:scroll">
	<div class="filter-links">
	<a href="<?php echo $error_log_url."?status=0"; ?>" class="<?php echo $error_class; ?>"><?php echo __("Failed"); ?></a>
	<a href="<?php echo $error_log_url."?status=2"; ?>" class="<?php echo $waiting_class; ?>"><?php echo __("Incomplete/Interrupted"); ?></a>
	<a href="<?php echo $error_log_url."?status=1"; ?>" class="<?php echo $success_class; ?>"><?php echo __("Success"); ?></a>
	<a href="<?php echo $error_log_url; ?>" class="btn grey  all"><?php echo __("All"); ?></a>
	</div>

	<table class="table table-striped table-bordered table-hover" id="error_log_table">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('user'); ?></th>
			<th><?php echo $this->Paginator->sort('objectid', 'Activity ID'); ?></th>
			<th><?php echo $this->Paginator->sort('statement_id'); ?></th>
			<th><?php echo $this->Paginator->sort('url'); ?></th>
			<th><?php echo $this->Paginator->sort('request_method'); ?></th>
		<?php /* 
			<th><?php echo $this->Paginator->sort('data'); ?></th>
		*/?>
			<th><?php echo $this->Paginator->sort('error_msg'); ?></th>
			<th><?php echo $this->Paginator->sort('error_code'); ?></th>
		<?php /* 
			<th><?php echo $this->Paginator->sort('response'); ?></th>
		*/?>
		<?php /* 
			<th><?php echo $this->Paginator->sort('status'); ?></th>
		*/?>
			<th><?php echo $this->Paginator->sort('IP'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($ErrorLogs as $ErrorLog): 
		$status = $ErrorLog['ErrorLog']['status'];
		$class = '';
		if(empty($status))
			$class = "error";
		else if ($status == 1)
			$class = "success";
		else if ($status == 2)
			$class = "waiting";
	?>
	<tr class="<?php echo $class; ?>">
		<td><?php echo h($ErrorLog['ErrorLog']['id']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['type']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['user']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['objectid']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['statement_id']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['url']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['request_method']); ?>&nbsp;</td>
		<?php /* 
		<td><?php echo h($ErrorLog['ErrorLog']['data']); ?>&nbsp;</td>
		*/?>
		<td><?php echo h($ErrorLog['ErrorLog']['error_msg']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['error_code']); ?>&nbsp;</td>
		<?php /* 
		<td><?php
				$response = $ErrorLog['ErrorLog']['response']; 
				$decoded = json_decode($response);
				if(!empty($decoded))
					echo h_array($decoded, $strip_tags = true);
				else 
					echo $response;
			?>&nbsp;</td>
		*/?>
		<?php /* 
		<td><?php echo h($ErrorLog['ErrorLog']['status']); ?>&nbsp;</td>
		*/?>
		<td><?php echo h($ErrorLog['ErrorLog']['IP']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['created']); ?>&nbsp;</td>
		<td><?php echo h($ErrorLog['ErrorLog']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $ErrorLog['ErrorLog']['id'])); ?>
			<?php 
				if($ErrorLog['ErrorLog']['type'] == "Trigger")
				echo $this->Html->link(__('Re-Triggers'), array('action' => 'index', "?" => array( "url" => $ErrorLog['ErrorLog']['url'], "statement_id" => $ErrorLog['ErrorLog']['statement_id']))); 
			?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
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
