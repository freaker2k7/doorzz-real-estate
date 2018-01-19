<?php

// If this file is called directly, abort.
if (!defined('WPINC') || !class_exists('DOORZZ_REAL_ESTATE')) {
	die;
}

?>
<div class="wrap">
	<form method="POST" action="?page=<?php echo self::$SLAG ?>" id="DOORZZ_REAL_ESTATE_CONF_FORM">
		<h1><?php _e('Doorzz Real Estate', self::$SLAG); ?> (<?php _e('Settings', self::$SLAG); ?>)</h1>
		<?php 
			if (isset($success)) {
				$key = 'doorzz_msg_' . rand();
		?>
			<div class="updated notice" id="<?php echo $key; ?>">
				<p><?php _e('Changes saved successfully!', self::$SLAG); ?></p>
				<script type="text/javascript">
					setTimeout(function() {
						var msg = document.getElementById('<?php echo $key; ?>');
						msg.parentNode.removeChild(msg);
					}, 20000);
				</script>
			</div>
		<?php } ?>
		<div class="input-field" style="margin: 2rem 0">
			<label for="DOORZZ_REAL_ESTATE_CARD_TEMPLATE"><h2><?php _e('This is a card\'s template', self::$SLAG); ?>:</h2></label>
			<br>
			<textarea name="DOORZZ_REAL_ESTATE_CARD_TEMPLATE" required="required" style="width: 700px; max-width: 90%; min-height: 300px;"><?php 
				echo esc_textarea(self::_get_template());
			?></textarea>
		</div>
		<div class="input-field">
			<button type="submit" name="DOORZZ_REAL_ESTATE_submit" value="save" class="button button-primary">
				<?php _e('Save Changes', self::$SLAG); ?>
			</button>
		</div>
	</form>
</div>