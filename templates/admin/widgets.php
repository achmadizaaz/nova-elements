<?php

use NovaElements\Core\Settings;

defined( 'ABSPATH' ) || exit;
?>
<section class="nova-admin__panel nova-admin__widgets" aria-labelledby="nova-widgets-title">
	<form action="options.php" method="post" data-nova-widgets>
		<?php settings_fields( Settings::WIDGETS ); ?>
		<div class="nova-admin__panel-head">
			<div>
				<h2 id="nova-widgets-title"><?php esc_html_e( 'Widget Manager', 'nova-elements' ); ?></h2>
				<p><?php esc_html_e( 'Disabling a widget hides it from the editor and pages that use it.', 'nova-elements' ); ?></p>
			</div>
			<div class="nova-admin__actions">
				<button type="button" class="button" data-nova-bulk="enable" hidden><?php esc_html_e( 'Enable all', 'nova-elements' ); ?></button>
				<button type="button" class="button" data-nova-bulk="disable" hidden><?php esc_html_e( 'Disable all', 'nova-elements' ); ?></button>
				<?php submit_button( __( 'Save changes', 'nova-elements' ), 'primary', 'submit', false ); ?>
			</div>
		</div>
		<div class="nova-admin__filters" data-nova-filters hidden>
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Search widgets', 'nova-elements' ); ?></span>
				<input type="search" placeholder="<?php esc_attr_e( 'Search widgets…', 'nova-elements' ); ?>" data-nova-search>
			</label>
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Filter by category', 'nova-elements' ); ?></span>
				<select data-nova-category>
					<option value=""><?php esc_html_e( 'All categories', 'nova-elements' ); ?></option>
					<?php foreach ( $categories as $id => $label ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</div>
		<table>
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Widget', 'nova-elements' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Category', 'nova-elements' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Enabled', 'nova-elements' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $available as $id => $widget ) : ?>
					<tr data-nova-widget="<?php echo esc_attr( $widget['category'] ); ?>">
						<th scope="row">
							<strong><?php echo esc_html( $widget['title'] ); ?></strong>
							<span><?php echo esc_html( $widget['description'] ); ?></span>
							<span class="nova-admin__mobile-category"><?php echo esc_html( $categories[ $widget['category'] ] ); ?></span>
						</th>
						<td><?php echo esc_html( $categories[ $widget['category'] ] ); ?></td>
						<td>
							<label class="nova-admin__switch">
								<span class="screen-reader-text"><?php echo esc_html( $widget['title'] ); ?></span>
								<input type="hidden" name="<?php echo esc_attr( Settings::WIDGETS . '[' . $id . ']' ); ?>" value="0">
								<input type="checkbox" role="switch" name="<?php echo esc_attr( Settings::WIDGETS . '[' . $id . ']' ); ?>" value="1" <?php checked( isset( $enabled[ $id ] ) ); ?>>
							</label>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="nova-admin__empty" data-nova-empty role="status" <?php if ( $available ) : ?>hidden<?php endif; ?>><?php esc_html_e( 'No widgets match this view.', 'nova-elements' ); ?></p>
	</form>
</section>
