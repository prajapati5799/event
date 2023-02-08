<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Anc_WP_Ajax_callback' ) ) {

	class Anc_WP_Ajax_callback {
		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {
			add_action( 'wp_ajax_filter_event', __CLASS__ . '::filter_event_callback' );
			add_action( 'wp_ajax_nopriv_filter_event', __CLASS__ . '::filter_event_callback' );
		}

		public static function filter_event_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'niuNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			$date_now = date('Y-m-d H:i:s');
			$args = array(
			'post_type' => 'events',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
	            array(
	                'key'           => 'start_date',
	                'compare'       => '>=',
	                'value'         => $date_now,
	                'type'          => 'DATETIME',
	            ),
	          ),
			);
				
			if(isset($_POST['eventname']) && $_POST['eventname'] != ''){
				$args['tax_query'] = array(
					array (
						'taxonomy' => 'events_organisers',
						'field' => 'slug',
						'terms' => $_POST['eventname'],
					),
				);
			}

			if(isset($_POST['eventlocation']) && $_POST['eventlocation'] != ''){
				$args['meta_query'] = array(
					array(
				        'key'       => 'address',
				        'value'     => $_POST['eventlocation'],
				        'compare'   => 'LIKE',
				    )
				);
			}	

			if(isset($_POST['eventstartdate']) && $_POST['eventstartdate'] != ''){
			    $args['meta_query'][] = array(
			        'key'       => 'start_date',
			        'value'     => date('Y-m-d', strtotime($_POST['eventstartdate'])),
			        'compare' 	=> '>=',
			        'type'      => 'date'
			      );
			   }
			   if(isset($_POST['eventenddate']) && $_POST['eventenddate'] != ''){
			      $args['meta_query'][] = array(
			        'key'       => 'end_date',
			        'value'     => date('Y-m-d', strtotime($_POST['eventenddate'])),
			        'compare' => '<=',
			        'type'        => 'date'
			      );
			   }
			$query = new WP_Query( $args );
			ob_start();
			if ( $query->have_posts() ) {
	  		 $pointerArray = [];
	            if ( $query->have_posts() ) {
	                while ( $query->have_posts() ) { $query->the_post();
	                    $latitude_meta = get_field('latitude', get_the_ID());
                        $longitude_meta = get_field('longitude', get_the_ID());                               
                        $attachment_url = site_url().'/wp-content/uploads/2022/07/localtion-pin.png';
                        $title_meta = get_the_title(get_the_ID());
                        $address_meta = get_field('address', get_the_ID());
                        $is_store_time = __('Date:', 'anc').' '.date('F j, Y g:i A', strtotime(get_field('start_date', get_the_ID()))).' '.__('to', 'anc').' '.date('F j, Y g:i A', strtotime(get_field('end_date', get_the_ID())));

                        $pointerArray[] = [ $latitude_meta, $longitude_meta, $attachment_url, $title_meta, $address_meta, $is_store_time, 'id_'.get_the_id() ];
	                }
	            }
				$ret_data['status'] = 'success';
				$ret_data['html'] = $html;
				$ret_data['pointer_array'] = $pointerArray;
			} else { 
				$ret_data['status'] = 'success';
				$ret_data['html'] = $html;
				$ret_data['pointer_array'] = $pointerArray;
			}		
			// echo wp_send_json($ret_data);
			echo json_encode($ret_data);
			exit();
		}
	}
	new Anc_WP_Ajax_callback();
}

