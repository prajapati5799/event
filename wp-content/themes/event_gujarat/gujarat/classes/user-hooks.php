<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Guj_User_Hooks' ) ) {

	class Guj_User_Hooks {
		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {

		}
		
	}

	new Guj_User_Hooks();
}
