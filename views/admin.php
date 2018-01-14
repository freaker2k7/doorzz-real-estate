<?php

// If this file is called directly, abort.
if (!defined('WPINC') || !class_exists('DOORZZ_REAL_ESTATE')) {
	die;
}

?>
<div class="wrap">
	<form method="POST" action="?page=<?php echo self::SLAG ?>" id="DOORZZ_REAL_ESTATE_CONF_FORM">
		<h1>Doorzz Real Estate (Settings)</h1>
		<?php if (isset($error)) { ?>
			<div class="error notice">
				<p><?php _e($error, self::SLAG . '-error'); ?></p>
			</div>
		<?php } else if (isset($success)) { ?>
			<div class="updated notice">
				<p><?php _e('Changes saved successfully!', self::SLAG . '-success'); ?></p>
			</div>
		<?php } ?>
		<div class="input-field" style="margin: 2rem 0">
			<label for="DOORZZ_REAL_ESTATE_CARD_TEMPLATE"><h2>This is a card's template:</h2></label>
			<br>
			<textarea name="DOORZZ_REAL_ESTATE_CARD_TEMPLATE" id="DOORZZ_REAL_ESTATE_CARD_TEMPLATE" 
						required="required" style="width: 700px; max-width: 90%; min-height: 300px;"><?php 
				echo esc_textarea(self::_get_template());
			?></textarea>
		</div>
		<div class="input-field">
			<button type="submit" name="DOORZZ_REAL_ESTATE_submit" value="save">Save</button>
		</div>
	</form>
</div>