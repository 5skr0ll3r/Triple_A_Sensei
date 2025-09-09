<div class="triple-a-export-button-container">
	<form id="exportForm" method="POST" action="<?php echo admin_url('admin-post.php'); ?>" target="_blank">
		<?php wp_nonce_field('export', '_triple-a_nonce'); ?>
		<input type="hidden" name="action" value="triple_a_export">
		<button type="submit">Export</button>
	</form>
</div>
