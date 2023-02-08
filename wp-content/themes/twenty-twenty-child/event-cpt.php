<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Anc_Events_CPT' ) ) {
	/**
	 * Handles functionality for particular custom post type
	 */
	class Anc_Events_CPT {
		const POST_TYPE_NAME = 'Events';
		const POST_TYPE_SLUG = 'events';
		const POST_META_PREFIX = 'events_';

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {
			add_action( 'init', __CLASS__ . '::create_post_type' );
			add_shortcode( 'search_events', __CLASS__ . '::render_event_locator_callback' );
		}

		/**
		 * Registers the custom post type
		 *
		 * @mvc Controller
		 */
		public static function create_post_type() {
			if ( ! post_type_exists( self::POST_TYPE_SLUG ) ) {
				$post_type_params = self::get_post_type_params();
				register_post_type( self::POST_TYPE_SLUG, $post_type_params );
			}
		}

		/**
		 * Defines the parameters for the custom post type
		 *
		 * @mvc Model
		 *
		 * @return array
		 */
		private static function get_post_type_params() {
			$labels = [
				'name'               => self::POST_TYPE_NAME,
				'singular_name'      => self::POST_TYPE_NAME,
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New ' . self::POST_TYPE_NAME,
				'edit'               => 'Edit',
				'edit_item'          => 'Edit ' . self::POST_TYPE_NAME,
				'new_item'           => 'New ' . self::POST_TYPE_NAME,
				'view'               => 'View ' . self::POST_TYPE_NAME,
				'all_items'          => 'All ' . self::POST_TYPE_NAME,
				'view_item'          => 'View ' . self::POST_TYPE_NAME,
				'search_items'       => 'Search ' . self::POST_TYPE_NAME,
				'not_found'          => 'No ' . self::POST_TYPE_NAME . 'found',
				'not_found_in_trash' => 'No ' . self::POST_TYPE_NAME . 'found in Trash',
				'parent'             => 'Parent ' . self::POST_TYPE_NAME
			];

			$post_type_params = [
				'labels'              => $labels,
				'singular_label'      => self::POST_TYPE_NAME,
				'public'              => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 35,
				'menu_icon'           => 'dashicons-location-alt',
				'hierarchical'        => true,
				'capability_type'     => 'post',
				'has_archive'         => false,
				'rewrite'             => [ 'slug' => 'events' ],
				'query_var'           => true,
				'supports'            => [ 'title', 'author','thumbnail','revisions' ]
			];
			
      $labels_branch = array(
          'name' => _x( 'Organisers', 'taxonomy general name' ),
          'singular_name' => _x(  ' Organisers', 'taxonomy singular name' ),
          'search_items' =>  __( 'Search Organisers' ),
          'all_items' => __( 'All Organisers' ),
          'parent_item' => __( 'Parent Organisers' ),
          'parent_item_colon' => __( 'Parent Organisers:' ),
          'edit_item' => __( 'Edit Organisers' ),
          'update_item' => __( 'Update Organisers' ),
          'add_new_item' => __( 'Add New Organisers' ),
          'new_item_name' => __( 'New Organisers Name' ),
          'menu_name' => __('Organisers' ),
      );
      register_taxonomy(self::POST_TYPE_SLUG.'_organisers',array(self::POST_TYPE_SLUG), array(
          'hierarchical' => true,
          'labels' => $labels_branch,
          'show_ui' => true,
          'show_admin_column' => true,
          'query_var' => true,
          'rewrite' => array( 'slug' => self::POST_TYPE_SLUG.'_organisers' ),
      ));
			return apply_filters( 'cat_post-type-params', $post_type_params );
		}


		/* Search brach locator call back action */
    public static function render_event_locator_callback( $atts ){
        	$atts = shortcode_atts( array(
				'lat' => '23.040375114877943',
				'lng' => '72.50363666547541',
				'height' => '710px',
				'key' => '',
			), $atts );
      ob_start();
      $date_now = date('Y-m-d H:i:s');
      $media_args = [
          'post_type'      => self::POST_TYPE_SLUG,
          'post_status'    => 'publish',
          'posts_per_page' => -1,
          'orderby'        => 'date',
          'order'          => 'DESC',
          'meta_query' => array(
            array(
                'key'           => 'start_date',
                'compare'       => '>=',
                'value'         => $date_now,
                'type'          => 'DATETIME',
            ),
          ),
      ];

      $media = new WP_Query( $media_args );?>
      <div class="event-locator">
     		<div class="event-filter">
     			<form id="filter-event" class="filter-event">
     				 <?php
	            $all_store_branches = [];
	            if( $media->have_posts() ) {
	            	while( $media->have_posts() ) { $media->the_post();
		            $all_store_branches[] = [
		            	'post_id' => get_the_ID(),
			            'name' => get_the_title(),
			            'location' => get_field('address', get_the_ID()),
		            ];
		          }
	            }
              $organisers = get_terms( array(
                  'taxonomy' => 'events_organisers',
                  'hide_empty' => false
                ) );
                $all_organisers = [];
                foreach ($organisers as $organiser) {
                  $all_organisers[] = [
                    'slug' => $organiser->slug,
                    'name' => $organiser->name,
                  ];
                }
	            ?>
             <div class="form-group">
                <label>Event Organiser</label>
                <select name="eventname" id="event-name">
                    <option value="">All</option>
					            <?php foreach($all_organisers as $organiser){?>
                        <option value="<?php echo $organiser['slug'];?>"><?php echo $organiser['name'];?></option>
					            <?php } ?>
                </select>
            </div>   
            <div class="form-group">
                <label>Event Location</label>
                <select name="eventlocation" id="event-location">
                    <option value="">All</option>
					            <?php foreach($all_store_branches as $store){?>
                        <option value="<?php echo $store['location'];?>"><?php echo $store['location'];?></option>
					            <?php } ?>
                </select>
            </div>       
     				<div class="form-group">
                <label>Event Start Date</label>
                <input type="text" name="eventstartdate" id="datepicker" readonly='true'>
            </div>
            <div class="form-group">
                <label>Event End Date</label>
                <input type="text" name="eventenddate" id="datepicker1" readonly='true'>
            </div>
     				<a href="javascript:void(0);" id="search_event">Search</a>
            <button id="resetFilter"> Reset</button>
     			</form>
     		</div>
        <div class="col-md-12">
            <?php            
            $locationArgs  = get_posts( array(
                'post_type'         => self::POST_TYPE_SLUG,
                'post_status'       => 'publish',
                'orderby'           => 'date',
                'order'             => 'DESC',
                'posts_per_page'    => -1,
                'meta_query' => array(
                  array(
                      'key'           => 'start_date',
                      'compare'       => '>=',
                      'value'         => $date_now,
                      'type'          => 'DATETIME',
                  ),
                ),
            ));

            // print_r($locationArgs);
            $pointerArr = '[';

            if( !empty( $locationArgs ) ) {
                foreach( $locationArgs as $locationArg ) {
                    $latitude_meta = get_field('latitude', $locationArg->ID);
                    $longitude_meta = get_field('longitude', $locationArg->ID);                               
                    $attachment_url = site_url().'/wp-content/uploads/2022/07/localtion-pin.png';
                    $title_meta = get_the_title($locationArg->ID);
                    $address_meta = get_field('address', $locationArg->ID);
                    $is_store_time = __('Date:', 'anc').' '.date('F j, Y g:i A', strtotime(get_field('start_date', $locationArg->ID))).' '.__('to', 'anc').' '.date('F j, Y g:i A', strtotime(get_field('end_date', $locationArg->ID)));

                    $pointerArr .= "[".$latitude_meta.",".$longitude_meta.",'".$attachment_url."','".$title_meta."','".$address_meta."','".$is_store_time."','id_".$locationArg->ID."'],";
                }
            }
            $pointerArr .= ']';
            // print_r($pointerArr);
            ?>
            <div id="map" style="height: <?php echo $atts['height']; ?>;"></div>
            <script>

            	var InforObj = [];
                var is_map_style = [
                  {
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#f5f5f5"
                      }
                    ]
                  },
                  {
                    "elementType": "labels.icon",
                    "stylers": [
                      {
                        "visibility": "off"
                      }
                    ]
                  },
                  {
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#616161"
                      }
                    ]
                  },
                  {
                    "elementType": "labels.text.stroke",
                    "stylers": [
                      {
                        "color": "#f5f5f5"
                      }
                    ]
                  },
                  {
                    "featureType": "administrative.land_parcel",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#bdbdbd"
                      }
                    ]
                  },
                  {
                    "featureType": "poi",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#eeeeee"
                      }
                    ]
                  },
                  {
                    "featureType": "poi",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#757575"
                      }
                    ]
                  },
                  {
                    "featureType": "poi.park",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#e5e5e5"
                      }
                    ]
                  },
                  {
                    "featureType": "poi.park",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#9e9e9e"
                      }
                    ]
                  },
                  {
                    "featureType": "road",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#ffffff"
                      }
                    ]
                  },
                  {
                    "featureType": "road.arterial",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#757575"
                      }
                    ]
                  },
                  {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#dadada"
                      }
                    ]
                  },
                  {
                    "featureType": "road.highway",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#616161"
                      }
                    ]
                  },
                  {
                    "featureType": "road.local",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#9e9e9e"
                      }
                    ]
                  },
                  {
                    "featureType": "transit.line",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#e5e5e5"
                      }
                    ]
                  },
                  {
                    "featureType": "transit.station",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#eeeeee"
                      }
                    ]
                  },
                  {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [
                      {
                        "color": "#c9c9c9"
                      }
                    ]
                  },
                  {
                    "featureType": "water",
                    "elementType": "labels.text.fill",
                    "stylers": [
                      {
                        "color": "#9e9e9e"
                      }
                    ]
                  }
                ];
                function initMap() {                	 
                  // Multiple Markers
                  var markers = <?php echo $pointerArr; ?>;
                  var bounds = new google.maps.LatLngBounds();
                  // console.log(markers);
                  // Styles a map in night mode.
                  var map = new google.maps.Map(document.getElementById('map'), {
                      center: {lat: <?php echo $atts['lat']; ?>, lng: <?php echo $atts['lng']; ?>},
                      maxZoom: 12.5,
                      zoom: 5,
                      mapTypeControl: false,
                      scaleControl: false,
                      streetViewControl: false,
                      overviewMapControl: false,
                      fullscreenControl: false,
                      panControl: false,
                      zoomControl: false,
                      styles: is_map_style
                  });

                  // Loop through our array of markers & place each one on the map
                  for( i = 0; i < markers.length; i++ ) {
                      var pointerCords = {
			                lat: markers[i][0],
			                lng: markers[i][1]
			            };
          				// console.log(pointerCords);
                  const marker = new google.maps.Marker({
                      position: pointerCords,
                        map: map,
                        title: markers[i][3],
                        icon: markers[i][2]
                  });

                var isInfoHtml = '<div class="info-modal-html">';
                isInfoHtml += '<h3>'+markers[i][3]+'</h3>';
                if( markers[i][4] != '' ) {
                	isInfoHtml += '<span class="address"><i class="fas fa-map-marker-alt"></i>'+markers[i][4]+'</span>';
                }
                if( markers[i][5] != '' ) {
                	isInfoHtml += '<span class="date"><i class="fas fa-location-arrow"></i>'+markers[i][5]+'</span>';
                }
                if( markers[i][0] != '' && markers[i][1] != '' ) {
                  isInfoHtml += '<span class="direction"><i class="fas fa-location-arrow"></i><a href="http://maps.google.com/maps?z=12&t=m&q='+markers[i][0]+','+markers[i][1]+'"target="_blank" tabindex="0">Get Directions</a></span>';
                }
                isInfoHtml += '</div>';

                const infowindow = new google.maps.InfoWindow({
                    content: isInfoHtml,
                    maxWidth: 400
                });

                marker.addListener('mouseover', function () {
                  closeOtherInfo();
                  var isMarkerTitle = marker.title;
                  isMarkerTitle = isMarkerTitle.replace("id_", "");
                  // console.log(isMarkerTitle);
                  jQuery('.all-event-location .brach-data').removeClass('is-active');
                  jQuery('.all-event-location div[data-title="'+isMarkerTitle+'"]').addClass('is-active');

                  var $selectedLocation = jQuery('.all-event-location .brach-data.is-active' );
                  var $locationContainer = jQuery('.all-event-location');
  						    $locationContainer.animate({
  						        scrollTop: $selectedLocation.offset().top - $locationContainer.offset().top + $locationContainer.scrollTop()
  						    });

                  infowindow.open(marker.get('map'), marker);
                  InforObj[0] = infowindow;
                });
                // new MarkerClusterer({ markers, map });
                marker.addListener('click', function () {
                    closeOtherInfo();
                    jQuery('.all-event-location .brach-data').removeClass('is-active');
                    infowindow.open(marker.get('map'), marker);
                    InforObj[0] = infowindow;
                });
                    }

                  function closeOtherInfo() {
                  	jQuery('.all-event-location .brach-data').removeClass('is-active');
                      if (InforObj.length > 0) {
                          InforObj[0].set("marker", null);
                          InforObj[0].close();
                          InforObj.length = 0;
                      }
                  }
                }
                (function($){
                    $(document).on('click','#search_event', function( e ){
                        e.preventDefault();
                        var passData = jQuery('#filter-event').serialize();
                        passData = passData+'&action=filter_event&nonce='+ajaxPar.niuNonce;
                        if (passData) {
                            $.ajax({
                                url: ajaxPar.ajaxUrl,
                                type: 'post',
                                data: passData,
                                dataType: 'JSON',
                                success: function (responce) {
                                   if ( responce.status === 'success' ) {
                                        jQuery('.all-event-location').html( responce.html );
                                        // initMap();
                                        if( typeof responce.pointer_array != 'undefined' ) {
                                            // Multiple Markers
                                            // var bounds = new google.maps.LatLngBounds();
                                            var markers = responce.pointer_array;
                                            var map = new google.maps.Map(document.getElementById('map'), {
                                                center: {lat: <?php echo $atts['lat']; ?>, lng: <?php echo $atts['lng']; ?>},
                                                maxZoom: 12,
                    														zoom: 5,
                                                mapTypeControl: false,
                                                scaleControl: false,
                                                streetViewControl: false,
                                                overviewMapControl: false,
                                                fullscreenControl: false,
                                                panControl: false,
                                                zoomControl: false,
                                                styles: is_map_style
                                            });

                                            for( i = 0; i < markers.length; i++ ) {
                                                var pointerCords = {
																                lat: parseFloat(markers[i][0]),
																                lng: parseFloat(markers[i][1])
						            										};						            
												                    const marker = new google.maps.Marker({
												                        position: pointerCords,
												                        map: map,
												                        title: markers[i][3],
								                                icon: markers[i][2]
												                    });
												                    var isInfoHtml = '<div class="info-modal-html">';
												                    isInfoHtml += '<h3>'+markers[i][3]+'</h3>';
												                    if( markers[i][4] != '' ) {
												                    	isInfoHtml += '<span class="address"><i class="fas fa-map-marker-alt"></i>'+markers[i][4]+'</span>';
												                    }
												                    if( markers[i][5] != '' ) {
															                    	isInfoHtml += '<span class="date"><i class="fas fa-location-arrow"></i>'+markers[i][5]+'</span>';
															                    }
														                if( markers[i][0] != '' && markers[i][1] != '' ) {
														                    	isInfoHtml += '<span class="direction"><i class="fas fa-location-arrow"></i><a href="http://maps.google.com/maps?z=12&t=m&q='+markers[i][0]+','+markers[i][1]+'"target="_blank" tabindex="0">Get Directions</a></span>';
															                }
												                    isInfoHtml += '</div>';

												                    const infowindow = new google.maps.InfoWindow({
												                        content: isInfoHtml,
												                        maxWidth: 400
												                    });

												                    marker.addListener('mouseover', function () {
												                        closeOtherInfo();
												                        infowindow.open(marker.get('map'), marker);
												                        InforObj[0] = infowindow;
												                    });

												                    marker.addListener('click', function () {
												                        closeOtherInfo();
												                        infowindow.open(marker.get('map'), marker);
												                        InforObj[0] = infowindow;
												                    });
							                           }
											                   function closeOtherInfo() {
														                if (InforObj.length > 0) {
														                    InforObj[0].set("marker", null);
														                    InforObj[0].close();
														                    InforObj.length = 0;
														                }
															           }
                                      }
                                    } else {
                                        jQuery('.all-event-location').html( responce.html );
                                    }
                                }
                            });
                        }
                    });
		
                })(jQuery);
            </script>
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwWV1BKqxFjRCd4H0zGR6xL_jXYJ7ZiBA&callback=initMap" defer></script>
        </div>
          </div>
      </div>
      <?php
      wp_reset_postdata();
      $html = ob_get_contents();
      ob_get_clean();
      return $html;
  }

	} // end Anc_Events_CPT

	new Anc_Events_CPT();
}
