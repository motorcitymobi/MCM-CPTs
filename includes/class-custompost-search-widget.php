<?php
/**
 * This widget presents a search widget which uses cpts' taxonomy for search fields.
 *
 * @package MCM
 * @since 2.0
 * @author Ron Rennick
 */
class MCM_CPTs_Search_Widget extends WP_Widget {

	function MCM_CPTs_Search_Widget() {
		$widget_ops = array( 'classname' => 'custompost-search', 'description' => __( 'Display custompost search dropdown', 'mcm-cpts' ) );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'custompost-search' );
		$this->WP_Widget( 'custompost-search', __( 'MCM - CPT Search', 'mcm-cpts' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
			'button_text' => __( 'Search CustomPosts', 'mcm-cpts' )
		) );

		global $_mcm_taxonomies;

		$cpts_taxonomies = $_mcm_taxonomies->get_taxonomies();

		extract( $args );

		echo $before_widget;

		if ( $instance['title'] ) echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

		echo '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" ><input type="hidden" value="" name="s" /><input type="hidden" value="cpt" name="post_type" />';

		foreach ( $cpts_taxonomies as $tax => $data ) {
			if ( ! isset( $instance[$tax] ) || ! $instance[$tax] )
				continue;

			$terms = get_terms( $tax, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 100, 'hierarchical' => false ) );
			if ( empty( $terms ) )
				continue;

			$current = ! empty( $wp_query->query_vars[$tax] ) ? $wp_query->query_vars[$tax] : '';
			echo "<select name='$tax' id='$tax' class='mcm-taxonomy'>\n\t";
			echo '<option value="" ' . selected( $current == '', true, false ) . ">{$data['labels']['name']}</option>\n";
			foreach ( (array) $terms as $term )
				echo "\t<option value='{$term->slug}' " . selected( $current, $term->slug, false ) . ">{$term->name}</option>\n";

			echo '</select>';
		}

		echo '<input type="submit" id="searchsubmit" class="searchsubmit" value="'. esc_attr( $instance['button_text'] ) .'" />
		<div class="clear"></div>
	</form>';

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
			'button_text' => __( 'Search CustomPosts', 'mcm-cpts' )
		) );

		global $_mcm_taxonomies;

		$cpts_taxonomies = $_mcm_taxonomies->get_taxonomies();
		$new_widget = empty( $instance );

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id( 'title' ), __( 'Title:', 'mcm-cpts' ), $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), esc_attr( $instance['title'] ), 'width: 95%;' );
		?>
		<h5><?php _e( 'Include these taxonomies in the search widget', 'mcm-cpts' ); ?></h5>
		<?php
		foreach ( (array) $cpts_taxonomies as $tax => $data ) {

			$terms = get_terms( $tax );
			if ( empty( $terms ) )
				continue;

			$checked = isset( $instance[ $tax ] ) && $instance[ $tax ];

			printf( '<p><label><input id="%s" type="checkbox" name="%s" value="1" %s />%s</label></p>', $this->get_field_id( 'tax' ), $this->get_field_name( $tax ), checked( 1, $checked, 0 ), esc_html( $data['labels']['name'] ) );

		}
		
		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id( 'button_text' ), __( 'Button Text:', 'mcm-cpts' ), $this->get_field_id( 'button_text' ), $this->get_field_name( 'button_text' ), esc_attr( $instance['button_text'] ), 'width: 95%;' );
	}
}