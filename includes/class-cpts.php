<?php
/**
 * This file contains the MCM_CPTs class.
 */

/**
 * This class handles the creation of the "CPTs" post type, and creates a
 * UI to display the CPT-specific data on the admin screens.
 *
 */
class MCM_CPTs {

	public $settings_field = 'mcm_taxonomies';
	public $menu_page = 'register-taxonomies';
	
	/**
	 * CustomPost details array.
	 */
	public $custompost_details;

	/**
	 * Construct Method.
	 */
	function __construct() {
		
		$this->custompost_details = apply_filters( 'mcm_custompost_details', array(
			'col1' => array( 
			    __( 'Price:', 'mcm-cpts' )   => '_cpt_price', 
			    __( 'Address:', 'mcm-cpts' ) => '_cpt_address', 
			    __( 'City:', 'mcm-cpts' )    => '_cpt_city', 
			    __( 'State:', 'mcm-cpts' )   => '_cpt_state', 
			    __( 'ZIP:', 'mcm-cpts' )     => '_cpt_zip' 
			), 
			'col2' => array( 
			    __( 'MLS #:', 'mcm-cpts' )       => '_cpt_mls', 
			    __( 'Square Feet:', 'mcm-cpts' ) => '_cpt_sqft', 
			    __( 'Bedrooms:', 'mcm-cpts' )    => '_cpt_bedrooms', 
			    __( 'Bathrooms:', 'mcm-cpts' )   => '_cpt_bathrooms', 
			    __( 'Basement:', 'mcm-cpts' )    => '_cpt_basement' 
			)
		) );

		add_action( 'init', array( $this, 'create_post_type' ) );

		add_filter( 'manage_edit-cpt_columns', array( $this, 'columns_filter' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );

		add_action( 'admin_menu', array( $this, 'register_meta_boxes' ), 5 );
		add_action( 'save_post', array( $this, 'metabox_save' ), 1, 2 );

		add_shortcode( 'custompost_details', array( $this, 'custompost_details_shortcode' ) );
		add_shortcode( 'custompost_map', array( $this, 'custompost_map_shortcode' ) );
		add_shortcode( 'custompost_video', array( $this, 'custompost_video_shortcode' ) );

		#add_action( 'admin_head', array( $this, 'admin_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );

	}

	/**
	 * Creates our "CPT" post type.
	 */
	function create_post_type() {

		$args = apply_filters( 'mcm_cpts_post_type_args',
			array(
				'labels' => array(
					'name'               => __( 'CPTs', 'mcm-cpts' ),
					'singular_name'      => __( 'CPT', 'mcm-cpts' ),
					'add_new'            => __( 'Add New', 'mcm-cpts' ),
					'add_new_item'       => __( 'Add New CPT', 'mcm-cpts' ),
					'edit'               => __( 'Edit', 'mcm-cpts' ),
					'edit_item'          => __( 'Edit CPT', 'mcm-cpts' ),
					'new_item'           => __( 'New CPT', 'mcm-cpts' ),
					'view'               => __( 'View CPT', 'mcm-cpts' ),
					'view_item'          => __( 'View CPT', 'mcm-cpts' ),
					'search_items'       => __( 'Search CPTs', 'mcm-cpts' ),
					'not_found'          => __( 'No cpts found', 'mcm-cpts' ),
					'not_found_in_trash' => __( 'No cpts found in Trash', 'mcm-cpts' )
				),
				'public'        => true,
				'query_var'     => true,
				'menu_position' => 6,
				'menu_icon'     => 'dashicons-admin-home',
				'has_archive'   => true,
				'supports'      => array( 'title', 'editor', 'comments', 'thumbnail', 'genesis-seo', 'genesis-layouts', 'genesis-simple-sidebars' ),
				'rewrite'       => array( 'slug' => 'cpts' ),
			)
		);

		register_post_type( 'cpt', $args );

	}

	function register_meta_boxes() {

		add_meta_box( 'cpt_details_metabox', __( 'CustomPost Details', 'mcm-cpts' ), array( &$this, 'cpt_details_metabox' ), 'cpt', 'normal', 'high' );

	}

	function cpt_details_metabox() {
		include( dirname( __FILE__ ) . '/views/cpt-details-metabox.php' );
	}

	function metabox_save( $post_id, $post ) {

		if ( ! isset( $_POST['mcm_details_metabox_nonce'] ) || ! isset( $_POST['ap'] ) )
			return;

		/** Verify the nonce */
	    if ( ! wp_verify_nonce( $_POST['mcm_details_metabox_nonce'], 'mcm_details_metabox_save' ) )
	        return;

		/** Run only on cpts post type save */
		if ( 'cpt' != $post->post_type )
			return;

	    /** Don't try to save the data under autosave, ajax, or future post */
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
	    if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;

	    /** Check permissions */
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return;

	    $custompost_details = $_POST['ap'];

	    /** Store the custom fields */
	    foreach ( (array) $custompost_details as $key => $value ) {

	        /** Save/Update/Delete */
	        if ( $value ) {
	            update_post_meta($post->ID, $key, $value);
	        } else {
	            delete_post_meta($post->ID, $key);
	        }

	    }

 		//* extra check for price that can create a sortable value
 		if ( isset( $custompost_details['_cpt_price'] ) && ! empty( $custompost_details['_cpt_price'] ) ) {

 			$price_sortable	= preg_replace( '/[^0-9\.]/', '', $custompost_details['_cpt_price'] );
 			update_post_meta( $post_id, '_cpt_price_sortable', floatval( $price_sortable ) );

 		} else {
 			delete_post_meta( $post_id, '_cpt_price_sortable' );
 		}

	}

	/**
	 * Filter the columns in the "CPTs" screen, define our own.
	 */
	function columns_filter ( $columns ) {

		$columns = array(
			'cb'                 => '<input type="checkbox" />',
			'cpt_thumbnail'  => __( 'Thumbnail', 'mcm-cpts' ),
			'title'              => __( 'CPT Title', 'mcm-cpts' ),
			'cpt_details'    => __( 'Details', 'mcm-cpts' ),
			'cpt_features'   => __( 'Features', 'mcm-cpts' ),
			'cpt_categories' => __( 'Categories', 'mcm-cpts' )
		);

		return $columns;

	}

	/**
	 * Filter the data that shows up in the columns in the "CPTs" screen, define our own.
	 */
	function columns_data( $column ) {

		global $post, $wp_taxonomies;

		switch( $column ) {
			case "cpt_thumbnail":
				printf( '<p>%s</p>', genesis_get_image( array( 'size' => 'thumbnail' ) ) );
				break;
			case "cpt_details":
				foreach ( (array) $this->custompost_details['col1'] as $label => $key ) {
					printf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
				}
				foreach ( (array) $this->custompost_details['col2'] as $label => $key ) {
					printf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
				}
				break;
			case "cpt_features":
				echo get_the_term_list( $post->ID, 'features', '', ', ', '' );
				break;
			case "cpt_categories":
				foreach ( (array) get_option( $this->settings_field ) as $key => $data ) {
					printf( '<b>%s:</b> %s<br />', esc_html( $data['labels']['singular_name'] ), get_the_term_list( $post->ID, $key, '', ', ', '' ) );
				}
				break;
		}

	}

	function custompost_details_shortcode( $atts ) {

		global $post;

		$output = '';

		$output .= '<div class="custompost-details">';

		$output .= '<div class="custompost-details-col1 one-half first">';
			foreach ( (array) $this->custompost_details['col1'] as $label => $key ) {
				$output .= sprintf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );	
			}
		$output .= '</div><div class="custompost-details-col2 one-half">';
			foreach ( (array) $this->custompost_details['col2'] as $label => $key ) {
				$output .= sprintf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );	
			}
		$output .= '</div><div class="clear">';
			$output .= sprintf( '<p><b>%s</b><br /> %s</p></div>', __( 'Additional Features:', 'mcm-cpts' ), get_the_term_list( $post->ID, 'features', '', ', ', '' ) );

		$output .= '</div>';

		return $output;

	}

	function custompost_map_shortcode( $atts ) {

		return genesis_get_custom_field( '_cpt_map' );

	}

	function custompost_video_shortcode( $atts ) {

		return genesis_get_custom_field( '_cpt_video' );

	}

	function admin_js() {

		wp_enqueue_script( 'accesspress-admin-js', APL_URL . 'includes/js/admin.js', array(), APL_VERSION, true );

	}

}