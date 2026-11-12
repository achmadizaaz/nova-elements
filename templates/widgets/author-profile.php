<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="nova-widget nova-author nova-author--<?php echo esc_attr( $preset ); ?>">
	<?php if ( ! $profile ) : ?>
		<p class="nova-context-empty"><?php esc_html_e( 'No public author profile is available.', 'nova-elements' ); ?></p>
	<?php else : ?>
		<div class="nova-author__panel">
			<div class="nova-author__portrait" aria-hidden="true">
				<?php if ( $profile['avatar'] ) : ?>
					<img src="<?php echo esc_url( $profile['avatar'] ); ?>" alt="" width="256" height="256" loading="lazy" decoding="async">
				<?php else : ?>
					<span><?php echo esc_html( $profile['initials'] ); ?></span>
				<?php endif; ?>
			</div>
			<div class="nova-author__copy">
				<?php if ( $profile['eyebrow'] ) : ?>
					<p class="nova-author__eyebrow"><?php echo esc_html( $profile['eyebrow'] ); ?></p>
				<?php endif; ?>
				<<?php echo esc_html( $tag ); ?> class="nova-author__name"><?php echo esc_html( $profile['name'] ); ?></<?php echo esc_html( $tag ); ?>>
				<?php if ( $profile['bio'] ) : ?>
					<p class="nova-author__bio"><?php echo esc_html( $profile['bio'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $profile['archive'] || $profile['website'] ) : ?>
				<div class="nova-author__actions">
					<?php if ( $profile['archive'] ) : ?>
						<a href="<?php echo esc_url( $profile['archive'] ); ?>" aria-label="<?php
							/* translators: %s: public author display name. */
							echo esc_attr( sprintf( __( 'View articles by %s', 'nova-elements' ), $profile['name'] ) );
						?>"><?php esc_html_e( 'View articles', 'nova-elements' ); ?></a>
					<?php endif; ?>
					<?php if ( $profile['website'] ) : ?>
						<a href="<?php echo esc_url( $profile['website'] ); ?>"><?php esc_html_e( 'Website', 'nova-elements' ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
