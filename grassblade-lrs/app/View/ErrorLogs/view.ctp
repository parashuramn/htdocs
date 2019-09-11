<style>
dd {
	margin: 0px 20px 20px;
}
</style>

<div class="ErrorLogs view">
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['user']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Statement Id'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['statement_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Url'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['url']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Request Method'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['request_method']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Data'); ?></dt>
		<dd>
			<?php
				$data = $ErrorLog['ErrorLog']['data']; 
				$decoded = json_decode($data);
				if(!empty($decoded))
					echo h_array($decoded, $strip_tags = true);
				else 
					echo $data;
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Error Msg'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['error_msg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Error Code'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['error_code']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Response'); ?></dt>
		<dd>
			<?php
				$response = $ErrorLog['ErrorLog']['response']; 
				$decoded = json_decode($response);
				if(!empty($decoded))
					echo h_array($decoded, $strip_tags = true);
				else 
					echo $response;
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo ($ErrorLog['ErrorLog']['status'])? "Success":"Failed"; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('IP'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['IP']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($ErrorLog['ErrorLog']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>