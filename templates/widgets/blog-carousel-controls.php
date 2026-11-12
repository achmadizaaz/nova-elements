<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="nova-blog__carousel-controls" hidden>
	<?php if ( $autoplay ) : ?>
		<button type="button" data-rotation data-play="<?php esc_attr_e( 'Start rotation', 'nova-elements' ); ?>" data-pause="<?php esc_attr_e( 'Pause rotation', 'nova-elements' ); ?>"><?php esc_html_e( 'Pause rotation', 'nova-elements' ); ?></button>
	<?php endif; ?>
	<?php if ( 'yes' === ( $settings['carousel_arrows'] ?? 'yes' ) ) : ?>
		<button type="button" data-step="-1"><?php esc_html_e( 'Previous slides', 'nova-elements' ); ?></button>
		<button type="button" data-step="1"><?php esc_html_e( 'Next slides', 'nova-elements' ); ?></button>
	<?php endif; ?>
	<?php if ( 'yes' === ( $settings['carousel_dots'] ?? 'yes' ) ) : ?>
		<?php /* translators: %s: slide page number. */ ?>
		<div class="nova-blog__carousel-pages" role="group" aria-label="<?php esc_attr_e( 'Slide pages', 'nova-elements' ); ?>" data-label="<?php esc_attr_e( 'Slide page %s', 'nova-elements' ); ?>"></div>
	<?php endif; ?>
</div>
