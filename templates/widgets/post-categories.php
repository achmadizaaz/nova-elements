<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="nova-widget nova-terms nova-terms--<?php echo esc_attr( $preset ); ?>">
	<?php if ( ! $terms ) : ?>
		<p class="nova-context-empty"><?php esc_html_e( 'No categories match this selection.', 'nova-elements' ); ?></p>
	<?php else : ?>
		<nav aria-label="<?php echo esc_attr( $label ); ?>">
			<ul class="nova-terms__list" role="list">
				<?php foreach ( $terms as $item ) : ?>
					<?php
					$term = $item['term'];
					$active = $current && $current->term_id === $term->term_id && $current->taxonomy === $term->taxonomy;
					?>
					<li>
						<a class="nova-terms__link" href="<?php echo esc_url( $item['url'] ); ?>"<?php if ( $active ) : ?> aria-current="page"<?php endif; ?>>
							<span class="nova-terms__name"><?php echo esc_html( $term->name ); ?></span>
							<?php if ( $show_count ) : ?>
								<span class="nova-terms__count"><?php
									/* translators: %s: number of posts assigned to this term. */
									echo esc_html( sprintf( _n( '%s post', '%s posts', $term->count, 'nova-elements' ), number_format_i18n( $term->count ) ) );
								?></span>
							<?php endif; ?>
							<?php if ( $show_description && '' !== $term->description ) : ?>
								<span class="nova-terms__description"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $term->description ), 22 ) ); ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
</div>
