<?php

defined( 'ABSPATH' ) || exit;

$title    = __( 'Design for what comes next.', 'nova-elements' );
$subtitle = __( 'Build with clarity and purpose.', 'nova-elements' );
$tag      = 'div';
$gradient = false;
?>
<section class="nova-admin__panel nova-admin__presets" aria-labelledby="nova-presets-title">
	<div class="nova-admin__panel-head">
		<h2 id="nova-presets-title"><?php esc_html_e( 'Widget layouts', 'nova-elements' ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=nova-elements-settings' ) ); ?>"><?php esc_html_e( 'Set default', 'nova-elements' ); ?></a>
	</div>
	<ul>
		<?php foreach ( $presets as $preset => $label ) : ?>
			<li class="<?php echo $default_preset === $preset ? 'nova-admin__preset--default' : ''; ?>">
				<div class="nova-admin__preview" aria-hidden="true">
					<?php require dirname( __DIR__ ) . '/widgets/heading.php'; ?>
				</div>
				<div>
					<strong><?php echo esc_html( $label ); ?></strong>
					<?php if ( $default_preset === $preset ) : ?>
						<span><?php esc_html_e( 'Site default', 'nova-elements' ); ?></span>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
