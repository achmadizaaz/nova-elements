<?php

defined( 'ABSPATH' ) || exit;

if ( $tabs ) :
	?>
	<nova-tabs class="nova-widget nova-panels nova-panels--<?php echo esc_attr( $preset ); ?><?php echo 'ne-content-toggle' === $this->get_name() ? ' nova-toggle' : ''; ?>" data-initial="<?php echo esc_attr( (string) $initial ); ?>">
		<div class="nova-tabs__layout">
			<div class="nova-tabs__list" aria-label="<?php echo esc_attr( '' !== $tabs_label ? $tabs_label : __( 'Information', 'nova-elements' ) ); ?>" hidden>
				<?php foreach ( $panels as $panel ) : ?>
					<button type="button" class="nova-panel__label"><?php echo esc_html( $panel['label'] ); ?></button>
				<?php endforeach; ?>
			</div>
			<div class="nova-tabs__panels">
				<?php foreach ( $panels as $panel ) : ?>
					<section class="nova-tabs__panel nova-panel__surface">
						<h3 class="nova-tabs__fallback-label"><?php echo esc_html( $panel['label'] ); ?></h3>
						<div class="nova-panel__content"><?php echo wp_kses_post( wpautop( $panel['content'] ) ); ?></div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
	</nova-tabs>
<?php else : ?>
	<nova-accordion class="nova-widget nova-panels nova-panels--<?php echo esc_attr( $preset ); ?>"<?php echo $multiple ? ' data-multiple' : ''; ?>>
		<?php foreach ( $panels as $index => $panel ) : ?>
			<details class="nova-accordion__item nova-panel__surface"<?php echo $initial === $index + 1 ? ' open' : ''; ?>>
				<summary class="nova-panel__label"><?php echo esc_html( $panel['label'] ); ?></summary>
				<div class="nova-panel__content"><?php echo wp_kses_post( wpautop( $panel['content'] ) ); ?></div>
			</details>
		<?php endforeach; ?>
	</nova-accordion>
<?php endif; ?>
