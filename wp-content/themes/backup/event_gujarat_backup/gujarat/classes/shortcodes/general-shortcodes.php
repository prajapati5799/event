<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Guj_Shortcodes' ) ) {
	class Guj_Shortcodes {

		public function __construct() {
			$this->register_hook_callbacks();
		}

		/**
		 * Register shortcodes here
		 */
		public static function register_hook_callbacks() {

		}

	}

	new Guj_Shortcodes();
}
