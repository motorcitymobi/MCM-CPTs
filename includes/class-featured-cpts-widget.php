<?php
/**
 * This widget presents loop content, based on your input, specifically for the homepage.
 *
 * @package MCM
 * @since 2.0
 * @author Nathan Rice
 */
class MCM_Featured_CPTs_Widget extends WP_Widget {

	function MCM_Featured_CPTs_Widget() {
		$widget_ops = array( 'classname' => 'featured-cpts', 'description' => __( 'Display grid-style featured cpts', 'mcm-cpts' ) );
		$control_ops = array( 'width' => 300, 'height' => 350 );
		$this->WP_Widget( 'featured-cpts', __( 'MCM - Featured CPTs', 'mcm-cpts' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
	
		/** defaults */
		$instance = wp_parse_args( $instance, array(
			'title'          => '',
			'posts_per_page' => 10
		) );
	
		extract( $args );
		
		echo $before_widget;
		
			if ( ! empty( $instance['title'] ) ) {
				echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
			}
			
			$toggle = ''; /** for left/right class */
			
			$query_args = array(
				'post_type'      => 'cpt',
				'posts_per_page' => $instance['posts_per_page'],
				'paged'          => get_query_var('paged') ? get_query_var('paged') : 1
			);
			
			query_posts( $query_args );
			if ( have_posts() ) : while ( have_posts() ) : the_post();

				//* initialze the $loop variable
				$loop        = '';

				//* Pull all the cpt information
				$custom_text = genesis_get_custom_field( '_cpt_text' );
				$price       = genesis_get_custom_field( '_cpt_price' );
				$address     = genesis_get_custom_field( '_cpt_address' );
				$city        = genesis_get_custom_field( '_cpt_city' );
				$state       = genesis_get_custom_field( '_cpt_state' );
				$zip         = genesis_get_custom_field( '_cpt_zip' );

				$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'customposts' ) ) );

				if ( $price ) {
					$loop .= sprintf( '<span class="cpt-price">%s</span>', $price );
				}

				if ( strlen( $custom_text ) ) {
					$loop .= sprintf( '<span class="cpt-text">%s</span>', esc_html( $custom_text ) );
				}

				if ( $address ) {
					$loop .= sprintf( '<span class="cpt-address">%s</span>', $address );
				}
				
				if ( $city || $state || $zip ) {

					//* count number of completed fields
					$pass = count( array_filter( array( $city, $state, $zip ) ) );

					//* If only 1 field filled out, no comma
					if ( 1 == $pass ) {
						$city_state_zip = $city . $state . $zip;
					}
					//* If city filled out, comma after city
					elseif ( $city ) {
						$city_state_zip = $city . ", " . $state . " " . $zip;
					}
					//* Otherwise, comma after state
					else {
						$city_state_zip = $city . " " . $state . ", " . $zip;
					}

					$loop .= sprintf( '<span class="cpt-city-state-zip">%s</span>', trim( $city_state_zip ) );

				}

				$loop .= sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), __( 'View CPT', 'mcm-cpts' ) );

				$toggle = $toggle == 'left' ? 'right' : 'left';

				/** wrap in post class div, and output **/
				printf( '<div class="%s"><div class="widget-wrap"><div class="cpt-wrap">%s</div></div></div>', join( ' ', get_post_class( $toggle ) ), apply_filters( 'mcm_featured_cpts_widget_loop', $loop ) );

			endwhile; endif;
			wp_reset_query();
		
		echo $after_widget;
		
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( $instance, array(
			'title'          => '',
			'posts_per_page' => 10
		) );
			
		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id('title'), __( 'Title:', 'mcm-cpts' ), $this->get_field_id('title'), $this->get_field_name('title'), esc_attr( $instance['title'] ), 'width: 95%;' );
		
		printf( '<p>%s <input type="text" name="%s" value="%s" size="3" /></p>', __( 'How many results should be returned?', 'mcm-cpts' ), $this->get_field_name('posts_per_page'), esc_attr( $instance['posts_per_page'] ) );
		
	}
}