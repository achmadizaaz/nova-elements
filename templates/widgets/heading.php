<?php

defined( 'ABSPATH' ) || exit;

// The caller allowlists the tag and preset. The template escapes all text.
$classes = 'nova-widget nova-heading nova-heading--' . $preset;
if ( $gradient ) {
	$classes .= ' nova-heading--gradient-text';
}
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<?php if ( '' !== $title ) : ?>
		<<?php echo esc_html( $tag ); ?> class="nova-heading__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
	<?php endif; ?>
	<?php if ( '' !== $subtitle ) : ?>
		<p class="nova-heading__subtitle"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>
</div>
