<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Guj_Theme_Customizer' ) ) {

	/**
	 * Handles customisations options for theme
	 *
	 */
	class Guj_Theme_Customizer {

		private static $theme_options = [];

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {

		}

	} // end Guj_Theme_Customizer

	new Guj_Theme_Customizer();
}
