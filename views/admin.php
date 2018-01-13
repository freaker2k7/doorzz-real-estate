<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

?>
<div class="wrap">
	<style type="text/css">
		#REAL_ESTATE_CONF_FORM {
			width: 37rem;
		}
		
		#REAL_ESTATE_CONF_FORM h1 {
			margin-bottom: 1rem;
		}
		
		#REAL_ESTATE_CONF_FORM .red {
			color: #ca4a1f;
		}
		
		#REAL_ESTATE_CONF_FORM .green {
			color: #006600;
		}
		
		#REAL_ESTATE_CONF_FORM .stats {
			float: left;
			margin: 2rem 0;
		}
		
		#REAL_ESTATE_CONF_FORM .input-field {
			margin: 0.5rem 0;
		}
		
		#REAL_ESTATE_CONF_FORM .input-field label {
			min-width: 10rem;
			display: inline-block;
			line-height: 1.6rem;
		}
		
		#REAL_ESTATE_CONF_FORM .input-field textarea {
			width: 100%;
			height: 500px;
			resize: no-resize;
		}
		
		#REAL_ESTATE_CONF_FORM .input-field button[type="submit"] {
			padding: 0.5rem 1.5rem;
			margin: 1.7rem;
			float: right;
			cursor: pointer;
		}
	</style>
	
	<form method="POST" action="?page=REAL_ESTATE" id="REAL_ESTATE_CONF_FORM">
		<h1>Real Estate Settings</h1>
		<?php if ($error) { echo '<h3 class="red">' . $error . '</h3>'; } ?>
		<div class="input-field">
			<label for="REAL_ESTATE_CARD_TEMPLATE">This is a card's template:</label>
			<br>
			<textarea name="REAL_ESTATE_CARD_TEMPLATE" id="REAL_ESTATE_CARD_TEMPLATE" required="required"><?php 
				echo file_get_contents(REAL_ESTATE_PLUGIN_DIR . 'views/card.php') 
			?></textarea>
		</div>
		<div class="input-field">
			<?php if ($success) { echo '<div class="stats green">Changes saved successfully!</div>'; } ?>
			<button type="submit" name="REAL_ESTATE_submit" value="save">Save</button>
		</div>
	</form>
</div>