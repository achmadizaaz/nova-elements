<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="nova-widget nova-button nova-button--<?php echo esc_attr( $preset ); ?> nova-button--icon-<?php echo esc_attr( $position ); ?>">
	<<?php echo esc_html( $tag ); ?> class="nova-button__surface" <?php if ( $linked ) { $this->print_render_attribute_string( 'button_link' ); } ?>>
		<span class="nova-button__copy">
			<span class="nova-button__label"><?php echo esc_html( $label ); ?></span>
			<?php if ( '' !== $detail ) : ?>
				<span class="nova-button__detail"><?php echo esc_html( $detail ); ?></span>
			<?php endif; ?>
		</span>
		<?php if ( '' !== $icon ) : ?>
			<span class="nova-button__icon" aria-hidden="true"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed arrow or rendered by Elementor's icon API. ?></span>
		<?php endif; ?>
	</<?php echo esc_html( $tag ); ?>>
</div>
