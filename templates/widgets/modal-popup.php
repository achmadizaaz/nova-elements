<?php

defined( 'ABSPATH' ) || exit;
?>
<nova-modal class="nova-widget nova-modal nova-modal--<?php echo esc_attr( $preset ); ?><?php echo '' !== $drawer ? esc_attr( ' nova-drawer nova-drawer--' . $drawer ) : ''; ?>">
	<button type="button" class="nova-modal__open" aria-haspopup="dialog" hidden><?php echo esc_html( '' !== $trigger ? $trigger : __( 'Open dialog', 'nova-elements' ) ); ?></button>
	<div class="nova-modal__content">
		<?php if ( '' !== $title ) : ?>
			<<?php echo esc_html( $tag ); ?> class="nova-modal__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
		<?php endif; ?>
		<?php if ( '' !== $description ) : ?>
			<p class="nova-modal__description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<?php if ( $linked ) : ?>
			<a class="nova-modal__action" <?php $this->print_render_attribute_string( 'modal_link' ); ?>><?php echo esc_html( $cta ); ?></a>
		<?php endif; ?>
	</div>
	<dialog class="nova-modal__dialog" aria-label="<?php echo esc_attr( '' !== $title ? $title : ( '' !== $trigger ? $trigger : __( 'Details', 'nova-elements' ) ) ); ?>">
		<div class="nova-modal__shell">
			<button type="button" class="nova-modal__close" autofocus aria-label="<?php esc_attr_e( 'Close dialog', 'nova-elements' ); ?>"><span aria-hidden="true">&times;</span></button>
		</div>
	</dialog>
</nova-modal>
