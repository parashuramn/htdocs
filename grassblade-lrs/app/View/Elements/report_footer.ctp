<br style="clear:both"/>
<div>
<?php
if(empty($disable_pagination)) {
if(empty($_GET))
echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:start} to {:end} of {:count}.')));
else
echo $this->Paginator->counter(array('format' => __('Page {:page}, showing {:start} to {:end}.')));

?>	
</div>
<div class="paging">
<?php

	echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	echo $this->Paginator->numbers(array('separator' => ''));
	echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
?>
</div><?php 		
}
echo $this->Html->script('/app/webroot/assets/global/plugins/select2/select2');
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/media/js/jquery.dataTables.min');
echo $this->Html->script('/app/webroot/js/table'); 
echo $this->Html->script('/app/webroot/js/md5'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min'); 
echo $this->Html->script('/app/webroot/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap'); 

?>
<script type="text/javascript">
	jQuery(function() {
		jQuery("#report_table").hide();
		jQuery("#report_table > thead th").each(function(i,v) {
			var html = jQuery("#report_table_column_toggler").html();
			html += '<label><input type="checkbox" checked data-column="' + (i + 1) + '">' + jQuery(v).text() + '</label>';
			jQuery("#report_table_column_toggler").html(html);
		});
		
		TableAdvanced.init();

		function hide_columns_with_less_data(datalevel) {
			gb_col = new Array();
			console.log(datalevel);
			if(typeof datalevel == "undefined" || datalevel == 0 || datalevel == "" || datalevel == undefined)
				var datalevel = 0.01;
			console.log(datalevel);
			jQuery("table#report_table > tbody > tr").each(function(i,v) {
				jQuery(v).children("td").each(function(j, g) {
					if(gb_col[j] == undefined)
						gb_col[j] = 0

					if(jQuery(g).text().trim().length > 0)
						gb_col[j]++;
				});
			});
			var rows = jQuery("table#report_table > tbody > tr").length;
			var required = rows * datalevel; 
			for(j = 1; j < gb_col.length; j++) {
				//console.log(j + ":" + gb_col[j] + ":" + required + ":" + (gb_col[j] < required));
				if(gb_col[j] < required)
					jQuery("#report_table_column_toggler [data-column=" + j + "]").click();
			}
			jQuery("#report_table").show();
		}
		hide_columns_with_less_data(<?php echo @$datalevel; ?>);
		hide_datatable_columns("#report_table", "#report_table_column_toggler");
	});

</script>
