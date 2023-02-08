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

			add_action( 'wp_ajax_add_event', __CLASS__ . '::add_event_callback' );
			add_action( 'wp_ajax_nopriv_add_event', __CLASS__ . '::add_event_callback' );

			add_action( 'wp_ajax_add_organiser', __CLASS__ . '::add_organiser_callback' );
			add_action( 'wp_ajax_nopriv_add_organiser', __CLASS__ . '::add_organiser_callback' );
		}

		/*
		* Filter event on google map callback
		*/		
		public static function filter_event_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
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
                        $is_store_time = date('F j, Y', strtotime(get_field('start_date', get_the_ID()))).' '.date('g:i A', strtotime(get_field('start_time', get_the_ID()))).' '.__('to', 'anc').' '.date('F j, Y', strtotime(get_field('end_date', get_the_ID()))).' '.date('g:i A', strtotime(get_field('end_time', get_the_ID())));

                        $organisers = get_the_terms( get_the_ID(), 'events_organisers' );
	                    $organiser_data = array(); 
	                    foreach ( $organisers as $organiser ) {
	                        $organiser_data[] = $organiser->name;
	                    }                                         
	                    $organiser_name = join( ", ", $organiser_data );

                        $pointerArray[] = [ $latitude_meta, $longitude_meta, $attachment_url, $title_meta, $address_meta, $is_store_time, $organiser_name, 'id_'.get_the_id() ];
	                }
	            }
				$ret_data['status'] = 'success';
				$ret_data['html'] = $html;
				$ret_data['pointer_array'] = $pointerArray;
			} else { 
				$ret_data['status'] = 'error';
				$ret_data['html'] = 'Your search result match not found!';
				$ret_data['pointer_array'] = $pointerArray;
			}		
			// echo wp_send_json($ret_data);
			echo json_encode($ret_data);
			exit();
		}

		/*
		* Add event callback
		*/		
		public static function add_event_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			$start_date = date('Ymd', strtotime($_POST['startdate']));
			$start_time = date('H:i:s', strtotime($_POST['starttime']));

			$end_date = date('Ymd', strtotime($_POST['enddate']));
			$end_time = date('H:i:s', strtotime($_POST['endtime']));
			// echo $start_date;
			// echo $end_date;

			// echo $start_time;
			// echo $end_time;
			// exit;
			
			$organiser = array_map( 'intval', $_POST['organiser'] );
			$organiser = array_unique( $organiser );

			$args = array(
			  'post_status'		=> 'publish',
			  'post_title'		=> $_POST['eventname'],
			  'post_type'		=> 'events',
			);
			$event_id = wp_insert_post($args);
			
			if($event_id){
				wp_set_object_terms($event_id, $organiser, 'events_organisers');
				update_post_meta($event_id,'address', $_POST['eventlocation']);
				update_post_meta($event_id,'start_date', $start_date);
				update_post_meta($event_id,'start_time', $start_time);
				update_post_meta($event_id,'end_date', $end_date);
				update_post_meta($event_id,'end_time', $end_time);
				update_post_meta($event_id,'latitude', $_POST['loc_lat']);
				update_post_meta($event_id,'longitude', $_POST['loc_long']);

				$ret_data['status'] = 'success';
				$ret_data['message'] = 'Your event added successfully.';
			} else { 
				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Something went wrong!';
			}	
			
			echo json_encode($ret_data);
			exit();
		}

		/*
		* Add organiser callback
		*/		
		public static function add_organiser_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			
			$cat  = get_term_by('name', $_POST['organisername'], 'events_organisers');

			if($cat == false){
				$cat = wp_insert_term($_POST['organisername'], 'events_organisers');
				$cat_id = $cat['term_id'] ;

				$ret_data['status'] = 'success';
				$ret_data['message'] = 'Organiser added successfully.';
			} else {
				$cat_id = $cat->term_id ;

				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Same name organiser already exist!';
			}

			echo json_encode($ret_data);
			exit();
		}

	}
	new Anc_WP_Ajax_callback();
}

