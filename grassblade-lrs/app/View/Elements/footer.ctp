<div class="page-footer">
	<div class="page-footer-inner">
		 <?php echo date("Y"); ?> &copy; GrassBlade LRS v<?php echo h(LRS_VERSION); ?>, a product of <a href="http://www.nextsoftwaresolutions.com/">Next Software Solutions</a>.<?php echo grassblade_show_version_message(); ?></div>
	<div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>
<script type="text/javascript">
jQuery(function() {
	jQuery(".tooltips").click(function() {
		//jQuery(this).tooltip('hide');

		if(jQuery(this).hasClass("collapse"))
			jQuery(this).attr("data-original-title", "<?php echo __('Maximize'); ?>");
		if(jQuery(this).hasClass("expand"))
			jQuery(this).attr("data-original-title", "<?php echo __('Minimize'); ?>");

		jQuery(this).filter(':hover').tooltip('show');
	});
});
 
</script>