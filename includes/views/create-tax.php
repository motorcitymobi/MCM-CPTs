<?php screen_icon( 'themes' ); ?>
<h2><?php _e( 'CPT Taxonomies', 'mcm-cpts' ); ?></h2>

<div id="col-container">

	<div id="col-right">
	<div class="col-wrap">

		<?php #print_r( get_option( $this->settings_field ) ); ?>

		<h3><?php _e( 'Current CPT Taxonomies', 'mcm-cpts' ); ?></h3>
		<table class="widefat tag fixed" cellspacing="0">
			<thead>
			<tr>
			<th scope="col" class="manage-column column-slug"><?php _e( 'ID', 'mcm-cpts' ); ?></th>
			<th scope="col" class="manage-column column-singular-name"><?php _e( 'Singular Name', 'mcm-cpts' ); ?></th>
			<th scope="col" class="manage-column column-plural-name"><?php _e( 'Plural Name', 'mcm-cpts' ); ?></th>
			</tr>
			</thead>

			<tfoot>
			<tr>
			<th scope="col" class="manage-column column-slug"><?php _e( 'ID', 'mcm-cpts' ); ?></th>
			<th scope="col" class="manage-column column-singular-name"><?php _e( 'Singular Name', 'mcm-cpts'); ?></th>
			<th scope="col" class="manage-column column-plural-name"><?php _e( 'Plural Name', 'mcm-cpts'); ?></th>
			</tr>
			</tfoot>

			<tbody id="the-list" class="list:tag">

				<?php
				$alt = true;

				$cpt_taxonomies = array_merge( $this->custompost_features_taxonomy(), get_option( $this->settings_field ) );

				foreach ( (array) $cpt_taxonomies as $id => $data ) :
				?>

				<tr <?php if ( $alt ) { echo 'class="alternate"'; $alt = false; } else { $alt = true; } ?>>
					<td class="slug column-slug">

					<?php if ( isset( $data['editable'] ) && 0 === $data['editable'] ) : ?>
						<?php echo '<strong>' . esc_html( $id ) . '</strong><br /><br />'; ?>
					<?php else : ?>
						<a class="row-title" href="<?php echo admin_url( 'admin.php?page=' . $this->menu_page . '&amp;view=edit&amp;id=' . esc_html( $id ) ); ?>"><?php echo esc_html( $id ); ?></a>

						<br />

						<div class="row-actions">
							<span class="edit"><a href="<?php echo admin_url( 'admin.php?page=' . $this->menu_page . '&amp;view=edit&amp;id=' . esc_html( $id ) ); ?>"><?php _e( 'Edit', 'mcm-cpts' ); ?></a> | </span>
							<span class="delete"><a class="delete-tag" href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=' . $this->menu_page . '&amp;action=delete&amp;id=' . esc_html( $id ) ), 'mcm-action_delete-taxonomy' ); ?>"><?php _e( 'Delete', 'mcm-cpts' ); ?></a></span>
						</div>
					<?php endif; ?>

					</td>
					<td class="singular-name column-singular-name"><?php echo esc_html( $data['labels']['singular_name'] )?></td>
					<td class="plural-name column-plural-name"><?php echo esc_html( $data['labels']['name'] )?></td>
				</tr>

				<?php endforeach; ?>

			</tbody>
		</table>

	</div>
	</div><!-- /col-right -->

	<div id="col-left">
	<div class="col-wrap">

		<div class="form-wrap">
			<h3><?php _e( 'Add New CPT Taxonomy', 'mcm-cpts' ); ?></h3>

			<form method="post" action="<?php echo admin_url( 'admin.php?page=register-taxonomies&amp;action=create' ); ?>">
			<?php wp_nonce_field( 'mcm-action_create-taxonomy' ); ?>

			<div class="form-field">
				<label for="taxonomy-id"><?php _e( 'ID', 'mcm-cpts' ); ?></label>
				<input name="mcm_taxonomy[id]" id="taxonomy-id" type="text" value="" size="40" />
				<p><?php _e( 'The unique ID is used to register the taxonomy.<br />(no spaces, underscores, or special characters)', 'mcm-cpts' ); ?></p>
			</div>

			<div class="form-field form-required">
				<label for="taxonomy-name"><?php _e( 'Plural Name', 'mcm-cpts' ); ?></label>
				<input name="mcm_taxonomy[name]" id="taxonomy-name" type="text" value="" size="40" />
				<p><?php _e( 'Example: "CustomPost Types" or "Locations"', 'mcm-cpts' ); ?></p>
			</div>

			<div class="form-field form-required">
				<label for="taxonomy-singular-name"><?php _e( 'Singular Name', 'mcm-cpts' ); ?></label>
				<input name="mcm_taxonomy[singular_name]" id="taxonomy-singular-name" type="text" value="" size="40" />
				<p><?php _e( 'Example: "CustomPost Type" or "Location"', 'mcm-cpts' ); ?></p>
			</div>

			<?php submit_button( __( 'Add New Taxonomy', 'mcm-cpts' ), 'secondary' ); ?>
			</form>
		</div>

	</div>
	</div><!-- /col-left -->

</div><!-- /col-container -->