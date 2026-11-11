<?php

use NovaElements\Core\Settings;

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap nova-admin">
	<header class="nova-admin__top">
		<a class="nova-admin__brand" href="<?php echo esc_url( admin_url( 'admin.php?page=nova-elements' ) ); ?>">
			<span class="nova-admin__mark" aria-hidden="true">N</span>
			<strong><?php esc_html_e( 'Nova Elements', 'nova-elements' ); ?></strong>
			<span class="nova-admin__version"><?php echo esc_html( 'v' . $version ); ?></span>
		</a>
		<nav aria-label="<?php esc_attr_e( 'Nova Elements pages', 'nova-elements' ); ?>">
			<?php foreach ( $pages as $slug => $label ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>" <?php if ( $page === $slug ) : ?>aria-current="page"<?php endif; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
	</header>

	<?php settings_errors(); ?>

	<?php if ( 'nova-elements' === $page ) : ?>
		<section class="nova-admin__hero">
			<div>
				<h1><?php esc_html_e( 'Nova Elements', 'nova-elements' ); ?></h1>
				<p><?php esc_html_e( 'Manage Elementor widgets, preview heading styles, and set your site palette.', 'nova-elements' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=nova-elements-widgets' ) ); ?>"><?php esc_html_e( 'Manage widgets', 'nova-elements' ); ?> <span aria-hidden="true">&rarr;</span></a>
			</div>
			<div class="nova-admin__hero-art" aria-hidden="true"><span></span><span></span><span></span></div>
		</section>
		<dl class="nova-admin__stats">
			<div><dt><span class="dashicons dashicons-screenoptions" aria-hidden="true"></span><?php esc_html_e( 'Available widgets', 'nova-elements' ); ?></dt><dd><?php echo esc_html( number_format_i18n( count( $available ) ) ); ?></dd></div>
			<div><dt><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php esc_html_e( 'Active widgets', 'nova-elements' ); ?></dt><dd><?php echo esc_html( number_format_i18n( count( $active ) ) ); ?></dd></div>
			<div><dt><span class="dashicons dashicons-art" aria-hidden="true"></span><?php esc_html_e( 'Style presets', 'nova-elements' ); ?></dt><dd><?php echo esc_html( number_format_i18n( $preset_count ) ); ?></dd></div>
			<div><dt><span class="dashicons dashicons-category" aria-hidden="true"></span><?php esc_html_e( 'Categories', 'nova-elements' ); ?></dt><dd><?php echo esc_html( number_format_i18n( count( $category_counts ) ) ); ?></dd></div>
			<div><dt><span class="dashicons dashicons-shield" aria-hidden="true"></span><?php esc_html_e( 'Elementor requirement', 'nova-elements' ); ?></dt><dd class="nova-admin__status"><?php echo $elementor_ready ? esc_html__( 'Met', 'nova-elements' ) : esc_html__( 'Action required', 'nova-elements' ); ?></dd></div>
		</dl>
		<div class="nova-admin__dashboard">
			<section class="nova-admin__panel nova-admin__categories">
				<h2><?php esc_html_e( 'Widget categories', 'nova-elements' ); ?></h2>
				<ul>
					<li><span><?php esc_html_e( 'All widgets', 'nova-elements' ); ?></span><strong><?php echo esc_html( number_format_i18n( count( $available ) ) ); ?></strong></li>
					<?php foreach ( $categories as $id => $label ) : ?>
						<li><span><?php echo esc_html( $label ); ?></span><strong><?php echo esc_html( number_format_i18n( $category_counts[ $id ] ?? 0 ) ); ?></strong></li>
					<?php endforeach; ?>
				</ul>
			</section>
			<?php require $template_path . 'widgets.php'; ?>
			<?php require $template_path . 'presets.php'; ?>
		</div>
	<?php else : ?>
		<h1 class="nova-admin__page-title"><?php echo esc_html( $pages[ $page ] ); ?></h1>
		<?php if ( 'nova-elements-widgets' === $page ) : ?>
			<?php require $template_path . 'widgets.php'; ?>
		<?php elseif ( 'nova-elements-styles' === $page ) : ?>
			<?php require $template_path . 'global-styles.php'; ?>
		<?php elseif ( 'nova-elements-settings' === $page ) : ?>
			<section class="nova-admin__panel nova-admin__settings">
				<h2><?php esc_html_e( 'Widget defaults', 'nova-elements' ); ?></h2>
				<form action="options.php" method="post">
					<?php settings_fields( Settings::PREFERENCES ); ?>
					<label for="nova-default-preset"><?php esc_html_e( 'Default widget layout', 'nova-elements' ); ?></label>
					<select id="nova-default-preset" name="<?php echo esc_attr( Settings::PREFERENCES . '[preset]' ); ?>" aria-describedby="nova-default-preset-help">
						<?php foreach ( $presets as $id => $label ) : ?>
							<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $default_preset, $id ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p id="nova-default-preset-help"><?php esc_html_e( 'Applies to existing and new widgets set to Site default. Nova Elements keeps these settings if you delete the plugin.', 'nova-elements' ); ?></p>
					<?php submit_button(); ?>
				</form>
			</section>
		<?php elseif ( 'nova-elements-integrations' === $page ) : ?>
			<section class="nova-admin__panel nova-admin__settings">
				<h2><?php esc_html_e( 'Elementor', 'nova-elements' ); ?></h2>
				<p><?php echo esc_html( $elementor_info ); ?></p>
				<h2><?php esc_html_e( 'WooCommerce', 'nova-elements' ); ?></h2>
				<p><?php echo $woo_active ? esc_html__( 'WooCommerce is active. This Nova Elements release contains no commerce widgets.', 'nova-elements' ) : esc_html__( 'WooCommerce is inactive. It is optional for this release.', 'nova-elements' ); ?></p>
			</section>
		<?php else : ?>
			<section class="nova-admin__panel nova-admin__settings">
				<h2><?php esc_html_e( 'Installed versions', 'nova-elements' ); ?></h2>
				<dl class="nova-admin__versions">
					<dt><?php esc_html_e( 'Nova Elements', 'nova-elements' ); ?></dt><dd><?php echo esc_html( $version ); ?></dd>
					<dt><?php esc_html_e( 'WordPress', 'nova-elements' ); ?></dt><dd><?php echo esc_html( get_bloginfo( 'version' ) ); ?></dd>
					<dt><?php esc_html_e( 'PHP', 'nova-elements' ); ?></dt><dd><?php echo esc_html( PHP_VERSION ); ?></dd>
					<dt><?php esc_html_e( 'Elementor', 'nova-elements' ); ?></dt><dd><?php echo esc_html( $elementor_info ); ?></dd>
				</dl>
			</section>
		<?php endif; ?>
	<?php endif; ?>
</div>
