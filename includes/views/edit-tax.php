`<?php
$options = get_option( $this->settings_field );

if ( array_key_exists( $_REQUEST['id'], (array) $options ) ) {
	$taxonomy = stripslashes_deep( $options[$_REQUEST['id']] );
} else {
	wp_die( __( "Nice try, partner. But that taxonomy doesn't exist or can't be edited. Click back and try again.", 'mcm-cpts' ) );
}
?>

<?php screen_icon( 'plugins' ); ?>
<h2><?php _e( 'Edit Taxonomy', 'mcm-cpts' ); ?></h2>

<form method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->menu_page . '&amp;action=edit' ); ?>">
<?php wp_nonce_field( 'mcm-action_edit-taxonomy' ); ?>
<table class="form-table">

	<tr class="form-field">
		<th scope="row" valign="top"><label for="mcm_taxonomy[id]"><?php _e( 'ID', 'mcm-cpts' ); ?></label></th>
		<td>
		<input type="text" value="<?php echo esc_html( $_REQUEST['id'] ); ?>" size="40" disabled="disabled" />
		<input name="mcm_taxonomy[id]" id="mcm_taxonomy[id]" type="hidden" value="<?php echo esc_html( $_REQUEST['id'] ); ?>" size="40" />
		<p class="description"><?php _e( 'The unique ID is used to register the taxonomy. (cannot be changed)', 'mcm-cpts' ); ?></p></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="mcm_taxonomy[name]"><?php _e( 'Plural Name', 'mcm-cpts' ); ?></label></th>
		<td><input name="mcm_taxonomy[name]" id="mcm_taxonomy[name]" type="text" value="<?php echo esc_html( $taxonomy['labels']['name'] ); ?>" size="40" />
		<p class="description"><?php _e( 'Example: "CustomPost Types" or "Locations"', 'mcm-cpts' ); ?></p></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="mcm_taxonomy[singular_name]"><?php _e( 'Singular Name', 'mcm-cpts' ); ?></label></th>
		<td><input name="mcm_taxonomy[singular_name]" id="mcm_taxonomy[singular_name]" type="text" value="<?php echo esc_html( $taxonomy['labels']['singular_name'] ); ?>" size="40" />
		<p class="description"><?php _e( 'Example: "CustomPost Type" or "Location"', 'mcm-cpts' ); ?></p></td>
	</tr>

</table>

<?php submit_button( __( 'Update', 'mcm-cpts' ) ); ?>

</form>