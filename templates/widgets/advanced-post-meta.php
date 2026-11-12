<?php

defined( 'ABSPATH' ) || exit;

if ( ! $items && ! $empty_message ) {
	return;
}
?>
<div class="nova-widget nova-post-meta nova-post-meta--<?php echo esc_attr( $preset ); ?>">
	<?php if ( $empty_message ) : ?>
		<p class="nova-post-meta__empty"><?php echo esc_html( $empty_message ); ?></p>
	<?php else : ?>
		<dl class="nova-post-meta__list">
			<?php foreach ( $items as $item ) : ?>
				<div class="nova-post-meta__item">
					<dt class="nova-post-meta__label"><?php echo esc_html( $item['label'] ); ?></dt>
					<dd class="nova-post-meta__value"><?php
						foreach ( $item['values'] as $index => $value ) {
							if ( $index ) {
								echo esc_html_x( ', ', 'separator between post metadata values', 'nova-elements' );
							}
							$url = esc_url( $value['url'] ?? '' );
							if ( ! empty( $value['datetime'] ) ) {
								printf( '<time datetime="%s">%s</time>', esc_attr( $value['datetime'] ), esc_html( $value['text'] ) );
							} elseif ( $url ) {
								printf( '<a href="%s">%s</a>', $url, esc_html( $value['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped above.
							} else {
								echo esc_html( $value['text'] );
							}
						}
					?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	<?php endif; ?>
</div>
