<?php

namespace AffiniPayWordPress;

interface AffiniPayWordPressInterface {

	function init();
	function admin_init();
	function plugin_activate();
	function plugin_deactivate();
	function register_styles();
	function register_scripts();
	function register_admin_scripts();
	function register_admin_styles();
	function register_shortcodes();
}
