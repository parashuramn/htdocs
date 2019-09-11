<?php 
	$filter_count = 0;
  	
	function add_filter_params_count($index) {
		if(!empty($filter_params[$index]) && is_array($filter_params[$index]))
			return count($filter_params[$index]);
		else
			return 0;
	}
  	 $filter_count = 	add_filter_params_count('objectid');
                        add_filter_params_count('agent_id') +
                        add_filter_params_count('verb_id') +
                        add_filter_params_count('group') + 
                        add_filter_params_count('related') + 
                        add_filter_params_count('object_is_parent') + 
                        add_filter_params_count('object_is_group') +
                        add_filter_params_count('content')
                        ;
	if(!empty($filter_params['timestamp_start'])) $filter_count++;
	if(!empty($filter_params['timestamp_end'])) $filter_count++;
	if(!empty($filter_params['score_min'])) $filter_count++;
	if(!empty($filter_params['score_max'])) $filter_count++;
	if(!empty($filter_params['score_percentage_min'])) $filter_count++;
	if(!empty($filter_params['score_percentage_max'])) $filter_count++;
	
	$filter_count = modified("filter_count", $filter_count, $filter_params);

        $filter_important = ($filter_count)? "important":"";
?>
<div class="actions" style=" margin: 0 10px; ">
	<div class="btn-group">
		<a class="tooltips btn default ggreen"  data-placement="top" data-original-title="<?php echo __("Choose the columns you want to see"); ?>"  href="#" data-toggle="dropdown">
		<?php echo __("Columns");?> <i class="fa fa-angle-down"></i>
		</a>
		<div id="report_table_column_toggler" class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
		</div>
	</div>
</div>
<div class="tools" style=" margin: 0 10px; ">
	<a href="javascript:;"  data-placement="top" data-original-title="Minimize"  class="tooltips collapse">
	</a>
	<a href="javascript:;" data-placement="top" data-original-title="Filter" onClick="$('body').toggleClass('page-quick-sidebar-open');" class="tooltips fa <?php echo h($filter_important); ?> fa-filter ">
		<span class="badge badge-danger"><?php echo h($filter_count); ?></span>
	</a>
	<a href="javascript:;"  data-placement="top" data-original-title="Print"  onClick="window.print();" class="tooltips fa fa-print">
	</a>
	<a href="javascript:;"  data-placement="top" data-original-title="<?php echo __("Download CSV"); ?>"  onClick="jQuery(this).children('form').submit();"  class="tooltips fa fa-file-excel-o"><form method="POST"><input type="hidden" name="csv" value="1"></form></a>		
	 <a href="javascript:;"  data-placement="top" data-original-title="<?php echo __("Download PDF"); ?>"  onClick="jQuery(this).children('form').submit();"  class="pdf_download tooltips fa fa-file-pdf-o"><form method="POST"><input type="hidden" name="pdf" value="1" />
	 <input type="hidden" name="column_values" value="" />
	 </form></a> 
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.pdf_download').submit(function() {
			var columns = get_selected_filter_columns();
			$(".gb_report .tools input[name='column_values']").val(columns);
		});
	});
</script>
