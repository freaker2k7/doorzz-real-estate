<?php 

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

global $DOORZZ_REAL_ESTATE_DEFAULT_TEMPLATE;
$DOORZZ_REAL_ESTATE_DEFAULT_TEMPLATE = 
'<a style="display: inline-block; width: 250px; height: 100%; vertical-align: top; margin: 0 10px; text-decoration: none;" href="{{LINK_TO_HID}}" target="_blank">
	<h2 style="font-size: 1rem;">{title}</h2>
	<div style="width: 100%; height: 100px; overflow: hidden; background: #efefef; position: relative;">
		<img src="{img}" alt="{free_text}" title="{free_text}" style="width: 100%; position: absolute; margin: auto; top: 0; left: 0; right: 0; bottom: 0;" />
	</div>
	<h2 style="text-transform: capitalize; font-size: 1rem; padding-top: 0.5rem; margin-bottom: 0;">
		<i class="local-icons {type} {subtype}"></i> {subtype} for {type}<br>
		${price}/{period}<br>
		{location}
	</h4>
</a>';