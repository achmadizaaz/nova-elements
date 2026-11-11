<?php

use NovaElements\Core\DesignSystem;
use NovaElements\Core\Settings;

defined( 'ABSPATH' ) || exit;

$themes = DesignSystem::themes();
$active_theme = $global_style['theme'] ?? '';
$effective_colors = $palette + ( $themes[ $active_theme ]['colors'] ?? array() );
$theme_labels = array( '' => __( 'Original Nova palette', 'nova-elements' ) ) + DesignSystem::presets();
$panel_titles = array(
	'typography' => __( 'Typography', 'nova-elements' ),
	'components' => __( 'Components', 'nova-elements' ),
	'responsive' => __( 'Responsive spacing', 'nova-elements' ),
	'motion' => __( 'Motion', 'nova-elements' ),
);
?>
<div class="nova-admin__design">
	<form action="options.php" method="post" class="nova-admin__design-form">
		<?php settings_fields( Settings::PALETTE ); ?>
		<fieldset class="nova-admin__panel nova-admin__design-panel">
			<legend><?php esc_html_e( 'Global design theme', 'nova-elements' ); ?></legend>
			<p><?php esc_html_e( 'Choose shared styling defaults. Widget layouts are selected independently in Elementor.', 'nova-elements' ); ?></p>
			<div class="nova-admin__theme-grid">
				<?php foreach ( $theme_labels as $id => $label ) : ?>
					<?php $theme_colors = $themes[ $id ]['colors'] ?? array(); ?>
					<label class="nova-admin__theme-choice">
						<input type="radio" name="<?php echo esc_attr( Settings::GLOBAL_STYLE . '[theme]' ); ?>" value="<?php echo esc_attr( $id ); ?>" <?php checked( $active_theme, $id ); ?>>
						<span class="nova-admin__theme-card">
							<span class="nova-admin__theme-swatches" aria-hidden="true">
								<?php foreach ( array( 'surface', 'primary', 'accent' ) as $key ) : ?>
									<span style="--nova-swatch:<?php echo esc_attr( $theme_colors[ $key ] ?? $tokens[ $key ][1] ); ?>"></span>
								<?php endforeach; ?>
							</span>
							<strong><?php echo esc_html( $label ); ?></strong>
						</span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<fieldset class="nova-admin__panel nova-admin__design-panel">
			<legend><?php esc_html_e( 'Colors', 'nova-elements' ); ?></legend>
			<p><?php esc_html_e( 'Custom colors take priority. Empty fields inherit the selected theme or the original palette.', 'nova-elements' ); ?></p>
			<div class="nova-admin__tokens">
				<?php foreach ( $tokens as $key => list( $label, $fallback ) ) : ?>
					<label>
						<span><?php echo esc_html( $label ); ?></span>
						<input type="text" name="<?php echo esc_attr( Settings::PALETTE . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( $palette[ $key ] ?? '' ); ?>" placeholder="<?php echo esc_attr( $effective_colors[ $key ] ?? $fallback ); ?>" pattern="#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})" title="<?php esc_attr_e( 'Use a hex color, such as #4f46e5.', 'nova-elements' ); ?>">
					</label>
				<?php endforeach; ?>
			</div>
			<label class="nova-admin__inherit">
				<input type="checkbox" name="<?php echo esc_attr( Settings::GLOBAL_STYLE . '[elementor_colors]' ); ?>" value="inherit" <?php checked( $global_style['elementor_colors'] ?? '', 'inherit' ); ?>>
				<?php esc_html_e( 'Use Elementor Global Colors for roles without a theme color or custom override', 'nova-elements' ); ?>
			</label>
		</fieldset>

		<div class="nova-admin__design-fields">
			<?php foreach ( DesignSystem::fields() as $panel => $fields ) : ?>
				<fieldset class="nova-admin__panel nova-admin__design-panel">
					<legend><?php echo esc_html( $panel_titles[ $panel ] ); ?></legend>
					<?php foreach ( $fields as $key => list( $label, $choices ) ) : ?>
						<label class="nova-admin__design-field">
							<span><?php echo esc_html( $label ); ?></span>
							<select name="<?php echo esc_attr( Settings::GLOBAL_STYLE . '[' . $key . ']' ); ?>">
								<?php foreach ( $choices as $value => $choice ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $global_style[ $key ] ?? '', $value ); ?>><?php echo esc_html( $choice ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endforeach; ?>
		</div>
		<?php submit_button( __( 'Save global styles', 'nova-elements' ) ); ?>
	</form>
	<aside aria-label="<?php esc_attr_e( 'Widget layout previews', 'nova-elements' ); ?>">
		<?php require __DIR__ . '/presets.php'; ?>
	</aside>
</div>
