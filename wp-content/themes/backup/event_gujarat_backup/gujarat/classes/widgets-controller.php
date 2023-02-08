<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Guj_Widgets_Controller' ) ) {

	class Guj_Widgets_Controller {

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {
			
		}
		
	}

	new Guj_Widgets_Controller();
}
