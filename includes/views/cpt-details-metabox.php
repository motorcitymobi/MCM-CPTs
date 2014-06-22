<?php
wp_nonce_field( 'mcm_details_metabox_save', 'mcm_details_metabox_nonce' );

echo '<div style="width: 90%; float: left">';

	printf( '<p><label>%s<input type="text" name="ap[_cpt_text]" value="%s" /></label></p>', __( 'Custom Text: ', 'mcm-cpts' ), esc_attr( genesis_get_custom_field('_cpt_text') ) );
	printf( '<p><span class="description">%s</span></p>', __( 'Custom text shows on the featured cpts widget image.', 'mcm-cpts' ) );

echo '</div><br style="clear: both;" /><br /><br />';

$pattern = '<p><label>%s<br /><input type="text" name="ap[%s]" value="%s" /></label></p>';

echo '<div style="width: 45%; float: left">';

	foreach ( (array) $this->custompost_details['col1'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( genesis_get_custom_field( $key ) ) );
	}
	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[custompost_details]\')', __( 'Send to text editor', 'mcm-cpts' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

	foreach ( (array) $this->custompost_details['col2'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( genesis_get_custom_field( $key ) ) );
	}

echo '</div><br style="clear: both;" /><br /><br />';

echo '<div style="width: 45%; float: left;">';

	printf( __( '<p><label>Enter Map Embed Code:<br /><textarea name="ap[_cpt_map]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'mcm-cpts' ), 'width: 99%;', htmlentities( genesis_get_custom_field('_cpt_map') ) );

	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[custompost_map]\')', __( 'Send to text editor', 'mcm-cpts' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

	printf( __( '<p><label>Enter Video Embed Code:<br /><textarea name="ap[_cpt_video]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'mcm-cpts' ), 'width: 99%;', htmlentities( genesis_get_custom_field('_cpt_video') ) );

	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[custompost_video]\')', __( 'Send to text editor', 'mcm-cpts' ) );

echo '</div><br style="clear: both;" />';